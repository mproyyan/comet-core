<?php

namespace Mproyyan\Comet\Suport\Facades;

use Mproyyan\Comet\Routing\Router;

/**
 * @method static \Mproyyan\Comet\Routing\Router get(string $url, array|callable $callback)
 */
class Route extends Facade
{
   protected static function getFacadeAccessor()
   {
      return Router::class;
   }
}
