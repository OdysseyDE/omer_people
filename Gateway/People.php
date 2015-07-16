<?php
class Gateway_People extends Gateway_Base{

  public function find( $ids ){
    $sql = "SELECT \"people\".\"data\"->>'id' AS \"id\",
                   \"people\".\"data\"
            FROM   \"people\"
            WHERE  \"deleted\" = FALSE
              AND  \"people\".\"data\"->>'id' IN ";
    $result = $this->findByIds($sql,$ids);
    $people = array();
    foreach ( $result as $row ){
      $people[$row['id']] = new Person($row['id'],
                                 json_decode($row['data']));
    }
    return $people;
  }

  public function delete( $ids ){
    return parent::delete('people', $ids);
  }

  public function create ( $person ){
    // Falls im Request bereits eine ID im JSON vorhanden ist, wird diese ignoriert.
    // Nur eine ID am Person-Objekt dirket wird berücksichtigt.
    if(is_null($person->id)){
      $person->id = str_replace('.','-',uniqid('', true));
    }
    $id = $person->id;
    $person->person->id = $id;
    $doc = json_encode($person->person);

    if(parent::create('people', $doc)){
      return $id;
    } else {
      return false;
    }

  }

  public function update ( $person ){
    if(is_null($person->id) && is_null($person->person->id)){
      throw new Error('Keine ID angegeben!');
    }
    $id = $person->id;
    $old = $this->findOne($id);
    if(!$old){
      throw new Error("Person mit der ID '".$id."' nicht gefunden!");
    }
    foreach($person->person as $key => $value){
      if(!($key == 'id'))
        $old->person->$key = $value;
    }
    $doc = json_encode($old->person);
    return parent::update('people', $id, $doc);
  }

}

?>