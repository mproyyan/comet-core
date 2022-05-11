<?php

namespace Mproyyan\Comet\Suport\Facades;

class Facade
{
   /**
    * @var \Mproyyan\Comet\Core\Application
    */
   protected static $app;

   protected static array $resolvedInstance = [];

   public static function getFacadeRoot()
   {
      return static::resolveFacadeInstance(static::getFacadeAccessor());
   }

   protected static function getFacadeAccessor()
   {
      throw new \RuntimeException('Facade does not implement getFacadeAccessor method.');
   }

   protected static function resolveFacadeInstance($name)
   {
      if (isset(static::$resolvedInstance[$name])) {
         return static::$resolvedInstance[$name];
      }

      if (static::$app) {
         return static::$resolvedInstance[$name] = static::$app->make($name);
      }
   }

   public static function setFacadeApplication($app)
   {
      static::$app = $app;
   }

   public static function getFacadeApplication()
   {
      return static::$app;
   }

   public static function clearResolvedInstances()
   {
      static::$resolvedInstance = [];
   }

   public static function __callStatic($method, $args)
   {
      $instance = static::getFacadeRoot();

      if (!$instance) {
         throw new \RuntimeException('A facade root has not been set.');
      }

      return $instance->$method(...$args);
   }
}
