<?php
define('WHITE', '⚪️', true);
define('BLUE', '🔵', true);
define('RED', '🔴', true);

$website = 'https://api.telegram.org/bot'.$token;

function apiRequest ($methode) {
  return file_get_contents($GLOBALS[website].'/'.$methode);
}//apiRequest

function sendMsg ($chatId, $msg, $mode) {
  if ($mode == '') $msg = urlencode($msg);
  apiRequest('sendmessage?parse_mode='.$mode.'&chat_id='.$chatId.'&text='.$msg);
}//sendMsg

function printField($chatId, $field) {
  for ($row=0; $row<=count($field); $row++) {
    for ($col=0; $col<count($field[0]); $col++) {
      if ($row == count($field)) {
        $out .= '`'.($col+1).'  `';
        continue;
      }//if
      switch ($field[$row][$col]) {
        case 1:
          $out .= RED;
          break;
        case 2:
          $out .= BLUE;
          break;
        default:
          $out .= WHITE;
          break;
      }//switch
    }//for
    $out .= urlencode("\n");
  }//for
  sendMsg($chatId, $out, 'Markdown');
}//printField

function inlineKeys ($buttons, $chatId, $msg) {
  $keyboard = json_encode(array('inline_keyboard' => $buttons));
  apiRequest('sendmessage?parse_mode=Markdown&chat_id='.$chatId.'&text='.urlencode($msg).'&reply_markup='.$keyboard);
}//inlineKeys

?>
