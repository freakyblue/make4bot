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
$msg['playerwins'] = 'Herzlichen Glückwunsch.'.PHP_EOL.'Sie haben gewonnen';
$msg['botwins'] = 'Muhaha! I won!'.PHP_EOL.'Rise of the machines!';
$msg['remi'] = 'Noone won!'.PHP_EOL.'Send /start play a new game';

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
  $arg2 = explode(' ', $callbackData)[2];
  //printField($callbackId, decField($arg2));
  switch($command) {
    case '/col':
      updateField($callbackId, $arg1, $arg2);
      break;
    default:
      break;
    }//switch
}//if


//command functions

function updateField ($callbackId, $col, $encField) {
  global $msg;
  $field = addStone(decField($encField), $col, 1);
  printField($callbackId, $field);
  if (checkWin($callbackId, $field) == 1)
    sendMsg($callbackId, $msg['playerwins'], '');
  else {
    $field = playBot($field);
    if ($field == FALSE)
      sendMsg($callbackId, $msg['remi'], '');
    printField($callbackId, $field);
    if (checkWin($callbackId, $field) == 2)
      sendMsg($callbackId, $msg['botwins'], '');
    else
      printSelection($callbackId, $field, $msg['selection']);
  }//else
}//updateField

function start ($chatId) {
  global $msg;
  //init field
  for ($row = 0; $row < 3; $row++)
    for ($col = 0; $col < 5; $col++)
      $field[$row][$col] = 0;
  printField($chatId, $field);
  printSelection($chatId, $field, $msg['selection']);
}//start

?>
