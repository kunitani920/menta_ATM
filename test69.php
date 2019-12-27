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
//ver4.全てクラス化。残高をmoney->balanceに変更

//クラス化
class Atm {
    //ATMメニュー
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
    
    public $user; //ログイン成功ユーザー
    public $visitor; //利用客入力内容
    public $error_money_count = 1;  //入出金エラーカウント変数

    public function __construct()
    {
        echo '青空銀行へようこそ。' . PHP_EOL;
        //ユーザー選択｜成功なら$userに情報が入りmainスタート、失敗なら終了
        $this->login();
        echo $this->user['name'] . '様、青空銀行へようこそ！ご希望のメニュー番号を入力してください。' . PHP_EOL;
    }

    public function login()
        {
        //class User情報取得
        require 'test69_user.php';
        $user_instance = new User();
        $user_list = $user_instance->getUser();
        //エラーカウント、初期値セット
        $error_login_count = 0;
        //ログインエラー許容回数分、トライ可能
        for($i = 0; $i < self::ERROR_LOGIN; $i++) {
            $this->setId();
            $this->setPassword();
            //登録ユーザー数分、ID/PASS照合
            for($j = 1; $j <= self::USER_MAX; $j++) {
                $login[$j] = $this->collation($user_list[$j]);
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
        if($error_login_count === self::ERROR_LOGIN) {
            echo '端末をロックしました。今日は利用出来ません。' . PHP_EOL;
            exit();
        }
        //ログイン成功者のID番号を返す
        $this->user = $user_list[$j];
        return;
    }

    public function setId()
    {
        echo 'ユーザーIDを入力してください。' . PHP_EOL;
        $this->visitor['id'] = trim(fgets(STDIN));
    }

    public function setPassword() 
    {
        echo 'パスワードを入力してください。' . PHP_EOL;
        $this->visitor['password'] = trim(fgets(STDIN));
    }

    public function collation($login_try)
    {
        if($this->visitor['id'] === $login_try['id'] && $this->visitor['password'] === $login_try['password']) {
            return true;
        }
        return false;
    }
    //------------ログイン処理、ココまで--------------
    //基本メニュー
    public function main()
    {
        echo self::MENU_SHOW . PHP_EOL;
        $select_menu = $this->input('menu');
        
        //【1】残高照会
        //self::ATM_MENU[$select_menu] = MONEY_SHOW
        //self::MONEY_SHOW = 残高照会
        //なので、右辺には「self::」をつけない
        //以下のメニューも同様
        if (self::ATM_MENU[$select_menu] === MONEY_SHOW) {
            return $this->atmShow();
        }

        //【2】入金
        if (self::ATM_MENU[$select_menu] === MONEY_IN) {
            return $this->atmDeposit();
        }

        //【3】出金
        if (self::ATM_MENU[$select_menu] === MONEY_OUT) {
            return $this->atmWithdraw();
        }

        //【9】終了
        if (self::ATM_MENU[$select_menu] === END_ATM_KEY) {
            return $this->atmEnd();
        }
    }

    //入力
    public function input($type)
    {
        if ($this->error_money_count % 6 === 0) {
            echo '〜ヘルプ！〜【' . self::ESCAPE . '】キーでメニューに戻ります。' . PHP_EOL;
            //エラーカウント、リセット
            $this->error_money_count = 1;
        }

        $input = trim(fgets(STDIN));

        if ($input === self::ESCAPE) {
            return $this->main();
        }

        if ($type === 'menu') {
            $check = $this->checkMenu($input);
            if (!$check) {
                echo 'エラー！ご希望のメニュー番号を入力してください。' . PHP_EOL;
                echo self::MENU_SHOW . PHP_EOL;
                return $this->input('menu');
            }
        }
        
        if ($type === 'deposit') {
            $check = $this->checkDeposit($input);
            if (!$check) {
                $this->error_money_count++;
                echo '入金額を入力してください。' . PHP_EOL;
                return $this->input('deposit');
            }
        }
        
        if ($type === 'withdraw') {
            $check = $this->checkWithdraw($input);
            if (!$check) {
                $this->error_money_count++;
                echo '出金額を入力してください。' . PHP_EOL;
                return $this->input('withdraw');
            }
        }

        return $input;
    }

    //選択メニューチェック
    public function checkMenu($input)
    {
        if (self::ATM_MENU[$input]) {
            return true;
        }
        return false;
    }

    //入金チェック
    public function checkDeposit($input)
    {
        if ($input <= 0) {
            //空白もここでエラーになる
            echo 'エラー！金額を入力してください。' . PHP_EOL;
            return false;
        }

        if (self::MONEY_MAX < ($input + $this->user['balance'])) {
            echo 'エラー！10,000,000円までしかお預かり出来ません。' . PHP_EOL;
            echo '残高｜¥ ';
            echo number_format($this->user['balance']) . PHP_EOL;
            echo '入金可能額｜¥ ';
            echo number_format(self::MONEY_MAX - $this->user['balance']) . PHP_EOL;
            return false;
        }

        return true;
    }

    //出金チェック
    public function checkWithdraw($input)
    {
        //空白もここでエラーになる
        if ($input <= 0) {
            echo 'エラー！金額を入力してください。' . PHP_EOL;
            return false;
        }
    
        if (($this->user['balance'] - $input) < 0) {
            echo 'エラー！残高を超えています。' . PHP_EOL;
            echo '残高｜¥ ';
            echo number_format($this->user['balance']) . PHP_EOL;
            return false;
        }

        if (self::WITHDRAW_MAX < $input) {
            echo 'エラー！出金限度額（50万）を超えています。' . PHP_EOL;
            return false;
        }

        return true;
    }

    //残高照会
    public function atmShow()
    {
        echo date('Y-m-d | 残高 |¥ ');
        echo number_format($this->user['balance']) .PHP_EOL;
        return $this->main();
    }

    //入金
    public function atmDeposit()
    {
        if ($this->user['balance'] === self::MONEY_MAX) {
            echo 'これ以上お預かり出来ません：お預かり限度額（1,000万）' . PHP_EOL;
            return $this->main();
        }

        echo '入金額を入力してください。' . PHP_EOL;
        $deposit_money = $this->input('deposit');
        $this->user['balance'] += $deposit_money;
        echo number_format($deposit_money);
        echo '円お預かりしました。' . PHP_EOL;
        return $this->main();
    }

    //出金
    public function atmWithdraw()
    {
        echo '出金額を入力してください。' . PHP_EOL;
        $withdraw_money = $this->input('withdraw');
        $this->user['balance'] -= $withdraw_money;
        echo number_format($withdraw_money);
        echo '円です。取り忘れにご注意ください。' . PHP_EOL;
        return $this->main();
    }

    //終了
    public function atmEnd()
    {
        echo 'ご利用、ありがとうございました。' . PHP_EOL;
        exit();
    }
}

$atm = new Atm();
$atm->main();


?>