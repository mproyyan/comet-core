<?php

namespace Tests\Config;

use Mproyyan\Comet\Config\Repository;
use Mproyyan\Comet\Core\Application;
use Mproyyan\Comet\Core\Http\Kernel;
use PHPUnit\Framework\TestCase;

class RepositoryTest extends TestCase
{
   protected Application $app;
   protected Kernel $kernel;
   protected Repository $repo;

   protected function setUp(): void
   {
      $this->app = new Application(dirname(dirname(__DIR__)));
      $this->kernel = new Kernel($this->app);
      $this->kernel->bootstrap();

      $this->repo = $this->app->make('config');
   }

   public function test_configuration_get_loaded()
   {
      $appConfig = $this->repo->get('app');

      // echo '<pre>';
      // print_r($appConfig);
      // echo '</pre>';

      $this->assertIsArray($appConfig);
      $this->assertArrayHasKey('author', $appConfig);
      $this->assertArrayHasKey('url', $appConfig);
      $this->assertArrayHasKey('providers', $appConfig);
   }

   public function test_get_nested_config()
   {
      $providers = $this->repo->get('app.providers');

      $this->assertIsArray($providers);
      $this->assertContains('service 1', $providers);
   }

   public function test_return_default_value_when_config_not_found()
   {
      $providers = $this->repo->get('app.providers.nothing', 'nilai default');

      $this->assertSame('nilai default', $providers);
   }

   public function test_make_sure_copy_array_items_to_new_variable_when_call_get_method()
   {
      $providers = $this->repo->get('app.providers');

      $this->assertIsArray($providers);
      $this->assertContains('service 1', $providers);

      $author = $this->repo->get('app.author');

      $this->assertSame('Muhammad Pandu Royyan', $author);
   }

   public function test_make_sure_another_config_file_get_loaded_too()
   {
      $anotherConfig = $this->repo->get('another');
      $alert = $this->repo->get('another.alert');
      $author = $this->repo->get('app.author');

      $this->assertIsArray($anotherConfig);
      $this->assertArrayHasKey('alert', $anotherConfig);
      $this->assertSame('this is alert from another config', $alert);
      $this->assertSame('Muhammad Pandu Royyan', $author);
   }

   public function test_make_sure_repository_is_singleton()
   {
      $newRepo = new Repository();
      $singletonRepo = $this->app->make('config');

      $this->assertNotSame($newRepo, $this->repo);
      $this->assertSame($singletonRepo, $this->repo);
   }
}
