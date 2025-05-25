<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Support\Str;
use OwenIt\Auditing\Contracts\Auditable as AuditableContract;
use OwenIt\Auditing\Auditable;

class Project extends Model implements AuditableContract
{
    use HasFactory, SoftDeletes, Auditable;

    protected $primaryKey = 'id';
    public $incrementing = false;
    protected $keyType = 'string';

    protected $fillable = [
        'id', 'name', 'settings', 'start_at', 'is_active',
    ];

    protected $casts = [
        'settings'   => 'array',
        'start_at'   => 'datetime',
        'is_active'  => 'boolean',
        'deleted_at' => 'datetime',
    ];

    protected static function booted()
    {
        static::creating(function ($model) {
            if (empty($model->{$model->getKeyName()})) {
                $model->{$model->getKeyName()} = (string) Str::uuid();
            }
        });

        // ✂️ Hapus seluruh block deleting & restored di sini
    }

    public function tasks()
    {
        return $this->hasMany(Task::class, 'project_id');
    }

    // Note: documents() di Project sebenarnya tidak langsung punya foreign key ke Project,
    // jadi Anda bisa menghapus method ini jika tidak dipakai.
}
