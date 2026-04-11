<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Setting extends Model
{
    protected $fillable = ['key', 'value', 'type', 'group', 'description'];

    public static function get($key, $default = null)
    {
        $setting = self::where('key', $key)->first();
        return $setting ? self::castValue($setting->value, $setting->type) : $default;
    }

    public static function set($key, $value, $type = 'string', $group = 'general')
    {
        return self::updateOrCreate(
            ['key' => $key],
            ['value' => $value, 'type' => $type, 'group' => $group]
        );
    }

    protected static function castValue($value, $type)
    {
        return match ($type) {
            'boolean' => $value == '1' || $value === true,
            'integer' => (int) $value,
            'json' => json_decode($value, true),
            default => $value,
        };
    }
}
