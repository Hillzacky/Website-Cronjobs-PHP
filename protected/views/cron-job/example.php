<div class="row">
    <div class="col-md-12">
        <div class="box">
            <div class="box-body">
                <p>
                    <?= Yii::t("app", "{appName} supports all cron expressions that you can find in general Linux Cron implementations.", [
                        "appName"=>Yii::$app->params['longAppName']
                    ]) ?>
                </p>
                <h3><?= Yii::t("app", "Examples") ?></h3>
                <table class="table table-bordered table-striped">
                    <thead>
                    <tr>
                        <th><?= Yii::t("app", "Expression") ?></th>
                        <th><?= Yii::t("app", "Meaning") ?></th>
                    </tr>
                    </thead>

                    <tbody>
                    <tr>
                        <td>
                            0 12 * * *
                        </td>
                        <td>
                            <?= Yii::t("app", "Fire at 12pm (noon) every day") ?>
                        </td>
                    </tr>
                    <tr>
                        <td>
                            15 10 * * *
                        </td>
                        <td>
                            <?= Yii::t("app", "Fire at 10:15am every day") ?>
                        </td>
                    </tr>
                    <tr>
                        <td>
                            * 14 * * *
                        </td>
                        <td>
                            <?= Yii::t("app", "Fire every minute starting at 2pm and ending at 2:59pm, every day") ?>
                        </td>
                    </tr>
                    <tr>
                        <td>
                            0/5 14,18 * * *
                        </td>
                        <td>
                            <?= Yii::t("app", "Fire every 5 minutes starting at 2pm and ending at 2:55pm, AND fire every 5 minutes starting at 6pm and ending at 6:55pm, every day") ?>
                        </td>
                    </tr>
                    <tr>
                        <td>
                            0-5 14 * * *
                        </td>
                        <td>
                            <?= Yii::t("app", "Fire every minute starting at 2pm and ending at 2:05pm, every day") ?>
                        </td>
                    </tr>
                    <tr>
                        <td>
                            10,44 14 * 3 3
                        </td>
                        <td>
                            <?= Yii::t("app", "Fire at 2:10pm and at 2:44pm every Wednesday in the month of March.") ?>
                        </td>
                    </tr>
                    <tr>
                        <td>
                            15 10 * * 1-5
                        </td>
                        <td>
                            <?= Yii::t("app", "Fire at 10:15am every Monday, Tuesday, Wednesday, Thursday and Friday") ?>
                        </td>
                    </tr>
                    <tr>
                        <td>
                            15 10 15 * *
                        </td>
                        <td>
                            <?= Yii::t("app", "Fire at 10:15am on the 15th day of every month") ?>
                        </td>
                    </tr>
                    <tr>
                        <td>
                            15 10 L * *
                        </td>
                        <td>
                            <?= Yii::t("app", "Fire at 10:15am on the last day of every month") ?>
                        </td>
                    </tr>
                    <tr>
                        <td>
                            15 10 * * 5L
                        </td>
                        <td>
                            <?= Yii::t("app", "Fire at 10:15am on the last Friday of every month") ?>
                        </td>
                    </tr>
                    <tr>
                        <td>
                            15 10 * * 5#3
                        </td>
                        <td>
                            <?= Yii::t("app", "Fire at 10:15am on the third Friday of every month") ?>
                        </td>
                    </tr>
                    <tr>
                        <td>
                            0 12 1/5 * *
                        </td>
                        <td>
                            <?= Yii::t("app", "Fire at 12pm (noon) every 5 days every month, starting on the first day of the month.") ?>
                        </td>
                    </tr>
                    <tr>
                        <td>
                            11 11 11 11 *
                        </td>
                        <td>
                            <?= Yii::t("app", "Fire every November 11th at 11:11am.") ?>
                        </td>
                    </tr>
                    <tr>
                        <td>
                            0 0 * * 3
                        </td>
                        <td>
                            <?= Yii::t("app", "Fire at midnight of each Wednesday.") ?>
                        </td>
                    </tr>
                    <tr>
                        <td>
                            0 0 1,2 * *
                        </td>
                        <td>
                            <?= Yii::t("app", "Fire at midnight of 1st, 2nd day of each month") ?>
                        </td>
                    </tr>
                    <tr>
                        <td>
                            0 0 1,2 * 3
                        </td>
                        <td>
                            <?= Yii::t("app", "Fire at midnight of 1st, 2nd day of each month, and each Wednesday.") ?>
                        </td>
                    </tr>
                    <tr>
                        <td>
                            20 10 20 1 * 2025
                        </td>
                        <td>
                            <?= Yii::t("app", "Fire only once in 2025 on January 20th at 20:10am") ?>
                        </td>
                    </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>


