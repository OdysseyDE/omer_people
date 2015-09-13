<?php
  spl_autoload_register(); // don't load our classes unless we use them

  require_once('../include/init.php');
  //$mode = 'debug'; // 'debug' or 'production'

  $mode = $GLOBALS['Debugging'] ? 'debug' : 'production';
  $server = new Jacwright\RestServer\RestServer($mode);
  // $server->refreshCache(); // uncomment momentarily to clear the cache if classes change in production mode
  // $server->addClass('TestController');
  $server->addClass('PeopleController', '/'); // adds this as a base to all the URLs in this class
  $server->handle();
?>