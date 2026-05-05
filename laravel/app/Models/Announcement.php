<?php

namespace App\Models;

use App\Observers\AnnouncementObserver;
use App\Traits\HasAuditColumns;
use Illuminate\Database\Eloquent\Attributes\ObservedBy;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

#[ObservedBy(AnnouncementObserver::class)]

class Announcement extends Model
{
    use HasAuditColumns;

    protected $fillable = [
        'title',
        'slug',
        'description',
        'content',
        'type',
        'priority',
        'status',
        'target_audience',
        'department_id',
        'published_at',
        'expires_at',
        'is_pinned',
        'created_by',
        'updated_by',
    ];

    protected $casts = [
        'published_at' => 'datetime',
        'expires_at'   => 'datetime',
        'is_pinned'    => 'boolean',
        'priority'     => 'integer',
    ];

    protected static function booted(): void
    {
        static::creating(function ($model) {
            if (empty($model->slug)) {
                $model->slug = Str::slug($model->title);
            }
        });
    }

    public function department()
    {
        return $this->belongsTo(Department::class);
    }
}
