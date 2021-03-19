<?php

namespace Tino\UserActivity\Http\Controllers\Api;

use Auth;
use Carbon\Carbon;
use Tino\Http\Controllers\Api\ApiController;
use Tino\UserActivity\Repositories\Activity\ActivityRepository;

/**
 * Class ActivityController
 * @package Tino\Http\Controllers\Api\Users
 */
class StatsController extends ApiController
{
    /**
     * @var ActivityRepository
     */
    private $activities;

    public function __construct(ActivityRepository $activities)
    {
        $this->middleware('auth');

        $this->activities = $activities;
    }

    /**
     * Get activities for specified user.
     * @return \Illuminate\Http\JsonResponse
     */
    public function show()
    {
        return $this->activities->userActivityForPeriod(
            Auth::user()->id,
            Carbon::now()->subWeeks(2),
            Carbon::now()
        );
    }
}
