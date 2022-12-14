<?php
/**
 * @var $placeholderKey string
 * @var $placeholderValue string
 * @var $modelAttr
 * @var $params array
 * @var $model \app\models\Schedule
 * @var
 */
//if(isset($params)) { var_dump($params); }
use yii\helpers\Html;
?>
<?php if(!empty($params) AND isset($params['key']) AND isset($params['value'])): ?>
    <?php foreach($params['key'] as $id=>$param): ?>
    <div class="form-group">
        <div class="col-md-4">
            <?php echo Html::activeTextInput($model, $modelAttr."[key]"."[".Html::encode($id)."]", [
                "class"=>"form-control",
                "placeholder"=>$placeholderKey,
                "value"=>$param,
            ]); ?>
        </div>
        <div class="col-md-6">
            <div class="input-group">
                <?php echo Html::activeTextInput($model, $modelAttr."[value]"."[".Html::encode($id)."]", [
                    "class"=>"form-control",
                    "placeholder"=>$placeholderValue,
                    "value"=>$params["value"][$id],
                ]); ?>
                <span class="input-group-btn">
                    <button class="btn btn-warning remove-param" type="button"><?php echo Yii::t("app", "Remove") ?></button>
                </span>
            </div>
        </div>
    </div>
    <?php endforeach; ?>
<?php elseif(!isset($params)): ?>
    <div class="form-group">
        <div class="col-md-4">
            <?php echo Html::activeTextInput($model, $modelAttr."[key][]", [
                "class"=>"form-control",
                "placeholder"=>$placeholderKey,
                "value"=>""
            ]); ?>
        </div>
        <div class="col-md-6">
            <div class="input-group">
                <?php echo Html::activeTextInput($model, $modelAttr."[value][]", [
                    "class"=>"form-control",
                    "placeholder"=>$placeholderValue,
                    "value"=>""
                ]); ?>
            <span class="input-group-btn">
                <button class="btn btn-warning remove-param" type="button"><?php echo Yii::t("app", "Remove") ?></button>
            </span>
            </div>
        </div>
    </div>
<?php endif; ?>