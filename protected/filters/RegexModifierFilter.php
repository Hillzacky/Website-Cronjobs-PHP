<?php
namespace app\filters;

class RegexModifierFilter
{
    public static function filter($value) {
        $modifiers = "imsxADSUXu";
        return count_chars(preg_replace("/[^{$modifiers}]/", "", (string) $value), 3);
    }
}