<?php

// download the data
//$HTML = file_get_contents('http://www.majorleaguedodgeball.com.au/footscray-mixed-league-wednesdays/');
$HTML = file_get_contents('http://www.majorleaguedodgeball.com.au/bulleen-mixed-league-tuesdays/');
//$HTML = file_get_contents('index.html');

// convert into readable data
$DOC = new DOMDocument;
@$DOC->loadHTML($HTML);

$ITEMS = $DOC->getElementsByTagName('p');

$CURRENT_ROUND = 0;

function get_team_from_lineup($LINEUP) {
  $TEAM1 = '';
  $TEAM2 = '';
  
  // loop through each word until it finds num - num
  $TEXT = explode(' ', str_replace(' ', ' ', $LINEUP));  // need to sanatise the input as sometimes it's chr(32) and others chr(160)
  print_r($TEXT);

  
  foreach ($TEXT as $INDEX => $WORD) {
    if ($INDEX == 0) {
      // if it's the first word, it's the time
      $GAME['time'] = substr($WORD, 0, strlen($WORD) - 1);
    } elseif ((is_numeric($WORD)) && ($TEXT[$INDEX + 1] == '–') && (is_numeric($TEXT[$INDEX + 2]))) {
      // if we have <num> - <num> it's the totals
      
      // find the first team name and build the array
      for ($i = 1; $i < $INDEX; $i++) {
        $TEAM1 .= ' '. $TEXT[$i];
      }
      $GAME['1']['team'] = trim($TEAM1);
      $GAME['1']['score'] = $WORD;
      
      // find the second team name and build the array
      for ($i = $INDEX + 3; $i < count($TEXT); $i++) {
        $TEAM2 .= ' '. $TEXT[$i];
      }
      $GAME['2']['team'] = trim($TEAM2);
      $GAME['2']['score'] = $TEXT[$INDEX + 2];
    } elseif ($WORD == 'vs.') {
      // if we have vs. in the text (games not played yet)
      
      // find the first team name and build the array
      for ($i = 1; $i < $INDEX; $i++) {
        $TEAM1 .= ' '. $TEXT[$i];
      }
      $GAME['1']['team'] = trim($TEAM1);
      $GAME['1']['score'] = -1;
      
      // find the second team name and build the array
      for ($i = $INDEX + 1; $i < count($TEXT); $i++) {
        $TEAM2 .= ' '. $TEXT[$i];
      }
      $GAME['2']['team'] = trim($TEAM2);
      $GAME['2']['score'] = -1;
    }
  }
  
  // return the game array
  return $GAME;
}

foreach ($ITEMS as $ITEM) {
  // generate a text array
  $TEXT = explode(' ', $ITEM->textContent);

  if (strtolower($TEXT[0]) == 'round') {
    // check if we found a round title
    echo '> Found round '. $CURRENT_ROUND .' @ '. date('r', $ROUND[$CURRENT_ROUND]['date']) . PHP_EOL;
    
    // push the data to array
    $CURRENT_ROUND = $TEXT[1];
    $ROUND[$CURRENT_ROUND]['round'] = $CURRENT_ROUND;
    $ROUND[$CURRENT_ROUND]['date'] = @strtotime($TEXT[3] .' '. $TEXT[4] .' '. substr($TEXT[5], 0, strlen($TEXT[5] - 1)));
    
  } elseif (count(explode(':', $TEXT[0])) < 2) {
    // check if we have valid data
    continue;
  
  } elseif ($CURRENT_ROUND > 0) {
    // check if we are in a round
    echo $ITEM->textContent.PHP_EOL;
    
    // get the game data
    $ROUND[$CURRENT_ROUND]['games'][] = get_team_from_lineup($ITEM->textContent);
  }
  
}

  print_r($ROUND);
// check the rounds

// check for changed data in previous rounds
// store the win/loss if applicable
// store the new data (next to the old data with updates)
// notify updates































?>