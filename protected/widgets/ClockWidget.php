<?php
namespace app\widgets;

use app\models\User;
use yii\base\Widget;
use yii\web\View;
use Yii;

class ClockWidget extends Widget
{
    public $user;

    public function init() {
        if(!($this->user instanceof User)) {
            throw new \Exception ("Property \$user must be an instance of app\\models\\User");
        }
    }
    public function run() {
        /**
         * @var $date \DateTime;
         */
        $date = $this->user->getDateObject();
        $this->view->registerJs('
            (function() {
                var d = new Date('.(int) $date->format("Y").', '. (int) ($date->format("n")-1).', '. (int) $date->format("d").', '. (int) $date->format("G").', '. (int) $date->format("i").', '. (int) $date->format("s").');

                function checkTime(i) {
                    if (i < 10) {i = "0" + i};  // add zero in front of numbers < 10
                    return i;
                }

                function tick() {
                    var h = d.getHours();
                    var m = d.getMinutes();
                    var s = d.getSeconds();
                    var D = d.getDate();
                    var M = d.getMonth() + 1;
                    var Y = d.getFullYear();

                    D = checkTime(D);
                    M = checkTime(M);
                    m = checkTime(m);
                    s = checkTime(s);

                    var time = Y +"-"+ M +"-"+ D +" "+ h+":"+m+":"+s;
                    $("#timer").text(time);
                    d.setSeconds(d.getSeconds() + 1);
                }

                tick();
                setInterval(tick, 1000);
            })();
        ', View::POS_END);

        return '<span class="hidden-xs">'.Yii::t("app", "Current time is:") .' </span><span id="timer">'.$date->format("Y-m-d H:i:s").'</span>';
    }
}