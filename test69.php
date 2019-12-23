<?php
/*
69 ATMを作成しよう
コマンドラインから実行すること
要件定義
・残額、入金、引き出しの機能を実装
実際にATMに必要な機能をリストアップして、ご自由に開発してみてください！
*/
//人認証しない
//預かり上限額、出金上限額を設定した

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
    global $error_count;
    if($error_count % 6 === 0) {
        echo '〜ヘルプ！〜【' . ESCAPE . '】キーでメニューに戻ります。' . PHP_EOL;
        //エラーカウント、リセット
        $error_count = 1;
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
            global $error_count;
            $error_count++;
            echo '入金額を入力してください。' . PHP_EOL;
            return input('deposit');
        }
    }

    if($type === 'withdraw') {
        $check = checkWithdraw($input);
        if(!$check) {
            global $error_count;
            $error_count++;
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
    /* 機能しなかったバリデーションチェック
    if(is_numeric(!$input)) {   
        echo 'エラー（numeric）！金額を入力してください。' . PHP_EOL;
        return false;
    }

    ●「$input」が、stringで来るので、「is_numeric」機能せず
    ●「checkDeposit(int $input)」にすれば良いのでは？
    数字以外を入力した時、
    「Fatal error: Uncaught TypeError: Argument 1 passed to checkDeposit() must be of the type integer, string given,」
    でエラー終了してしまうので、ダメ。
    ●「数字＋数字以外」を通してしまう ex:「12en」は「12」と認識される
    マイナスや、限度額を超える場合はエラーに出来るので、良しとした

    上記内容は、checkWithdraw($input)も同様なので、省略
    */

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
echo '青空銀行へようこそ！ご希望のメニュー番号を入力してください。' . PHP_EOL;
//初期金額設定
$money = 800000;
$error_count = 1;
atm();
?>