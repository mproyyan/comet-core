<?php

namespace Tests\Helper\Object;

class Foo
{
   public $bar;

   public function __construct(string $bar)
   {
      $this->bar = $bar;
   }
}
