<?php

require_once('../../include/init.php');

Flight::set('flight.base_url','/api/');
Flight::set('flight.log_errors',true);

$command = new Api_Controller;
Flight::route('*',array($command,'run'));
Flight::start();

?>
