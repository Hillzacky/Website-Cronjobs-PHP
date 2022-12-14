<?php
namespace app\controllers;

use app\components\AppController;
use app\components\ConsoleCommandRunner;
use app\components\Cron;
use app\models\Log;
use app\models\Schedule;
use app\models\search\LogSearch;
use app\models\search\ScheduleSearch;
use app\models\Stat;
use app\models\User;
use yii\data\ArrayDataProvider;
use yii\db\Connection;
use yii\helpers\ArrayHelper;
use yii\helpers\Html;
use yii\helpers\Json;
use Yii;
use yii\helpers\Url;
use yii\web\Cookie;
use yii\web\HttpException;
use yii\web\Response;
use yii\filters\AccessControl;
use Throwable;

class CronJobController extends AppController
{
    public function behaviors() {
        $behaviors = parent::behaviors();
        $behaviors['access'] = [
            'class'=>AccessControl::class,
            'rules' => [
                [
                    'allow'=>true,
                    'actions'=>['exec'],
                    'roles'=>['?'],
                ],
                [
                    'allow'=>true,
                    'roles'=>['@'],
                ]
            ],
        ];
        return $behaviors;
    }

    public function actionCreate() {
        $this->getView()->title = \Yii::t("app", "Create New Cron Job");
        $type = in_array(Yii::$app->request->get("type"), array_keys(Schedule::getTypeArray())) ? Yii::$app->request->get("type") : Schedule::SCHEDULE_TYPE_HTTP;
        /**
         * @var User $user
         */
        $user = Yii::$app->getUser()->getIdentity();

        $this->breadcrumbs = [
            [
                "url"=>Url::to(["index"]),
                "label"=>Yii::t("app", "My Cron Jobs"),
            ],
            [
                "label"=>$this->getView()->title
            ]
        ];

        $model = new Schedule();
        $model->user_id = $user->id;
        $model->populateRelation('user', $user);
        $model->setScenario(Schedule::SCENARIO_MANAGE);
        $model->command_type = $type;
        $model->loadDefaultValues();

        if(Yii::$app->getRequest()->getIsPost()) {
            $request = Yii::$app->getRequest();
            if($model->load($request->post()) AND $model->validate()) {
                $model->save(false);
                Yii::$app->getSession()->setFlash("success", Yii::t("app", "Cron job has been added to tasks queue"));
                return $this->redirectAfterSave($model);
            }
        }

        $this->registerJs($model);

        $months = include Yii::getAlias("@app/config/month.php");
        $days = include Yii::getAlias("@app/config/day.php");

        return $this->render("create_edit", [
            "months"=>$months,
            "days"=>$days,
            "model"=>$model,
            "selectedExpr"=>Yii::$app->request->post("cron_alias"),
            "customExpr"=>Yii::$app->request->post("cron_expression"),
            "btnText"=>Yii::t("app", "Create"),
        ]);
    }

    public function actionUpdate($id) {
        /**
         * @var $model Schedule
         */
        $model = $this->loadModel(Schedule::class, $id);
        $model->setScenario(Schedule::SCENARIO_MANAGE);

        $this->getView()->title = Yii::t("app", "Edit Cron Job | {title}", [
            "title"=>$model->getOldAttribute('title'),
        ]);

        $this->breadcrumbs = [
            [
                "url"=>Url::to(["index"]),
                "label"=>Yii::t("app", "My Cron Jobs"),
            ],
            [
                "label"=>$this->getView()->title
            ]
        ];

        $months = include Yii::getAlias("@app/config/month.php");
        $days = include Yii::getAlias("@app/config/day.php");

        if(Yii::$app->getRequest()->getIsPost()) {
            $request = Yii::$app->getRequest();
            if($model->load($request->post()) AND $model->validate()) {
                $model->save(false);
                Yii::$app->getSession()->setFlash("success", Yii::t("app", "Cron job has been modified"));
                return $this->redirectAfterSave($model);
            }
        } else {
            $model->buildAdditionalParams();
        }

        $this->registerJs($model);

        return $this->render("create_edit", [
            "months"=>$months,
            "days"=>$days,
            "model"=>$model,
            "selectedExpr"=>$model->isAlias() ? $model->expression : null,
            "customExpr"=>$model->isExpression() ? $model->expression : null,
            "btnText"=>Yii::t("app", "Update"),
        ]);
    }

