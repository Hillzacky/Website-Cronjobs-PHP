<?php
/**
 * @var $months array
 * @var $days array
 * @var $model \app\models\Schedule
 * @var $selectedExpr string
 * @var $customExpr string
 * @var $btnText string
 * @var $this \yii\web\View
 */

use yii\helpers\Url;
use yii\helpers\Html;
use app\models\Category;
use yii\helpers\ArrayHelper;
use kartik\datetime\DateTimePicker;
?>
<?php echo Html::beginForm("", "post", [
    "class"=>"form-horizontal",
]); ?>

<?php if(!$model->isNewRecord AND !$model->hasErrors() AND ($model->max_executions > 0 OR $model->stop_at_user)): ?>
    <div class="alert alert-warning" role="alert">
        <p><?php echo $model->shuttingDownMsg() ?></p>
    </div>
<?php endif; ?>

<div class="row">
    <div class="col-md-12">
        <div class="nav-tabs-custom">
            <?php echo Html::errorSummary($model, array(
                'class' => 'alert alert-danger',
            )); ?>

            <ul class="nav nav-tabs">
                <li class="active">
                    <a href="#tab_general" data-toggle="tab" aria-expanded="true">
                        <?php echo Yii::t("app", "General") ?>
                    </a>
                </li>
                <li>
                    <a href="#tab_criterion" data-toggle="tab" aria-expanded="false">
                        <?php echo Yii::t("app", "Criterion") ?>
                    </a>
                </li>
                <li>
                    <a href="#tab_timeout" data-toggle="tab" aria-expanded="false">
                        <?php echo Yii::t("app", "Timeout") ?>
                    </a>
                </li>
                <? if($model->isHttp()): ?>
                <li>
                    <a href="#tab_advanced" data-toggle="tab" aria-expanded="false">
                        <?php echo Yii::t("app", "Method and Headers") ?>
                    </a>
                </li>
                <li>
                    <a href="#tab_auth" data-toggle="tab" aria-expanded="false">
                        <?php echo Yii::t("app", "HTTP Basic Authorization") ?>
                    </a>
                </li>
                <? endif; ?>
                <li>
                    <a href="#tab_terminate" data-toggle="tab" aria-expanded="false">
                        <?php echo Yii::t("app", "Terminate") ?>
                    </a>
                </li>
                <li class="pull-right">
                    <a href="<?= Url::to(["examples"]) ?>" target="_blank">
                        <strong><?php echo Yii::t("app", "Examples of Cron Expression") ?></strong>
                    </a>
                </li>
            </ul>
            <div class="tab-content">
                <div class="tab-pane active" id="tab_general">

                    <div class="form-group<?php echo $model->hasErrors("url") ? ' has-error' : null ?>">
                        <label for="schedule-url" class="col-sm-2 control-label"><?php echo $model->getAttributeLabel("url") ?></label>
                        <div class="col-sm-4">
                            <?php echo Html::activeTextInput($model, "url", [
                                "class"=>"form-control",
                            ]); ?>
                        </div>
                    </div>

                    <div class="form-group affect-expression<?php echo $model->hasErrors("expression") ? ' has-error' : null ?>">
                        <label for="schedule-type" class="col-sm-2 control-label"><?php echo Yii::t("app", "When to execute"); ?></label>
                        <div class="col-sm-8">
                            <div class="radio-wrapper inline-cron-ui">
                                <div class="radio inline-cron-ui">
                                    <?php echo Html::radio("Schedule[type]", $model->isAlias(), [
                                        "label"=>Yii::t("app", "Every"),
                                        "value"=>$model::TYPE_ALIAS,
                                        "data-association"=>"#dropdown-expr"
                                    ]); ?>
                                </div>
                                &nbsp;&nbsp;
                                <?php echo Html::dropDownList("cron_alias", $selectedExpr, include_once Yii::getAlias("@app/config/common_cron_expressions.php"), [
                                    "class"=>"form-control inline-cron-ui",
                                    "id"=>"dropdown-expr",
                                ]); ?>
                            </div>

                            <span class="inline-cron-ui">&nbsp;&nbsp;<?php echo Yii::t("app", "or") ?>&nbsp;&nbsp;</span>

                            <div class="radio-wrapper inline-cron-ui">
                                <div class="radio inline-cron-ui">
                                    <?php echo Html::radio("Schedule[type]", $model->isExpression(), [
                                        "label"=>Yii::t("app", "Specify with cron expression"),
                                        "value"=>$model::TYPE_EXPRESSION,
                                        "data-association"=>"#input-expr"
                                    ]); ?>
                                </div>
                                <a href="<?= Url::to(["examples"]) ?>" target="_blank"><?php echo mb_strtolower(Yii::t("app", "Help"), Yii::$app->charset) ?>&nbsp;&nbsp;</a>
                                <?php echo Html::input("text", "cron_expression", $customExpr, [
                                    "class"=>"form-control inline-cron-ui",
                                    "id"=>"input-expr",
                                ]); ?>
                            </div>

                            <span class="inline-cron-ui">&nbsp;&nbsp;<?php echo Yii::t("app", "or") ?>&nbsp;&nbsp;</span>
                            <div class="radio inline-cron-ui">
                                <?php echo Html::radio("Schedule[type]", $model->isGui(), [
                                    "label"=>Yii::t("app", "Select manually"),
                                    "value"=>$model::TYPE_GUI,
                                    "data-association"=>"#hidden-expression",
                                    "data-gui"=>1,
                                ]); ?>
                            </div>
                        </div>
                    </div>

                    <div id="cron_gui" class="table-responsive col-sm-offset-2 affect-expression"<?php if (!$model->isGui()): ?> style="display: none"<?php endif; ?>>

                        <table class="table" style="min-width: 800px;">
                            <thead>
                            <tr>
                                <td><?php echo Yii::t("app", "Minutes") ?></td>
                                <td><?php echo Yii::t("app", "Hours") ?></td>
                                <td><?php echo Yii::t("app", "Days") ?></td>
                                <td><?php echo Yii::t("app", "Months") ?></td>
                                <td><?php echo Yii::t("app", "Weekdays") ?></td>
                            </tr>
                            </thead>
                            <tr>

                                <td class="minutes_stack"><!-- Minutes -->

                                    <div class="radio">
                                        <label>
                                            <input type="radio" name="minutes" value="*" id="min_all" checked>
                                            <?php echo Yii::t("app", "All") ?>
                                        </label>
                                    </div>

                                    <div class="radio">
                                        <label>
                                            <input type="radio" name="minutes" data-group="minutes_range" value="@">
                                            <?php echo Yii::t("app", "Selected") ?>
                                        </label>
                                    </div>

                                    <table>
                                        <tr valign="top">
                                            <?php for($i=0; $i<5; $i++): ?>
                                                <td>
                                                    <select name="minutes_range" size="12" class="form-control" multiple="multiple">
                                                        <?php for($j=0; $j<12; $j++): ?>
                                                            <option value="<?php echo $i*12+$j ?>"><?php echo $i*12+$j ?></option>
                                                        <?php endfor; ?>
                                                    </select>
                                                </td>
                                            <?php endfor; ?>
                                        </tr>
                                    </table>

                                    <div class="radio">
                                        <label>
                                            <input type="radio" name="minutes" data-group="minutes_interval" value="@">
                                            <?php echo Yii::t("app", "Every X minute(s)") ?>
                                        </label>
                                    </div>

                                    <table>
                                        <tr valign="top">
                                            <td>
                                                <select name="minutes_interval" class="form-control">
                                                    <?php for($i=1; $i<60; $i++): ?>
                                                        <option value="*/<?php echo $i ?>"><?php echo Yii::t("app", "Every {n} minutes", ["n"=>$i]) ?></option>
                                                    <?php endfor; ?>
                                                </select>
                                            </td>
                                        </tr>
                                    </table>

                                </td><!-- /Minutes -->

                                <td class="hours_stack"><!-- Hours -->

                                    <div class="radio">
                                        <label>
                                            <input type="radio" name="hours" value="*" id="hours_all" checked>
                                            <?php echo Yii::t("app", "All") ?>
                                        </label>
                                    </div>

                                    <div class="radio">
                                        <label>
                                            <input type="radio" name="hours" data-group="hours_range" value="@">
                                            <?php echo Yii::t("app", "Selected") ?>
                                        </label>
                                    </div>

                                    <table>
                                        <tr valign="top">
                                            <?php for($i=0; $i<2; $i++): ?>
                                                <td>
                                                    <select name="hours_range" class="form-control" size="12" multiple="multiple">
                                                        <?php for($j=0; $j<12; $j++): ?>
                                                            <option value="<?php echo $i*12+$j ?>"><?php echo $i*12+$j ?></option>
                                                        <?php endfor; ?>
                                                    </select>
                                                </td>
                                            <?php endfor; ?>
                                        </tr>
                                    </table>

                                    <div class="radio">
                                        <label>
                                            <input type="radio" name="hours" data-group="hours_interval" value="@">
                                            <?php echo Yii::t("app", "Every X hour(s)") ?>
                                        </label>
                                    </div>

                                    <table>
                                        <tr valign="top">
                                            <td>
                                                <select name="hours_interval" class="form-control">
                                                    <?php for($i=1; $i<24; $i++): ?>
                                                        <option value="*/<?php echo $i ?>"><?php echo Yii::t("app", "Every {n} hours", ["n"=>$i]) ?></option>
                                                    <?php endfor; ?>
                                                </select>
                                            </td>
                                        </tr>
                                    </table>

                                </td><!-- /Hours -->

                                <td class="dom_stack"><!-- Days -->

                                    <div class="radio">
                                        <label>
                                            <input type="radio" name="dom" value="*" id="dom_all" checked>
                                            <?php echo Yii::t("app", "All") ?>
                                        </label>
                                    </div>

                                    <div class="radio">
                                        <label>
                                            <input type="radio" name="dom" value="L" id="dom_last">
                                            <?php echo Yii::t("app", "Last day of the month") ?>
                                        </label>
                                    </div>

                                    <div class="radio">
                                        <label>
                                            <input type="radio" name="dom" value="@" data-group="dom_range">
                                            <?php echo Yii::t("app", "Selected") ?>
                                        </label>
                                    </div>

                                    <table>
                                        <tr valign="top">
                                            <?php for($i=0; $i<3; $i++): ?>
                                                <?php $to = $i==2 ? 7 : 12; ?>
                                                <td>
                                                    <select name="dom_range" size="<?php echo $to ?>" multiple="multiple" class="form-control">
                                                        <?php for($j=0; $j<$to; $j++): ?>
                                                            <option value="<?php echo ($i*12+$j)+1 ?>"><?php echo ($i*12+$j)+1 ?></option>
                                                        <?php endfor; ?>
                                                    </select>
                                                </td>
                                            <?php endfor; ?>
                                        </tr>
                                    </table>

                                    <div class="radio">
                                        <label>
                                            <input type="radio" name="dom" data-group="dom_interval" value="@">
                                            <?php echo Yii::t("app", "Every X days(s)") ?>
                                        </label>
                                    </div>

                                    <table>
                                        <tr valign="top">
                                            <td>
                                                <select name="dom_interval" class="form-control">
                                                    <?php for($i=1; $i<32; $i++): ?>
                                                        <option value="*/<?php echo $i ?>"><?php echo Yii::t("app", "Every {n} days", ["n"=>$i]) ?></option>
                                                    <?php endfor; ?>
                                                </select>
                                            </td>
                                        </tr>
                                    </table>

                                    <div class="radio">
                                        <label>
                                            <input type="radio" name="dom" value="@" id="dom_nearest" data-group="dom_nearest">
                                            <?php echo Yii::t("app", "Nearest weekday of") ?>
                                        </label>
                                    </div>

                                    <table>
                                        <tr valign="top">
                                            <td>
                                                <select name="dom_nearest" class="form-control">
                                                    <?php for($i=1; $i<32; $i++): ?>
                                                        <option value="<?php echo $i ?>W"><?php echo Yii::t("app", "{n} days", ["n"=>$i]) ?></option>
                                                    <?php endfor; ?>
                                                </select>
                                            </td>
                                        </tr>
                                    </table>

                                </td><!-- /Days -->

                                <td class="month_stack"><!-- Months -->

                                    <div class="radio">
                                        <label>
                                            <input type="radio" name="month" value="*" checked>
                                            <?php echo Yii::t("app", "All") ?>
                                        </label>
                                    </div>

                                    <div class="radio">
                                        <label>
                                            <input type="radio" name="month" value="@" data-group="month_range">
                                            <?php echo Yii::t("app", "Selected") ?>
                                        </label>
                                    </div>

                                    <table>
                                        <tr valign="top">
                                            <td>
                                                <select name="month_range" size="<?php echo count($months) ?>" multiple="multiple" class="form-control">
                                                    <?php foreach($months as $monthId=>$month): ?>
                                                        <option value="<?php echo $monthId ?>"><?php echo $month ?></option>
                                                    <?php endforeach; ?>
                                                </select>
                                            </td>
                                        </tr>
                                    </table>

                                </td><!-- /Months -->


                                <td class="dow_stack"><!-- Weekdays -->

                                    <div class="radio">
                                        <label>
                                            <input type="radio" name="dow" id="dom_all" value="*" checked>
                                            <?php echo Yii::t("app", "All") ?>
                                        </label>
                                    </div>


                                    <div class="radio">
                                        <label>
                                            <input type="radio" name="dow" value="@" data-group="dow_range">
                                            <?php echo Yii::t("app", "Selected") ?>
                                        </label>
                                    </div>

                                    <table>
                                        <tr valign="top">
                                            <td>
                                                <select name="dow_range" size="<?php echo count($days) ?>" multiple="multiple" class="form-control">
                                                    <?php foreach($days as $dayId=>$day): ?>
                                                        <option value="<?php echo $dayId ?>"><?php echo $day ?></option>
                                                    <?php endforeach; ?>
                                                </select>
                                            </td>
                                        </tr>
                                    </table>

                                    <div class="radio">
                                        <label>
                                            <input type="radio" name="dow" value="@" data-group="dow_day">
                                            <?php echo Yii::t("app", "Dow position") ?>
                                        </label>
                                        <select name="dow_day" id="dow_position" class="form-control" style="margin-bottom: 5px">
                                            <option value="1"><?php echo Yii::t("app", "First") ?></option>
                                            <option value="2"><?php echo Yii::t("app", "Second") ?></option>
                                            <option value="3"><?php echo Yii::t("app", "Third") ?></option>
                                            <option value="4"><?php echo Yii::t("app", "Forth") ?></option>
                                            <option value="L"><?php echo Yii::t("app", "Last") ?></option>
                                        </select>
                                        <select name="dow_day" id="dow_day" class="form-control">
                                            <?php foreach($days as $dayId=>$day): ?>
                                                <option value="<?php echo $dayId ?>"><?php echo $day ?></option>
                                            <?php endforeach; ?>
                                        </select>
                                    </div>

                                </td><!-- /Weekdays -->
                            </tr>
                        </table>
                        <?php echo Html::activeHiddenInput($model, "expression", ["id"=>"hidden-expression"]); ?>
                    </div>

                    <div class="form-group<?php echo $model->hasErrors("start_at_user") ? ' has-error' : null ?>">
                        <label for="schedule-start_at_user" class="col-sm-2 control-label"><?php echo Yii::t("app", "Start run at") ?></label>
                        <div class="col-sm-4">
                            <?php echo DateTimePicker::widget([
                                'id'=>'schedule-start_at_user',
                                'name' => "Schedule[start_at_user]",
                                'layout'=>'{picker}{remove}{input}',
                                'options' => [
                                    'placeholder' => Yii::t("app", "Current datetime"),
                                    'readonly'=>true,
                                ],
                                'convertFormat' => true,
                                'value'=>$model->start_at_user,
                                'pluginOptions' => [
                                    'autoclose'=>true,
                                    'format' => 'yyyy-MM-dd HH:i',
                                    'startDate' => Yii::$app->getUser()->getIdentity()->getDateObject()->format("Y-m-d H:i"),
                                    'todayHighlight' => true,
                                    'locale'=> Yii::$app->language,
                                    'minuteStep'=>1,
                                ]
                            ]); ?>
                        </div>
                    </div>

                    <div class="form-group">
                        <div class="col-sm-offset-2 col-sm-10">
                            <button class="btn btn-info" id="get-prediction-log">
                                <?php echo Yii::t("app", "Get next {n} run dates", [
                                    "n"=>Yii::$app->params['ajaxPrediction']
                                ]) ?>
                            </button>
                            <div id="prediction-log"></div>
                        </div>
                    </div>

                    <div class="form-group<?php echo $model->hasErrors("title") ? ' has-error' : null ?>">
                        <label for="schedule-title" class="col-sm-2 control-label"><?php echo Yii::t("app", "Cron job name"); ?></label>
                        <div class="col-sm-4">
                            <?php echo Html::activeTextInput($model, "title", [
                                "class"=>"form-control",
                            ]); ?>
                        </div>
                    </div>

                    <div class="form-group<?php echo $model->hasErrors("description") ? ' has-error' : null ?>">
                        <label for="schedule-description" class="col-sm-2 control-label"><?php echo $model->getAttributeLabel("description") ?></label>
                        <div class="col-sm-4">
                            <?php echo Html::activeTextarea($model, "description", [
                                "class"=>"form-control",
                            ]); ?>
                        </div>
                    </div>

                    <div class="form-group<?php echo $model->hasErrors("category") ? ' has-error' : null ?>">
                        <label for="schedule-category" class="col-sm-2 control-label"><?php echo Yii::t("app", "Category"); ?></label>
                        <div class="col-sm-4">
                            <?php echo Html::activeDropDownList($model, "category_id", ArrayHelper::map(Category::find()->owner()->asArray()->all(), "id", "title"), [
                                "class"=>"form-control",
                                "prompt"=>Yii::t("app", "None"),
                            ]); ?>
                        </div>
                    </div>

                    <div class="form-group<?php echo $model->hasErrors("status") ? ' has-error' : null ?>">
                        <label for="schedule-status" class="col-sm-2 control-label"><?php echo Yii::t("app", "Status"); ?></label>
                        <div class="col-sm-4">

                            <?php echo Html::activeRadioList($model, "status", [
                                $model::STATUS_ENABLED=>Yii::t("app", "Enabled"),
                                $model::STATUS_DISABLED=>Yii::t("app", "Disabled"),
                            ], [
                                "id"=>"raising-capital-switcher",
                                "item"=>function ($index, $label, $name, $checked, $value) {
                                    return '<div class="radio"><label>' . Html::radio($name, $checked, [
                                        'value'  => $value,
                                    ]) . $label . '</label></div>';
                                }
                            ]); ?>

                        </div>
                    </div>

                    <div class="form-group<?php echo $model->hasErrors("notify") ? ' has-error' : null ?>">
                        <label for="schedule-notify" class="col-sm-2 control-label"><?php echo Yii::t("app", "Email Me"); ?></label>
                        <div class="col-sm-4">
                            <?php echo Html::activeDropDownList($model, "notify", $model::getNotifyArray(), [
                                "class"=>"form-control",
                            ]); ?>
                        </div>
                    </div>

                </div>

                <? if($model->isHttp()): ?>
                <div class="tab-pane" id="tab_advanced">
                    <div class="row">
                        <div class="col-md-12">
                            <div class="alert alert-info" role="alert">
                                <?php echo Yii::t("app", "Note! Parameter name and value are automatically encoded."); ?>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <!-- Cookie -->
                        <div class="col-md-4">
                            <h2><?php echo Yii::t("app", "Cookies") ?></h2>
                            <div id="cookie-param-container">
                                <?php echo $this->render("_additional_params", [
                                    "placeholderKey"=>Yii::t("app", "Cookie name"),
                                    "placeholderValue"=>Yii::t("app", "Cookie value"),
                                    "modelAttr"=>"cookieParams",
                                    "model"=>$model,
                                    "params"=>$model->cookieParams,
                                ]); ?>
                            </div>
                            <button type="button" data-tmpl="cookie" class="btn btn-sm btn-success add-param"><?php echo Yii::t("app", "Add Cookie param") ?></button>
                        </div>
                        <!-- /Cookie -->

                        <!-- POST -->
                        <div class="col-md-4">
                            <h2><?php echo Yii::t("app", "POST Values") ?></h2>
                            <div id="post-param-container">
                                <?php echo $this->render("_additional_params", [
                                    "placeholderKey"=>Yii::t("app", "Post name"),
                                    "placeholderValue"=>Yii::t("app", "Post value"),
                                    "modelAttr"=>"postParams",
                                    "model"=>$model,
                                    "params"=>$model->postParams,
                                ]); ?>
                            </div>
                            <button type="button" data-tmpl="post" class="btn btn-sm btn-success add-param"><?php echo Yii::t("app", "Add POST param") ?></button>
                        </div>
                        <!-- /POST -->

                        <!-- Headers -->
                        <div class="col-md-4">
                            <h2><?php echo Yii::t("app", "Headers") ?></h2>
                            <div id="headers-param-container">
                                <?php echo $this->render("_additional_params", [
                                    "placeholderKey"=>Yii::t("app", "Header name"),
                                    "placeholderValue"=>Yii::t("app", "Header value"),
                                    "modelAttr"=>"headerParams",
                                    "model"=>$model,
                                    "params"=>$model->headerParams,
                                ]); ?>
                            </div>
                            <button type="button" data-tmpl="headers" class="btn btn-sm btn-success add-param"><?php echo Yii::t("app", "Add Header") ?></button>
                        </div>
                        <!-- /Headers -->
                    </div>
                </div>
                <div class="tab-pane" id="tab_auth">
                    <div class="form-group<?php echo $model->hasErrors("http_auth_username") ? ' has-error' : null ?>">
                        <label for="schedule-http_auth_username" class="col-sm-2 control-label"><?php echo Yii::t("app", "Username"); ?></label>
                        <div class="col-sm-4">
                            <?php echo Html::activeTextInput($model, "http_auth_username", [
                                "class"=>"form-control",
                            ]); ?>
                        </div>
                    </div>

                    <div class="form-group<?php echo $model->hasErrors("http_auth_password") ? ' has-error' : null ?>">
                        <label for="schedule-http_auth_password" class="col-sm-2 control-label"><?php echo Yii::t("app", "Password"); ?></label>
                        <div class="col-sm-4">
                            <?php echo Html::activeTextInput($model, "http_auth_password", [
                                "class"=>"form-control",
                            ]); ?>
                        </div>
                    </div>
                </div>
                <? endif; ?>

                <div class="tab-pane" id="tab_terminate">
                    <div class="form-group<?php echo $model->hasErrors("stop_at_user") ? ' has-error' : null ?>">
                        <label for="schedule-stop_at_user" class="col-sm-2 control-label"><?php echo Yii::t("app", "Stop running at") ?></label>
                        <div class="col-sm-4">
                            <?php echo DateTimePicker::widget([
                                'id'=>'schedule-stop_at_user',
                                'name' => "Schedule[stop_at_user]",
                                'layout'=>'{picker}{remove}{input}',
                                'options' => [
                                    'placeholder' => Yii::t("app", "Please select date and time..."),
                                    'readonly'=>true,
                                ],
                                'convertFormat' => true,
                                'value'=>$model->stop_at_user,
                                'pluginOptions' => [
                                    'autoclose'=>true,
                                    'format' => 'yyyy-MM-dd HH:i',
                                    'startDate' => Yii::$app->getUser()->getIdentity()->getDateObject()->format("Y-m-d H:i"),
                                    'todayHighlight' => true,
                                    'locale'=> Yii::$app->language,
                                    'minuteStep'=>1,
                                ]
                            ]); ?>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-sm-2"><p class="pull-right"><?php echo Yii::t("app", "or"); ?></p></div>
                    </div>


                    <div class="form-group<?php echo $model->hasErrors("max_executions") ? ' has-error' : null ?>">
                        <label for="schedule-max_executions" class="col-sm-2 control-label"><?php echo Yii::t("app", "Stop after X iterations"); ?></label>
                        <div class="col-sm-4">
                            <div class="input-group">
                                <?php echo Html::activeTextInput($model, "max_executions", [
                                    "class"=>"form-control",
                                    "type"=>"number",
                                    "id"=>"inputMaxExec",
                                    "min"=>"0"
                                ]); ?>
                                <span class="input-group-btn">
                                        <button class="btn btn-default" type="button" style="border-radius: 0; -webkit-border-radius: 0" onclick="document.getElementById('inputMaxExec').value = 0;"><?= Yii::t("app", "Clear") ?></button>
                                    </span>
                            </div><!-- /input-group -->

                        </div>
                    </div>
                </div>

                <div class="tab-pane" id="tab_timeout">
                    <div class="form-group<?php echo $model->hasErrors("timeout") ? ' has-error' : null ?>">
                        <label for="schedule-timeout" class="col-sm-2 control-label"><?php echo Yii::t("app", "Timeout"); ?></label>
                        <div class="col-sm-4">
                            <?php echo Html::activeTextInput($model, "timeout", [
                                "class"=>"form-control",
                                "min"=>1,
                                "type"=>"number"
                            ]); ?>
                        </div>
                    </div>
                    <? if($model->isHttp()): ?>
                    <div class="form-group<?php echo $model->hasErrors("connection_timeout") ? ' has-error' : null ?>">
                        <label for="schedule-connection_timeout" class="col-sm-2 control-label"><?php echo Yii::t("app", "Connection Timeout"); ?></label>
                        <div class="col-sm-4">
                            <?php echo Html::activeTextInput($model, "connection_timeout", [
                                "class"=>"form-control",
                                "min"=>1,
                                "type"=>"number"
                            ]); ?>
                        </div>
                    </div>
                    <? endif; ?>
                </div>

                <div class="tab-pane" id="tab_criterion">
                    <p>
                        <?php echo Yii::t("app", "Rules for considering the cron job execution successful or failed by the response from your cron job script:") ?>
                    </p>
                    <? if($model->isHttp()): ?>
                        <p>
                            1. <?php echo Yii::t("app", "Consider failed when the HTTP Status is NOT 2xx (e.g. 200, 201, etc.).") ?>
                        </p>
                        <p>
                            2. <?php echo Yii::t("app", "When the HTTP Status is 2xx") ?>,
                        </p>
                    <? elseif ($model->isCommand()): ?>
                        <p>
                            1. <?php echo Yii::t("app", "Consider failed when the Exit Code is not 0 (e.g. 1, 126, 127, etc.).") ?>
                        </p>
                        <p>
                            2. <?php echo Yii::t("app", "When the Exit Code is 0") ?>,
                        </p>
                    <? endif; ?>

                    <div class="radio">
                        <?php echo Html::activeRadio($model, "considerInput", [
                            "value"=>\app\models\Schedule::CONSIDER_DEFAULT,
                            "label"=>Yii::t("app", "consider succeeded"),
                        ]); ?>
                    </div>

                    <div class="radio">
                        <?php echo Html::activeRadio($model, "considerInput", [
                            "value"=>\app\models\Schedule::CONSIDER_SUCCESS,
                            "uncheck"=>false,
                            "label"=>$model->isHttp() ? Yii::t("app", "consider succeeded if the HTTP message body matches regular expression") : ($model->isCommand() ? Yii::t("app", "consider succeeded if the command std output matches regular expression") : null),
                        ]); ?>
                        <div class="row">
                            <div class="col-md-8">
                                <div class="row" style="margin-left: 20px">
                                    <div class="col-md-8">
                                        <div class="form-group<?php echo $model->hasErrors("success_if") ? ' has-error' : null ?>">
                                            <div class="input-group">
                                                <span class="input-group-addon">/</span>
                                                <?php echo Html::activeTextInput($model, "success_if", [
                                                    "class"=>"form-control",
                                                ]); ?>
                                                <span class="input-group-addon">/</span>
                                            </div>
                                        </div>

                                    </div>
                                    <div class="col-md-4">
                                        <div class="input-group">
                                            <span class="input-group-addon"><?php echo Yii::t("app", "Modifier") ?></span>
                                            <?php echo Html::activeTextInput($model, "success_if_modificator", [
                                                "class"=>"form-control",
                                            ]); ?>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="radio">
                        <?php echo Html::activeRadio($model, "considerInput", [
                            "value"=>\app\models\Schedule::CONSIDER_FAIL,
                            "uncheck"=>false,
                            "label"=>$model->isHttp() ? Yii::t("app", "consider failed if the HTTP message body matches regular expression") : ($model->isCommand() ? Yii::t("app", "consider failed if the command std output matches regular expression") : null),
                        ]); ?>
                        <div class="row">
                            <div class="col-md-8">
                                <div class="row" style="margin-left: 20px">
                                    <div class="col-md-8">
                                        <div class="form-group<?php echo $model->hasErrors("fail_if") ? ' has-error' : null ?>">
                                            <div class="input-group">
                                                <span class="input-group-addon">/</span>
                                                <?php echo Html::activeTextInput($model, "fail_if", [
                                                    "class"=>"form-control",
                                                ]); ?>
                                                <span class="input-group-addon">/</span>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="input-group">
                                            <span class="input-group-addon"><?php echo Yii::t("app", "Modifier") ?></span>
                                            <?php echo Html::activeTextInput($model, "fail_if_modificator", [
                                                "class"=>"form-control",
                                            ]); ?>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                </div>

                <hr>
                <input type="submit" value="<?php echo $btnText ?>" class="btn btn-primary">
                &nbsp;&nbsp;
                <button type="submit" name="and" value="exit" class="btn btn-warning">
                    <?= Yii::t("app", "Save and Exit"); ?>
                </button>
                &nbsp;&nbsp;
                <button type="submit" name="and" value="test" class="btn btn-success">
                    <?= Yii::t("app", "Save and Run Test"); ?>
                </button>
                &nbsp;&nbsp;
                <a href="<?php echo Url::to(["/cron-job"]) ?>" class="btn btn-default"><?php echo Yii::t("app", "Cancel") ?></a>
            </div>
        </div>
    </div>
</div>
<?php echo Html::endForm(); ?>