<?php

namespace App\Providers;

use Inertia\Inertia;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\ServiceProvider;

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
    Inertia::share([
      'errors' => function () {
        return Session::get('errors')
          ? Session::get('errors')->getBag('default')->getMessages()
          : (object) [];
      },
    ]);

    Inertia::share('flash', function () {
      return [
        'message' => Session::get('message'),
      ];
    });

    Inertia::share('appSettings', config('settings.appSettings'));

    Inertia::share('isAdmin', function () {
      return (Auth::user()->role->name == 'Admin' || Auth::user()->role->name == 'SAdmin') ? true : false;
    });

    Inertia::share([
      'auth' => function () {
        return [
          'user' => Auth::user() ? [
            'name' => Auth::user()->name,
            'email' => Auth::user()->email,
            'role' => Auth::user()->role->name,
            'menu_access' => \config('settings.menu_access')[Auth::user()->role->name],
          ] : null,
        ];
      },
    ]);
  }
}
