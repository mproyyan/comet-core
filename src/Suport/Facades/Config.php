<?php

namespace Mproyyan\Comet\Suport\Facades;

/**
 * @method static \Mproyyan\Comet\Config\Repository get($key, $default = null)
 */
class Config extends Facade
{
   protected static function getFacadeAccessor()
   {
      return 'config';
   }
}
