<?php
namespace app\models\query;

use yii\db\ActiveQuery;

class CategoryQuery extends ActiveQuery
{
    use OwnerTrait;
}