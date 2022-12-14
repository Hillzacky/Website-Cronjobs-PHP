<?php
/**
 * @var $model \app\models\Schedule
 * @var $provider \yii\data\ArrayDataProvider
 * @var $startDate \DateTime;
 * @var $currentDate \DateTime;
 */

use yii\helpers\Url;
use yii\grid\GridView;
use yii\widgets\LinkPager;
use yii\widgets\DetailView;
use app\components\Helper;
use kartik\datetime\DateTimePicker;
use yii\helpers\Html;
?>

<div class="row">
    <div class="col-md-12">
        <div class="box">
            <div class="box-body">
                <div class="row">
                    <div class="col-md-6">
                        <?php echo GridView::widget([
                            "dataProvider"=>$provider,
                            "emptyText"=>Yii::t("app", "No upcoming scheduled tasks for given date"),
                            "layout" => "{summary}\n{items}",
                            'columns' => [
                                [
                                    'class' => 'yii\grid\SerialColumn',
                                ],
                                [
                                    'label'=>Yii::t("app", "Next Run Date"),
                                    'value'=>function($model) {
                                        return $model->format("Y-m-d H:i:s");
                                    }
                                ],

                            ],
                        ]);
                        ?>
                    </div>
                    <div class="col-md-6">
                        <h3><?php echo Yii::t("app", "Start Date") ?></h3>
                        <?php echo Html::beginForm(Url::to(['predict', 'id'=>$model->id]), 'get', [
                            'name'=>'startForm'
                        ]); ?>
                        <?php echo DateTimePicker::widget([
                            'name' => 'startDate',
                            'layout'=>'{picker}{remove}{input}<span class="input-group-addon btn btn-primary" onclick="startForm.submit()">'.Yii::t("app", "Apply").'</span>',
                            'options' => [
                                'placeholder' => Yii::t("app", "Please select date and time..."),
                                'readonly'=>true,
                            ],
                            'convertFormat' => true,
                            'value'=>$startDate->format("Y-m-d H:i"),
                            'pluginOptions' => [
                                'autoclose'=>true,
                                'format' => 'yyyy-MM-dd H:i',
                                'startDate' => $currentDate->format("Y-m-d H:i"),
                                'todayHighlight' => true,
                                'locale'=> Yii::$app->language,
                                'minuteStep'=>1,
                            ]
                        ]); ?>
                        <?php echo Html::endForm(); ?>
                        <hr>
                        <h3><?php echo Yii::t("app", "Cron Job Setting") ?></h3>

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
                                        'label'=>Yii::t("app", "HTTP Basic Authorization"),
                                        'value'=>$model->getHttpAuthMsg(),
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
                        <a href="<?= Url::to(["cron-job/update", "id"=>$model->id]) ?>" class="btn btn-primary pull-left"><?= Yii::t("app", "Edit") ?></a>
                    </div>
                </div>
            </div>
            <div class="box-footer clearfix">
                <div class="row">
                    <div class="col-md-6">
                        <?php echo LinkPager::widget([
                            "pagination"=>$provider->getPagination(),
                            "options"=>[
                                "class"=>"pagination pagination-sm no-margin pull-right",
                            ]
                        ]) ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
