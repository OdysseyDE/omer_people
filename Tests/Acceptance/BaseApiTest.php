<?php

require_once __DIR__.'/../../Controller/Wrapper.php';

abstract class BaseApiTest extends  PHPUnit_Framework_TestCase 
{
  protected function createWrapper ( $key = '' )
  {
    return new Wrapper($key,'http://localhost:8801');
  }

}

?>
