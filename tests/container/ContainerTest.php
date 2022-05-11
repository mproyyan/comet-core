<?php

namespace Tests\Container;

use Mproyyan\Comet\Container\Container;
use Mproyyan\Comet\Container\Exceptions\BindingResolutionException;
use Mproyyan\Comet\Request\Request;
use Mproyyan\Comet\Routing\Router;
use PHPUnit\Framework\TestCase;
use Tests\Helper\Object\Bar;
use Tests\Helper\Object\FooInterface;
use Tests\Helper\Object\Union;

class ContainerTest extends TestCase
{
   private Container $container;

   protected function setUp(): void
   {
      $this->container = new Container;
   }

   public function test_get_container_not_singleton()
   {
      $router1 = new Router(new Request);
      $router2 = $this->container->get(Router::class);

      $this->assertEquals($router1, $router2);
   }

   public function test_container_have_instance()
   {
      $this->container->singleton(Router::class, function ($app) {
         return new Router($app->make(Request::class));
      });

      $this->container->make(Router::class);

      $available = $this->container->has(Router::class);

      $this->assertTrue($available);
   }

   public function test_container_doesnt_have_instance()
   {
      $available = $this->container->has(Router::class);
      $this->assertFalse($available);
   }

   public function test_container_have_bindings()
   {
      $this->container->bind(Router::class, function ($app) {
         return new Router($app->make(Request::class));
      });

      $available = $this->container->has(Router::class);
      $this->assertTrue($available);
   }

   public function test_container_doesnt_have_bindings()
   {
      $available = $this->container->has(Router::class);
      $this->assertFalse($available);
   }

   public function test_container_resolved_instance_is_singleton()
   {
      $this->container->singleton(Router::class, function ($app) {
         return new Router($app->make(Request::class));
      });

      $router1 = $this->container->get(Router::class);
      $router2 = $this->container->get(Router::class);

      $this->assertSame($router1, $router2);
   }

   public function test_container_resolved_instance_is_not_singleton()
   {
      $this->container->bind(Router::class, function ($app) {
         return new Router($app->make(Request::class));
      });

      $router1 = $this->container->get(Router::class);
      $router2 = $this->container->get(Router::class);

      $this->assertEquals($router1, $router2);
      $this->assertNotSame($router1, $router2);
   }

   public function test_container_make_method()
   {
      $router1 = new Router(new Request);
      $router2 = $this->container->get(Router::class);

      $this->assertEquals($router1, $router2);
   }

   public function test_container_resolve_error_because_class_not_found()
   {
      $this->expectException(BindingResolutionException::class);
      $this->container->make('App\Not\Found');
   }

   public function test_container_resolve_error_because_class_is_not_instantiable()
   {
      $this->expectException(BindingResolutionException::class);
      $this->container->make(FooInterface::class);
   }

   public function test_container_build_error_because_parameter_is_not_type_hinted()
   {
      $this->expectException(BindingResolutionException::class);
      $this->container->make(Bar::class);
   }

   public function test_container_build_error_because_parameter_is_union_type()
   {
      $this->expectException(BindingResolutionException::class);
      $this->container->make(Union::class);
   }

   public function test_instance_method()
   {
      $container = new Container;
      $instance = $this->container->instance(Container::class, $container);

      $this->assertSame($instance, $container);
   }

   public function test_set_instance_method()
   {
      $instance = Container::setInstance($this->container);

      $this->assertSame($this->container, $instance);
   }
}
