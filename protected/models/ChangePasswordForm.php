<?php
namespace app\models;

use yii\base\Model;
use Yii;

class ChangePasswordForm extends Model
{
    /**
     * @var string Old user password
     */
    public $oldPassword;

    /**
     * @var string New password
     */
    public $newPassword;

    /**
     * @var string Re New password
     */
    public $reNewPassword;


    /**
     * @var User current logged user
     */
    private $_user;

    /**
     * @return array the validation rules.
     */
    public function rules() {
        return [
            [['oldPassword','newPassword','reNewPassword'], 'required'],
            [['oldPassword','newPassword','reNewPassword'], 'string', 'min'=>3],
            ['newPassword', 'compare', 'compareAttribute'=>'reNewPassword'],
            [['oldPassword'], function($attr) {
                if(!$this->getUser()->validatePassword($this->$attr)) {
                    $this->addError($attr, \Yii::t("app", "Old password is invalid"));
                }
            }],
        ];
    }

    private function getUser() {
        if(!$this->_user) {
            $this->_user = \Yii::$app->user->identity;
        }
        return $this->_user;
    }

    public function attributeLabels() {
        return [
            "oldPassword"=>Yii::t("app", "Old password"),
            "newPassword"=>Yii::t("app", "New password"),
            "reNewPassword"=>Yii::t("app", "Repeat new password"),
        ];
    }
}