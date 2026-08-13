<?php

namespace App\Classes\Enum;

enum DiaryRecordType: int
{
    case MEAL = 3;
    case COMMENT = 1;
    case GLUCOSE = 2;

    public static function getAll():string
    {
        return implode(',', array_map(fn($v) => $v->value, self::cases()));
    }

    public static function getAllArray():array
    {
        return [
            self::MEAL,
            self::COMMENT,
            self::GLUCOSE,
        ];
    }
}
