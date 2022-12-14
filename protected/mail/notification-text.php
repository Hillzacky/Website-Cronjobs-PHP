<?php
/**
 * @var $response string
 * @var $schedule \app\models\Schedule
 * @var $handler \Curl\Curl|\app\components\ScheduleProcess;
 * @var $info array
 * @var $logId int
 */
use yii\helpers\Html;
use yii\helpers\Url;
?>
<?php echo Yii::t("app", "Cron Job Name", [], $schedule->user->lang_id) ?>: <?= Html::encode($schedule->title) ?>

<?php echo Yii::t("app", "URL or Shell Command", [], $schedule->user->lang_id) ?>: <?= $schedule->url ?>

<?php echo Yii::t("app", "Expression", [], $schedule->user->lang_id) ?>: <?= $schedule->expression ?>

<?php echo Yii::t("app", "Command Type", [], $schedule->user->lang_id) ?>: <?= $schedule->getType() ?>

<?php echo Yii::t("app", "Regular Expression", [], $schedule->user->lang_id) ?>: <?= $schedule->getRegexString() ?>

<?php echo Yii::t("app", "Execution time", [], $schedule->user->lang_id) ?>: <?= $schedule->send_at_user ?>

<?php echo Yii::t("app", "HTTP Code", [], $schedule->user->lang_id) ?>: <?= $schedule->getExitCode($handler) ?>

<?php echo Yii::t("app", "Status", [], $schedule->user->lang_id) ?> : <?= $info['error'] ? Yii::t("app", "Failed") . " : ".  $info['errorMessage'] : Yii::t("app", "Succeeded"); ?>

<?php echo Yii::t("app", "More info", [], $schedule->user->lang_id) ?>: <?= Url::to(["cron-job/log", "id"=>$logId, "language"=>$schedule->user->lang_id], true) ?>

<?php echo Yii::t("app", "First {n} KB output", [
    "n"=>Yii::$app->params['kbEmailOutput']
], $schedule->user->lang_id) ?>


>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>


<?php echo Html::encode($response) ?>