<?php

namespace App\Providers;

use App\Models\RegionsModel;
use App\PollResult;
use App\User;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Cookie;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;
use App\Tools\Geo;
use App\Phone;
use App\Models\VendorsModel;
use App\Models\CategoryModel;
use App\Http\Controllers\Common\HeaderController;
use App\Models\GeoModel;
use App\Models\DeliveryModel;
use App\Models\FavouritsTargetModel;
use Illuminate\Support\Facades\Auth;
use App\Models\OrderModel;

class ViewServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        //
    }

    /**
     * определение переменных для view
     *
     * @return void
     */
    public function boot()
    {

        setlocale(LC_TIME, 'ru_RU.UTF-8');

        View::composer(
            [
                '*'
            ], function ($view)
            {
                $user = Auth::user();
                $showPoll = false;

//                if ($user && $user->role_id == 4) {
//
//                    $childrenIds = $user->children->pluck('student_id');
//                    $children = User::query()->whereIn('id', $childrenIds)->get();
//
//                    foreach ($children as $child) {
//                        if ($child->grade->number <= 10) {
//                            $showPoll = true;
//                            break;
//                        }
//                    }
//
//                    if($user->id == 84) {
//                        $showPoll = true;
//                    }
//
//                    if (
//                        PollResult::query()
//                        ->where('user_id', $user->id)
//                        ->exists()
//                    ) {
//                        $showPoll = false;
//                    }
//                }



                $view->with('show_poll', $showPoll);
            }
        );

    }
}
