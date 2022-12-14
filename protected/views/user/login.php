<?php
/**
 * @var $this \yii\web\View
 * @var $form \app\models\LoginForm
 * @var $showCaptcha boolean
 */
use yii\helpers\Html;
use yii\captcha\Captcha;
?>
<div class="row">
    <div class="col-md-offset-4 col-md-4 col-sm-6 col-sm-offset-3">
        <div class="box box-info">
            <div class="box-header with-border">
                <h3 class="box-title"><?php echo Yii::t("app", "Login Form") ?></h3>
            </div><!-- /.box-header -->
            <!-- form start -->
            <?php echo Html::beginForm("", "post", [
                "class"=>"form-horizontal",
            ]); ?>

                <div class="box-body">

                    <?php echo Html::errorSummary($form, array(
                        'class' => 'alert alert-danger',
                    )); ?>

                    <div class="form-group<?php echo $form->hasErrors("login") ? ' has-error' : null ?>">
                        <label for="loginform-login" class="col-sm-3 control-label"><?php echo Yii::t("app", "Login") ?></label>
                        <div class="col-sm-9">
                            <?php echo Html::activeTextInput($form, "login", [
                                "class"=>"form-control",
                                "placeholder"=>Yii::t("app", "Login"),
                            ]); ?>
                        </div>
                    </div>


                    <div class="form-group<?php echo $form->hasErrors("password") ? ' has-error' : null ?>">
                        <label for="loginform-password" class="col-sm-3 control-label"><?php echo Yii::t("app", "Password"); ?></label>
                        <div class="col-sm-9">
                            <?php echo Html::activePasswordInput($form, "password", [
                                "class"=>"form-control",
                                "placeholder"=>Yii::t("app", "Password"),
                            ]); ?>
                        </div>
                    </div>

                    <div class="form-group<?php echo $form->hasErrors("rememberMe") ? ' has-error' : null ?>">
                        <div class="col-sm-offset-3 col-sm-9">
                            <div class="checkbox">
                                <?php echo Html::activeCheckbox($form, 'rememberMe') ?>
                            </div>
                        </div>
                    </div>
                </div><!-- /.box-body -->
                <div class="box-footer">
                    <button type="submit" class="btn btn-info pull-right"><?= Yii::t("app", "Sign in") ?></button>
                </div><!-- /.box-footer -->
            <?php echo Html::endForm(); ?>
        </div>
    </div>
</div>