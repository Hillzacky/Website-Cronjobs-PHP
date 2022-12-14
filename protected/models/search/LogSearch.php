<?php
namespace app\models\search;

use app\models\Log;
use app\models\Schedule;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use yii\data\Sort;
use Yii;

class LogSearch extends Log {
    public $firstDate;
    public $secondDate;

    public $startDate;

    public function rules() {
        return [
            [['schedule_time', 'http_code', 'is_error', 'firstDate', 'secondDate'], 'safe'],
        ];
    }

    public function scenarios() {
        return Model::scenarios();
    }

    public function search($scheduleId, $params) {
        $query = Log::find()->owner(null, Log::tableName().".")
            ->andWhere("schedule_id=:schedule_id", [
                ":schedule_id"=>$scheduleId,
            ]);

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'sort'=> [
                'defaultOrder' => ['schedule_time'=>SORT_DESC]
            ]
        ]);

        // load the search form data and validate
        if (!($this->load($params) && $this->validate())) {
            return $dataProvider;
        }


        $query->andFilterWhere([
            "is_error" => $this->is_error,
            "http_code" => $this->http_code,
        ]);

        if(!empty($this->firstDate) AND !empty($this->secondDate)) {
            $query->andFilterWhere(['between', self::tableName().".schedule_time", $this->firstDate." 00:00:00", $this->secondDate. " 23:59:59"]);
        }

        return $dataProvider;
    }

    /*public function attributeLabels() {
        $labels = parent::attributeLabels();
        $labels['']
        return [
            "id"=>Yii::t("app", "ID"),
            "response"=>Yii::t("app", "Response"),
            "http_code"=>Yii::t("app", "HTTP Code"),
            "error_msg"=>Yii::t("app", "Error Message"),
            "is_error"=>Yii::t("app", "Status"),
        ];
    }*/
}