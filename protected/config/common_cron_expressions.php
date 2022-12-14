<?php
return [
    "0"=>Yii::t("app", "Choose an interval"),
    "* * * * *"=>Yii::t("app", "{n} minutes", ['n'=>1]),
    "*/2 * * * *"=>Yii::t("app", "{n} minutes", ['n'=>2]),
    "*/3 * * * *"=>Yii::t("app", "{n} minutes", ['n'=>3]),
    "*/4 * * * *"=>Yii::t("app", "{n} minutes", ['n'=>4]),
    "*/5 * * * *"=>Yii::t("app", "{n} minutes", ['n'=>5]),
    "*/6 * * * *"=>Yii::t("app", "{n} minutes", ['n'=>6]),
    "*/10 * * * *"=>Yii::t("app", "{n} minutes", ['n'=>10]),
    "*/12 * * * *"=>Yii::t("app", "{n} minutes", ['n'=>12]),
    "*/15 * * * *"=>Yii::t("app", "{n} minutes", ['n'=>15]),
    "*/20 * * * *"=>Yii::t("app", "{n} minutes", ['n'=>20]),
    "*/30 * * * *"=>Yii::t("app", "{n} minutes", ['n'=>30]),

    "0 * * * *"=>Yii::t("app", "{n} hours", ['n'=>1]),
    "0 */2 * * *"=>Yii::t("app", "{n} hours", ['n'=>2]),
    "0 */3 * * *"=>Yii::t("app", "{n} hours", ['n'=>3]),
    "0 */4 * * *"=>Yii::t("app", "{n} hours", ['n'=>4]),
    "0 */6 * * *"=>Yii::t("app", "{n} hours", ['n'=>6]),
    "0 */8 * * *"=>Yii::t("app", "{n} hours", ['n'=>8]),
    "0 */12 * * *"=>Yii::t("app", "{n} hours", ['n'=>12]),

    "0 0 * * *"=>Yii::t("app", "{n} days", ['n'=>1]),
    "0 0 */2 * *"=>Yii::t("app", "{n} days", ['n'=>2]),
    "0 0 */3 * *"=>Yii::t("app", "{n} days", ['n'=>3]),
    "0 0 */5 * *"=>Yii::t("app", "{n} days", ['n'=>5]),
    "0 0 */10 * *"=>Yii::t("app", "{n} days", ['n'=>10]),
    "0 0 */15 * *"=>Yii::t("app", "{n} days", ['n'=>15]),

    "0 0 * * 0"=>Yii::t("app", "weekly"),
    "0 0 1 * *"=>Yii::t("app", "month"),
    "0 0 1 1 *"=>Yii::t("app", "annually/yearly"),
];