    public function actionClone($id) {
        /**
         * @var $model Schedule
         * @var $oldModel Schedule
         */
        if(Yii::$app->getRequest()->getIsPost()) {
            $oldModel = $this->loadModel(Schedule::class, $id);

            $model = new Schedule();
            $model->setScenario(Schedule::SCENARIO_MANAGE);
            $model->command_type = $oldModel->command_type;
            $model->populateRelation("user", $oldModel->user);
            $model->user_id = $oldModel->user_id;

            $request = Yii::$app->getRequest();

            if($model->load($request->post()) AND $model->validate()) {
                $model->save(false);
                Yii::$app->getSession()->setFlash("success", Yii::t("app", "Cron Job has been cloned"));
                return $this->redirectAfterSave($model);
            }
        } else {
            $model = $this->loadModel(Schedule::class, $id);
            $model->buildAdditionalParams();
        }

        $this->getView()->title = Yii::t("app", "Clone Cron Job");

        $this->breadcrumbs = [
            [
                "url"=>Url::to(["index"]),
                "label"=>Yii::t("app", "My Cron Jobs"),
            ],
            [
                "label"=>$this->getView()->title
            ]
        ];

        $months = include Yii::getAlias("@app/config/month.php");
        $days = include Yii::getAlias("@app/config/day.php");

        $this->registerJs($model);

        return $this->render("create_edit", [
            "months"=>$months,
            "days"=>$days,
            "model"=>$model,
            "selectedExpr"=>$model->isAlias() ? $model->expression : null,
            "customExpr"=>$model->isExpression() ? $model->expression : null,
            "btnText"=>Yii::t("app", "Clone"),
        ]);
    }

    public function actionDelete($id) {
        $model = $this->loadModel(Schedule::class, $id);
        if($model->delete()) {
            Yii::$app->getSession()->setFlash("success", Yii::t("app", "Cron job has been deleted."));
        }
        return $this->goToPreviousPage(["index"]);
    }

    public function actionRun($id) {
        /**
         * @var $model Schedule
         */
        $model = $this->loadModel(Schedule::class, $id);

        if(Yii::$app->request->getIsAjax()) {
            $handler = $model->initHandler();
            $startTime = microtime(true);
            $model->runHandler($handler);
            $finishTime = microtime(true);
            $responseInfo = $model->responseInfo($handler);

            return $this->renderPartial("exec-{$model->command_type}", [
                "handler"=>$handler,
                "startTime"=>$startTime,
                "finishTime"=>$finishTime,
                "responseInfo"=>$responseInfo,
                "model"=>$model,
            ]);
        }

        $this->getView()->title = Yii::t("app", "Cron Job Manual Execution Test");

        $this->breadcrumbs = [
            [
                "url"=>Url::to(["index"]),
                "label"=>Yii::t("app", "My Cron Jobs"),
            ],
            [
                "label"=>$this->getView()->title
            ]
        ];

        $this->getView()->registerJs("WebCronApp.cronRun({
            execUrl: ".Json::encode(Url::to(["run", "id"=>$model->id]))."
        })");

