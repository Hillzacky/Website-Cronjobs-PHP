<?
/**
 * @var $upcomingSchedule array
 * @var $recentLogs array
 * @var $last31days array
 * @var $last12months array
 */

use yii\helpers\Html;
use yii\helpers\Url;
use app\components\Helper;
use dosamigos\chartjs\ChartJs;
use yii\helpers\ArrayHelper;
?>
<div class="row">
    <div class="col-md-5">
        <!-- Upcoming schedules -->
        <div class="box box-warning">
            <div class="box-header with-border">
                <h3 class="box-title"><?= Yii::t("app", "Tasks Queued") ?></h3>
                <div class="box-tools pull-right">
                    <button class="btn btn-box-tool" data-widget="collapse"><i class="fas fa-minus"></i></button>
                </div>
            </div><!-- /.box-header -->
            <div class="box-body">
                <div style="height: 350px" class="table-responsive">
                <table class="table table-bordered">
                    <thead>
                        <tr>
                            <th><?= Yii::t("app", "Title") ?></th>
                            <th><?= Yii::t("app", "URL or Shell Command") ?></th>
                            <th><?= Yii::t("app", "Expression") ?></th>
                            <th><?= Yii::t("app", "Execute at") ?></th>
                            <th></th>
                        </tr>
                    </thead>
                    <tbody>
                    <? foreach($upcomingSchedule as $schedule) : ?>
                        <tr>
                            <td><?= Html::encode($schedule->title) ?></td>
                            <td><?= Html::encode($schedule->url) ?></td>
                            <td><?= Html::encode($schedule->expression) ?></td>
                            <td><?= Html::encode($schedule->send_at_user) ?></td>
                            <td>
                                <div class="btn-group-vertical" role="group">
                                    <a class="btn bg-navy" href="<?= Url::to(["cron-job/statistic", "id"=>$schedule->id]) ?>"><?= Yii::t("app", "Schedule Stat") ?></a>
                                    <a class="btn btn-success" href="<?= Url::to(["cron-job/update", "id"=>$schedule->id]) ?>"><?= Yii::t("app", "Edit") ?></a>
                                </div>

                            </td>
                        </tr>
                    <? endforeach; ?>
                    </tbody>
                </table>
                </div>
            </div><!-- /.box-body -->
        </div><!--/.direct-chat -->
    </div><!-- /.col -->

    <div class="col-md-7">
        <!-- Recent logs -->
        <div class="box box-danger">
            <div class="box-header with-border">
                <h3 class="box-title"><?= Yii::t("app", "Latest Logs") ?></h3>
                <div class="box-tools pull-right">
                    <button class="btn btn-box-tool" data-widget="collapse"><i class="fas fa-minus"></i></button>
                </div>
            </div><!-- /.box-header -->
            <div class="box-body">
                <div style="height: 350px" class="table-responsive">
                <table class="table table-bordered">
                    <thead>
                    <tr>
                        <th><?= Yii::t("app", "Title") ?></th>
                        <th><?= Yii::t("app", "URL or Shell Command") ?></th>
                        <th><?= Yii::t("app", "Code") ?></th>
                        <th><?= Yii::t("app", "Started at") ?></th>
                        <th><?= Yii::t("app", "Finished at") ?></th>
                        <th></th>
                    </tr>
                    </thead>
                    <tbody>
                    <? foreach($recentLogs as $log) : ?>
                        <tr class="<?= $log->is_error ? "danger" : "success" ?>">
                            <td><a href="<?= Url::to(["cron-job/update", "id"=>$log->schedule_id])?>"><?= Html::encode($log->schedule->title) ?></a></td>
                            <td><?= Html::encode($log->schedule->url) ?></td>
                            <td><?= Html::encode($log->http_code) ?></td>
                            <td><?= Html::encode(Helper::covertToUserDate($log->start_at)) ?></td>
                            <td><?= Html::encode(Helper::covertToUserDate($log->finish_at)) ?></td>
                            <td>
                                <div class="btn-group-vertical" role="group">
                                    <a class="btn bg-navy" href="<?= Url::to(["cron-job/statistic", "id"=>$log->schedule_id]) ?>"><?= Yii::t("app", "Schedule Stat") ?></a>
                                    <a class="btn bg-purple" href="<?= Url::to(["cron-job/log", "id"=>$log->id]) ?>"><?= Yii::t("app", "Explore") ?></a>
                                </div>
                            </td>
                        </tr>
                    <? endforeach; ?>
                    </tbody>
                </table>
                </div>
            </div><!-- /.box-body -->
        </div><!--/.box -->
    </div><!-- /.col -->
