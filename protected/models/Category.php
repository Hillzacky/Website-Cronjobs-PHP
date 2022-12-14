<?php
namespace app\models;

use app\components\Helper;
use app\models\query\OwnerTrait;
use yii\db\ActiveRecord;
use app\models\query\CategoryQuery;
use Yii;

/**
 * Class Category
 * @property int $id
 * @property string $title
 * @property string $created_at
 * @property string $modified_at
 * @property int $user_id
 * @package app\models
 */
class Category extends ActiveRecord
{
    use OwnerTrait;

    public static function tableName() {
        return "{{%category}}";
    }

    public static function find() {
        return new CategoryQuery(get_called_class());
    }

    public function rules() {
        return [
            [['title'], 'required'],
            ['title', 'string', 'length'=>['min'=>2, 'max'=>60]],
            ['user_id', 'exist', 'targetClass'=>User::class, 'targetAttribute'=>'id'],
        ];
    }

    public function beforeSave($insert) {
        if(!parent::beforeSave($insert)) {
            return false;
        }
        $now = Helper::systemTimestamp();
        if($this->isNewRecord) {
            $this->created_at = $now;
        }
        $this->modified_at = $now;
        return true;
    }

    public function attributeLabels() {
        return [
            "id"=>Yii::t("app", "ID"),
            "title"=>Yii::t("app", "Title"),
            "user_id"=>Yii::t("app", "User"),
            "created_at"=>Yii::t("app", "Created at"),
            "modified_at"=>Yii::t("app", "Modified at"),
        ];
    }
}