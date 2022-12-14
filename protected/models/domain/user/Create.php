<?php


namespace app\models\domain\user;


use app\components\Helper;
use app\models\exceptions\DomainModelException;
use app\models\User;
use yii\db\ActiveRecord;
use Throwable;
use Yii;

class Create
{
    public IUserEntity $entity;

    public function __construct(IUserEntity $entity)
    {
        $this->entity = $entity;
    }

    /**
     * @throws DomainModelException
     * @throws Throwable
     */
    public function execute(): User
    {
        $user = $this->entity->getUser();
        $profile = $this->entity->getProfile();
        $settings = $this->entity->getSettings();
        $models = [$user, $profile, $settings];

        $errors = [];

        /**
         * @var $model ActiveRecord
         */
        foreach ($models as $model) {
            if(!$model->validate()) {
                Helper::appendModelErrors($errors, $model->getErrors());
            }
        }

        if(!empty($errors)) {
            throw new DomainModelException($errors);
        }

        $transaction = Yii::$app->db->beginTransaction();
        try {
            $user->save(false);
            $profile->user_id = $user->id;
            $settings->user_id = $user->id;
            $profile->save(false);
            $settings->save(false);
            $user->populateRelation('profile', $profile);
            $user->populateRelation('settings', $settings);

            $transaction->commit();
        } catch (Throwable $exception) {
            $transaction->rollBack();
            throw $exception;
        }

        return $user;
    }
}