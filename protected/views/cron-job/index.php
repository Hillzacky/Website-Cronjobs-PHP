<?php
/**
 * @var $dataProvider \yii\data\ActiveDataProvider
 * @var $searchModel \app\models\search\ScheduleSearch
 */
use yii\helpers\Html;
use yii\helpers\Url;
use yii\grid\GridView;
use yii\widgets\LinkPager;
use app\widgets\PerPage;
use app\models\Schedule;
use yii\helpers\Json;
use app\models\Category;
use yii\helpers\ArrayHelper;
use dosamigos\datepicker\DatePicker;
use dosamigos\datepicker\DateRangePicker;

?>
<?php echo Html::beginForm(); ?>
<div class="row">
    <div class="col-md-12">
        <div class="box">
            <div class="box-header">
                <select id="bulk-action" class="form-control pull-left" style="display: none;" name="action">
                    <option disabled selected><?php echo Yii::t("app", "Bulk Action") ?></option>

                    <optgroup data-name="status" label="<?php echo Yii::t("app", "Update Status") ?>">
                        <option value="<?php echo Schedule::STATUS_ENABLED ?>"><?php echo Yii::t("app", "Enable") ?></option>
                        <option value="<?php echo Schedule::STATUS_DISABLED ?>"><?php echo Yii::t("app", "Disable") ?></option>
                    </optgroup>

                    <optgroup data-name="notify" label="<?php echo Yii::t("app", "Update Email Notification") ?>">
                        <option value="<?php echo Schedule::NOTIFY_NEVER ?>"><?php echo Yii::t("app", "Never") ?></option>
                        <option value="<?php echo Schedule::NOTIFY_FAILS ?>"><?php echo Yii::t("app", "If execution fails") ?></option>
                        <option value="<?php echo Schedule::NOTIFY_AFTER ?>"><?php echo Yii::t("app", "After execution") ?></option>
                    </optgroup>

                    <optgroup data-name="general" label="<?php echo Yii::t("app", "General") ?>">
                        <option value="reset" data-confirm=<?php echo Json::encode(Yii::t("app", "Are you sure you wish to reset logs and stat?")) ?>><?php echo Yii::t("app", "Reset logs and stat") ?></option>
                        <option value="delete" data-confirm=<?php echo Json::encode(Yii::t("app", "Are you sure you wish to delete selected tasks?"))?>><?php echo Yii::t("app", "Delete") ?></option>
                    </optgroup>
                </select>
                <input type="hidden" name="bulk-group">
                <input type="hidden" name="bulk-value">

                <? if(ArrayHelper::getValue(Yii::$app->params, 'canSetupProcess', false)): ?>
                    <div class="btn-group pull-right">
                        <button type="button" class="btn btn-large btn-primary" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false"><?php echo Yii::t("app", "Create New Cron Job") ?> <span class="caret"></span></button>
                        <ul class="dropdown-menu">
                            <li>
                                <a href="<?php echo Url::to(["create", "type"=>Schedule::SCHEDULE_TYPE_HTTP]) ?>"><?php echo Yii::t("app", "Add HTTP Command") ?></a>
                            </li>
                            <li>
                                <a href="<?php echo Url::to(["create", "type"=>Schedule::SCHEDULE_TYPE_COMMAND]) ?>"><?php echo Yii::t("app", "Add Shell Command") ?></a>
                            </li>
                        </ul>
                    </div>
                <? else: ?>
                <a href="<?php echo Url::to(["create", "type"=>Schedule::SCHEDULE_TYPE_HTTP]) ?>" class="btn btn-large btn-primary pull-right"><?php echo Yii::t("app", "Create New Cron Job") ?></a>
                <? endif; ?>
                <a href="<?php echo Url::to(["index", "reset"=>1]) ?>" class="btn btn-large btn-default pull-right" style="margin-right: 20px"><?php echo Yii::t("app", "Clear filter") ?></a>
            </div>
            <div class="box-body">
                <div class="table-responsive">
                    <?php $widget = GridView::begin([
                        "dataProvider"=>$dataProvider,
                        'filterModel' => $searchModel,
                        "layout" => "{items}",
                        'rowOptions'=>[
                            'class'=>'data-row table-row',
                        ],
                        'tableOptions'=>[
                            'class' => 'table table-striped table-bordered',
                            'style' => 'min-width: 1200px',
                        ],
                        'columns'=>[
                            [
                                'class'=>'yii\grid\CheckboxColumn',
                                'checkboxOptions'=>[
                                    'data-type'=>'action'
                                ]

                            ],
                            'id',
                            [
                                'attribute'=>'url',
                                'label'=>Yii::t("app", "URL or Shell Command"),
                                'filter'=>Html::activeTextInput($searchModel, "url", [
                                    "class"=>"form-control",
                                ]),
                            ],
                            [
                                'attribute'=>'title',
                                'filter'=>Html::activeTextInput($searchModel, "title", [
                                    "class"=>"form-control",
                                ]),
                            ],
                            [
                                'label'=>Yii::t("app", "Statistic"),
                                'value'=>function($model) {
                                    $sc = (int) $model->totalSuccess;
                                    $te = (int) $model->totalFailed;
                                    $lc = $sc + $te;
                                    if(!$lc) {
                                        return Yii::t("app", "There are no statistics");
                                    }
                                    $success = $sc;
                                    $successPercent = round(100*$success/$lc, 2);
                                    $failurePercent = round(100*$te/$lc, 2);

                                    $totalPhrase = Yii::t("app", "Total executions: {0}", number_format($lc));
                                    $successPhrase = Yii::t("app", "Succeed: {0} ({1} %)", [number_format($success), $successPercent]);
                                    $failPhrase = Yii::t("app", "Failed: {0} ({1} %)", [number_format($te), $failurePercent]);

                                    return '
                                    <a href="'. Url::to(["statistic", "id"=>$model->id]).'">
                                        <div class="progress" style="min-width: 100px">
                                          <div class="progress-bar progress-bar-success progress-bar-striped" style="width: '.$successPercent.'%" data-toggle="tooltip" data-html="true" data-placement="top" title='. Json::encode($totalPhrase.'<br>'.$successPhrase) .'>
                                            <span class="sr-only">'.$totalPhrase.'<br>'.$successPhrase.'</span>
                                          </div>
                                          <div class="progress-bar progress-bar-danger progress-bar-striped" style="width: '.$failurePercent.'%" data-toggle="tooltip" data-html="true" data-placement="top" title='. Json::encode($totalPhrase.'<br>'.$failPhrase) .'>
                                            <span class="sr-only">'.$totalPhrase.'<br>'.$failPhrase.'</span>
                                          </div>
                                        </div>
                                    </a>
                                    ';
                                },
                                'format'=>'raw'
                            ],
                            [
                                'attribute'=>'category_id',
                                'label'=>Yii::t("app", "Category"),
                                'value'=>function($model) {
                                    return $model->category ? $model->category->title : null;
                                },
                                'filter'=>Html::activeDropDownList($searchModel, "category_id", ArrayHelper::map(array_merge([["id"=>"null", "title"=>Yii::t("app", "Not set")]], Category::find()->owner()->asArray()->all()), "id", "title"), [
                                    "class"=>"form-control",
                                    "prompt"=>Yii::t("app", "Please Select..."),
                                ]),
                            ],
                            [
                                'attribute'=>'command_type',
                                'value'=>function($model) {
                                    return $model->getType();
                                },
                                'filter'=>Html::activeDropDownList($searchModel, "command_type", Schedule::getTypeArray(), [
                                    "class"=>"form-control",
                                    "prompt"=>Yii::t("app", "Please Select..."),
                                ]),
                            ],
                            [
                                'attribute'=>'expression',
                                'enableSorting'=>false,
                            ],
                            [
                                'attribute'=>'send_at_user',
                                //'format'=>'datetime',
                                'filter'=>DateRangePicker::widget([
                                    'model'=>$searchModel,
                                    'attribute'=>'sendAtFrom',
                                    'attributeTo'=>'sendAtTo',
                                    'labelTo'=>Yii::t("app", "to"),
                                    'language'=>substr(Yii::$app->language, 0, 2) == 'en' ? 'en-GB' : substr(Yii::$app->language, 0, 2),
                                    'clientOptions' => [
                                        'autoclose' => true,
                                        'format' => 'yyyy-mm-dd',
                                        'startDate'=>Yii::$app->getUser()->getIdentity()->getDateObject()->format("Y-m-d"),
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
                                    $link = Html::a(Yii::t("app", "Prediction"), Url::to(["predict", "id"=>$model->id]), [
                                        "class"=>"btn btn-info btn-sm",
                                        "target"=>"_blank",
                                    ]);
                                    return $model->send_at_user. '&nbsp;&nbsp;&nbsp;'. $link;
                                },
                                'format'=>'raw',
                            ],
                            'status'=>[
                                'class'=>'app\components\switch_column\SwitchColumn',
                                'show'=>function($model) {
                                    return true; //$model->future_execution == 1;
                                },
                                'emptyText'=>Yii::t("app", "Disabled"),
                                'attribute'=>'status',
                                'filter'=>Html::activeDropDownList($searchModel, "status", $searchModel::getStatusArray(), [
                                    "class"=>"form-control",
                                    "prompt"=>Yii::t("app", "Please Select..."),
                                ]),

                                'route'=>function($model) {
                                    return Url::to(["switch", "id"=>$model->id]);
                                },
                                'enabledValue'=>Schedule::STATUS_ENABLED,
                                'disabledValue'=>Schedule::STATUS_DISABLED,
                                'switcherOptions'=>[
                                    'data-on-color'=>'success',
                                    'data-off-color'=>'danger',
                                    'data-on-text'=>Yii::t("app", "Enabled"),
                                    'data-off-text'=>Yii::t("app", "Disabled"),
                                ],
                                'switcherEvents'=>[
                                    'beforeSend'=>'WebCronApp.switcher.beforeSend',
                                    'afterSend'=>'WebCronApp.switcher.afterSend',
                                    'error'=>'WebCronApp.switcher.error',
                                ],
                            ],
                            'notify'=>[
                                'label'=>Yii::t("app", "Email Me"),
                                'value'=>function($model) {
                                    return $model->getNotifyMsg();
                                }
                            ],
                            [
                                'label'=>Yii::t("app", "Shutting down"),
                                'value'=>function($model) {
                                    if($model->max_executions > 0) {
                                        return '<i class="fas fa-list-ol pointer fa-2x" data-toggle="tooltip" data-placement="top" data-html="true" title='. Json::encode($model->shuttingDownMsg()).'></i>';
                                    } elseif($model->stop_at_user) {
                                        return '<i class="far fa-clock pointer fa-2x" data-toggle="tooltip" data-placement="top" data-html="true" title='. Json::encode($model->shuttingDownMsg()).'></i>';
                                    } else {
                                        return '<i class="fas fa-sync pointer fa-2x" data-toggle="tooltip" data-placement="top" data-html="true" title='. Json::encode($model->shuttingDownMsg()).'></i>';
                                    }
                                },
                                "format"=>"raw",
                                "contentOptions"=>[
                                    "style"=>"text-align: center"
                                ]
                            ],
                            [
                                'class' => 'yii\grid\ActionColumn',
                                'header'=>Yii::t("app", "Actions"),
                                'template'=>'
                                <div class="text-center">
                                <div class="btn-group align-center" style="font-size: 18px">
                                    {update} {clone} {run} {reset} {delete}
                                </div>
                                </div>
                            ',
                                'buttons'=>[
                                    'update'=>function($url, $model, $key) {
                                        return Html::a('<i class="far fa-edit"></i>', $url, [
                                            "title"=>Yii::t('app', "Edit")
                                        ])."&nbsp;&nbsp;";
                                    },
                                    'clone'=>function($url, $model, $key) {
                                        return Html::a('<i class="far fa-clone"></i>', $url, [
                                            "title"=>Yii::t('app', "Clone")
                                        ])."&nbsp;&nbsp;";
                                    },
                                    'run'=>function($url, $model, $key) {
                                        return Html::a('<i class="fas fa-flask"></i>', $url, [
                                            "title"=>Yii::t('app', "Test cron job manually"),
                                            "target"=>"_blank",
                                        ])."&nbsp;&nbsp;";
                                    },
                                    'reset'=>function($url, $model, $key) {
                                        return Html::a('<i class="fas fa-sync"></i>', $url, [
                                            "title"=>Yii::t('app', "Reset execution logs and statistics"),
                                            'data-confirm' => Yii::t('app', 'Are you sure you want to reset cron job (ID: {id}) log and statistic?', [
                                                "id"=>$model->id
                                            ]),
                                        ])."&nbsp;&nbsp;";
                                    },
                                    'delete'=>function($url, $model, $key) {
                                        return Html::a('<i class="fas fa-times"></i>', $url, [
                                            "title"=>Yii::t('app', "Delete this cron job"),
                                            'data-confirm' => Yii::t('app', 'Are you sure you want to delete the cron job (ID: {id}) ?', [
                                                "id"=>$model->id
                                            ]),
                                        ]);
                                    },
                                ],
                                'options'=>[
                                    'style'=>'width: 150px',
                                ]
                            ],
                        ],
                    ]); $widget->end(); ?>
                </div>
            </div>
            <div class="box-footer clearfix">
                <div class="pull-left">
                    <?php echo $widget->renderSummary(); ?>
                </div>

                <div class="pull-right">
                    <table>
                        <tr>
                            <td style="padding-right: 20px">
                                <?php echo PerPage::widget([
                                    "pagination"=>$dataProvider->getPagination(),
                                    'layout'=>'vertical',
                                ]); ?>
                            </td>
                            <td>
                                <?php echo LinkPager::widget([
                                    "pagination"=>$dataProvider->getPagination(),
                                    "options"=>[
                                        "class"=>"pagination pagination-sm no-margin",
                                    ]
                                ]) ?>
                            </td>
                        </tr>
                    </table>
                </div>

            </div>
        </div>
    </div>
</div>
<?php echo Html::endForm(); ?>
