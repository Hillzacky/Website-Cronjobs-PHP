<?php
namespace app\events;

use yii\base\Event;

class ChangedAttributesEvent extends Event
{
    public $changedAttributes;
}