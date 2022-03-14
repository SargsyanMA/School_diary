<?php

namespace App\Providers;

use App\Custom\Year;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\View;
use Illuminate\Support\Facades\Schema;

class AppServiceProvider extends ServiceProvider
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
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        $currentYear = Year::getInstance()->getYear();
        $yearsList = range(2018, $currentYear+1);

        View::share('year_list', $yearsList);
        View::share('current_year', $currentYear);

        $loader = new \Twig_Loader_Filesystem(resource_path('/twig'));

        $twig = new \Twig_Environment($loader, array(
            //'cache' => '/path/to/compilation_cache',
        ));

        $filter = new \Twig_Filter('safe', function ($string) {return $string;} , array('is_safe' => array('html')));
        $twig->addFilter($filter);

        View::share('twig', $twig);

        Schema::defaultStringLength(191);

    }
}