</div>



<div class="row">
    <div class="col-md-6">
        <!-- Last 31 days -->
        <div class="box box-success">
            <div class="box-header with-border">
                <h3 class="box-title"><?= Yii::t("app", "Last 31 days") ?></h3>
                <div class="box-tools pull-right">
                    <button class="btn btn-box-tool" data-widget="collapse"><i class="fas fa-minus"></i></button>
                </div>
            </div><!-- /.box-header -->
            <div class="box-body">
                <div style="width: 98%">
                <?= ChartJs::widget([
                    'type' => 'line',
                    'clientOptions'=>[
                        'responsive'=>true,
                    ],
                    'data' => [
                        'labels' => ArrayHelper::getColumn($last31days, 'insert_at'),
                        'datasets' => [
                            [
                                'label'=>Yii::t("app", "Number of successful requests"),
                                'backgroundColor' => "rgba(220,220,220,0.5)",
                                'borderColor' => "rgba(220,220,220,1)",
                                'pointColor' => "rgba(220,220,220,1)",
                                'pointStrokeColor' => "#fff",
                                'data' => ArrayHelper::getColumn($last31days, 'totalSuccess')
                            ],
                            [
                                'label'=>Yii::t("app", "Number of failed requests"),
                                'backgroundColor' => "rgba(151,187,205,0.5)",
                                'borderColor' => "rgba(151,187,205,1)",
                                'pointColor' => "rgba(151,187,205,1)",
                                'pointStrokeColor' => "#fff",
                                'data' => ArrayHelper::getColumn($last31days, 'totalFailed')
                            ]
                        ]
                    ]
                ]);
                ?>
                </div>
            </div><!-- /.box-body -->
        </div><!--/.direct-chat -->
    </div><!-- /.col -->

    <div class="col-md-6">
        <!-- Last 12 months -->
        <div class="box box-primary">
            <div class="box-header with-border">
                <h3 class="box-title"><?= Yii::t("app", "Last 12 months") ?></h3>
                <div class="box-tools pull-right">
                    <button class="btn btn-box-tool" data-widget="collapse"><i class="fas fa-minus"></i></button>
                </div>
            </div><!-- /.box-header -->
            <div class="box-body">
                <div style="width: 98%">
                    <?= ChartJs::widget([
                        'type' => 'line',
                        'clientOptions'=>[
                            'responsive'=>true,
                        ],
                        'data' => [
                            'labels' => ArrayHelper::getColumn($last12months, 'month'),
                            'datasets' => [
                                [
                                    'label'=>Yii::t("app", "Number of successful requests"),
                                    'backgroundColor' => "rgba(220,220,220,0.5)",
                                    'borderColor' => "rgba(220,220,220,1)",
                                    'pointColor' => "rgba(220,220,220,1)",
                                    'pointStrokeColor' => "#fff",
                                    'data' => ArrayHelper::getColumn($last12months, 'totalSuccess')
                                ],
                                [
                                    'label'=>Yii::t("app", "Number of failed requests"),
                                    'backgroundColor' => "rgba(151,187,205,0.5)",
                                    'borderColor' => "rgba(151,187,205,1)",
                                    'pointColor' => "rgba(151,187,205,1)",
                                    'pointStrokeColor' => "#fff",
                                    'data' => ArrayHelper::getColumn($last12months, 'totalFailed')
                                ]
                            ]
                        ]
                    ]);
                    ?>
                </div>
            </div><!-- /.box-body -->
        </div><!--/.box -->
    </div><!-- /.col -->
</div>