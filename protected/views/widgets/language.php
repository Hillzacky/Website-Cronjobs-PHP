<?php
/**
 * @var $languages array
 */

use yii\helpers\Url;
use yii\helpers\Html;
?>

<li class="dropdown">
    <a href="#" class="dropdown-toggle" data-toggle="dropdown"><?php echo Yii::t("app", "Language") ?> <b class="caret"></b></a>
    <ul class="dropdown-menu">
        <?php foreach($languages as $lang_id=>$language):?>
            <?php if($lang_id == Yii::$app->language) continue;
            $route = array_merge($_GET, ["language"=>$lang_id]);
            $route[0] = '';
            $url=Url::to($route);
            ?>
            <li>
                <?= Html::a($language, $url) ?>
            </li>
        <?php endforeach; ?>
        <li class="divider"></li>
        <li><a href="<?php echo Yii::$app->request->url ?>" class="disabled"><?php echo $languages[Yii::$app->language] ?></a></li>
    </ul>
</li>