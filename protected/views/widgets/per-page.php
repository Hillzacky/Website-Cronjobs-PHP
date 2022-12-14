<?php
/**
 * @var $this \yii\web\View
 * @var $pagination \yii\data\Pagination;
 * @var $perPage array
 * @var $onPage int
 */
use yii\helpers\Url;
?>

<div class="per-page <?php echo $this->context->layout ?>">
    <label for="per-page-dropdown"><?php echo Yii::t("app", "Records per page") ?></label>
    &nbsp;&nbsp;&nbsp;
    <select id="per-page-dropdown" class="form-control" onchange="window.location.href = this.options[this.selectedIndex].value;">
        <?php foreach($perPage as $pp): ?>
            <option value="<?php echo Url::current([$pagination->pageSizeParam=>$pp]) ?>"<?php echo $onPage == $pp ? " selected" : null ?>><?php echo $pp ?></option>
        <?php endforeach; ?>
    </select>
</div>
<br>
