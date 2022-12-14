<?php
namespace app\models\query;

/**
 * Class OwnerTrait
 * @package app\models\query
 */
trait OwnerTrait
{
    /**
     * @param $userId int
     * @param $t string
     * @return ScheduleQuery
     */
    public function owner($userId = null, $t = null) {
        if(!$userId) {
            $userId = \Yii::$app->getUser()->getId();
        }
        return parent::andWhere([
            "{$t}user_id"=>$userId
        ]);
    }
}