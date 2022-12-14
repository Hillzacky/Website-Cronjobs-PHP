<?php
/**
 * @var $model \app\models\Category
 * @var $this \yii\web\View
 */

use yii\helpers\Html;
?>

<?php echo Html::beginForm("", "post", [
    "class"=>"form-horizontal",
]); ?>

<div class="row">
    <div class="col-md-12">
        <div class="box">
            <div class="box-header with-border">
                <h3 class="box-title">
                    <?php echo Html::encode($this->title); ?>
                </h3>
            </div>
            <div class="box-body">
                <div class="row">
                    <div class="col-md-6">
                        <?php echo Html::errorSummary($model, array(
                            'class' => 'alert alert-danger',
                        )); ?>
                        <div class="form-group<?php echo $model->hasErrors("title") ? ' has-error' : null ?>">
                            <label for="category-title" class="col-sm-3 control-label"><?php echo Yii::t("app", "Title") ?></label>
                            <div class="col-sm-9">
                                <?php echo Html::activeTextInput($model, "title", [
                                    "class"=>"form-control",
                                ]); ?>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="box-footer">
                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                            <div class="col-sm-9 col-sm-offset-3">
                                <button type="submit" class="btn btn-info"><?php echo $model->isNewRecord ? Yii::t("app", "Create") : Yii::t("app", "Update"); ?></button>
                            </div>
                        </div>
                    </div>
                </div>
            </div><!-- /.box-footer -->
        </div>
    </div>
</div>
<?php echo Html::endForm(); ?>