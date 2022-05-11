<?php

namespace Mproyyan\Comet\Core\Bootstrap;

use Mproyyan\Comet\Core\Application;
use Mproyyan\Comet\Suport\Facades\Facade;

class RegisterFacades
{
   public function bootstrap(Application $app)
   {
      Facade::clearResolvedInstances();

      Facade::setFacadeApplication($app);
   }
}
