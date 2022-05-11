<?php

namespace Mproyyan\Comet\Routing;

use Mproyyan\Comet\Core\Application;
use Mproyyan\Comet\Request\Request;

class RoutingServiceProvider
{
   /**
    * @var \Mproyyan\Comet\Core\Application
    */
   protected $app;

   public function __construct(Application $app)
   {
      $this->app = $app;
   }

   public function register()
   {
      $this->registerRouter();
   }

   public function registerRouter()
   {
      $this->app->singleton(Router::class, function ($app) {
         return new Router($app, $app->make(Request::class));
      });
   }
}
