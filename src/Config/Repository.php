<?php

namespace Mproyyan\Comet\Config;

class Repository
{
   protected $items = [];

   public function __construct($items = [])
   {
      $this->items = $items;
   }

   public function set($key, $value)
   {
      $this->items[$key] = $value;
   }

   public function get($key, $default = null)
   {
      $items = $this->items;

      if (!str_contains($key, '.')) {
         return $items[$key];
      }

      $key = trim($key, ' ');
      $key = trim($key, '.');

      foreach (explode('.', $key) as $segment) {
         $items = $items[$segment] ?? $default;
      }

      return $items;
   }
}
