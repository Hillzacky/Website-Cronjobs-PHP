<?php
/**
 * @var $this \yii\web\View
 * @var $content string
 */
use yii\helpers\Html;
use app\assets\FontAwesomeAsset;
use yii\helpers\Url;
use yii\widgets\Breadcrumbs;
use app\widgets\LanguageDropdown;
use app\widgets\ClockWidget;
use app\components\Helper;

FontAwesomeAsset::register($this);
?>
<?php $this->beginContent('@app/views/layouts/main.php'); ?>
    <!-- Main Header -->
    <header class="main-header">

        <!-- Logo -->
        <a href="<?= Url::home() ?>" class="logo">
            <!-- mini logo for sidebar mini 50x50 pixels -->
            <span class="logo-mini"><?= Yii::$app->params['shortAppName'] ?></span>
            <!-- logo for regular state and mobile devices -->
            <span class="logo-lg"><?= Yii::$app->params['longAppName'] ?></span>
        </a>

        <!-- Header Navbar -->
        <nav class="navbar navbar-static-top" role="navigation">
            <!-- Sidebar toggle button-->
            <a href="#" class="sidebar-toggle" data-toggle="offcanvas" role="button"></a>
            <!-- Navbar Right Menu -->
            <div class="navbar-custom-menu">
                <ul class="nav navbar-nav">
                    <li>
                        <a href="<?= Url::to(["user/edit", "tab"=>"settings"]) ?>">
                            <?= ClockWidget::widget([
                                "user"=>Yii::$app->getUser()->getIdentity()
                            ]); ?>
                        </a>
                    </li>
                    <?= LanguageDropdown::widget() ?>
                    <li>
                        <a href="<?= Url::to(['cron-job/examples']) ?>"><i class="far fa-question-circle"></i></span></a>
                    </li>
                    <!-- User Account Menu -->
                    <li class="dropdown user user-menu">
                        <!-- Menu Toggle Button -->
                        <a href="#" class="dropdown-toggle" data-toggle="dropdown">
                            <!-- The user image in the navbar-->
                            <img src="<?php echo Yii::$app->user->identity->profile->getAvatar(); ?>" class="user-image" alt="<?php echo Html::encode(Yii::$app->user->identity->profile->getOldAttribute('name')) ?>">
                            <!-- hidden-xs hides the username on small devices so only the image appears. -->
                            <span class="hidden-xs"><?php echo Html::encode(Yii::$app->user->identity->profile->getOldAttribute('name')) ?></span>
                        </a>
                        <ul class="dropdown-menu">
                            <!-- The user image in the menu -->
                            <li class="user-header">
                                <img src="<?php echo Yii::$app->user->identity->profile->getAvatar(); ?>" class="img-circle" alt="<?php echo Html::encode(Yii::$app->user->identity->profile->getOldAttribute('name')) ?>">
                                <p>
                                    <?php echo Html::encode(Yii::$app->user->identity->profile->getOldAttribute('name')) ?>
                                    <small><?php echo Yii::t("app", "Member since {0}", Helper::covertToUserDate(Yii::$app->user->identity->registered_at)) ?></small>
                                </p>
                            </li>
                            <!-- Menu Footer-->
                            <li class="user-footer">
                                <div class="pull-left">
                                    <a href="<?php echo Url::toRoute(["user/edit", "tab"=>"profile"]) ?>" class="btn btn-default btn-flat"><?= Yii::t("app", "Profile") ?></a>
                                </div>
                                <div class="pull-right">
                                    <a href="<?php echo Url::toRoute("user/logout") ?>" class="btn btn-default btn-flat"><?php echo Yii::t("app", "Log out") ?></a>
                                </div>
                            </li>
                        </ul>
                    </li>
                </ul>
            </div>
        </nav>
    </header>
    <!-- Left side column. contains the logo and sidebar -->
    <aside class="main-sidebar">

        <!-- sidebar: style can be found in sidebar.less -->
        <section class="sidebar">

            <!-- Sidebar user panel (optional) -->
            <div class="user-panel">
                <div class="pull-left image">
                    <img src="<?php echo Yii::$app->user->identity->profile->getAvatar(); ?>" class="img-circle" alt="<?php echo Html::encode(Yii::$app->user->identity->profile->getOldAttribute('name')) ?>">
                </div>
                <div class="pull-left info">
                    <p><?php echo Html::encode(Yii::$app->user->identity->profile->getOldAttribute('name')) ?></p>
                    <a href="<?= Url::to(["user/edit", "tab"=>"profile"]); ?>"><i class="fas fa-circle text-success"></i> <?= Yii::t("app", "Online") ?></a>
                </div>
            </div>

            <!-- Sidebar Menu -->
            <ul class="sidebar-menu">
                <li class="header"><?php echo Yii::t("app", "Menu") ?></li>
                <li<?php echo $this->context->id === "cron-job" ? ' class="active"' : null ?>>
                    <a href="<?php echo Url::to(["/cron-job"])?>">
                        <i class="fas fa-tasks"></i>&nbsp;&nbsp;<span><?php echo Yii::t("app", "Cron Jobs") ?></span>
                    </a>
                </li>
                <li<?php echo $this->context->id === "category" ? ' class="active"' : null ?>>
                    <a href="<?php echo Url::to(["/category"])?>">
                        <i class="fas fa-folder-open"></i>&nbsp;&nbsp;<span><?php echo Yii::t("app", "Categories") ?></span>
                    </a>
                </li>
                <li class="treeview<?php echo $this->context->id === "user" ? ' active' : null ?>">
                    <a href="#"><i class="fas fa-cogs"></i>&nbsp;&nbsp;<span><?php echo Yii::t("app", "Account Settings") ?></span> <i class="fas fa-angle-left pull-right"></i></a>
                    <ul class="treeview-menu">
                        <li><a href="<?php echo Url::to(["user/edit", "tab"=>"profile"] )?>"><?php echo Yii::t("app", "Profile Settings") ?></a></li>
                        <li><a href="<?php echo Url::to(["user/edit", "tab"=>"password"] )?>"><?php echo Yii::t("app", "Change Password") ?></a></li>
                        <li><a href="<?php echo Url::to(["user/edit", "tab"=>"settings"] )?>"><?php echo Yii::t("app", "Account Settings") ?></a></li>
                    </ul>
                </li>
                <li class="treeview">
                    <a href="#"><i class="fas fa-eraser" aria-hidden="true"></i>&nbsp;&nbsp;<span><?php echo Yii::t("app", "Clear Logs") ?></span> <i class="fas fa-angle-left pull-right"></i></a>
                    <ul class="treeview-menu">
                        <li><a href="<?php echo Url::to(["log/clear", "period"=>"all"] )?>"><?php echo Yii::t("app", "All") ?></a></li>
                        <li><a href="<?php echo Url::to(["log/clear", "period"=>"one-day"] )?>"><?php echo Yii::t("app", "Older than one day") ?></a></li>
                        <li><a href="<?php echo Url::to(["log/clear", "period"=>"one-week"] )?>"><?php echo Yii::t("app", "Older than one week") ?></a></li>
                        <li><a href="<?php echo Url::to(["log/clear", "period"=>"one-month"] )?>"><?php echo Yii::t("app", "Older than one month") ?></a></li>
                        <li><a href="<?php echo Url::to(["log/clear", "period"=>"three-months"] )?>"><?php echo Yii::t("app", "Older than three months") ?></a></li>
                        <li><a href="<?php echo Url::to(["log/clear", "period"=>"six-months"] )?>"><?php echo Yii::t("app", "Older than six months") ?></a></li>
                        <li><a href="<?php echo Url::to(["log/clear", "period"=>"one-year"] )?>"><?php echo Yii::t("app", "Older than one year") ?></a></li>
                    </ul>
                </li>
            </ul><!-- /.sidebar-menu -->
        </section>
        <!-- /.sidebar -->
    </aside>

    <!-- Content Wrapper. Contains page content -->
    <div class="content-wrapper">
        <!-- Content Header (Page header) -->
        <section class="content-header">
            <h1>
                <?php echo Html::encode($this->title); ?>
            </h1>
            <?php echo Breadcrumbs::widget([
                'homeLink' => [
                    'label' => '<i class="fas fa-tachometer-alt"></i> '.Yii::t('app', 'Dashboard'),
                    'url' => Yii::$app->homeUrl,
                    'encode'=>false,
                ],
                'links'=>$this->context->breadcrumbs,
           ]); ?>
        </section>

        <!-- Main content -->
        <section class="content">
            <div id="app-notification">
                <?php echo app\widgets\Alert::widget() ?>
            </div>
            <?php echo $content; ?>
            <!-- Your Page Content Here -->

        </section><!-- /.content -->
    </div><!-- /.content-wrapper -->

    <!-- Main Footer -->
    <footer class="main-footer">
        <!-- Default to the left -->
            <?= Yii::t("app", "Developed by {developer}", [
                "developer"=>'<strong><a href="http://php8developer.com" target="_blank">PHP8 Developer</a></strong>',
            ])?>
    </footer>

    <!-- Add the sidebar's background. This div must be placed
         immediately after the control sidebar -->
    <div class="control-sidebar-bg"></div>
<?php $this->endContent(); ?>