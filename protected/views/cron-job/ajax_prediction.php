<?php
/**
 * @var $predictions array
 * @var $expression string
 * @var $start \DateTime
 */
use yii\helpers\Html;
?>
<div class="panel panel-default">
    <div class="panel-heading">
        <?php echo Yii::t("app", "Expression") ?>: <strong><?php echo Html::encode($expression); ?></strong>
        <br/>
        <?php echo Yii::t("app", "Start run at") ?>: <strong><?php echo $start->format("Y-m-d H:i") ?></strong>
    </div>
    <table class="table table-striped table-bordered">
        <thead>
            <tr>
                <th>
                    #
                </th>
                <th>
                    <?php echo Yii::t("app", "Next Run Date") ?>
                </th>
            </tr>
        </thead>
        <tbody>
            <?php foreach($predictions as $i=>$prediction): ?>
            <tr>
                <td>
                    <?php echo $i+1; ?>
                </td>
                <td>
                    <?php echo $prediction->format("Y-m-d H:i:s"); ?>
                </td>
            </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</div>
