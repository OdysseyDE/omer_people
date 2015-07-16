<?php

abstract class BaseClass
{
  private $classRestrictions;
  private $conversions;
  private $noSet;
  private $exports;

  public function __construct ( )
  {
    $this->classRestrictions = $this->conversions = $this->noSet = $this->exports = array();
  }

  protected function addClassRestriction ( $variable, $class )
  {
    $this->classRestrictions[$variable] = $class;
    if ( is_subclass_of($class,'BaseClass') && !is_object($this->$variable) )
      $this->$variable = new $class;
  }

  protected function addConversion ( $variable, $type )
  {
    $this->conversions[$variable] = $type;
  }

  protected function addNoSet ( $variable )
  {
    $this->noSet[$variable] = $variable;
  }

  protected function addExport ( $variable, $exports = array() )
  {
    $this->exports[$variable] = $exports;
  }

  public function __toString ( )
  {
    return '';
  }

  protected function getAttributes ( )
  {
    $attributes = array();
    foreach ( get_class_vars(get_class($this)) as $var => $value )
      if ( !in_array($var,array('classRestrictions','conversions','exports')) )
        {
          if ( is_object($this->$var) )
            {
              if ( is_subclass_of($this->$var,'BaseClass') )
                $attributes[$var] = $this->$var->attributes;
              else
                $attributes[$var] = $this->$var->__toString();
            }
          else
            $attributes[$var] = $this->$var;
        }
    return $attributes;
  }

  public function __get ( $name )
  {
    $function = 'get'.$name;
    if ( method_exists($this,$function) )
      return $this->$function();

    if ( !empty($this->exports) )
      foreach ( $this->exports as $var => $variables )
        {
          foreach ( $variables as $test )
            if ( $test == $name )
              return $this->$var === null ? null : $this->$var->$test;
        }

    if ( !property_exists($this,$name) )
      throw new Exception(get_class($this).' does not have getable property: '.$name);
    return $this->$name;
  }

  protected function setAttributes ( $values )
  {
    foreach ( $values as $name => $value )
      {
        if ( is_array($value) && is_object($this->$name) )
          $this->$name->attributes = $value;
        elseif ( isset($this->classRestrictions[$name]) && !is_a($value,$this->classRestrictions[$name]) && $value !== null )
          $this->$name = new $this->classRestrictions[$name]($value);
        else
          $this->__set($name,$value);
      }
  }

  public function __set ( $name, $value )
  {
    if ( isset($this->noSet[$name]) )
      throw new Exception($name.' may not be set directly.');;

    $function = 'set'.$name;
    if ( method_exists($this,$function) )
      return $this->$function($value);

    if ( !empty($this->exports) )
      foreach ( $this->exports as $var => $variables )
        {
          foreach ( $variables as $test )
            if ( $test == $name )
              return $this->$var->$test = $value;
        }

    if ( !property_exists($this,$name) )
      throw new Exception(get_class($this).' does not have setable property: '.$name);

    if ( isset($this->classRestrictions[$name]) && $value !== null && !is_a($value,$this->classRestrictions[$name]) )
      throw new Exception('property: '.$name.' of class '.get_class($this).' has to have type '.$this->classRestrictions[$name]);

    $this->$name = $value;
    if ( isset($this->conversions[$name]) )
      {
        switch ( $this->conversions[$name] )
          {
          case 'bool' :
            $this->$name = (bool)$this->$name; break;
          case 'boolNull' :
            $this->$name = $this->$name === null ? null : (bool)$this->$name; break;
          default :
            throw new Exception($this->conversions[$name].' type conversion not supported');
          }
      }
  }

}

?>
