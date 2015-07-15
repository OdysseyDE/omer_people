<?php
class Person extends BaseClassWithID
{
  protected $person;
  public function __construct ( $id = null, $person = null )
  {
    parent::__construct($id);
    $this->person = $person;
  }
  public function __toString ( )
  {
    $parts = array();
    if(isset($this->id))
       array_push($parts,$this->id.':');
    if ( $this->person->vorname > '' )
      array_push($parts,$this->person->vorname);
    if ( $this->person->name > '' )
      array_push($parts,$this->person->name);
    return implode(' ',$parts);
  }
  
}
?>