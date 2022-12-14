<?php
/**
 * @var $changePasswordForm \app\models\ChangePasswordForm
 * @var $settings \app\models\Settings
 * @var $profile \app\models\Profile
 */
use yii\helpers\Html;
use yii\helpers\Json;
use yii\helpers\Url;
?>
<div class="row">
    <div class="col-md-12">
        <!-- Custom Tabs -->
        <div class="nav-tabs-custom">
            <ul class="nav nav-tabs">
                <li<?php echo Yii::$app->request->get("tab") == "profile" ? ' class="active"' : null; ?>>
                    <a href="#tab_profile" data-toggle="tab" data-url=<?php echo Json::encode(Url::toRoute(["user/edit", "tab"=>"profile"])) ?> aria-expanded="<?php echo Yii::$app->request->get("tab") == "profile" ? "true": "false"; ?>">
                        <?php echo Yii::t("app", "Profile") ?>
                    </a>
                </li>
                <li<?php echo Yii::$app->request->get("tab") == "password" ? ' class="active"' : null; ?>>
                    <a href="#tab_password" data-toggle="tab" data-url=<?php echo Json::encode(Url::toRoute(["user/edit", "tab"=>"password"])) ?> aria-expanded="<?php echo Yii::$app->request->get("tab") == "password" ? "true": "false"; ?>">
                        <?php echo Yii::t("app", "Change Password") ?>
                    </a>
                </li>
                <li<?php echo Yii::$app->request->get("tab") == "settings" ? ' class="active"' : null; ?>>
                    <a href="#tab_settings" data-toggle="tab" data-url=<?php echo Json::encode(Url::toRoute(["user/edit", "tab"=>"settings"])) ?> aria-expanded="<?php echo Yii::$app->request->get("tab") == "settings" ? "true": "false"; ?>">
                        <?php echo Yii::t("app", "Settings") ?>
                    </a>
                </li>
            </ul>
            <div class="tab-content">
                <div class="tab-pane<?php echo Yii::$app->request->get("tab") == "profile" ? " active": null; ?>" id="tab_profile">
                    <div class="row">
                        <div class="col-md-6">
                            <h3><?php echo Yii::t("app", "Update Profile") ?></h3>

                            <hr>

                            <?php echo Html::beginForm("", "post", [
                                "class"=>"form-horizontal",
                                "enctype" => "multipart/form-data",
                            ]); ?>

                            <?php echo Html::errorSummary($profile, array(
                                'class' => 'alert alert-danger',
                            )); ?>

                            <?php if(Yii::$app->request->getIsPost() AND isset($_POST['Profile']) AND !$profile->hasErrors()): ?>
                                <div class="alert alert-success alert-dismissable">
                                    <button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button>
                                    <?php echo Yii::t("app", "Profile has been updated"); ?>
                                </div>
                            <?php endif; ?>

                            <div class="form-group<?php echo $profile->hasErrors("name") ? ' has-error' : null ?>">
                                <label for="profile-name" class="col-sm-3 control-label"><?php echo Yii::t("app", "Name") ?></label>
                                <div class="col-sm-9">
                                    <?php echo Html::activeTextInput($profile, "name", [
                                        "class"=>"form-control",
                                    ]); ?>
                                    <?php echo Html::error($profile, "name", [
                                        "tag"=>"p",
                                        "class"=>"help-block"
                                    ]); ?>
                                </div>
                            </div>

                            <div class="form-group<?php echo $profile->hasErrors("email") ? ' has-error' : null ?>">
                                <label for="profile-email" class="col-sm-3 control-label"><?php echo Yii::t("app", "Email") ?></label>
                                <div class="col-sm-9">
                                    <?php echo Html::activeTextInput($profile, "email", [
                                        "class"=>"form-control",
                                    ]); ?>
                                    <?php echo Html::error($profile, "email", [
                                        "tag"=>"p",
                                        "class"=>"help-block"
                                    ]); ?>
                                </div>
                            </div>

                            <div class="form-group<?php echo $profile->hasErrors("fileAvatar") ? ' has-error' : null ?>">
                                <label for="profile-fileavatar" class="col-sm-3 control-label"><?php echo Yii::t("app", "Profile picture") ?></label>
                                <div class="col-sm-9">
                                    <?php echo Html::activeFileInput($profile, "fileAvatar", [
                                        "class"=>"form-control",
                                    ]); ?>
                                    <?php echo Html::error($profile, "fileAvatar", [
                                        "tag"=>"p",
                                        "class"=>"help-block"
                                    ]); ?>
                                </div>
                            </div>

                            <div class="form-group<?php echo $profile->hasErrors("deleteAvatar") ? ' has-error' : null ?>">
                                <div class="col-sm-9 col-sm-offset-3">
                                    <p><?php echo Yii::t("app", "Currently picture") ?></p>
                                    <img class="img-rounded" src="<?php echo $profile->getAvatar() ?>" alt="<?php echo Html::encode($profile->name) ?>">
                                    <? if (!empty($profile->avatar)): ?>
                                    <div class="checkbox">
                                        <?php echo Html::activeCheckbox($profile, 'deleteAvatar') ?>
                                    </div>
                                    <? endif; ?>
                                </div>
                            </div>

                            <hr>

                            <div class="form-group">
                                <div class="col-sm-9 col-sm-offset-3">
                                    <button type="submit" class="btn btn-info"><?php echo Yii::t("app", "Update Profile") ?></button>
                                </div>
                            </div>

                            <?php echo Html::endForm(); ?>
                        </div>
                    </div>
                </div><!-- /.tab-pane -->
                <div class="tab-pane<?php echo Yii::$app->request->get("tab") == "password" ? " active": null; ?>" id="tab_password">
                    <div class="row">
                        <div class="col-md-6">
                            <?php echo Html::beginForm("", "post", [
                                "class"=>"form-horizontal",
                            ]); ?>
                            <h3><?php echo Yii::t("app", "Update Password") ?></h3>

                            <hr>

                            <?php echo Html::errorSummary($changePasswordForm, array(
                                'class' => 'alert alert-danger',
                            )); ?>

                            <?php if(Yii::$app->request->getIsPost() AND isset($_POST['ChangePasswordForm']) AND !$changePasswordForm->hasErrors()): ?>
                            <div class="alert alert-success alert-dismissable">
                                <button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button>
                                <?php echo Yii::t("app", "The password has been changed"); ?>
                            </div>
                            <?php endif; ?>

                            <div class="form-group<?php echo $changePasswordForm->hasErrors("oldPassword") ? ' has-error' : null ?>">
                                <label for="changepasswordform-oldpassword" class="col-sm-3 control-label"><?php echo Yii::t("app", "Old password") ?></label>
                                <div class="col-sm-9">
                                    <?php echo Html::activePasswordInput($changePasswordForm, "oldPassword", [
                                        "class"=>"form-control",
                                    ]); ?>
                                    <?php echo Html::error($changePasswordForm, "oldPassword", [
                                        "tag"=>"p",
                                        "class"=>"help-block"
                                    ]); ?>
                                </div>
                            </div>

                            <div class="form-group<?php echo $changePasswordForm->hasErrors("newPassword") ? ' has-error' : null ?>">
                                <label for="changepasswordform-newpassword" class="col-sm-3 control-label"><?php echo Yii::t("app", "New password") ?></label>
                                <div class="col-sm-9">
                                    <?php echo Html::activePasswordInput($changePasswordForm, "newPassword", [
                                        "class"=>"form-control",
                                    ]); ?>
                                    <?php echo Html::error($changePasswordForm, "newPassword", [
                                        "tag"=>"p",
                                        "class"=>"help-block"
                                    ]); ?>
                                </div>
                            </div>

                            <div class="form-group<?php echo $changePasswordForm->hasErrors("reNewPassword") ? ' has-error' : null ?>">
                                <label for="changepasswordform-renewpassword" class="col-sm-3 control-label"><?php echo Yii::t("app", "Repeat new password") ?></label>
                                <div class="col-sm-9">
                                    <?php echo Html::activePasswordInput($changePasswordForm, "reNewPassword", [
                                        "class"=>"form-control",
                                    ]); ?>
                                    <?php echo Html::error($changePasswordForm, "newPassword", [
                                        "tag"=>"p",
                                        "class"=>"help-block"
                                    ]); ?>
                                </div>
                            </div>

                            <hr>

                            <div class="form-group">
                                <div class="col-sm-9 col-sm-offset-3">
                                    <button type="submit" class="btn btn-info"><?php echo Yii::t("app", "Update Password") ?></button>
                                </div>
                            </div>

                            <?php echo Html::endForm(); ?>
                        </div>
                    </div>
                </div><!-- /.tab-pane -->
                <div class="tab-pane<?php echo Yii::$app->request->get("tab") == "settings" ? " active": null; ?>" id="tab_settings">
                    <div class="row">
                        <div class="col-md-6">
                            <h3><?php echo Yii::t("app", "Account Settings") ?></h3>

                            <hr>

                            <?php echo Html::beginForm("", "post", [
                                "class"=>"form-horizontal",
                            ]); ?>

                            <?php echo Html::errorSummary($settings, array(
                                'class' => 'alert alert-danger',
                            )); ?>

                            <?php if(Yii::$app->request->getIsPost() AND isset($_POST['Settings']) AND !$settings->hasErrors()): ?>
                                <div class="alert alert-success alert-dismissable">
                                    <button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button>
                                    <?php echo Yii::t("app", "Account settings have been changed"); ?>
                                </div>
                            <?php endif; ?>

                            <div class="form-group<?php echo $settings->hasErrors("timezone") ? ' has-error' : null ?>">
                                <label for="settings-timezone" class="col-sm-3 control-label"><?php echo Yii::t("app", "Timezone") ?></label>
                                <div class="col-sm-9">
                                    <?php echo Html::activeDropDownList($settings, "timezone", \app\components\Timezone::all(), [
                                        "class"=>"form-control",
                                    ]); ?>
                                    <?php echo Html::error($changePasswordForm, "timezone", [
                                        "tag"=>"p",
                                        "class"=>"help-block"
                                    ]); ?>
                                </div>
                            </div>

                            <hr>

                            <div class="form-group">
                                <div class="col-sm-9 col-sm-offset-3">
                                    <button type="submit" class="btn btn-info"><?php echo Yii::t("app", "Edit") ?></button>
                                </div>
                            </div>

                            <?php echo Html::endForm(); ?>
                        </div>
                    </div>
                </div><!-- /.tab-pane -->
            </div><!-- /.tab-content -->
        </div><!-- nav-tabs-custom -->
    </div><!-- /.col -->
</div><!-- /.row -->
