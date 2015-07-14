<?php

abstract class BaseClassWithID extends BaseClass
{
  protected $id;

  public function __construct ( $id = null )
  {
    parent::__construct();
    $this->id = $id;
  }


}

?>
