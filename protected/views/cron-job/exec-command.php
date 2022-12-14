<?php
/**
 * @var $startTime string
 * @var $finishTime string
 * @var $curl_info array
 * @var $handler \app\components\ScheduleProcess
 * @var $responseInfo array
 * @var $model \app\models\Schedule
 */
use yii\helpers\Html;
use app\components\Helper;
?>
<tr class="odd">
    <td class="text-center"><?php echo Helper::covertToUserDate(date("Y-m-d H:i:s", (int) $startTime)); ?></td>
    <td class="text-center"><?php echo Helper::covertToUserDate(date("Y-m-d H:i:s", (int) $finishTime)); ?></td>v
    <td class="text-center"><?php echo $finishTime - $startTime ?></td>
    <td class="text-center"><?php echo $handler->getExitCode() ?></td>
    <td class="text-center">
        <?php if($responseInfo['error']): ?>
            <?php echo Yii::t("app", "Failed") ?> <a id="view-cron-job-output-error" style="text-decoration: underline; cursor: pointer;"><?php echo Yii::t("app", "Details") ?></a>
        <?php else: ?>
            <?php echo Yii::t("app", "Succeeded") ?>
        <?php endif; ?>
    </td>
    <td class="text-center" style="padding: 4px 6px 3px;">
        <button class="btn btn-small btn-default" id="view-cron-job-output"> <?php echo Yii::t("app", "View Output") ?></button>
    </td>
</tr>

<tr id="output-error-row" style="display: none">
    <td class="alignCenter" colspan="9">
        <textarea rows="4" class="form-control" readonly><?php echo $responseInfo['errorMessage'] ?></textarea>
    </td>
</tr>

<tr id="output-row" style="display: none">
    <td class="alignCenter" colspan="9">
        <textarea rows="15" class="form-control" readonly>
<?php echo Html::encode($model->formatOutput($handler)); ?>
        </textarea>
    </td>
</tr>