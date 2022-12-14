<?php
namespace app\models\search;

use app\models\Category;
use app\models\Schedule;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use yii\db\Query;

class ScheduleSearch extends Schedule {
    public $sendAtFrom;
    public $sendAtTo;

    public function rules() {
        return [
            [['category_id', 'title', 'url', 'status', 'sendAtFrom', 'sendAtTo', 'command_type'], 'safe'],
        ];
    }

    public function scenarios() {
        return Model::scenarios();
    }

    public function search($params) {
        $query = Schedule::find()->owner(null, Schedule::tableName().".")
            ->select([
                Schedule::tableName().".*",
                "SUM({{%stats}}.success) as totalSuccess, SUM({{%stats}}.failed) as totalFailed"
            ])
            ->joinWith('category')
            ->leftJoin('{{%stats}}', "{{%stats}}.schedule_id={{%schedule}}.id")
            ->groupBy('{{%schedule}}.id');

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'sort'=> [
                'defaultOrder' => [
                    'created_at'=>SORT_DESC
                ]
            ],
        ]);

        $categoryTitleField = Category::tableName().".title";

        $dataProvider->sort->attributes['category_id'] = [
            'asc' => [$categoryTitleField => SORT_ASC],
            'desc' => [$categoryTitleField => SORT_DESC],
        ];

        // load the search form data and validate
        if (!($this->load($params) && $this->validate())) {
            $dataProvider->setTotalCount((int) $this->getCountQuery($query)->count());
            return $dataProvider;
        }

        $query->andFilterWhere(['like', self::tableName().".title", $this->title]);
        $query->andFilterWhere(['like', self::tableName().".url", $this->url]);
        if(!empty($this->sendAtFrom) AND !empty($this->sendAtTo)) {
            $query->andFilterWhere(['between', self::tableName().".send_at_user", $this->sendAtFrom." 00:00:00", $this->sendAtTo. " 23:59:59"]);
        }

        $query->andFilterWhere([
            "status" => $this->status,
        ]);
        $query->andFilterWhere([
            "command_type" => $this->command_type,
        ]);

        if($this->category_id === "null") {
            $query->andWhere([
                "category_id" =>null,
            ]);
        } else {
            $query->andFilterWhere([
                "category_id" => $this->category_id,
            ]);
        }

        $dataProvider->setTotalCount((int) $this->getCountQuery($query)->count());
        return $dataProvider;
    }

    private function getCountQuery(Query $query)
    {
        $countQuery = clone $query;
        $countQuery->select=null;
        $countQuery->join=null;
        $countQuery->groupBy=null;
        $countQuery->joinWith=null;
        $countQuery->orderBy=null;
        return $countQuery;
    }
}