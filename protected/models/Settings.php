<?php
namespace app\models;

use app\components\Timezone;
use app\events\ChangedAttributesEvent;
use yii\db\ActiveRecord;
use Yii;

/**
 * Class Settings
 * @property int $user_id
 * @property string $timezone
 * @package app\models
 */
class Settings extends ActiveRecord
{
    const EVENT_TIMEZONE_CHANGED = 'timezoneChanged';

    public static function tableName() {
        return "{{%settings}}";
    }

    public function getUser() {
        return $this->hasOne(User::class, ['id'=>'user_id']);
    }

    public function rules() {
        return [
            ["timezone", 'required'],
            ["timezone", function($attr) {
                if(!Timezone::isValid($this->$attr)) {
                    $this->addError($attr, \Yii::t("app", "Timezone is invalid"));
                }
            }]
        ];
    }

    public function afterSave($insert, $changedAttributes) {
        parent::afterSave($insert, $changedAttributes);

        if(!$insert AND array_key_exists('timezone', $changedAttributes)) {
            $e = new ChangedAttributesEvent();
            $e->changedAttributes = $changedAttributes;
            $this->trigger(self::EVENT_TIMEZONE_CHANGED, $e);
        }
    }

    public function attributeLabels() {
        return [
            'user_id'=>Yii::t("app", "User"),
            'timezone'=>Yii::t("app", "Timezone"),
        ];
    }
}