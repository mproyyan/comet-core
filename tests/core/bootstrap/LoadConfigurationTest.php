<?php

namespace Tests\Core\Bootstrap;

use Mproyyan\Comet\Core\Application;
use Mproyyan\Comet\Core\Bootstrap\LoadConfiguration;
use PHPUnit\Framework\TestCase;

class LoadConfigurationTest extends TestCase
{
   protected LoadConfiguration $config;
   protected LoadConfiguration $configMock;
   protected Application $app;
   protected Application $appMock;

   protected function setUp(): void
   {
      $this->app = new Application(dirname(dirname(dirname(__DIR__))));
      $this->appMock = $this->createMock(Application::class);
      $this->config = new LoadConfiguration;
      $this->configMock = $this->createMock(LoadConfiguration::class);
   }

   public function test_get_file_name_without_extension()
   {
   }
}
