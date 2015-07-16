<?php
  
  use Jacwright\RestServer\RestException;
class PeopleController{
  
  /**
   * Gibt das aktuelle Datum zurück.
   *
   * @url GET /test
   * @noAuth
   */
  public function test()
  {
    return ["date" => date(DateTime::ISO8601)];
  }

  /**
   * phpinfo()
   *
   * @url GET /php
   */
  public function php()
  {
    if($GLOBALS['Debugging'] != 1){
      throw new RestException(403, 'Zugriff verweigert!');
    }
    return phpinfo();
  }

  /**
   * DB-Config
   *
   * @url GET /db
   */
  public function db(){
    if($GLOBALS['Debugging'] != 1){
      throw new RestException(403, 'Zugriff verweigert!');
    }
    return array_merge(
           $GLOBALS['Settings']['DB'],
           ['pdo_drivers' => PDO::getAvailableDrivers()]
    );
  }

  /**
   * Gets a person by id
   *
   * @url GET /$id
   */
  public function getPerson( $id = null ){
    $person = Gateway_Base::factory('people')->findOne($id);
    if(!$person)
      throw new RestException(404, "Person mit der ID '".$id."' nicht gefunden.");
    return $person->person;
  }

  /**
   * Creates a person by id
   *
   * @url POST /
   * @url POST /$id
   */
  public function createPerson( $id = null, $data ){
    $person = new Person($id, $data);
    try{
      $result = Gateway_Base::factory('people')->create($person);
      if($result!==false){
        header('Location: /people/'.$result, true, 201);
        return ["id" => $result];
      }
      return ["result" => $result];
    } catch (Exception $e){
      throw new RestException(409,$e->getMessage());
    }
  }

  /**
   * Deletes a person by id
   *
   * @url DELETE /$id
   */
  public function deletePerson( $id = null ){
    // TODO: param check
    $result = Gateway_Base::factory('people')->delete($id);
    return ["result" => $result];
  }

}
?>