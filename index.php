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
$msg['start_desc'] = 'Wähle eine Reihe aus:';
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


//command functions
function start ($chatId) {
  global $msg;
  //init field
  for ($row = 0; $row < 7; $row++)
    for ($col = 0; $col < 7; $col++)
      $field[$row][$col] = 0;
  printField($chatId, $field);

  for ($col = 0; $col < 7; $col++)
    $but[0][$col] = array('text' => strval($col+1), 'callback_data' => '/col '.strval($col));
  inlineKeys($but, $chatId, $msg['start_desc']);
}//start

?>
