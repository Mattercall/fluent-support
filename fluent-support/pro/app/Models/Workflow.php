<?php

namespace FluentSupportPro\App\Models;

use FluentSupport\App\Models\Model;
use FluentSupportPro\App\Services\ProHelper;

class Workflow extends Model
{
    protected $table = 'fs_workflows';

    protected $fillable = ['title', 'trigger_key', 'trigger_type', 'created_by', 'settings', 'status', 'last_ran_at'];

    /**
     * $searchable Columns in table to search
     * @var array
     */
    protected $searchable = [
        'title'
    ];

    public static function boot()
    {
        parent::boot();

        static::creating(function ($model) {
            $model->created_by = get_current_user_id();
            $model->status = 'draft';
        });
    }

    public function setSettingsAttribute($settings)
    {
        $this->attributes['settings'] = \maybe_serialize($settings);
    }

    public function getSettingsAttribute($value)
    {
        return ProHelper::safeUnserialize($this->attributes['settings']);
    }

    /**
     * Local scope to filter workflows by search/query string
     * @param \FluentSupport\Framework\Database\Orm\Builder $query
     * @param string $search
     * @return \FluentSupport\Framework\Database\Orm\Builder
     */
    public function scopeSearchBy($query, $search)
    {
        if ($search) {
            $fields = $this->searchable;
            $query->where(function ($query) use ($fields, $search) {
                $query->where(array_shift($fields), 'LIKE', "%$search%");

                foreach ($fields as $field) {
                    $query->orWhere($field, 'LIKE', "%$search%");
                }
            });
        }

        return $query;
    }

}