<?php

namespace Tests\Core;

use Mproyyan\Comet\Core\Application;
use PHPUnit\Framework\TestCase;

class ApplicationTest extends TestCase
{
   protected Application $app;

   protected function setUp(): void
   {
      $this->app = new Application(
         dirname(dirname(__DIR__))
      );
   }

   public function test_base_path_successfully_set()
   {
      $basePath = dirname(dirname(__DIR__));
      $this->assertSame($basePath, $this->app->basePath());
   }

   public function test_config_path_successfully_set()
   {
      print_r($this->app->configPath() . DIRECTORY_SEPARATOR . 'app.php');
      $configPath = $this->app->basePath() . DIRECTORY_SEPARATOR . 'config';
      $this->assertSame($configPath, $this->app->configPath());
   }
}
