<?php
namespace app\commands;

use app\components\Helper;
use app\models\Log;
use app\models\User;
use yii\console\Controller;
use DateTime;
use yii\helpers\ArrayHelper;

class LogController extends Controller
{
    public $condition;

    public array $user = [];

    public function options($actionID)
    {
        return ['condition', 'user'];
    }

    public function actionClear(string $period): int
    {
        $now = new DateTime();
        $now->modify($period);

        $condition = [
            'and', ["<", "added_at", $now->format("Y-m-d H:i:s")]
        ];

        if(!empty($this->condition) AND !empty($this->user)) {
            $ids = ArrayHelper::getColumn(User::find()->andWhere(["login"=>$this->user])->asArray()->all(), "id");
            $condition[] = [$this->condition, "user_id", $ids];
        }

        Log::deleteAll($condition);

        return 0;
    }
}