<?php

namespace Mproyyan\Comet\Core;

use Mproyyan\Comet\Container\Container;
use Mproyyan\Comet\Routing\Router;
use Mproyyan\Comet\Routing\RoutingServiceProvider;
use Mproyyan\Comet\Suport\Facades\Facade;

class Application extends Container
{
   protected $basePath;

   public function __construct($basePath = null)
   {
      if ($basePath) {
         $this->setBasePath($basePath);
      }

      $this->registerBaseBindings();
      $this->registerBaseServiceProvider();
   }

   public function run()
   {
      return $this->get(Router::class)->resolve();
   }

   protected function registerBaseBindings()
   {
      static::setInstance($this);

      $this->instance('app', $this);
      $this->instance(Container::class, $this);
   }

   protected function registerBaseServiceProvider()
   {
      $this->register(new RoutingServiceProvider($this));
   }

   public function register($provider)
   {
      $provider->register();

      return $provider;
   }

   protected function setBasePath($basePath)
   {
      $this->basePath = rtrim($basePath, '\/');

      return $this;
   }

   public function configPath($path = '')
   {
      return $this->basePath . DIRECTORY_SEPARATOR . 'config' . ($path != '' ? DIRECTORY_SEPARATOR . $path : '');
   }

   public function basePath($path = '')
   {
      return $this->basePath . ($path != '' ? DIRECTORY_SEPARATOR . $path : '');
   }

   public function boostrapWith(array $bootstrappers)
   {
      foreach ($bootstrappers as $bootstrapper) {
         $this->make($bootstrapper)->bootstrap($this);
      }
   }
}
