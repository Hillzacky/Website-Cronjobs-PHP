<?php
/**
 * @var $startTime string
 * @var $finishTime string
 * @var $curl_info array
 * @var $handler \Curl\Curl
 * @var $responseInfo array
 * @var \app\models\Schedule $model
 */

use yii\helpers\ArrayHelper;
use yii\helpers\Html;
use app\components\Helper;
$curl_info = curl_getinfo($handler->curl);
?>
<tr class="odd">
    <td class="text-center"><?php echo Helper::covertToUserDate(date("Y-m-d H:i:s", (int) $startTime)); ?></td>
    <td class="text-center"><?php echo Helper::covertToUserDate(date("Y-m-d H:i:s", (int) $finishTime)); ?></td>v
    <td class="text-center"><?php echo ArrayHelper::getValue($curl_info, 'connect_time'); ?></td>
    <td class="text-center"><?php echo ArrayHelper::getValue($curl_info, 'total_time'); ?></td>
    <td class="text-center"><?php echo $handler->httpStatusCode; ?></td>
    <td class="text-center">
        <?php if($responseInfo['error']): ?>
            <?php echo Yii::t("app", "Failed") ?> <a id="view-cron-job-output-error" style="text-decoration: underline; cursor: pointer;"><?php echo Yii::t("app", "Details") ?></a>
        <?php else: ?>
            <?php echo Yii::t("app", "Succeeded") ?>
        <?php endif; ?>
    </td>
    <td class="text-center"><?php echo Yii::$app->getFormatter()->asShortSize(ArrayHelper::getValue($curl_info, 'size_download')); ?></td>
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
        <textarea rows="15" class="form-control" readonly><?php echo Html::encode($model->formatOutput($handler)); ?></textarea>
    </td>
</tr>
