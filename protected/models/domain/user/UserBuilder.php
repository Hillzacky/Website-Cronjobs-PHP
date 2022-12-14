<?php
namespace app\models\domain\user;

use app\components\Helper;
use app\models\Profile;
use app\models\Settings;
use app\models\User;
use Cassandra\Set;
use DateTime;
use yii\base\BaseObject;

class UserBuilder implements IUserBuilder
{
    private string $login;

    private string $password;

    private string $email;

    private ?int $status = null;

    private ?string $ip = null;

    private ?string $last_login_ip = null;

    private ?string $role = null;

    private ?DateTime $registered_at = null;

    private ?DateTime $modified_at = null;

    private ?DateTime $last_login_at = null;

    private ?string $access_token = null;

    private ?string $auth_key = null;

    private ?string $language = null;

    private ?string $name = null;

    private ?string $avatar = null;

    private ?string $timezone = null;

    public function __construct(string $login, string $password, string $email)
    {
        $this->login = $login;
        $this->password = $password;
        $this->email = $email;
    }

    public function setStatus(int $status): self
    {
        $this->status = $status;
        return $this;
    }

    public function setIp(?string $ip): self
    {
        $this->ip = $ip;
        return $this;
    }

    public function setLastLoginIp(?string $last_login_ip): self
    {
        $this->last_login_ip = $last_login_ip;
        return $this;
    }

    public function setRole(string $role): self
    {
        $this->role = $role;
        return $this;
    }

    public function setRegisteredAt(?DateTime $registered_at): self
    {
        $this->registered_at = $registered_at;
        return $this;
    }

    public function setModifiedAt(?DateTime $modified_at): self
    {
        $this->modified_at = $modified_at;
        return $this;
    }

    public function setLastLoginAt(?DateTime $last_login_at): self
    {
        $this->last_login_at = $last_login_at;
        return $this;
    }


    public function setAccessToken(?string $access_token): self
    {
        $this->access_token = $access_token;
        return $this;
    }

    public function setAuthKey(?string $auth_key): self
    {
        $this->auth_key = $auth_key;
        return $this;
    }

    public function setLanguage(string $language): self
    {
        $this->language = $language;
        return $this;
    }

    public function setAvatar(?string $avatar): self
    {
        $this->avatar = $avatar;
        return $this;
    }

    public function setName(string $name): self
    {
        $this->name = $name;
        return $this;
    }

    public function setTimezone(?string $timezone): self
    {
        $this->timezone = $timezone;
        return $this;
    }

    public function build(): IUserEntity
    {
        $user = new User();
        $profile = new Profile();
        $settings = new Settings();

        $user->login = $this->login;
        $user->setPassword($this->password);
        $user->status = $this->status ?? User::STATUS_ACTIVE;
        $user->ip = $this->ip ?? "127.0.0.1";
        $user->last_login_ip = $this->last_login_ip;
        $user->role = $this->role ?? User::ROLE_USER;

        $now = Helper::systemTimestamp();

        $user->registered_at = $this->registered_at ? $this->registered_at->format("Y-m-d H:i:s") : $now;
        $user->modified_at = $this->modified_at ? $this->modified_at->format("Y-m-d H:i:s") : $now;
        if($this->last_login_at) {
            $user->last_login_at = $this->last_login_at->format("Y-m-d H:i:s");
        }

        if(!$this->access_token) {
            $user->setAccessToken();
        } else {
            $user->access_token = $this->access_token;
        }
        if(!$this->auth_key) {
            $user->generateAuthKey();
        } else {
            $user->auth_key = $this->auth_key;
        }
        $user->lang_id = $this->language ?? \Yii::$app->language;

        $profile->name = $this->name ?? "User";
        $profile->email = $this->email;
        $profile->avatar = $this->avatar;

        $settings->timezone = $this->timezone ?? \Yii::$app->getTimeZone();

        return new class($user, $profile, $settings) implements IUserEntity {
            private User $user;
            private Settings $settings;
            private Profile $profile;

            public function __construct(User $user, Profile $profile, Settings $settings)
            {
                $this->user = $user;
                $this->profile = $profile;
                $this->settings = $settings;
            }

            public function getUser(): User
            {
                return $this->user;
            }

            public function getProfile(): Profile
            {
                return $this->profile;
            }

            public function getSettings(): Settings
            {
                return $this->settings;
            }
        };
    }
}