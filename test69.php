<?php
/*
69 ATMを作成しよう
コマンドラインから実行すること
要件定義
・残額、入金、引き出しの機能を実装
実際にATMに必要な機能をリストアップして、ご自由に開発してみてください！
*/
//ver2.人認証（ログイン）機能追加。2つのclassをrequireして実装
//ver3.ユーザー数追加。ユーザー毎に、name/money（残高）を持たせる
//入出金結果をmoneyに反映する、残高履歴管理はデータベースを絡めた方が良さそうなので、やめた

const MENU_SHOW = '【1】残高照会 【2】入金 【3】出金 【9】終了';
const MONEY_SHOW = '残高照会';
const MONEY_IN = '入金';
const MONEY_OUT = '引き出し';
const END_ATM_KEY = '終了'; 
const ATM_MENU = array(
    1 => MONEY_SHOW,
    2 => MONEY_IN,
    3 => MONEY_OUT,
    9 => END_ATM_KEY
);
//預かり上限額
const MONEY_MAX = 10000000;
//1回の出金限度額（本来は1日の限度額だが、今回はパス）
const WITHDRAW_MAX = 500000;
//Escキー
const ESCAPE = 'q';
//ログインエラー許容回数
const ERROR_LOGIN = 3;
//ユーザー数
const USER_MAX = 2;
//class User
const NAME = 'name';
const MONEY = 'money';

//基本メニュー
function atm() {
    echo MENU_SHOW . PHP_EOL;
    $select_menu = input('menu');
    
    //不正入力
    if(!$select_menu) {
        return atm();
    }

    //【1】残高照会
    if(ATM_MENU[$select_menu] === MONEY_SHOW) {
        return atmShow();
    }

    //【2】入金
    if(ATM_MENU[$select_menu] === MONEY_IN) {
        return atmDeposit();
    }

    //【3】出金
    if(ATM_MENU[$select_menu] === MONEY_OUT) {
        return atmWithdraw();
    }

    //【9】終了
    if(ATM_MENU[$select_menu] === END_ATM_KEY) {
        return atmEnd();
    }
}

//入力
function input($type) {
    //入金・出金で5回エラーの度、ESCAPEキーの提示
    global $error_money_count;
    if($error_money_count % 6 === 0) {
        echo '〜ヘルプ！〜【' . ESCAPE . '】キーでメニューに戻ります。' . PHP_EOL;
        //エラーカウント、リセット
        $error_money_count = 1;
    }

    $input = trim(fgets(STDIN));

    if($input === ESCAPE) {
        return atm();
    }

    if($type === 'menu') {
        $check = checkMenu($input);
        if(!$check) {
            echo 'エラー！ご希望のメニュー番号を入力してください。' . PHP_EOL;
            echo MENU_SHOW . PHP_EOL;
            return input('menu');
        }
    }

    if($type === 'deposit') {
        $check = checkDeposit($input);
        if(!$check) {
            global $error_money_count;
            $error_money_count++;
            echo '入金額を入力してください。' . PHP_EOL;
            return input('deposit');
        }
    }

    if($type === 'withdraw') {
        $check = checkWithdraw($input);
        if(!$check) {
            global $error_money_count;
            $error_money_count++;
            echo '出金額を入力してください。' . PHP_EOL;
            return input('withdraw');
        }
    }

    return $input;
}

//選択メニューチェック
function checkMenu($input) {
    if(ATM_MENU[$input]) {
        return true;
    }
    return false;
}

//入金チェック
function checkDeposit($input) {
    if($input <= 0) {
        //空白もここでエラーになる
        echo 'エラー！金額を入力してください。' . PHP_EOL;
        return false;
    }

    global $money;
    if(MONEY_MAX < ($input + $money)) {
        echo 'エラー！10,000,000円までしかお預かり出来ません。' . PHP_EOL;
        echo '残高｜¥ ';
        echo number_format($money) . PHP_EOL;
        echo '入金可能額｜¥ ';
        echo number_format(MONEY_MAX - $money) . PHP_EOL;
        return false;
    }

    return true;
}

//出金チェック
function checkWithdraw($input) {
    //空白もここでエラーになる
    if($input <= 0) {
        echo 'エラー！金額を入力してください。' . PHP_EOL;
        return false;
    }
    
    global $money;
    if(($money - $input) < 0) {
        echo 'エラー！残高を超えています。' . PHP_EOL;
        echo '残高｜¥ ';
        echo number_format($money) . PHP_EOL;
        return false;
    }

    if(WITHDRAW_MAX < $input) {
        echo 'エラー！出金限度額（50万）を超えています。' . PHP_EOL;
        return false;
    }

    return true;
}

//残高照会
function atmShow() {
    global $money;
    echo date('Y-m-d | 残高 |¥ ');
    echo number_format($money) .PHP_EOL;
    return atm();
}

//入金
function atmDeposit() {
    global $money;
    if($money === MONEY_MAX) {
        echo 'これ以上お預かり出来ません：お預かり限度額（1,000万）' . PHP_EOL;
        return atm();
    }

    echo '入金額を入力してください。' . PHP_EOL;
    $deposit_money = input('deposit');
    $money += $deposit_money;
    echo number_format($deposit_money);
    echo '円お預かりしました。' . PHP_EOL;
    return atm();
}

//出金
function atmWithdraw() {
    echo '出金額を入力してください。' . PHP_EOL;
    $withdraw_money = input('withdraw');
    global $money;
    $money -= $withdraw_money;
    echo number_format($withdraw_money);
    echo '円です。取り忘れにご注意ください。' . PHP_EOL;
    return atm();
}

//終了
function atmEnd() {
    echo 'ご利用、ありがとうございました。' . PHP_EOL;
    exit();
}

//スタート
//ユーザーの設定、残高追加
require 'test69_user.php';
$tanaka = new User();
$tanaka->setId('123') ;
$tanaka->setPass('pass') ;
$tanaka->setName('田中') ;
$tanaka->setMoney('500000') ;
//$userを二次元配列にして、getUserの値を格納する
$user = array();
$user[1] = $tanaka->getUser();
//ユーザーの設定２
$hara = new User();
$hara->setId('456') ;
$hara->setPass('word') ;
$hara->setName('原') ;
$hara->setMoney('1000000') ;
$user[2] = $hara->getUser();
//ログイン
$error_login_count = 0;
require 'test69_atm.php';
$atm = new Atm();

for($i = 0; $i < ERROR_LOGIN; $i++) {
    $atm->setId();
    $atm->setPass();
    for($j = 1; $j <= USER_MAX; $j++) {
        $login[$j] = $atm->collation($user[$j]);
        if($login[$j]) {
            break;
        }
    }
    if($login[$j]) {
        break;
    }
    echo 'ユーザーIDかパスワードが違います。' . PHP_EOL;
    $error_login_count++;
}

//ログイン、規定回数失敗
if($error_login_count === ERROR_LOGIN) {
    echo '端末をロックしました。今日は利用出来ません。' . PHP_EOL;
    exit();
}

//ログイン成功。$login[$j]がユーザー情報
echo $user[$j][NAME] . '様、青空銀行へようこそ！ご希望のメニュー番号を入力してください。' . PHP_EOL;
//初期金額設定
$money = $user[$j][MONEY];
$error_money_count = 1;
atm();

?>