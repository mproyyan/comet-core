<?php

namespace Mproyyan\Comet\Request;

class Request
{
   public function method()
   {
      return strtolower($_SERVER['REQUEST_METHOD']);
   }

   public function path()
   {
      $path = $_SERVER['REQUEST_URI'];
      $position = strpos($path, '?');

      if ($position !== false) {
         $path = substr($path, 0, $position);
      }

      return $path;
   }
}
