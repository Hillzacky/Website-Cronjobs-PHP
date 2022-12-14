<?php
namespace app\models\search;

use yii\base\Model;
use app\models\Category;
use yii\data\ActiveDataProvider;

class CategorySearch extends Category
{
    public function rules() {
        return [
            ['title', 'safe'],
        ];
    }

    public function scenarios() {
        return Model::scenarios();
    }

    public function search($params) {
        $query = Category::find()->owner()->orderBy("created_at DESC");

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
        ]);

        // load the search form data and validate
        if (!($this->load($params) && $this->validate())) {
            return $dataProvider;
        }

        // adjust the query by adding the filters
        $query->andFilterWhere(['like', 'title', $this->title]);

        return $dataProvider;
    }
}