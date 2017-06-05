<?php
$debug = TRUE;

define('blue', '🔵', true);
define('red', '🔴', true);

require_once('token.php');    //bot identifier
require_once('basicfunctions.php');    //bot identifier
require_once('../../mysqli_connect.php');   //db-connection

$input = json_decode(file_get_contents('php://input'), TRUE);
$chatId = $input['message']['chat']['id'];
$inputMsg = $input['message']['text'];
$command = explode(' ', $inputMsg)[0];
$sender = $input['message']['from'];
$callbackId = $input['callback_query']['from']['id'];
$callbackData = $input['callback_query']['data'];


if ($chatId) {    //to hide warnings from website
  switch ($command) {
    case '/start':
      sendMsg($chatId, $msg['start'], '');
      break;
    case '/help':
      sendMsg($chatId, $msg['help'], '');
      break;
    default:

      break;
  }//switch
}//if


//answer messagee
$msg['start'] = 'Hallo '.$sender['first_name'].PHP_EOL.'Schön, dass Sie diesen Bot gefunden haben.'.PHP_EOL.
  'Dieser Bot befindet sich noch in der Entwicklungsphase.';
$msg['help'] = 'Hallo '.$sender['first_name'].PHP_EOL.'Womit kann ich helfen?';

//command functions

?>
