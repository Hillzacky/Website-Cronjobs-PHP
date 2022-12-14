<?php
/**
 * @var $dataProvider \yii\data\ActiveDataProvider
 * @var $searchModel \app\models\search\LogSearch;
 * @var $schedule \app\models\Schedule;
 * @var $stats array;
 */

use app\widgets\PerPage;
use yii\grid\GridView;
use yii\widgets\LinkPager;
use app\components\Helper;
use yii\helpers\Html;
use dosamigos\datepicker\DateRangePicker;
use yii\helpers\Url;
use yii\widgets\DetailView;
use yii\helpers\Json;
?>
<?php echo Html::beginForm(); ?>
<div class="row">
    <div class="col-md-12">
        <div class="box">
            <div class="box-body">
                <div class="row">
                    <div class="col-lg-3">
                        <div class="box box-default collapsed-box box-solid">
                            <div class="box-header with-border">
                                <h3 class="box-title"><?php echo Yii::t("app", "Statistic") ?></h3>
                                <div class="box-tools pull-right">
                                    <button class="btn btn-box-tool" data-widget="collapse"><i class="fas fa-plus"></i></button>
                                </div><!-- /.box-tools -->
                            </div><!-- /.box-header -->
                            <div class="box-body">
                                <?php if($lc): ?>
                                    <p><?= $totalPhrase ?></p>
                                    <p><?= $successPhrase ?></p>
                                    <p><?= $failPhrase ?></p>
                                    <div class="progress" style="min-width: 100px">
                                        <div class="progress-bar progress-bar-success progress-bar-striped" style="width: <?php echo $successPercent ?>%" data-toggle="tooltip" data-html="true" data-placement="top" title=<?php echo Json::encode($totalPhrase. "<br>". $successPhrase) ?>>
                                            <span class="sr-only"><?php echo $totalPhrase ?><br><?php echo $successPhrase ?></span>
                                        </div>
                                        <div class="progress-bar progress-bar-danger progress-bar-striped" style="width: <?php echo $failurePercent?>%" data-toggle="tooltip" data-html="true" data-placement="top" title=<?php echo Json::encode($totalPhrase. "<br>". $failPhrase) ?>>
                                            <span class="sr-only"><?php echo $totalPhrase ?><br><?php echo $failPhrase ?></span>
                                        </div>
                                    </div>
                                <?php else: ?>
                                    <p><?= Yii::t("app", "There are no statistics") ?></p>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>

                    <div class="col-lg-6">
                        <div class="box box-default collapsed-box box-solid">
                            <div class="box-header with-border">
                                <h3 class="box-title"><?php echo Yii::t("app", "Cron Job Setting") ?></h3>
                                <div class="box-tools pull-right">
                                    <button class="btn btn-box-tool" data-widget="collapse"><i class="fas fa-plus"></i></button>
                                </div><!-- /.box-tools -->
                            </div><!-- /.box-header -->
                            <div class="box-body">

                                    <?php echo DetailView::widget([
                                        "model"=>$schedule,
                                        "attributes"=>[
                                            "title",
                                            "category_id"=>[
                                                "label"=>Yii::t("app", "Category"),
                                                "value"=>$schedule->category ? $schedule->category->title : null,
                                            ],
                                            "url",
                                            "expression",
                                            [
                                                "attribute"=>"start_at_user",
                                                "visible"=>!is_null($schedule->start_at_user)
                                            ],
                                            [
                                                "attribute"=>"send_at_user"
                                            ],
                                            "status"=>[
                                                "label"=>Yii::t("app", "Status"),
                                                "value"=>$schedule->getStatusMsg()
                                            ],
                                            "notify"=>[
                                                "label"=>Yii::t("app", "Email Me"),
                                                "value"=>$schedule->getNotifyMsg()
                                            ],
                                            [
                                                'label'=>Yii::t("app", "Shutting down"),
                                                'value'=>$schedule->shuttingDownMsg(),
                                                'format'=>'raw',
                                            ],
                                            [
                                                'label'=>Yii::t("app", "HTTP Basic Authorization"),
                                                'value'=>$schedule->getHttpAuthMsg(),
                                            ],
                                        ]
                                    ]) ?>

                            </div><!-- /.box-body -->
                        </div><!-- /.box -->
                    </div>

                    <div class="col-lg-3">
                        <div class="box box-default collapsed-box box-solid">
                            <div class="box-header with-border">
                                <h3 class="box-title"><?php echo Yii::t("app", "Actions") ?></h3>
                                <div class="box-tools pull-right">
                                    <button class="btn btn-box-tool" data-widget="collapse"><i class="fas fa-plus"></i></button>
                                </div><!-- /.box-tools -->
                            </div><!-- /.box-header -->
                            <div class="box-body">
                                <div class="list-group" style="margin-bottom: 0 !important;
                                ">
                                    <a href="<?= Url::to(["cron-job/update", "id"=>$schedule->id])?>" class="list-group-item">
                                        <i class="far fa-edit"></i>&nbsp;&nbsp;<?php echo Yii::t("app", "Edit") ?>
                                    </a>
                                    <a href="<?= Url::to(["cron-job/clone", "id"=>$schedule->id])?>" class="list-group-item">
                                        <i class="far fa-clone"></i>&nbsp;&nbsp;<?php echo Yii::t("app", "Clone") ?>
                                    </a>
                                    <a href="<?= Url::to(["cron-job/run", "id"=>$schedule->id])?>" class="list-group-item">
                                        <i class="fas fa-flask"></i>&nbsp;&nbsp;<?php echo Yii::t("app", "Test cron job manually") ?>
                                    </a>
                                    <a href="<?= Url::to(["cron-job/reset", "id"=>$schedule->id])?>" class="list-group-item" data-confirm=<?php echo Json::encode(Yii::t('app', 'Are you sure you want to reset cron job (ID: {id}) log and statistic?', [
                                        "id"=>$schedule->id
                                    ])) ?>>
                                        <i class="fas fa-sync"></i>&nbsp;&nbsp;<?php echo Yii::t("app", "Reset execution logs and statistics") ?>
                                    </a>
                                    <a href="<?= Url::to(["cron-job/delete", "id"=>$schedule->id, "back"=>Url::to(["cron-job/index"])])?>" class="list-group-item" data-confirm=<?php echo Json::encode(Yii::t('app', 'Are you sure you want to delete the cron job (ID: {id}) ?', [
                                        "id"=>$schedule->id,
                                    ])) ?>>
                                        <i class="fas fa-times"></i>&nbsp;&nbsp;<?php echo Yii::t("app", "Delete this cron job") ?>
                                    </a>
                                    <a href="<?= Url::to(["cron-job/predict", "id"=>$schedule->id])?>" class="list-group-item">
                                        <i class="fas fa-forward"></i>&nbsp;&nbsp;<?php echo Yii::t("app", "Prediction") ?>
                                    </a>

                                </div>
                            </div><!-- /.box-body -->
                        </div><!-- /.box -->
                    </div>

                </div>

                <a href="<?= Url::to(["cron-job/update", "id"=>$schedule->id]) ?>" class="btn btn-primary"><?= Yii::t("app", "Edit") ?></a>

                <div class="pull-right">
                    <?php echo PerPage::widget([
                        "pagination"=>$dataProvider->getPagination(),
                        "layout"=>"horizontal"
                    ]); ?>
                </div>
                <div class="clearfix"></div><br>

                <div class="table-responsive">
                <?php echo GridView::widget([
                    "dataProvider"=>$dataProvider,
                    'filterModel' => $searchModel,
                    'afterRow'=>function($model, $key, $index, $grid) {
                        return '
                        <tr style="display: none;" data-row="error"  data-key="'.$model->id .'">
                        <td colspan="9">
                        <textarea rows="2" class="form-control" readonly>'. Html::encode($model->error_msg).'</textarea>
                        </td>
                        </tr>
                        <tr style="display: none;" data-row="response" data-key="'.$model->id .'">
                        <td colspan="9">
                        <textarea rows="10" class="form-control" readonly>'.Html::encode($model->response).'</textarea>
                        </td>
                        </tr>
                        ';
                    },
                    "layout" => "{summary}\n{items}",
                    "tableOptions"=>[
                        "class"=>"table"
                    ],
                    'rowOptions'=>function($model, $key, $index, $grid) {
                        $opts = [];
                        $opts['class'] = $model->is_error ? 'danger' : 'success';
                        Html::addCssClass($opts, "table-row");
                        return $opts;
                    },
                    'columns' => [
                        [
                            'attribute'=>'schedule_time',
                            'filter'=>DateRangePicker::widget([
                                'model'=>$searchModel,
                                'attribute'=>'firstDate',
                                'attributeTo'=>'secondDate',
                                'labelTo'=>Yii::t("app", "to"),
                                'language'=>substr(Yii::$app->language, 0, 2) == 'en' ? 'en-GB' : substr(Yii::$app->language, 0, 2),
                                'clientOptions' => [
                                    'autoclose' => true,
                                    'format' => 'yyyy-mm-dd',
                                    'todayHighlight'=>true
                                ],
                                'options'=>[
                                    'readonly'=>true,
                                ],
                                'optionsTo'=>[
                                    'readonly'=>true,
                                ]
                            ]),
                            'options'=>[
                                'style'=>'width: 250px'
                            ],
                            'value'=>function($model) {
                                return $model->schedule_time ? Helper::covertToUserDate($model->schedule_time) : null;
                            },
                            'format'=>'raw',
                        ],
                        [
                            'header'=>Yii::t("app", "Start Time"),
                            'value'=>function($model, $key, $index, $column) {
                                return $model->start_at ? Helper::covertToUserDate($model->start_at) : null;
                            },
                            'filter'=>false,
                            'enableSorting'=>false,
                        ],
                        [
                            'header'=>Yii::t("app", "Finished at"),
                            'value'=>function($model, $key, $index, $column) {
                                return $model->finish_at ? Helper::covertToUserDate($model->finish_at) : null;
                            },
                            'enableSorting'=>false,
                        ],
                        [
                            'header'=>Yii::t("app", "Connect Time (second)"),
                            'content'=>function($model, $key, $index, $column) {
                                return $model->getConnectTime();
                            }
                        ],
                        [
                            'header'=>Yii::t("app", "Total Time (second)"),
                            'content'=>function($model, $key, $index, $column) {
                                return $model->getTotalTime();
                            }
                        ],
                        [
                            'attribute'=>'http_code',
                            'label'=>Yii::t("app", "HTTP Code") ." | ". Yii::t("app", "Exit Code"),
                        ],
                        [
                            'attribute'=>'is_error',
                            'content'=>function($model, $key, $index, $column) {
                                $html = $model->getStatusMsg();
                                if($model->is_error) {
                                    $html .= " <u>". Html::a(Yii::t("app", "Details"), "#", [
                                        "class"=>"show-row",
                                        "data-show-row"=>"error",
                                        "data-id"=>$model->id,
                                    ])."</u>";
                                }
                                return $html;
                            },
                            'filter'=>Html::activeDropDownList($searchModel, "is_error", $searchModel->getStatusArray(), [
                                "class"=>"form-control",
                                "prompt"=>Yii::t("app", "Please Select..."),
                            ]),
                        ],
                        [
                            'header'=>Yii::t("app", "Output"),
                            'content'=>function($model, $key, $index, $column) {
                                return Html::button(Yii::t("app", "View Output"), [
                                    "class"=>"btn btn-default show-row",
                                    "data-id"=>$model->id,
                                    "data-show-row"=>"response"
                                ]);
                            },
                            'filter'=>Html::a(Yii::t("app", "Clear filter"), \yii\helpers\Url::to(["cron-job/statistic", "id"=>$schedule->id]), [
                                "class"=>"btn btn-default"
                            ]),
                        ],
                    ],
                ]);
                ?>
                </div>
            </div>
            <div class="box-footer clearfix">
                <?php echo LinkPager::widget([
                    "pagination"=>$dataProvider->getPagination(),
                    "options"=>[
                        "class"=>"pagination pagination-sm no-margin pull-right",
                    ]
                ]) ?>
            </div>
        </div>
    </div>
</div>
<?php echo Html::endForm(); ?>


