<?php
/* @var $this \yii\web\View */
/* @var $content string */
?>
<?php $this->beginContent('@app/views/layouts/main.php'); ?>
<!-- Content Wrapper. Contains page content -->
<div class="content-wrapper" style="margin-left: 0;">
    <!-- Main content -->
    <section class="content">
        <?php echo $content; ?>
        <!-- Your Page Content Here -->
    </section><!-- /.content -->
</div><!-- /.content-wrapper -->
<?php $this->endContent(); ?>
