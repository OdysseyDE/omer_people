<?php

class Wrapper
{
  private $key;
  private $url;
  private $body;

  public function __construct ( $key, $server = 'https://odysseyofthemind.de/omer_people' )
  {
    $this->key = $key;
    $this->url = $server;
  }

  /*
   * ask for user information providing
   */
  /*
   * people related methods
   */
  public function createPerson ( Person $person )
  {
    return $this->executeRequest('people','POST',$person);
  }

  public function findPerson ( )
  {
    $result = $this->executeRequest('people','GET');
    return new Person($result);
  }

  public function findPeople ( $ids )
  {
    $result = $this->executeRequest('people/','GET',array('ids' => $ids));
    if ( isset($result->errorCode) )
      return $result;

    $people = array();
    foreach ( $result as $row )
      $people[] = new Person($row); break;

    return $people;
  }

  public function updatePerson ( Person $person )
  {
    return $this->executeRequest('people/'.$person->id,'PUT',$person);
  }





  /*
   * helper methods
   */
  final protected function executeRequest ( $request, $method, $params = array())
  {
    $url = $this->buildUrl($request);
    $this->buildJsonData($params);
    return $this->executeCurl($url,$method);
  }

  private function buildUrl ( $request )
  {
    $url = $this->url;
    if ( substr($url,-1) != '/' )
      $url .= '/';

    return $url . 'api/'.$request;
  }

  private function buildJsonData ( $params )
  {
    $this->body = array();
    foreach ( $params as $key => $value )
      if ( $value !== null )
        $this->body[$key] = $value;
    $this->body = json_encode($this->body);
  }

  private function executeCurl ( $url, $method )
  {
    $ch = curl_init();    

    $params = '';
    if ( $method == 'GET' )
      $params = http_build_query(json_decode($this->body));
    if ( !empty($params) )
      $params .= '&';
    
    $url .= '?'.$params.'key='.$this->key;;
    
    //echo $url."\n".$this->body."\n";
    
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_ENCODING, "UTF-8");
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

    if ( $method == 'POST' )
      {
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $this->body);
        curl_setopt($ch, CURLOPT_HTTPHEADER, array(
                                                   'Content-Type: application/json',
                                                   'Content-Length: ' . strlen($this->body)
                                                   )
                    );
      }
 
    if ( $method == 'DELETE' )
      {
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'DELETE');
      }

    if ( $method == 'PUT' )
      {
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'PUT');
        curl_setopt($ch, CURLOPT_POSTFIELDS, $this->body);
      }

    return json_decode(curl_exec($ch));
  }

}

?>
