<?php
$debug = TRUE;

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

//answer messagee
$msg['start'] = 'Hallo '.$sender['first_name'].PHP_EOL.'Schön, dass Sie diesen Bot gefunden haben.'.PHP_EOL.
  'Dieser Bot befindet sich noch in der Entwicklungsphase.';
$msg['selection'] = 'Wähle eine Reihe aus:';
$msg['help'] = 'Hallo '.$sender['first_name'].PHP_EOL.'Womit kann ich helfen?';


if ($chatId) {    //to hide warnings from website
  switch ($command) {
    case '/start':
      sendMsg($chatId, $msg['start'], '');
      start($chatId);
      break;
    case '/help':
      sendMsg($chatId, $msg['help'], '');
      break;
    default:

      break;
  }//switch
}//if


if($input['callback_query']) {
  $command = explode(' ', $callbackData)[0];
  $arg1 = explode(' ', $callbackData)[1];
  //$arg2 = explode(' ', $callbackData)[2].' '.explode(' ', $callbackData)[3].' '.explode(' ', $callbackData)[4];
  switch($command) {
    case '/col':
      updateField($callbackId, $arg1);
      break;
    default:
      break;
    }//switch
}//if


//command functions

function updateField ($callbackId, $col) {
}//updateField

function start ($chatId) {
  global $msg;
  //init field
  for ($row = 0; $row < 7; $row++)
    for ($col = 0; $col < 7; $col++)
      $field[$row][$col] = 0;
  printField($chatId, $field);
  printSelection($chatId, $msg['selection']);
}//start

?>
