<?php

namespace app\models;
use yii\db\ActiveRecord;
use yii\web\IdentityInterface;
use \DateTime;
use \DateTimeZone;

/**
 * Class User
 *
 * @package app\models
 * @property int $id
 * @property string $login
 * @property string $password
 * @property string $ip
 * @property string $auth_key
 * @property string $access_token
 * @property string $status
 * @property string $last_login_ip
 * @property string $role
 * @property string $registered_at
 * @property string $modified_at
 * @property string $last_login_at
 * @property string $lang_id
 * @property Settings $settings
 * @property Profile $profile
 */
class User extends ActiveRecord implements IdentityInterface
{
    const STATUS_DELETED = 0;
    const STATUS_ACTIVE = 1;

    const ROLE_USER = 'user';

    public function beforeSave($insert) {
        if(!parent::beforeSave($insert)) {
            return false;
        }
        if(!$this->isNewRecord) {
            $this->modified_at = date("Y-m-d H:i:s");
        }
        return true;
    }

    public function rules() {
        return [
            ['login', 'filter', 'filter'=>'trim'],
            ['lang_id', 'default', 'value'=>\Yii::$app->language],
            [['login', 'password'], 'required'],
            ['login', 'unique'],
            ['login', 'string', 'min'=>2, 'max'=>25],
            ['login', 'match', 'pattern' => '/^[a-z0-9_-]+$/i', 'message'=>'Login must contain only [a-z0-9_-] characters'],
            ['status', 'default', 'value'=> self::STATUS_ACTIVE],
            [['ip', 'last_login_ip'], 'string', 'max'=>46],
            [['ip', 'last_login_ip'], 'ip'],
            ['role', 'in', 'range'=>[self::ROLE_USER]],
            [['access_token', 'auth_key'], 'string', 'max'=>32],
            ['lang_id', 'string', 'max'=>5],
            ['status', 'in', 'range'=>[self::STATUS_ACTIVE, self::STATUS_DELETED]],
        ];
    }

    public static function tableName() {
        return "{{%user}}";
    }

    /**
     * @inheritdoc
     */
    public static function findIdentity($id)
    {
        return static::find()
            ->with(["profile", "settings"])
            ->andWhere([
                "id"=>$id,
                "status"=>self::STATUS_ACTIVE,
            ])
            ->one();
    }


    public function setPassword($password) {
        $this->password = \Yii::$app->security->generatePasswordHash($password);
    }

    public function generateAuthKey() {
        $this->auth_key = \Yii::$app->security->generateRandomString();
    }

    public function setAccessToken() {
        $this->access_token = md5(\Yii::$app->security->generateRandomString());
    }
    /**
     * @inheritdoc
     */
    public static function findIdentityByAccessToken($token, $type = null)
    {
        return static::findOne([
            "access_token"=>$token,
            "status"=>self::STATUS_ACTIVE,
        ]);
    }

    /**
     * Finds user by login
     *
     * @param  string      $login
     * @return static|null
     */
    public static function findByLogin($login)
    {
        return static::findOne([
            "login"=>$login,
            "status"=>self::STATUS_ACTIVE,
        ]);
    }

    /**
     * @inheritdoc
     */
    public function getId()
    {
        return $this->getPrimaryKey();
    }

    /**
     * @inheritdoc
     */
    public function getAuthKey()
    {
        return $this->auth_key;
    }

    /**
     * @inheritdoc
     */
    public function validateAuthKey($authKey)
    {
        return $this->auth_key === $authKey;
    }

    /**
     * Validates password
     *
     * @param  string  $password password to validate
     * @return boolean if password provided is valid for current user
     */
    public function validatePassword($password)
    {
        return \Yii::$app->security->validatePassword($password, $this->password);
    }


    public function getProfile() {
        return $this->hasOne(Profile::class, ["user_id"=>"id"]);
    }

    public function getSettings() {
        return $this->hasOne(Settings::class, ["user_id"=>"id"]);
    }

    public function getDateObject($time = 'now') {
        return new DateTime($time, new DateTimeZone($this->settings->timezone));
    }
}
