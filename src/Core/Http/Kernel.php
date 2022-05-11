<?php

namespace Mproyyan\Comet\Core\Http;

use Mproyyan\Comet\Core\Application;

class Kernel
{
   /**
    * @var \Mproyyan\Comet\Core\Application
    */
   protected $app;

   /**
    * @var \Mproyyan\Comet\Routing\Router
    */
   protected $router;

   protected $bootstrappers = [
      \Mproyyan\Comet\Core\Bootstrap\LoadConfiguration::class,
      \Mproyyan\Comet\Core\Bootstrap\RegisterFacades::class,
   ];

   public function __construct(Application $application)
   {
      $this->app = $application;
   }

   public function bootstrap()
   {
      $this->app->boostrapWith($this->bootstrappers());
   }

   protected function bootstrappers()
   {
      return $this->bootstrappers;
   }
}
