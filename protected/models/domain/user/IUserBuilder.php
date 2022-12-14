<?php


namespace app\models\domain\user;

use app\models\User;

interface IUserBuilder
{
    public function build(): IUserEntity;
}