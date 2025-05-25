<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;
use OwenIt\Auditing\Contracts\Auditable as AuditableContract;
use OwenIt\Auditing\Auditable;

class Task extends Model implements AuditableContract
{
    use HasFactory, SoftDeletes, Auditable;

    protected $primaryKey = 'id';
    public $incrementing = false;
    protected $keyType     = 'string';

    protected $fillable = [
        'id',
        'project_id',
        'title',
        'description',
        'metadata',
        'due_at',
        'done',
    ];

    protected $casts = [
        'metadata'   => 'array',
        'due_at'     => 'datetime',
        'done'       => 'boolean',
        'deleted_at' => 'datetime',
    ];

    protected static function booted()
    {
        static::creating(function ($model) {
            if (empty($model->{$model->getKeyName()})) {
                $model->{$model->getKeyName()} = (string) Str::uuid();
            }
        });

        // ❌ Tidak perlu cascade delete di sini
    }

    public function project()
    {
        // Dengan withTrashed() meski project terhapus lembut, Anda tetap bisa akses:
        return $this->belongsTo(Project::class, 'project_id')
                    ->withTrashed();
    }

    public function documents()
    {
        return $this->hasMany(Document::class, 'task_id');
    }
}
