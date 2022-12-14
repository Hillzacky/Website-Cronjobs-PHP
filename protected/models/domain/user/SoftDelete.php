<?php
namespace app\models\domain\user;

use app\models\Schedule;
use app\models\User;
use Yii;
use Throwable;

class SoftDelete
{
    private User $user;

    public function __construct(User $user)
    {
        $this->user = $user;
    }

    /**
     * @return bool
     * @throws \Throwable
     */
    public function execute(): bool
    {
        $transaction = Yii::$app->db->beginTransaction();
        try {
            $this->user->updateAttributes([
                "status"=>User::STATUS_DELETED
            ]);
            Schedule::updateAll([
                "status"=>Schedule::STATUS_DISABLED
            ], [
                "user_id"=>$this->user->id
            ]);
            $transaction->commit();
        } catch (Throwable $throwable) {
            $transaction->rollBack();
            throw $throwable;
        }
        return true;
    }
}