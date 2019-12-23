<?php

const VICTORY_MAX_COUNT = 3;
const ROCK = 'グー';
const SCISSORS = 'チョキ';
const PAPER = 'パー';
const END_GAME_KEY = 'ゲームを止める';
const COMMAND_LIST = array(
    1 => ROCK,
    2 => SCISSORS,
    3 => PAPER,
    9 => END_GAME_KEY
);

function game($victory = 0, $com_victory = 0) {
    $myhand = selectHand();
    if(COMMAND_LIST[$myhand] === END_GAME_KEY) {
        return gameEnd();
    }
    $comhand = getComHand();
    echo 'あなた：' . COMMAND_LIST[$myhand];
    echo ' COM：' . COMMAND_LIST[$comhand] . PHP_EOL;
    
    $result = judge($myhand, $comhand);
    show($result);
    if($result === 1) {
        $com_victory++;
    } elseif($result === 2) {
        $victory++;
    }

    if($victory === VICTORY_MAX_COUNT || $com_victory === VICTORY_MAX_COUNT) {
        showResult($victory, $com_victory);

        $cont = selectContinue();

        if(COMMAND_LIST[$cont] === END_GAME_KEY) {
            return gameEnd();
        }

        return game();
    }
    
    return game($victory, $com_victory);
}

function selectHand() {
    foreach(COMMAND_LIST as $key => $value) {
        echo $key . ',' . $value . PHP_EOL;
    }
    $input = trim(fgets(STDIN));
    $check = check($input);
    if(!$check) {
        return selectHand();
    }
    return (int)$input;   
}

function getComHand() {
    return mt_rand(1, 3);
}

function judge($myhand, $comhand) {
    $result = ($myhand - $comhand + 3) % 3;
    return $result;
}

function show($result) {
    if($result === 0) {
        echo 'あいこ' . PHP_EOL;
    } elseif($result === 1) {
        echo '負け' . PHP_EOL;
    } elseif($result === 2) {
        echo '勝ち' . PHP_EOL;
    }
    return;
}

function showResult($victory, $com_victory) {
    if($victory === VICTORY_MAX_COUNT) {
        echo sprintf('%d-%d：あたなの勝ち！', $victory, $com_victory) . PHP_EOL;
    } elseif($com_victory === VICTORY_MAX_COUNT) {
        echo sprintf('%d-%d：あたなの負け。', $victory, $com_victory) . PHP_EOL;
    }
    return;
}

function selectContinue(){
    echo '1~3,もう１度遊ぶ　　9,ゲームを止める' .PHP_EOL;
    $input = trim(fgets(STDIN));
    $check = check($input);
    if(!$check) {
        return selectContinue();
    }
    return (int)$input;
}

function gameEnd(){
    echo 'ゲームを終了します' . PHP_EOL;
    exit();
}     

function check($input) {
    if(COMMAND_LIST[$input]) {
        return true;
    }
    echo 'Error!指定された数字を入力してください。' . PHP_EOL;
    return false;
}

echo 'ジャンケンゲームでCOMと勝負！先に３勝すれば勝ちです。' . PHP_EOL;
echo '数字を入力してください。' . PHP_EOL;
game();

?>