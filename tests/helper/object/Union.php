<?php

namespace Tests\Helper\Object;

class Union
{
   public $biz;

   public function __construct(string|int $biz)
   {
      $this->biz = $biz;
   }
}
