<?php
namespace app\models;


use app\models\query\StatQuery;
use yii\db\ActiveRecord;
use Yii;

class Stat extends ActiveRecord
{
    public $totalFailed = 0;
    public $totalSuccess = 0;
    public $month;

    public static function tableName() {
        return "{{%stats}}";
    }

    public function getSchedule() {
        return $this->hasOne(Schedule::class, ["schedule_id"=>"id"]);
    }

    public static function find() {
        return new StatQuery(get_called_class());
    }

    public function attributeLabels() {
        return [
            'insert_at'=>Yii::t("app", "Inserted at"),
            'failed'=>Yii::t("app", "Failed"),
            'success'=>Yii::t("app", "Succeeded"),
            'user_id'=>Yii::t("app", "User"),
            'schedule_id'=>Yii::t("app", "Cron Job"),
        ];
    }
}