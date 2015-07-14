<?php
  
  use Jacwright\RestServer\RestException;

class PeopleController{
  
  /**
   * Gibt das aktuelle Datum zurück.
   *
   * @url GET /
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
    return array_merge($GLOBALS['Settings']['DB'],['pdo_drivers' => PDO::getAvailableDrivers()]);
  }

}
?>