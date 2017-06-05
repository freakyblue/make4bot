<?php
define('WHITE', '⚪️', true);
define('BLUE', '🔵', true);
define('RED', '🔴', true);

$website = 'https://api.telegram.org/bot'.$token;

function apiRequest ($methode) {
  return file_get_contents($GLOBALS[website].'/'.$methode);
}//apiRequest

function checkWin ($chatId, $field) {
  for ($row = 0; $row < count($field); $row++)
    for ($col = 0; $col < count($field[0]); $col++) {
      if ($col < (count($field[0])-3))    //check for horizontal lines
        if (($field[$row][$col] > 0) && ($field[$row][$col] == $field[$row][$col+1]) &&
          ($field[$row][$col+1] == $field[$row][$col+2]) && ($field[$row][$col+2] == $field[$row][$col+3]))
          return $field[$row][$col];
      if ($row < (count($field)-3))    //check for vertical lines
        if (($field[$row][$col] > 0) && ($field[$row][$col] == $field[$row+1][$col]) &&
          ($field[$row+1][$col] == $field[$row+2][$col]) && ($field[$row+2][$col] == $field[$row+3][$col]))
          return $field[$row][$col];
      if (($row < (count($field)-3)) && ($col < (count($field[0])-3)))    //check for diagonal up lines
        if (($field[$row][$col] > 0) && ($field[$row][$col] == $field[$row+1][$col+1]) &&
          ($field[$row+1][$col+1] == $field[$row+2][$col+2]) && ($field[$row+2][$col+2] == $field[$row+3][$col+3]))
          return $field[$row][$col];
      if (($row > 2)  && ($col < (count($field[0])-3)))    //check for diagonal down lines
        if (($field[$row][$col] > 0) && ($field[$row][$col] == $field[$row-1][$col+1]) &&
          ($field[$row-1][$col+1] == $field[$row-2][$col+2]) && ($field[$row-2][$col+2] == $field[$row-3][$col+3]))
          return $field[$row][$col];
    }//for
  return 0;
}//checkWin

function sendMsg ($chatId, $msg, $mode) {
  if ($mode == '') $msg = urlencode($msg);
  apiRequest('sendmessage?parse_mode='.$mode.'&chat_id='.$chatId.'&text='.$msg);
}//sendMsg

function printField($chatId, $field) {
  $out = '`';
  for ($row = (count($field)-1); $row >= 0; $row--) {
    for ($col = 0; $col < count($field[0]); $col++) {
      if ($row == count($field)) {
        $out .= ' '.($col+1).' ';
        continue;
      }//if
      switch ($field[$row][$col]) {
        case 1:
          $out .= RED.' ';
          break;
        case 2:
          $out .= BLUE.' ';
          break;
        default:
          $out .= WHITE.' ';
          break;
      }//switch
    }//for
    $out .= urlencode("\n");
  }//for
  $out .= '`';
  sendMsg($chatId, $out, 'Markdown');
}//printField

function inlineKeys ($buttons, $chatId, $msg) {
  $keyboard = json_encode(array('inline_keyboard' => $buttons));
  apiRequest('sendmessage?parse_mode=Markdown&chat_id='.$chatId.'&text='.urlencode($msg).'&reply_markup='.$keyboard);
}//inlineKeys

?>
