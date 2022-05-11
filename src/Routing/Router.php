<?php

namespace Mproyyan\Comet\Routing;

use Mproyyan\Comet\Core\Application;
use Mproyyan\Comet\Request\Request;

class Router
{
   protected Application $app;

   protected array $routes = [];

   protected array $routeParameters = [];

   public Request $request;

   public function __construct(Application $app, Request $request)
   {
      $this->app = $app;
      $this->request = $request;
   }

   public function get(string $url, array|callable $callback)
   {
      $this->routes['get'][$url] = $callback;
   }

   public function post(string $url, array|callable $callback)
   {
      $this->routes['post'][$url] = $callback;
   }

   protected function getCallback($path)
   {
      $method = $this->request->method();
      $path = trim($path, '/');

      // get all routes by method
      $routes = $this->routes[$method];

      foreach ($routes as $route => $callback) {
         $route = trim($route, '/');
         $parametersName = [];

         // extract parameter name from route
         if (preg_match_all('/{(\w+)}/', $route, $matches)) {
            $parametersName = $matches[1];
         }

         // convert route into regex pattern
         $routeRegex = '@^' . preg_replace_callback('/\{(\w+)}/', fn ($m) => '(\w+)', $route) . '$@';

         // test and match current route againts $routeRegex if match return the callback
         if (preg_match_all($routeRegex, $path, $matches)) {
            $parameters = [];

            for ($i = 1; $i < count($matches); $i++) {
               $parameters[] = $matches[$i][0];
            }

            $this->routeParameters = array_combine($parametersName, $parameters);
            return $callback;
         }
      }
   }

   public function resolve()
   {
      $method = $this->request->method();
      $path = $this->request->path();
      $callback = $this->routes[$method][$path] ?? false;

      if (!$callback) {
         $callback = $this->getCallback($path);

         if (!$callback) {
            throw new \Exception('Not Found', 404);
         }
      }

      if (is_array($callback)) {
         $controller = $this->app->make($callback[0]);
         $callback[0] = $controller;
      }

      $parameters = $this->app->resolveCallbackDependencies($callback, $this->routeParameters);

      return call_user_func_array($callback, $parameters);
   }
}
