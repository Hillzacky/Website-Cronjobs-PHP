<?php
/**
* @var $model \app\models\Schedule
* @var $cookies array
* @var $post array
*/

use yii\helpers\Html;
use yii\widgets\DetailView;
use app\components\Helper;
use yii\helpers\Url;

?>
<div class="row">
    <div class="col-md-12">
        <div class="box">
            <div class="box-body">
                <div class="row">
                    <div class="col-md-12">
                        <h3 class="box-title">
                            <?php echo Yii::t("app", "Cron Job Setting") ?>
                        </h3>
                        <hr>
                        <div class="table-responsive">
                            <?php echo DetailView::widget([
                                "model"=>$model,
                                "attributes"=>[
                                    "title",
                                    [
                                        "attribute"=>"description",
                                        "format"=>"ntext"
                                    ],
                                    "category_id"=>[
                                        "label"=>Yii::t("app", "Category"),
                                        "value"=>$model->category ? $model->category->title : null,
                                    ],
                                    "url",
                                    "expression",
                                    [
                                        "attribute"=>"start_at_user",
                                        "visible"=>!is_null($model->start_at_user)
                                    ],
                                    [
                                        "attribute"=>"send_at_user"
                                    ],
                                    "notify"=>[
                                        "label"=>Yii::t("app", "Email Me"),
                                        "value"=>$model->getNotifyMsg()
                                    ],
                                    [
                                        'label'=>Yii::t("app", "Regular Expression"),
                                        'value'=>$model->getRegexString(),
                                        "format"=>"raw",
                                    ],
                                    [
                                        'label'=>Yii::t("app", "Shutting down"),
                                        'value'=>$model->shuttingDownMsg(),
                                        'format'=>'raw',
                                    ],
                                    [
                                        'attribute'=>'timeout',
                                    ],
                                    [
                                        "attribute"=>"created_at",
                                        "value"=>Helper::covertToUserDate($model->created_at),
                                    ],
                                    [
                                        "attribute"=>"modified_at",
                                        "value"=>Helper::covertToUserDate($model->modified_at),
                                    ],
                                ]
                            ]) ?>
                        </div>
                        <a href="<?= Url::to(["cron-job/update", "id"=>$model->id]) ?>" class="btn btn-primary"><?= Yii::t("app", "Edit") ?></a>
                        <a href="<?= Url::current() ?>" class="btn btn-default"><?= Yii::t("app", "Re-run") ?></a><br><br>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-12">
                        <div class="table-responsive">
                            <table class="table table-bordered" id="log-list">
                                <thead>
                                <tr>
                                    <th class="text-center"><?php echo Yii::t("app", "Start Time") ?></th>
                                    <th class="text-center"><?php echo Yii::t("app", "End Time") ?></th>
                                    <th class="text-center"><?php echo Yii::t("app", "Total Time (second)") ?></th>
                                    <th class="text-center"><?php echo Yii::t("app", "Exit Code") ?></th>
                                    <th class="text-center"><?php echo Yii::t("app", "Status") ?></th>
                                    <th class="text-center"><?php echo Yii::t("app", "Output") ?></th>
                                </tr>
                                </thead>
                                <tbody>

                                <tr class="odd" id="cron-job-test-result">
                                    <td colspan="8"><?php echo Yii::t("app", "Testing cron job, please wait...") ?></td>
                                </tr>

                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
