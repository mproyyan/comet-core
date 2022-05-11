<?php

namespace Mproyyan\Comet\Container;

use Closure;
use Psr\Container\ContainerInterface;
use ReflectionClass;
use ReflectionException;
use Mproyyan\Comet\Container\Exceptions\BindingResolutionException;
use ReflectionFunction;
use ReflectionMethod;
use ReflectionNamedType;
use ReflectionParameter;
use ReflectionUnionType;

class Container implements ContainerInterface
{
   /**
    * @var \Mproyyan\Comet\Container\Container
    */
   protected static $instance;

   protected array $bindings = [];

   protected array $instances = [];

   public function get(string $id)
   {
      return $this->resolve($id);
   }

   public function has(string $abstract): bool
   {
      return isset($this->bindings[$abstract]) || isset($this->instances[$abstract]);
   }

   public function bind(string $abstract, Closure $concrete, $singleton = false): void
   {
      $this->bindings[$abstract] = ['concrete' => $concrete, 'singleton' => $singleton];
   }

   public function singleton(string $abstract, Closure $concrete)
   {
      $this->bind($abstract, $concrete, true);
   }

   public function make($abstract)
   {
      return $this->resolve($abstract);
   }

   protected function resolve(string $abstract)
   {
      if (isset($this->instances[$abstract])) {
         return $this->instances[$abstract];
      }

      $concrete = $this->getConcrete($abstract);

      if ($this->isBuildable($concrete, $abstract)) {
         $object = $this->build($concrete);
      } else {
         $object = $this->make($abstract);
      }

      if ($this->isSingleton($abstract)) {
         $this->instances[$abstract] = $object;
      }

      return $object;
   }

   protected function getConcrete(string $abstract)
   {
      if (isset($this->bindings[$abstract])) {
         return $this->bindings[$abstract]['concrete'];
      }

      return $abstract;
   }

   protected function isBuildable($concrete, $abstract)
   {
      return $concrete === $abstract || $concrete instanceof Closure;
   }

   protected function isSingleton($abstract)
   {
      return isset($this->instances[$abstract]) ||
         (isset($this->bindings[$abstract]['singleton']) &&
            $this->bindings[$abstract]['singleton'] === true);
   }

   protected function build($concrete)
   {
      if ($concrete instanceof Closure) {
         return $concrete($this);
      }

      try {
         $reflector = new ReflectionClass($concrete);
      } catch (ReflectionException $e) {
         throw new BindingResolutionException("Target class [$concrete] does not exist.", 0, $e);
      }

      if (!$reflector->isInstantiable()) {
         throw new BindingResolutionException("Target [$concrete] is not instantiable.");
      }

      $constructor = $reflector->getConstructor();

      if (!$constructor) {
         return new $concrete;
      }

      $parameters = $constructor->getParameters();

      if (!$parameters) {
         return new $concrete;
      }

      try {
         $instances = $this->resolveDependencies($parameters);
      } catch (BindingResolutionException $e) {
         throw $e;
      }

      return $reflector->newInstanceArgs($instances);
   }

   protected function resolveDependencies($parameters)
   {
      $dependencies = array_map(
         function (ReflectionParameter $parameter) {
            $name = $parameter->getName();
            $type = $parameter->getType();

            if (!$type) {
               throw new BindingResolutionException("Failed to resolve dependency [$name] because is missing a type hint.");
            }

            if ($type instanceof ReflectionUnionType) {
               throw new BindingResolutionException("Failed to resolve dependency [$name] because of union type.");
            }

            if ($type instanceof ReflectionNamedType && !$type->isBuiltin()) {
               return $this->make($type->getName());
            }

            throw new BindingResolutionException("Failed to resolve dependency [$name] because invalid parameter");
         },
         $parameters
      );

      return $dependencies;
   }

   public function instance($abstract, $instance)
   {
      if (!isset($this->instances[$abstract])) {
         $this->instances[$abstract] = $instance;
      }

      return $instance;
   }

   public static function setInstance(Container $container = null)
   {
      return static::$instance = $container;
   }

   public function resolveCallbackDependencies($source, $primitiveParameters = [])
   {
      if ($source instanceof \Closure) {
         return $this->getParametersFromFunction($source, $primitiveParameters);
      }

      if (is_array($source)) {
         return $this->getParametersFromMethod($source, $primitiveParameters);
      }
   }

   protected function getParametersFromFunction(\Closure $callback, $primitiveParameters = [])
   {
      $callback = new ReflectionFunction($callback);
      $parameters = $callback->getParameters();

      return $this->combineParameters($parameters, $primitiveParameters);
   }

   protected function getParametersFromMethod(array $callback, $primitiveParameters = [])
   {
      $callback = new ReflectionMethod($callback[0], $callback[1]);
      $parameters = $callback->getParameters();

      return $this->combineParameters($parameters, $primitiveParameters);
   }

   protected function combineParameters($typeHintedParams, $primitiveParams = [])
   {
      $parameters = [];

      foreach ($typeHintedParams as $parameter) {
         $name = $parameter->getName();
         $type = $parameter->getType();

         if ($type === null && array_key_exists($name, $primitiveParams)) {
            $parameters[] = $primitiveParams[$name];
         }

         if ($type instanceof ReflectionUnionType) {
            throw new BindingResolutionException("Failed to resolve dependency [$name] because of union type.");
            break;
         }

         if ($type instanceof ReflectionNamedType && !$type->isBuiltin()) {
            $parameters[] = $this->make($type->getName());
         }
      }

      return $parameters;
   }
}
