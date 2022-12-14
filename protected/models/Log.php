<?php
namespace app\models;

use app\models\query\LogQuery;
use yii\db\ActiveRecord;
use yii\helpers\ArrayHelper;
use yii\helpers\Json;
use Yii;

class Log extends ActiveRecord
{
    protected $decodedCurlInfo;

    public static function tableName() {
        return "{{%log}}";
    }

    public function getSchedule() {
        return $this->hasOne(Schedule::class, ["id"=>"schedule_id"]);
    }

    public static function find() {
        return new LogQuery(get_called_class());
    }

    public function getConnectTime() {
        return $this->getCurlInfoKey("connect_time", 0);
    }

    public function getTotalTime() {
        if($this->schedule->isHttp()) {
            return $this->getCurlInfoKey("total_time");
        } else {
            return strtotime($this->finish_at) - strtotime($this->start_at);
        }

    }

    public function getSizeDownload() {
        return $this->getCurlInfoKey("size_download");
    }

    protected function getCurlInfoKey($key, $default = null) {
        if(!$this->decodedCurlInfo) {
            try {
                $this->decodedCurlInfo = Json::decode($this->curl_info);
            } catch(\Exception $e) {
                $this->decodedCurlInfo = [];
            }
        }
        return ArrayHelper::getValue($this->decodedCurlInfo, $key, $default);
    }

    public function attributeLabels() {
        return [
            "id"=>Yii::t("app", "ID"),
            "response"=>Yii::t("app", "Response"),
            "http_code"=>Yii::t("app", "HTTP Code"),
            "error_msg"=>Yii::t("app", "Error Message"),
            "is_error"=>Yii::t("app", "Status"),
            'schedule_time'=>Yii::t("app", "Execution time")
        ];
    }

    public function getStatusMsg() {
        return !$this->is_error ? Yii::t("app", "Succeeded") : Yii::t("app", "Failed");
    }

    public function getStatusArray() {
        return [
            0 => Yii::t("app", "Succeeded"),
            1 => Yii::t("app", "Failed"),
        ];
    }

    public function clearByAttributes()
    {

    }
}