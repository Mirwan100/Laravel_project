<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;
use OwenIt\Auditing\Contracts\Auditable as AuditableContract;
use OwenIt\Auditing\Auditable;

class Document extends Model implements AuditableContract
{
    use HasFactory, SoftDeletes, Auditable;

    protected $primaryKey = 'id';
    public $incrementing = false;
    protected $keyType   = 'string';

    protected $fillable = [
        'id',
        'task_id',
        'filename',
        'path',
        'uploaded_at',
        'is_verified', // typo diperbaiki
    ];

    protected $casts = [
        'uploaded_at' => 'datetime',
        'is_verified' => 'boolean',
    ];

    protected static function booted()
    {
        static::creating(function ($model) {
            if (! $model->{$model->getKeyName()}) {
                $model->{$model->getKeyName()} = (string) Str::uuid();
            }
        });
    }

    public function task()
    {
        // Dengan withTrashed() meski task terhapus lembut, Anda tetap bisa akses:
        return $this->belongsTo(Task::class, 'task_id')
                    ->withTrashed();
    }
}
