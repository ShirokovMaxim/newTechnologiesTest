<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class BaseModel extends Model
{
    public static function isModelsExists(array $modelIds): bool
    {
        $count = self::whereIn('id', $modelIds)->count();

        return $count === count($modelIds);
    }
}
