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
<table border="1" cellpadding="5">
    <tr>
        <td><?php echo Yii::t("app", "Cron Job Name", [], $schedule->user->lang_id) ?>: </td>
        <td><?= Html::encode($schedule->title) ?></td>
    </tr>
    <tr>
        <td><?php echo Yii::t("app", "URL or Shell Command", [], $schedule->user->lang_id) ?>: </td>
        <td><?= $schedule->url ?></td>
    </tr>
    <tr>
        <td><?php echo Yii::t("app", "Expression", [], $schedule->user->lang_id) ?>: </td>
        <td><?= $schedule->expression ?></td>
    </tr>
    <tr>
        <td><?php echo Yii::t("app", "Command Type", [], $schedule->user->lang_id) ?>: </td>
        <td><?= $schedule->getType() ?></td>
    </tr>
    <tr>
        <td><?php echo Yii::t("app", "Regular Expression", [], $schedule->user->lang_id) ?>: </td>
        <td><?= $schedule->getRegexString() ?></td>
    </tr>
    <tr>
        <td><?php echo Yii::t("app", "Execution time", [], $schedule->user->lang_id) ?>: </td>
        <td><?= $schedule->send_at_user ?></td>
    </tr>
    <tr>
        <td><?php echo Yii::t("app", "HTTP Code", [], $schedule->user->lang_id) ?> | <?php echo Yii::t("app", "Exit Code", [], $schedule->user->lang_id) ?>: </td>
        <td><?= $schedule->getExitCode($handler) ?></td>
    </tr>
    <tr>
        <td>
            <?php echo Yii::t("app", "Status", [], $schedule->user->lang_id) ?>:
        </td>
        <td>
            <?php if($info['error']): ?>
                <?php echo Yii::t("app", "Failed") ?> <br>
                <?php echo $info['errorMessage']; ?>
            <?php else: ?>
                <?php echo Yii::t("app", "Succeeded") ?>
            <?php endif; ?>
        </td>
    </tr>
    <tr>
        <td><?php echo Yii::t("app", "More info", [], $schedule->user->lang_id) ?>: </td>
        <td><?= Html::a(Yii::t("app", "Click here", [], $schedule->user->lang_id), Url::to(["cron-job/log", "id"=>$logId, "language"=>$schedule->user->lang_id], true))?></td>
    </tr>
</table>

<p>
    <?php echo Yii::t("app", "First {n} KB output", [
        "n"=>Yii::$app->params['kbEmailOutput']
    ], $schedule->user->lang_id) ?>
</p>

<hr>

<pre>
<?php echo Html::encode($response) ?>
</pre>