<?php
/**
 * @var $dataProvider \yii\data\ActiveDataProvider
 * @var $searchModel \app\models\search\CategorySearch
 */

use yii\grid\GridView;
use yii\helpers\Html;
use yii\widgets\LinkPager;
use yii\helpers\Url;
use app\widgets\PerPage;
use app\components\Helper;
?>

<div class="row">
    <div class="col-md-12">
        <div class="box">
            <div class="box-header">
                <a href="<?php echo Yii::$app->getUrlManager()->createUrl("category/create") ?>" class="btn btn-large btn-primary pull-right"><?php echo Yii::t("app", "Create Category") ?></a>
                <a href="<?php echo Url::to(["index"]) ?>" class="btn btn-large btn-default pull-right margin-r-20"><?php echo Yii::t("app", "Clear filter") ?></a>
            </div>
            <div class="box-body">

                <div class="pull-right">
                    <?php echo PerPage::widget([
                        "pagination"=>$dataProvider->getPagination(),
                        "layout"=>"horizontal",
                    ]); ?>
                </div>
                <div class="clear-fix"></div><br><br>

                <?php echo GridView::widget([
                    "dataProvider"=>$dataProvider,
                    'filterModel' => $searchModel,
                    "layout" => "{summary}\n{items}",
                    'rowOptions'=>[
                        'class'=>'table-row'
                    ],
                    'columns' => [
                        [
                            'class' => 'yii\grid\SerialColumn',
                        ],
                        [
                            'attribute'=>'title',
                            'enableSorting'=>false,
                        ],
                        [
                            'attribute'=>'created_at',
                            'value'=>function($model) { return Helper::covertToUserDate($model->created_at); },
                            'enableSorting'=>false,
                        ],
                        [
                            'class' => 'yii\grid\ActionColumn',
                            'template'=>'
                                <div class="text-center">
                                <div class="btn-group align-center">
                                    {view} {update} {delete}
                                </div>
                                </div>
                            ',
                            'buttons'=>[
                                'update'=>function($url, $model, $key) {
                                    return Html::a(Yii::t('yii', "Update"), $url, [
                                        "class"=>"btn btn-info",
                                    ]);
                                },
                                'delete'=>function($url, $model, $key) {
                                    return Html::a(Yii::t('yii', "Delete"), $url, [
                                        "class"=>"btn btn-danger",
                                        'data-confirm' => Yii::t('yii', 'Are you sure you want to delete this item?'),
                                        'data-method' => 'post',
                                    ]);
                                },
                                'view'=>function($url, $model, $key) {
                                    return Html::a(Yii::t('yii', "View"), $url, [
                                        "class"=>"btn bg-olive",
                                    ]);
                                }
                            ],
                            'options'=>[
                                'style'=>'width: 300px',
                            ]
                        ],
                    ],
                ]);
                ?>
            </div>
            <div class="box-footer clearfix">
                <?php echo LinkPager::widget([
                    "pagination"=>$dataProvider->getPagination(),
                    "options"=>[
                        "class"=>"pagination pagination-sm no-margin pull-right",
                    ]
                ]) ?>
            </div>
        </div>
    </div>
</div>


