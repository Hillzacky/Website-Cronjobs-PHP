<?php
namespace app\models\query;

use app\models\Schedule;
use yii\db\ActiveQuery;

class ScheduleQuery extends ActiveQuery
{
    use OwnerTrait;

    public function enabled() {
        return $this->andWhere([
            "status"=>Schedule::STATUS_ENABLED,
        ]);
    }
}