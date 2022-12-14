<?php
namespace app\components\switch_column;

use yii\web\AssetBundle;

class SwitchAsset extends AssetBundle
{
    public $sourcePath = '@app/components/switch_column/bundle';
    public $css = [
        'css/bootstrap-switch.css',
    ];
    public $js = [
        'js/bootstrap-switch.min.js',
        'js/switcher.js?v=0.1',
    ];
    public $depends = [
        'yii\web\YiiAsset',
        'yii\bootstrap\BootstrapAsset',
        'yii\bootstrap\BootstrapPluginAsset',
    ];
}