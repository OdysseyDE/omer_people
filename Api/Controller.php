<?php

class Api_Controller
{
  public function run ( )
  {
    $request = Flight::request();
    $url = strToLower(reset(explode('?',str_replace('/api/2.0/','',$request->base.$request->url))));

    $urlParts = explode('/',$url);

    $id = null;

    $klassenName = 'Api_'.ucfirst($urlParts[0]);
    if ( isset($urlParts[1]) && is_numeric($urlParts[1]) )
      {
        $id = $urlParts[1];
        if ( $request->method == 'GET' )
          $klassenName .= '_FindOneCommand';
        elseif ( $request->method == 'PUT' )
          $klassenName .= '_UpdateCommand';
        elseif ( $request->method == 'DELETE' )
          $klassenName .= '_DeleteCommand';
        else
          return $this->error(ERROR_InvalidMethod);
      }
    else
      {
        if ( $request->method == 'GET' )
          $klassenName .= '_FindCommand';
        elseif ( $request->method == 'POST' )
          $klassenName .= '_CreateCommand';
        else
          return $this->error(ERROR_InvalidMethod);
      }

    try
      {
        $command = new $klassenName($id);
        $command->run();
      }
    catch ( Exception $e )
      {
        $fh = fopen('/tmp/api_errors','a');
        fputs($fh,print_r($e,true));
        fclose($fh);

        return $this->error(ERROR_Generic);
      }

    Flight::json($command->result);
  }

}

?>
