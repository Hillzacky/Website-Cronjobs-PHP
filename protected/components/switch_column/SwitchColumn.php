<?php
namespace app\components\switch_column;

use yii\grid\DataColumn;
use yii\helpers\Html;
use yii\helpers\Json;
use yii\helpers\Url;
use yii\web\JsExpression;

class SwitchColumn extends DataColumn
{
    public $enabledValue = 1;
    public $disabledValue = 0;
    public $route = 'switch';
    public $switcherOptions = [];
    public $show;
    public $emptyText = 'Disabled';

    public $switcherEvents = [
        'beforeSend'=>null,
        'afterSend'=>null,
        'error'=>null,
    ];

    public $switcherName = 'switcher-checkbox';
    public $attribute = 'switchTo';

    public function init() {
        parent::init();
        SwitchAsset::register($this->grid->view);
        foreach($this->switcherEvents as & $event) {
            $event = new JsExpression($event);
        }
    }

    public function renderDataCellContent($model, $key, $index) {
        if($this->show instanceof \Closure) {
            $show = call_user_func($this->show, $model);
        } else {
            $show = $this->show;
        }
        if(!$show) {
            return $this->emptyText;
        }

        if(!is_string($this->route)) {
            $route = call_user_func($this->route, $model);
        } else {
            $route = Url::to($this->route);
        }
        $this->switcherOptions['data-url'] = $route;

        $view = $this->grid->view;
        $clientOpts = [];
        $clientOpts['switchSelector'] = $this->switcherName;
        $clientOpts = array_merge($clientOpts, $this->switcherEvents);
        $clientOpts['enabledValue'] = $this->enabledValue;
        $clientOpts['disabledValue'] = $this->disabledValue;
        $clientOpts['attribute'] = $this->attribute;

        $view->registerJs('Switcher.init('.Json::encode($clientOpts).');');

        return Html::checkbox($this->switcherName, $model->isEnabled(), array_merge($this->switcherOptions, [
            "value"=>$this->enabledValue,
        ]));
    }
}