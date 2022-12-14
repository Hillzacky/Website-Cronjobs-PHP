<?php
namespace app\assets;


use yii\web\AssetBundle;

class FontAwesomeAsset extends AssetBundle
{
    public $sourcePath = '@bower/components-font-awesome';
    public $css = [
        'css/all.min.css'
    ];
    public function init() {
        $this->publishOptions['beforeCopy'] = function($from, $to) {
            if(!preg_match("#\.[\w\d]+$#iu", $from)) {
                $dirname = pathinfo($from, PATHINFO_BASENAME);
            } else {
                $dirname = basename(dirname($from));
            }
            return $dirname === 'webfonts' || $dirname === 'css';
        };
    }
}