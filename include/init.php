<?php

if ( false === function_exists('lcfirst') ):
  function lcfirst( $str )
  { return (string)(strtolower(substr($str,0,1)).substr($str,1));}
endif;

// Pflichtangaben, die local/config enthalten sein müssen
$pflichtAngaben = array('DB' => array('Host',
                                      'Database',
                                      'User',
                                      'Password',
                                      ),
                        );

// falls keine local/config existiert, anlegen
$localConf = dirname(__FILE__).'/../local/config.php';
if ( !file_exists($localConf) )
  {
    if ( ($fh = @fopen($localConf,'w')) === false )
      die("Nicht möglich, $localConf anzulegen, Rechteproblem?");

    fputs($fh,"<?php\n");
    foreach ( $pflichtAngaben as $title => $angaben )
      {
        if ( !is_array($angaben) )
          fputs($fh,"\$GLOBALS['Settings']['$angaben'] = '';\n");
        else
          foreach($angaben as $angabe )
            fputs($fh,"\$GLOBALS['Settings']['$title']['$angabe'] = '';\n");
        fputs($fh,"\n");
      }
    fputs($fh,"?>\n");
  } 

require_once('config.php');
require_once($localConf);

// prüfen, ob alle Pflichtangabe gesetzt sind
foreach ( $pflichtAngaben as $title => $angaben )
{
  foreach ( $angaben as $angabe )
    {
      if ( !isset($GLOBALS['Settings'][$title][$angabe]) || $GLOBALS['Settings'][$title][$angabe] == '' )
        die("$title-$angabe fehlt in der local_config.php");
    }
}


$paths = array($GLOBALS['Settings']['RootPath'],
	       $GLOBALS['Settings']['RootPath']."/include",

	       $GLOBALS['Settings']['RootPath']."/Controller",
	       $GLOBALS['Settings']['RootPath']."/Gateway",
	       $GLOBALS['Settings']['RootPath']."/Model",
	       );
ini_set('include_path',implode(":",$paths));


function autoload ( $class )
{
  if ( $class == 'Smarty' )
    include_once("Smarty.class.php");
  else
    include_once(str_replace('_','/',$class).".php");
}
spl_autoload_register('autoload');
require $GLOBALS['Settings']['RootPath'].'/vendor/autoload.php';

set_exception_handler('ExceptionHandler');
set_error_handler('ErrorHandler');

function errhndlOffline ($err) 
{
  $body = "<h1>FEHLER</h1>".chr(10);
  $body .= $err->getMessage()."<br>".chr(10);
  $body .= $err->getUserInfo()."<br>".chr(10);
  $body .= "<div>Backtrace:</div>".chr(10);
  $body .= "<ul>".chr(10);
  $backtrace = $err->getBackTrace();
  foreach ($backtrace as $trace)
    {
      if ( is_array($trace['args']) && strToLower(get_class($trace['args'][0])) != 'smarty' )
	$body .= "<li>".$trace['file'].": ".$trace['line']." - ".$trace['function']."(".implode(', ',$trace['args']).") </li>".chr(10);
      else
	$body .= "<li>".$trace['file'].": ".$trace['line']." - ".$trace['function']."(".$trace['args'].") </li>".chr(10);
    }
  $body .= "</ul>".chr(10);
  echo $body;
  die();
}

function ExceptionHandler ( $exception )
{
  if ( $GLOBALS['Settings']['OnServer'] )
    echo "Es ist eine schwerer Fehler aufgetreten. Die Anwendung wird beendet.";
  else
    print_r($exception);
  die();
}

function ErrorHandler ( $fehlercode, $fehlertext, $fehlerdatei, $fehlerzeile )
{
  if ( in_array($fehlercode,array(E_NOTICE,E_USER_NOTICE,E_STRICT,E_DEPRECATED,2)) )
    return true;

  echo $fehlercode." ".$fehlertext." ".$fehlerdatei." ".$fehlerzeile;
  die();
}

//session_name("");
session_start();


?>