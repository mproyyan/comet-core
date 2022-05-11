<?php

namespace Tests\Helper\Object;

class Bar
{
   public $bar;

   public function __construct($username)
   {
      $this->bar = $username;
   }
}
