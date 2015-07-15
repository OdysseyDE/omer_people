<?php

class Gateway_Base
{
  private $connection;
  private $statement;
  
  final public function __construct ( )
  {
    $dsn = sprintf('pgsql:host=%s;dbname=%s',$GLOBALS['Settings']['DB']['Host'],$this->databaseName());
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

  public function databaseName ( )
  {
    return $GLOBALS['Settings']['DB']['Database'];
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

  public function truncate ( $table, $cascade = false )
  {
    $this->query("TRUNCATE `$table`".$cascade ? " CASCADE" : "");
  }

  public function findOne ( $id )
  {
    if ( !$id )
      return false;

    $result = $this->find(array("'".$id."'"));
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

  private function executeStatement ( $sql, $data, $stopOnError = true )
  {
    $this->statement = $this->connection->prepare($sql);
    $this->statement->execute(array_values($data));
    if ( $this->statement->errorCode() > 0 && $stopOnError )
      throw new Exception('DB-Fehler: '.var_export($this->statement->errorInfo()));
  }
  
}

?>
