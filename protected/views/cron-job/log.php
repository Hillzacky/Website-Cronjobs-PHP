<?php
/**
 * @var $log \app\models\Log
 */

use yii\widgets\DetailView;
use app\components\Helper;
use yii\helpers\Html;
use yii\helpers\ArrayHelper;
?>
<div class="row">
    <div class="col-md-12">
        <div class="box">
            <div class="box-body">
                <div class="row">
                    <div class="col-md-8">
                        <div class="table-responsive">
                            <table class="table table-bordered">
                            <thead>
                            <tr>
                                <th class="text-center"><?php echo Yii::t("app", "Start Time") ?></th>
                                <th class="text-center"><?php echo Yii::t("app", "End Time") ?></th>
                                <th class="text-center"><?php echo Yii::t("app", "Connect Time (second)") ?></th>
                                <th class="text-center"><?php echo Yii::t("app", "Total Time (second)") ?></th>
                                <th class="text-center"><?php echo Yii::t("app", "HTTP Code") ?> | <?php echo Yii::t("app", "Exit Code") ?></th>
                                <th class="text-center"><?php echo Yii::t("app", "Status") ?></th>
                            </tr>
                            </thead>
                            <tbody>
                            <tr class="odd">
                                <td class="text-center"><?php echo Helper::covertToUserDate($log->start_at); ?></td>
                                <td class="text-center"><?php echo Helper::covertToUserDate($log->finish_at); ?></td>
                                <td class="text-center"><?php echo $log->getConnectTime(); ?></td>
                                <td class="text-center"><?php echo $log->getTotalTime(); ?></td>
                                <td class="text-center"><?php echo $log->http_code ?></td>
                                <td class="text-center">
                                    <?php if($log->is_error): ?>
                                        <?php echo Yii::t("app", "Failed") ?>
                                    <?php else: ?>
                                        <?php echo Yii::t("app", "Succeeded") ?>
                                    <?php endif; ?>
                                </td>
                            </tr>
                            </tbody>
                        </table>
                        </div>
                        <? if($log->is_error): ?>
                        <h3><?= Yii::t("app", "Error") ?></h3>
                        <p class="alert alert-danger"><?= Html::encode($log->error_msg)?></p>
                        <hr>
                        <? endif; ?>
                        <h3><?= Yii::t("app", "Output") ?></h3>
                        <pre>
<?= Html::encode($log->response); ?>
                        </pre>
                    </div>
                    <div class="col-md-4">
                        <h2><?= Yii::t("app", "Current Schedule Settings") ?></h2>
                        <div class="table-responsive">
                            <?php echo DetailView::widget([
                                "model"=>$log->schedule,
                                "attributes"=>[
                                    "title",
                                    "category_id"=>[
                                        "label"=>Yii::t("app", "Category"),
                                        "value"=>$log->schedule->category ? $log->schedule->category->title : null,
                                    ],
                                    "url",
                                    "expression",
                                    [
                                        "attribute"=>"start_at_user",
                                        "visible"=>!is_null($log->schedule->start_at_user)
                                    ],
                                    [
                                        "attribute"=>"send_at_user"
                                    ],
                                    [
                                        'attribute'=>'timeout',
                                    ],
                                    "notify"=>[
                                        "label"=>Yii::t("app", "Email Me"),
                                        "value"=>$log->schedule->getNotifyMsg()
                                    ],
                                    [
                                        'label'=>Yii::t("app", "Shutting down"),
                                        'value'=>$log->schedule->shuttingDownMsg(),
                                        'format'=>'raw',
                                    ],
                                    [
                                        'label'=>Yii::t("app", "HTTP Basic Authorization"),
                                        'value'=>$log->schedule->getHttpAuthMsg(),
                                    ],
                                    [
                                        "attribute"=>"description",
                                        "format"=>"ntext"
                                    ],
                                    [
                                        "attribute"=>"created_at",
                                        "value"=>Helper::covertToUserDate($log->schedule->created_at),
                                    ],
                                    [
                                        "attribute"=>"modified_at",
                                        "value"=>Helper::covertToUserDate($log->schedule->modified_at),
                                    ],
                                ]
                            ]) ?>
                        </div>
                        <a href="<?= \yii\helpers\Url::to(["cron-job/update", "id"=>$log->schedule->id]) ?>" class="btn btn-primary"><?= Yii::t("app", "Edit") ?></a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
