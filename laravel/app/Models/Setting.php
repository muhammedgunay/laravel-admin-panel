<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Setting extends Model
{
    protected $fillable = [
        'group',
        'key',
        'value',
        'type',
        'label',
        'description',
        'is_public',
        'is_locked',
        'updated_by',
    ];

    protected $casts = [
        'is_public' => 'boolean',
        'is_locked' => 'boolean',
    ];

    // ── Relations ─────────────────────────────────────────────────

    public function updater()
    {
        return $this->belongsTo(User::class, 'updated_by');
    }

    // ── Helpers ───────────────────────────────────────────────────

    /**
     * Get a setting value by key with optional default.
     */
    public static function get(string $key, mixed $default = null): mixed
    {
        $setting = static::where('key', $key)->first();

        if (! $setting) {
            return $default;
        }

        return static::castValue($setting->value, $setting->type);
    }

    /**
     * Set a setting value by key.
     */
    public static function set(string $key, mixed $value): void
    {
        static::where('key', $key)->update([
            'value'      => is_array($value) ? json_encode($value) : $value,
            'updated_by' => auth()->id(),
        ]);
    }

    /**
     * Cast raw value to the appropriate PHP type.
     */
    protected static function castValue(mixed $value, string $type): mixed
    {
        return match ($type) {
            'boolean' => filter_var($value, FILTER_VALIDATE_BOOLEAN),
            'integer' => (int) $value,
            'float'   => (float) $value,
            'json'    => json_decode($value, true),
            default   => $value,
        };
    }

    /**
     * Get cast value for this instance.
     */
    public function getCastedValueAttribute(): mixed
    {
        return static::castValue($this->value, $this->type);
    }
}
