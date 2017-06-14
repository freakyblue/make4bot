<?php
define('WHITE', '⚪️', true);
define('RED', '🔴', true);  //user plays red 1
define('BLUE', '🔵', true); //bot plays blue 2

require_once('../../mysqli_connect.php');

$website = 'https://api.telegram.org/bot'.$token;


function addStone ($field, $col, $player) {
  for ($row = 0; $row < count($field); $row++)
    if ($field[$row][$col] == 0) {
      $field[$row][$col] = $player;
      return $field;
    }//if
  return FALSE;
}//addStone

function apiRequest ($methode) {
  return file_get_contents($GLOBALS[website].'/'.$methode);
}//apiRequest

function bestmove ($r) {
  $best = 0;
  $w = -10;
  for ($x = 0; $x < count($r); $x++)
    if ($r[$x][1] > $w){
      $best = $r[$x][0];
      $w = $r[$x][1];
    }//if
  return $best;
}//bestmove

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

function decField ($encField) {
  $rows = explode('|', $encField);
  for ($row = 0; $row < count($rows); $row++)
    $field[$row] = str_split($rows[$row]);
  return $field;
}//decField

function encField ($field) {
  for ($row = 0; $row < count($field); $row++) {
    for ($col = 0; $col < count($field[0]); $col++)
      $out .= $field[$row][$col];
    if ($row < count($field)-1)
      $out .= '|';
  }//for
  return $out;
}//encField

function findOpponent($chatId) {
  global $dbc;
  $opponents = mysqli_fetch_array(@mysqli_query(
    $dbc, 'SELECT * FROM `bot_make4bot_s2` WHERE `chat_id` != '.$chatId.' AND
    `time` > DATE_SUB(CURRENT_TIMESTAMP,INTERVAL 90 MINUTE) ORDER BY `time` ASC LIMIT 1'
  ));
  if (isset($opponents)) {
    @mysqli_query(
      $dbc, 'DELETE FROM `bot_make4bot_s2` WHERE `chat_id` = '.$opponents['chat_id'].' AND
      `time` = \''.$opponents['time'].'\' LIMIT 1'
    );
    @mysqli_query(
      $dbc, 'INSERT INTO `bot_make4bot_games` (`player1`, `player2`, `last_move`)
      VALUES ('.$chatId.', '.$opponents['chat_id'].', CURRENT_TIMESTAMP)'
    );
  }//if
  else $opponents['chat_id'] = 0;
  return $opponents['chat_id'];
}//findOpponent

function inlineKeys ($buttons, $chatId, $msg) {
  $keyboard = json_encode(array('inline_keyboard' => $buttons));
  apiRequest('sendmessage?parse_mode=Markdown&chat_id='.$chatId.'&text='.urlencode($msg).'&reply_markup='.$keyboard);
}//inlineKeys

function playBot ($field) {

  //level 1
  //if bot can win
  foreach (possibleCols($field) as $option)
    if (checkWin(0, addStone($field, $option, 2)) == 2)
      return addStone($field, $option, 2);
    //if bot can prevent win of user
  foreach (possibleCols($field) as $option)
    if (checkWin(0, addStone($field, $option, 1)) == 1)
      return addStone($field, $option, 2);

  //level 2
  foreach (possibleCols($field) as $myoption) {
    $usersfield = addStone($field, $myoption, 2);
    foreach (possibleCols($usersfield) as $useroption) {
      $mynewfield = addStone($usersfield, $useroption, 1);
      foreach (possibleCols($mynewfield) as $mynewoption)
        if (checkWin(0, addStone($mynewfield, $mynewoption, 2)) == 2)
          return addStone($field, $myoption, 2);
    }//foreach
  }//foreach

  //return addStone($field, bestmove(rating($field)), 2);

  //level 0
  // place stones random
  do {
    $newfield = addStone($field, rand(0,count($field[0])-1), 2);
    if (count(possibleCols($field)) == 0)
      return FALSE;
  }//do
  while ($newfield == FALSE);
  return $newfield;
}//playBot

function possibleCols ($field) {
  for ($col = 0; $col < count($field[0]); $col++)
    if ($field[(count($field)-1)][$col] == 0)
      $out[] = $col;
  return $out;
}//possibleCols

function pr($r){
  for ($x = 0; $x < count($r); $x++)
    $out .= $r[$x][0].' -> '.$r[$x][1].PHP_EOL;
}//pr

function printField($chatId, $field) {
  $out = '`';
  for ($row = (count($field)-1); $row >= -1; $row--) {
    for ($col = 0; $col < count($field[0]); $col++) {
      if ($row == -1) {
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

function printSelection ($chatId, $field, $msg) {
  foreach (possibleCols($field) as $col)
    $but[0][] = array(
      'text' => ' '.strval($col+1).' ',
      'callback_data' => '/col '.strval($col).' '.encField($field)
    );
  inlineKeys($but, $chatId, $msg);
}//printSelection

function rating ($field) {
  foreach (possibleCols($field) as $option)   //initialize
    $rating[] = array($option, 0);
  for ($x = 0; $x < count($rating); $x++) {
    if (checkWin(0, addStone($field, $rating[$x][0], 2)) == 2)
      $rating[$x][1] = 10;
    if (checkWin(0, addStone($field, $rating[$x][0], 1)) == 1)
      $rating[$x][1] = 9;
  }//for
  pr($rating);
  return $rating;
}//rating

function register($chatId) {
  global $dbc;
  @mysqli_query($dbc,
    'INSERT INTO `bot_make4bot_s2` (`chat_id`, `time`) VALUES ('.$chatId.', CURRENT_TIMESTAMP)'
  );
}//register

function sendMsg ($chatId, $msg, $mode) {
  if ($mode == '') $msg = urlencode($msg);
  apiRequest('sendmessage?parse_mode='.$mode.'&chat_id='.$chatId.'&text='.$msg);
}//sendMsg

?>
