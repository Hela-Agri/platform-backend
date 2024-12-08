<?php

namespace App\Repositories;

use App\Models\Center;
use App\Repositories\BaseRepository;
use Illuminate\Database\Eloquent\Builder;

class CenterRepository extends BaseRepository
{
    protected $fieldSearchable = [
        'name',
        'code',
        'description'
    ];

    public function getFieldsSearchable(): array
    {
        return $this->fieldSearchable;
    }

    public function model(): string
    {
        return Center::class;
    }
    public function scopeWithFarmerCount(): Builder
    {
        return Center::withCount(['farmActivities as farmers_count' => function ($query) {
            $query->select(\DB::raw('COUNT(DISTINCT users.id)'))
                ->join('users', 'farm_activities.user_id', '=', 'users.id')
                ->whereNull('users.deleted_at');
        }]);
    }

    public function paginate($limit = 200)
    {
        return $this->scopeWithFarmerCount()->paginate($limit);
    }
}
