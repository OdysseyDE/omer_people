<?php
class Gateway_People extends Gateway_Base{

  public function find( $ids ){
    $sql = "SELECT people.data->>'id' AS id,
                   people.data
            FROM   people
            WHERE  deleted = FALSE
              AND  people.data->>'id' IN ";
    $result = $this->findByIds($sql,$ids);
    $people = array();
    foreach ( $result as $row ){
      $people[$row['id']] = new Person($row['id'],
                                 json_decode($row['data']));
    }
    return $people;
  }

}

?>