<?php

class Gateway_Base
{
  private $connection;
  private $statement;
  
  final public function __construct ( )
  {
    $dsn = sprintf('pgsql:host:%s;dbname=%s',$GLOBALS['Settings']['DB']['Host'],$this->databaseName());
    try
      {
        $this->connection = new PDO($dsn,$GLOBALS['Settings']['DB']['User'],$GLOBALS['Settings']['DB']['Password']);
      }
    catch (PDOException $e)
      {
        echo 'Connection failed: ' . $e->getMessage();
      }
  }
  
  public function factory ( $object )
  {
    $className = 'Gateway_'.ucfirst($object);
    return new $className;
  }

  



  public function query ( $sql, $data = array() )
  {
    $this->executeStatement($sql,$data);
    return $this->statement->rowCount();
  }

  public function getOne ( $sql, $data = array() )
  {
    $this->executeStatement($sql,$data);
    return $this->statement->fetchColumn();
  }

  public function getCol ( $sql, $data = array() )
  {
    $this->executeStatement($sql,$data);

    $col = array();
    while ( ($value = $this->statement->fetchColumn()) !== false )
      $col[] = $value;

    return $col;
  }
  
  public function getRow ( $sql, $data = array() )
  {
    $this->executeStatement($sql,$data);
    return $this->statement->fetch(PDO::FETCH_ASSOC);
  }

  public function getAll ( $sql, $data = array() )
  {
    $this->executeStatement($sql,$data);
    return $this->statement->fetchAll(PDO::FETCH_ASSOC);
  }

  public function delete ( $ids )
  {
    if ( empty($ids) )
      return true;

    $sql = "DELETE FROM IDs WHERE ID IN (";
    if ( is_array($ids) )
      $sql .= implode(",",$ids).")";
    else
      $sql .= addslashes($ids).")";

    $this->executeStatement($sql,array(),false);
    return $this->statement->errorCode() == 0;
  }

  public function truncate ( $table )
  {
    $this->delete($this->getCol("SELECT ID FROM `$table`"));
    $this->query("TRUNCATE `$table`");
  }

  public function databaseName ( )
  {
    return $GLOBALS['Settings']['DB']['Database'];
  }





  public function findOne ( $id )
  {
    if ( $id == 0 )
      return false;

    $result = $this->find(array($id));
    return isset($result[$id]) ? $result[$id] : false;
  }

  protected function findByIds ( $sql, $ids )
  {
    if ( !is_array($ids) || empty($ids) )
      return array();

    $sql .= ' ('.implode(',',$ids).')';
    return $this->getAll($sql);
  }
  
  








  protected function getValue ( $value, $colName, &$sql, &$data, $emptyStringIsNull = true )
  {
    if ( $value === null || ($value === '' && $emptyStringIsNull) )
      {
	$comperator = "EQUALS NULL";
      }
    else
      {
	$comperator = "= ?";
	$data["`$colName`"] = $value;
      }
    $sql["`$colName`"] = "`$colName` $comperator";
  }

  protected function updateOrInsert ( $sql, $data, $table, $id, $idCol = 'id' )
  {
    if ( $id > 0 )
      {
        $this->updateEntry($sql,$data,$table,$id,$idCol);
        return $id;
      }

    return $this->insertEntry($sql,$data,$table,$idCol);
  }

  protected function insertEntry ( $sql, $data, $table, $idCol = 'id' )
  {
    $insert = "INSERT INTO `$table` SET ".str_replace("EQUALS","=",implode(", ",$sql));
    $this->query($insert,$data);

    return $this->getOne($select,$data);
  }

  protected function updateEntry ( $sql, $data, $table, $id, $idCol = 'id' )
  {
    $update = "UPDATE $table SET ".str_replace("EQUALS","=",implode(", ",$sql))." WHERE $idCol = ?";
    $data[] = $id;
    return $this->query($update,$data);
  }

  



  private function executeStatement ( $sql, $data, $stopOnError = true )
  {
    $this->statement = $this->connection->prepare($sql);
    $this->statement->execute(array_values($data));
    if ( $this->statement->errorCode() > 0 && $stopOnError )
      throw new BasicException('DB-Fehler: '.var_export($this->statement->errorInfo()));
  }
  
}

?>
