<?php


namespace app\components;

use yii\web\View as YiiView;

class View extends YiiView
{
    public function registerJs($js, $position = YiiView::POS_READY, $key = null)
    {
        $js = '(function(){ "use strict"; '.$js.' })();';
        YiiView::registerJs($js, $position, $key);
    }
}