<?php
namespace app\controllers;

use app\models\ChangePasswordForm;
use app\models\LoginForm;
use app\models\User;
use app\components\AppController;
use yii\helpers\Url;
use yii\filters\AccessControl;
use yii\imagine\Image;
use yii\web\UploadedFile;
use yii\helpers\FileHelper;
use yii;

class UserController extends AppController
{
    public function behaviors() {
        $behaviors = parent::behaviors();
        $behaviors['access'] = [
            'class'=>AccessControl::class,
            'rules' => [
                [
                    'allow'=>true,
                    'actions'=>['login'],
                    'roles'=>['?'],
                ],
                [
                    'allow'=>true,
                    'roles'=>['@'],
                ]
            ],
        ];
        return $behaviors;
    }

    public function actionLogin() {
        if(!Yii::$app->user->isGuest) {
            $this->goHome();
        }
        $this->getView()->title = Yii::t("app", "Login Form");

        $form = new LoginForm();

        if($form->load(Yii::$app->request->post()) && $form->login()) {
            return $this->goBack();
        }

        return $this->render("login", [
            "form"=>$form,
        ]);
    }

    public function actionLogout() {
        Yii::$app->user->logout();
        return Yii::$app->getResponse()->redirect(Url::toRoute("user/login"));
    }

    public function actionEdit() {
        /**
         * @var $user User;
         */
        $user = Yii::$app->user->identity;

        $changePasswordForm = new ChangePasswordForm();

        $this->getView()->title = Yii::t("app", "My Account");

        if($changePasswordForm->load(Yii::$app->request->post()) AND $changePasswordForm->validate()) {
            $user->setPassword($changePasswordForm->newPassword);
            $user->save();
            foreach ($changePasswordForm->attributes as $attribute=>$value) {
                $changePasswordForm->$attribute = NULL;
            }
        }

        if($user->profile->load(Yii::$app->request->post())) {
            $user->profile->fileAvatar = UploadedFile::getInstance($user->profile, "fileAvatar");
            if($user->profile->validate()) {
                if($user->profile->fileAvatar) {
                    $user->profile->unlinkAvatar();
                    $user->profile->setAvatarName($user->profile->fileAvatar->name, $user->profile->fileAvatar->extension);
                    $avatar_path = $user->profile->getAvatarPath();
                    FileHelper::createDirectory(dirname($avatar_path));
                    $user->profile->fileAvatar->saveAs($avatar_path);
                    $width = 128;
                    $height = 128;
                    $options = [
                        "quality"=>100,
                    ];
                    Image::thumbnail($avatar_path, $width, $height)->save($avatar_path, $options);
                }
                if($user->profile->deleteAvatar) {
                    $user->profile->unlinkAvatar();
                    $user->profile->avatar = null;
                }
                $user->profile->save(false);
            }
        }

        ($user->settings->load(Yii::$app->request->post()) AND $user->settings->save());

        return $this->render("edit", [
            "changePasswordForm"=>$changePasswordForm,
            "settings"=>$user->settings,
            "profile"=>$user->profile,
        ]);
    }
}