        return $this->render("run-{$model->command_type}", [
            "model"=>$model,
            "cookies"=>$model->decodeCookies(),
            "post"=>$model->decodePost(),
            "headers"=>$model->decodeHeaders(),
        ]);
    }

    protected function registerJs(Schedule $model) {
        $this->getView()->registerJs("WebCronApp.cronJob({
            predictionUrl:".Json::encode(Url::to(["ajax-prediction"])).",
            ui: {
                ". ($model->isGui() ? "command: ". Json::encode($model->expression) : null) ."
            },
            params: {
                cookieTmpl: ".Json::encode($this->renderPartial("_additional_params", [
                "placeholderKey"=>Yii::t("app", "Cookie name"),
                "placeholderValue"=>Yii::t("app", "Cookie value"),
                "modelAttr"=>"cookieParams",
                "model"=>$model,
            ])).",
                postTmpl: ".Json::encode($this->renderPartial("_additional_params", [
                "placeholderKey"=>Yii::t("app", "Post name"),
                "placeholderValue"=>Yii::t("app", "Post value"),
                "modelAttr"=>"postParams",
                "model"=>$model,
            ])).",
                headersTmpl: ".Json::encode($this->renderPartial("_additional_params", [
                "placeholderKey"=>Yii::t("app", "Header name"),
                "placeholderValue"=>Yii::t("app", "Header value"),
                "modelAttr"=>"headerParams",
                "model"=>$model,
            ]))."
            }
        });");
    }

    public function actionIndex() {
        if(Yii::$app->getRequest()->getIsPost()) {
            $method = "bulk".Yii::$app->getRequest()->post('bulk-group');
            $ids = (array) Yii::$app->getRequest()->post('selection');
            $value = Yii::$app->getRequest()->post('bulk-value');
            if(method_exists($this, $method) AND !empty($ids)) {
                return $this->$method($value, $ids);
            }
        }

        $this->getView()->title = Yii::t("app", "My Cron Jobs");
        $jsUi = [
            "loading"=>Yii::t("app", "Loading..."),
        ];
        $this->getView()->registerJs("WebCronApp.setUi(".Json::encode($jsUi).")");
        $searchModel = new ScheduleSearch();

        $formName = $searchModel->formName();
        $reset = (bool) Yii::$app->request->get("reset");
        if($reset) {
            Yii::$app->response->cookies->remove($formName);
        }
        $queryParams = Yii::$app->request->queryParams;
        if(isset($queryParams[$formName])) {
            Yii::$app->response->cookies->add(new Cookie([
                'name' => $formName,
                'value' => $queryParams[$formName],
                'secure' => Yii::$app->params['cookieSecure'],
                'sameSite' => Yii::$app->params['cookieSameSite'],
            ]));
            $searchParams = Yii::$app->request->get();
        } else {
            $searchParams = (Yii::$app->request->cookies->has($formName) AND !$reset) ? [$formName => (array) Yii::$app->request->cookies->get($formName)->value] : [];
        }
        $dataProvider = $searchModel->search($searchParams);

        $this->breadcrumbs = [
            [
                "label"=>$this->getView()->title
            ]
        ];

        $this->getView()->registerJs("WebCronApp.cronIndex({})");

        return $this->render("index", [
            "searchModel"=>$searchModel,
            "dataProvider"=>$dataProvider,
        ]);
    }

    public function actionSwitch($id) {
        /**
         * @var $model Schedule
         */
        $model = $this->loadModel(Schedule::class, $id);
        $status = Yii::$app->getRequest()->get('status');
        Yii::$app->response->format = Response::FORMAT_JSON;

        if(!in_array($status, array_keys(Schedule::getStatusArray()))) {
            return [
                "error"=>Yii::t("app", "Internal server error"),
            ];
        }

        $model->status = $status;

        if($model->save()) {
            if($model->isEnabled()) {
                $msg =Yii::t("app", "Cron job (ID: {id}) has been enabled.", [
                    "id"=>$model->id,
                ]);
            } else {
                $msg = Yii::t("app", "Cron job (ID: {id}) has been disabled.", [
                    "id"=>$model->id,
                ]);
            }
            return [
                "success"=>$msg
            ];
        } else {
            return [
                "error"=>Html::errorSummary($model, [
                    "header"=>Yii::t("app", "The above error occurred while the Web server was processing your request."),
                ]),
            ];
        }
    }

    public function actionPredict($id) {
        /**
         * @var User $user
         * @var Schedule $model
         */
        $model = $this->loadModel(Schedule::class, $id);
        $user = Yii::$app->getUser()->getIdentity();

        $request_future_date = Yii::$app->getRequest()->get('startDate');

        $currentDate = $user->getDateObject();
        $modelFutureDate = $model->start_at_user ? $user->getDateObject($model->start_at_user) : $currentDate;
        try {
            $futureDate = $request_future_date ? $user->getDateObject($request_future_date) : $modelFutureDate;
        } catch (Throwable $throwable) {
            $futureDate = $modelFutureDate;
        }

        $cron = Cron::factory($model->expression);
        $prediction = $cron->getNextMultipleRunDates(Yii::$app->params['schedulePrediction'], $futureDate);

        $this->getView()->title = Yii::t("app", "Cron Job Prediction");
        $this->breadcrumbs = [
            [
                'label'=>Yii::t("app", "Cron Jobs"),
                'url'=>Url::to(["index"]),
            ],
            [
                "label"=>$this->getView()->title
            ]
        ];
        $provider = new ArrayDataProvider([
            'allModels'=>$prediction,
            "pagination"=>[
                'pageSize'=>20,
            ]
        ]);

        return $this->render("predict", [
            "provider"=>$provider,
            "model"=>$model,
            "startDate"=>$futureDate,
            "currentDate"=>$currentDate
        ]);
    }

    public function actionAjaxPrediction() {
        $expr = (string) Yii::$app->getRequest()->get('expression');
        $start_str = (string) Yii::$app->getRequest()->get('start_at');
        $response = Yii::$app->getResponse();
        $response->format = Response::FORMAT_JSON;
        /**
         * @var User $user
         */
        $user = Yii::$app->getUser()->getIdentity();
        try {
            $start_date = $user->getDateObject($start_str);
        } catch (Throwable $throwable) {
            $start_date = $user->getDateObject();
        }

        if(!Cron::hasNextExecutionDate($expr, $user->getDateObject())) {
            $response = $this->renderPartial("ajax_invalid_expr");
        } else {
            $cron = Cron::factory($expr);
            $prediction = $cron->getNextMultipleRunDates(Yii::$app->params['ajaxPrediction'], $start_date);
            if(!empty($prediction)) {
                $response = $this->renderPartial("ajax_prediction", [
                    "predictions"=>$prediction,
                    "expression"=>$expr,
                    "start"=>$start_date
                ]);
            } else {
                $response = $this->renderPartial("ajax_invalid_expr");
            }
        }

        return [
            'output'=>$response,
        ];
    }

    public function actionReset($id) {
        Yii::$app->db->transaction(function($db) use($id) {
            /**
             * @var $db Connection
             */
            Log::deleteAll([
                "user_id"=>Yii::$app->getUser()->getId(),
                "schedule_id"=>$id,
            ]);
            Stat::deleteAll([
                "user_id"=>Yii::$app->getUser()->getId(),
                "schedule_id"=>$id,
            ]);
        });
        Yii::$app->getSession()->setFlash("success", Yii::t("app", "Successfully reset the logs and stat. of #{id} cron job", [
            "id"=>$id
        ]));
        $this->goToPreviousPage(["index"]);
    }

    public function actionStatistic($id) {
        $schedule = $this->loadModel(Schedule::class, $id);
        $stats = Stat::find()
            ->select("SUM(success) as success, SUM(failed) as failed")
            ->asArray()
            ->where([
                "schedule_id"=>$schedule->id
            ])
            ->one();

        $lc = $stats['success'] + $stats['failed'];
        if($lc) {
            $success = $stats['success'];
            $successPercent = round(100*$success/$lc, 2);
            $failurePercent = round(100*$stats['failed']/$lc, 2);

            $totalPhrase = Yii::t("app", "Total executions: {0}", "<span class='label label-info'><strong>".number_format($lc)."</strong></span>");
            $successPhrase = Yii::t("app", "Succeed: {0} ({1} %)", ["<span class='label label-success'><strong>".number_format($success)."</strong></span> ", $successPercent]);
            $failPhrase = Yii::t("app", "Failed: {0} ({1} %)", ["<span class='label label-danger'><strong>".number_format($stats['failed'])."</strong></span>", $failurePercent]);
        } else {
            $success = $successPercent = $failurePercent = $totalPhrase = $successPhrase = $failPhrase = null;
        }



        $this->getView()->title = Yii::t("app", "{title} | Schedule Statistic", [
            "title"=>$schedule->title,
        ]);

        $searchModel = new LogSearch();
        $dataProvider = $searchModel->search($schedule->id, Yii::$app->request->get());

        $this->breadcrumbs = [
            [
                "url"=>Url::to(["index"]),
                "label"=>Yii::t("app", "My Cron Jobs"),
            ],
            [
                "label"=>$this->getView()->title
            ]
        ];

        $this->getView()->registerJs("WebCronApp.cronLog({})");

        return $this->render("statistic", [
            "searchModel"=>$searchModel,
            "dataProvider"=>$dataProvider,
            "schedule"=>$schedule,
            "stats"=>$stats,
            "lc"=>$lc,
            "totalPhrase"=>$totalPhrase,
            "successPhrase"=>$successPhrase,
            "failPhrase"=>$failPhrase,
            "successPercent"=>$successPercent,
            "failurePercent"=>$failurePercent,
            "success"=>$success,
        ]);
    }

    public function actionExec() {
        $key = (string) ArrayHelper::getValue(Yii::$app->params, "webHandlerKey");
        $get_key = (string) Yii::$app->getRequest()->get("key");

        if(empty($key) OR (strcmp($key, $get_key) !== 0)) {
            throw new HttpException(403, "You are not allowed to perform this action");
        }
        @ini_set('max_execution_time', 0);
        @ini_set('max_input_time', -1);

        $runner = new ConsoleCommandRunner();
        $runner->run("exec");
        return $runner->getExitCode()."\n\n".$runner->getOutput();
    }

    public function actionLog($id) {
        $log = $this->loadModel(Log::class, $id);
        $this->getView()->title = Yii::t("app", "Cron Job Execution Log | {title}", [
            "title"=>$log->schedule->title
        ]);

        $this->breadcrumbs = [
            [
                'label'=>Yii::t("app", "Cron Jobs"),
                'url'=>Url::to(["index"]),
            ],
            [
                "label"=>$this->getView()->title
            ]
        ];

        return $this->render("log", [
            "log"=>$log,
        ]);
    }

    public function actionExamples() {
        $this->getView()->title = Yii::t("app", "What cron expression does {appName} support?", [
            "appName"=>Yii::$app->params['longAppName'],
        ]);

        $this->breadcrumbs = [
            [
                'label'=>Yii::t("app", "Cron Jobs"),
                'url'=>Url::to(["index"]),
            ],
            [
                "label"=>$this->getView()->title
            ]
        ];
        return $this->render("example");
    }

    protected function bulkStatus($value, array $ids) {
        if(!in_array($value, array_keys(Schedule::getStatusArray()))) {
            return false;
        }

        $schedules = Schedule::find()->where([
            "user_id"=>Yii::$app->user->id,
            "id"=>$ids,
        ])->all();

        $html = '';

        foreach($schedules as $schedule) {
            $schedule->status = $value;
            if($schedule->save()) {
                if($value == Schedule::STATUS_ENABLED) {
                    $msg = Yii::t("app", "Cron job (ID: {id}) has been enabled.", ["id"=>$schedule->id]);
                } else {
                    $msg = Yii::t("app", "Cron job (ID: {id}) has been disabled.", ["id"=>$schedule->id]);
                }
                $html .= '<div class="alert alert-success margin-r-20">'.$msg.'</div>';
            } else {
                $html .= '<div class="alert alert-danger margin-r-20">'.Html::errorSummary($schedule, [
                    "header"=>Yii::t("app", "Error") . " | " . Html::encode($schedule->title)
                ]).'</div>';
            }
        }

        Yii::$app->getSession()->setFlash("neutral", $html);
        return $this->refresh();
    }

    protected function bulkNotify($value, array $ids) {
        if(!in_array($value, array_keys(Schedule::getNotifyArray()))) {
            return false;
        }

        Schedule::updateAll([
            "notify"=>$value,
        ], [
            "user_id"=>Yii::$app->user->id,
            "id"=>$ids,
        ]);

        $msg = Yii::t("app", "Notification status of the selected cron jobs has been changed");

        Yii::$app->getSession()->setFlash("success", $msg);
        return $this->refresh();
    }

    protected function bulkGeneral($value, array $ids) {
        switch ($value) {
            case "reset":
                Yii::$app->db->transaction(function($db) use($ids) {
                    /**
                     * @var $db Connection
                     */
                    Log::deleteAll(['and', 'user_id=:user_id', ['in', 'schedule_id', $ids]], [
                        "user_id"=>Yii::$app->getUser()->getId(),
                    ]);
                    Stat::deleteAll(['and', 'user_id=:user_id', ['in', 'schedule_id', $ids]], [
                        "user_id"=>Yii::$app->getUser()->getId(),
                    ]);
                });
                Yii::$app->getSession()->setFlash("success", Yii::t("app", "Successfully reset the logs and stat. of {ids} cron job", [
                    "ids"=>implode(",", $ids)
                ]));
            break;
            case "delete":
                Schedule::deleteAll(['and', 'user_id=:user_id', ['in', 'id', $ids]], [
                    "user_id"=>Yii::$app->getUser()->getId(),
                ]);
                Yii::$app->getSession()->setFlash("success", Yii::t("app", "Selected cron jobs have been successfully deleted: {ids}", [
                    "ids"=>implode(",", $ids)
                ]));
            break;
        }
        return $this->refresh();
    }


    private function redirectAfterSave(Schedule $schedule, $action = null) {
        $action = $action ?? strtolower(Yii::$app->request->post("and"));
        switch ($action) {
            case "exit":
                return $this->redirect(["cron-job/index"]);
            case "test":
                return $this->redirect(["cron-job/run", "id"=>$schedule->id]);
            default:
                return $this->redirect(["cron-job/update", "id"=>$schedule->id]);
        }
    }
}