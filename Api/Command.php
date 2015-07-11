<?php

define('ERROR_Generic',0);
define('ERROR_MissingKey',1);
define('ERROR_InvalidApiKey',2);
define('ERROR_InvalidMethod',3);
define('ERROR_UnsupportedCall',5);

define('STATUS_Required',1);
define('STATUS_Optional',2);

abstract class Api_Command extends Command
{
  protected $result;
  protected $attributeList;

  private $data;


  public function __construct ( )
  {
    parent::__construct();
    $this->result = array();

    $this->data = false;

    $this->defineAttributes();
    $this->checkAttributes();
    $this->extractData();
  }

  protected function defineAttributes ( )
  {
    $this->attributeList = array();
  }

  protected function checkAttributes ( )
  {
    $data = $this->retrieveData();

    foreach ( $this->attributeList as $attribute => $status )
      if ( $status == STATUS_Required && !isset($data[$attribute]) )
        $this->error(ERROR_MissingParameter,$attribute);

    foreach ( $data as $attribute => $value )
      {
        if ( $attribute == 'key' )
          continue;

        if ( !isset($this->attributeList[$attribute]) )
          $this->error(ERROR_NotSupportedParameter,$attribute);
      }
  }

  protected function extractData ( )
  {
    $data = $this->retrieveData();

    foreach ( $this->attributeList as $attribute => $status )
      {
        if ( $status == STATUS_Optional && (!isset($data[$attribute]) || $data[$attribute] === '') )
          $this->$attribute = null;
        else
          $this->$attribute = $data[$attribute];
      }
  }

  protected function error ( $errorCode, $additionalInfo = '', $status = 400 )
  {
    $message = $this->errorMessage($errorCode);
    if ( $additionalInfo > '' )
      $message .= ': '.$additionalInfo;

    $result = array('errorCode' => $errorCode,
                    'errorMessage' => $message);
    Flight::json($result,$this->errorStatus($errorCode));
  }

  private function errorMessage ( $errorCode )
  {
    $messages = array(ERROR_Generic => 'Generic error',
                      ERROR_InvalidApiKey => 'Invalid Api key',
                      ERROR_MissingRequest => 'Missing request parameter',
                      ERROR_AddOnNotIncluded => 'Add on not included',
                      ERROR_UnsupportedCall => 'Call ist not supported',
                      ERROR_InvalidMethod => 'Method now allowed: ',
                      ERROR_InvalidRequest => 'Invalid request',
                      ERROR_AccessDenied => 'Access denied, this request ist not supported for this api key',
                      ERROR_MissingParameter => 'Missing required parameter',
                      ERROR_InvalidParameterValue => 'Invalid parameter value',
                      ERROR_InvalidDateRange => 'Invalid dateRange',
                      ERROR_NotSupportedParameter => 'Parameter not supported',
                      ERROR_DuplicateEmail => 'Duplicate email',
                      ERROR_InvalidAccountId => 'Invalid Account Id',
                      ERROR_LoginFailed => 'Login failed',
                      ERROR_LoginFailedDeactivated => 'Login failed, Account is not active',
                      ERROR_LimitExceededCustomers => 'Customer limit exceeded',
                      ERROR_LimitExceededProjects => 'Project limit exceeded',
                      ERROR_InvalidCustomerId => 'Invalid customer id',
                      ERROR_InvalidInvoiceId => 'Invalid invoice id',
                      ERROR_InvalidType => 'Invalid type or invoice detail',
                      ERROR_InvalidEventId => 'Invalid event id',
                      ERROR_InvalidProductId => 'Invalid product id',
                      ERROR_InvalidTimeEntryId => 'Invalid time entry id',
                      ERROR_DeletingBilledProduct => 'Trying to delete an already billed product',
                      ERROR_DeletingBilledTimeEntry => 'Trying to delete an already billed time entry',
                      ERROR_InvalidProjectId => 'Invalid project id',
                      );
    return $messages[$errorCode];
  }

  private function errorStatus ( $errorCode )
  {
    $messages = array(ERROR_MissingKey => 403,
                      ERROR_InvalidApiKey => 403,
                      ERROR_AccessDenied => 403,
                      ERROR_AddOnNotIncluded => 403,
                      );
    return isset($messages[$errorCode]) ? $messages[$errorCode] : 400;
  }

  private function retrieveData ( )
  {
    if ( $this->data !== false )
      return $this->data;

    $request = Flight::request();

    if ( $request->method == 'PUT' || $request->method == 'POST' )
      $this->data = json_decode(file_get_contents('php://input'),true);
    elseif ( $request->method == 'GET' )
      $this->data = $request->query;
    else
      $this->data = $request->data;

    return $this->data;
  }

}

?>
