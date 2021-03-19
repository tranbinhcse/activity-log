<?php

namespace Tino\UserActivity\Http\Controllers\Api;

use Spatie\QueryBuilder\AllowedFilter;
use Spatie\QueryBuilder\QueryBuilder;
use Tino\UserActivity\Http\Resources\ActivityResource;
use Tino\UserActivity\Activity;
use Tino\UserActivity\Http\Requests\GetActivitiesRequest;
use Tino\Http\Controllers\Api\ApiController;

/**
 * Class ActivityController
 * @package Tino\Http\Controllers\Api
 */
class ActivityController extends ApiController
{
    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware('permission:users.activity');
    }

    /**
     * Paginate user activities.
     * @param GetActivitiesRequest $request
     * @return \Illuminate\Http\Resources\Json\AnonymousResourceCollection
     */
    public function index(GetActivitiesRequest $request)
    {
        $activities = QueryBuilder::for(Activity::class)
            ->allowedIncludes('user')
            ->allowedFilters([
                AllowedFilter::partial('description'),
                AllowedFilter::exact('user', 'user_id')
            ])
            ->allowedSorts('created_at')
            ->defaultSort('-created_at')
            ->paginate($request->per_page ?: 20);

        return ActivityResource::collection($activities);
    }
}
