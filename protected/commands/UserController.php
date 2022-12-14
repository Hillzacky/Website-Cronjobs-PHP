<?php
namespace app\commands;

use app\models\domain\user\Create;
use app\models\domain\user\Remove;
use app\models\domain\user\RestoreSoftDelete;
use app\models\domain\user\SoftDelete;
use app\models\domain\user\UserBuilder;
use app\models\exceptions\DomainModelException;
use app\models\User;
use yii\console\Controller;
use yii\helpers\Console;
use Yii;
use yii\helpers\Url;

class UserController extends Controller
{
    public function actionCreate(string $login, string $password, string $email, string $name = "User"): int
    {
        //$date1 = new \DateTime('2021-01-01 03:12:23');
        //$date2 = new \DateTime('2021-01-02 03:12:23');
        //$date3 = new \DateTime('2021-01-03 03:12:23');

        $userEntity = (new UserBuilder($login, $password, $email))
            //->setStatus(User::STATUS_ACTIVE)
            //->setIp('::1')
            //->setLastLoginIp('::1')
            //->setRole(User::ROLE_USER)
            //->setRegisteredAt($date1)
            //->setModifiedAt($date2)
            //->setLastLoginAt($date3)
            //->setAccessToken('access_key')
            //->setAuthKey('auth_key')
            //->setLanguage('lang')
            //->setAvatar('/avatart/foo.jpg')
            ->setName($name)
            //->setTimezone('Europe/London')
            ->build()
        ;

        try {
            (new Create($userEntity))->execute();
            $this->stdout("New user [${login} | ${password}] has been created\n", Console::FG_GREEN);
        } catch (DomainModelException $exception) {
            $this->stderr("User [${login}] data is not valid\n", Console::FG_RED);
            foreach ($exception->getErrors() as $error) {
                $this->stderr("- {$error}\n");
            }
            return 1;
        } catch (\Throwable $exception) {
            $this->stderr("Unable to save user [{$login}]\n", Console::FG_RED);
            $this->stderr($exception);
            return 1;
        }
        return 0;
    }

    public function actionRemove(string $login): int
    {
        $user = User::findOne([
            "login"=> $login
        ]);

        if(!$user) {
            $this->stderr("Unable to find [{$login}] user\n", Console::FG_RED);
            return 1;
        }

        try {
            (new Remove($user))->execute();
        } catch (\Throwable $exception) {
            $this->stderr("Unable to remove [{$login}] user\n", Console::FG_RED);
            $this->stderr($exception);
            return 1;
        }

        $this->stdout("User [${login}] has been removed\n", Console::FG_GREEN);
        return 0;
    }

    public function actionSoftRemove(string $login): int
    {
        $user = User::findOne([
            "login"=> $login
        ]);
        if(!$user) {
            $this->stderr("Unable to find [{$login}] user\n", Console::FG_RED);
            return 1;
        }

        try {
            (new SoftDelete($user))->execute();
        } catch (\Throwable $exception) {
            $this->stderr("Unable to disable [{$login}] user\n", Console::FG_RED);
            $this->stderr($exception);
            return 1;
        }

        $this->stdout("User [${login}] has been disabled\n", Console::FG_GREEN);
        return 0;
    }

    public function actionRestore(string $login): int
    {
        $user = User::findOne([
            "login"=> $login
        ]);
        if(!$user) {
            $this->stderr("Unable to find [{$login}] user\n", Console::FG_RED);
            return 1;
        }
        try {
            (new RestoreSoftDelete($user))->execute();
        } catch (\Throwable $exception) {
            $this->stderr("Unable to restore [{$login}] user\n", Console::FG_RED);
            $this->stderr($exception);
            return 1;
        }

        $this->stdout("User [${login}] has been restored\n", Console::FG_GREEN);
        return 0;
    }

    public function actionChangePassword(string $login, string $password): int
    {
        $user = User::findOne([
            "login"=> $login
        ]);
        if(!$user) {
            $this->stderr("Unable to find [{$login}] user\n", Console::FG_RED);
            return 1;
        }
        $user->setPassword($password);
        if(!$user->save(false)) {
            $this->stderr("Unable to update [{$login}] password\n", Console::FG_RED);
            return 1;
        }

        $this->stdout("New password [{$password}] has been set for [${login}]\n", Console::FG_GREEN);

        return 0;
    }
}