<?php

namespace Mproyyan\Comet\Core\Bootstrap;

use Mproyyan\Comet\Config\Repository;
use Mproyyan\Comet\Core\Application;

class LoadConfiguration
{
   public function bootstrap(Application $app)
   {
      $app->instance('config', $config = new Repository());
      $this->loadConfigurationFiles($app, $config);
   }

   protected function loadConfigurationFiles(Application $app, Repository $repository)
   {
      $files = $this->getConfigurationFiles($app);

      foreach ($files as $key => $path) {
         $repository->set($key, require $path);
      }
   }

   protected function getConfigurationFiles(Application $app)
   {
      $files = [];

      $configPath = realpath($app->configPath());

      $scannedDirectory = scandir($configPath);

      foreach ($scannedDirectory as $configFile) {
         if ($configFile === '.' || $configFile === '..') {
            continue;
         }

         $files[$this->getFileName($configFile)] = $app->configPath() . DIRECTORY_SEPARATOR . $configFile;
      }

      return $files;
   }

   protected function getFileName($file)
   {
      return explode('.', $file)[0];
   }
}
