<?php


namespace app\models\domain\user;


use app\models\Profile;
use app\models\Settings;
use app\models\User;

interface IUserEntity
{
    public function getUser(): User;
    public function getProfile(): Profile;
    public function getSettings(): Settings;
}