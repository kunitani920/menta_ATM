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
//ver5.requireをTOPへ。ログイン方法変更。ESCキー表示までのエラー回数を定数へ。
//ver6.ATM_MENUの中と、mainの基本メニューの定数に、self::を付加。menu,deposit,withdraw,balanceを定数化。inputのif文をswitch文へ。
//ver7.ログインエラー回数をプロパティ管理。ログイン入力をinputメソッドにまとめ。入力値を半角数字に自動変更。バリデーションチェックを別のクラス化。
//ver8.バリデーションチェックを、前回作成したクラスに集約＆staticからインスタンスに呼び出し方法変更。
//ログイン方法と、ESCAPEキーの処理について、致命的エラー発見。$login変数で、ログイン管理して解決。
//ver9.エラーメッセージ表示、ログイン状態切替をメソッド化。ログイン状態、定数化。終了前、ログアウトを追加。requireをrequire_onceへ。
//カプセル化で、mainと__construct以外、private化。

require_once 'Atm_user.php';
require_once 'validation/MenuValidation.php';
require_once 'validation/MoneyValidation.php';
require_once 'validation/IdValidation.php';
require_once 'validation/PasswordValidation.php';

class Atm {
    //ATMメニュー
    const MENU_SHOW = '【1】残高照会 【2】入金 【3】出金 【9】終了';
    const MONEY_SHOW = '残高照会';
    const MONEY_IN = '入金';
    const MONEY_OUT = '引き出し';
    const END_ATM_KEY = '終了'; 
    const ATM_MENU = array(
        1 => self::MONEY_SHOW,
        2 => self::MONEY_IN,
        3 => self::MONEY_OUT,
        9 => self::END_ATM_KEY
    );
    
    const MENU = 'menu';
    const DEPOSIT = 'deposit';
    const WITHDRAW = 'withdraw';
    const BALANCE = 'balance';

    //預かり上限額
    const MONEY_MAX = 10000000;
    //1回の出金限度額（本来は1日の限度額だが、今回はパス）
    const WITHDRAW_MAX = 500000;
    //Escキー
    const ESCAPE = 'q';
    //Escキー表示までのエラー回数(+1)
    const ERROR_INPUT = 6;
    //ログイン状態
    const LOGIN_STATUS_TRUE = 1;
    const LOGIN_STATUS_FALSE = 0;
    //ログインエラー許容回数
    const ERROR_LOGIN = 3;
    //ユーザー数
    const USER_MAX = 2;
    
    private $user; //ログイン成功ユーザー
    private $login = self::LOGIN_STATUS_FALSE; //ログイン状態初期値
    private $error_login_count = 0;  //ログインエラーカウント変数
    private $error_money_count = 1;  //入出金エラーカウント変数

    public function __construct()
    {
        echo '青空銀行へようこそ。' . PHP_EOL;
        //ユーザー選択｜成功なら$userに情報が入りmainスタート、失敗なら終了
        $this->login();
        echo $this->user['name'] . '様、青空銀行へようこそ！ご希望のメニュー番号を入力してください。' . PHP_EOL;
    }

    private function login()
    {       
        //ログイン、規定回数失敗
        if($this->error_login_count === self::ERROR_LOGIN) {
            echo '端末をロックしました。今日は利用出来ません。' . PHP_EOL;
            exit();
        }

        //ID入力
        echo 'ユーザーIDを入力してください。' . PHP_EOL;
        $id = $this->input('id');
        
        //ログイン済なら終了
        if($this->isLogin()) {
            return;
        }

        //$id でユーザー情報を取得
        $this->user = User::getUserById($id);

        //パスワード入力
        echo 'パスワードを入力してください。' . PHP_EOL;
        $password = $this->input('password');

        //ログイン成功
        $this->login = self::LOGIN_STATUS_TRUE;
        return;
    }

    //ログイン状態管理
    private function isLogin() {
        if($this->login === self::LOGIN_STATUS_TRUE) {
            return true;
        }
        return false;
    }

    //------------ログイン処理、ココまで--------------
    //基本メニュー
    public function main()
    {
        echo self::MENU_SHOW . PHP_EOL;
        $select_menu = $this->input(self::MENU);

        if (self::ATM_MENU[$select_menu] === self::MONEY_SHOW) {
            return $this->atmShow();
        }

        //【2】入金
        if (self::ATM_MENU[$select_menu] === self::MONEY_IN) {
            return $this->atmDeposit();
        }

        //【3】出金
        if (self::ATM_MENU[$select_menu] === self::MONEY_OUT) {
            return $this->atmWithdraw();
        }

        //【9】終了
        if (self::ATM_MENU[$select_menu] === self::END_ATM_KEY) {
            return $this->atmEnd();
        }
    }

    //入力
    private function input($type)
    {
        if ($this->error_money_count % self::ERROR_INPUT === 0) {
            echo '〜ヘルプ！〜【' . self::ESCAPE . '】キーでメニューに戻ります。' . PHP_EOL;
            //エラーカウント、リセット
            $this->error_money_count = 1;
        }
        
        $input = trim(fgets(STDIN));
        $input = mb_convert_kana($input, 'n');   //全角数字→半角数字へ
        
        if ($this->login === self::LOGIN_STATUS_TRUE && $input === self::ESCAPE) {  //ログインしる場合のみ発動
            return $this->main();
        }
        
        switch($type) {
            case 'id' :
                $validation = new IdValidation;
                $check = $validation->check($input);
                if(!$check) {
                    $error_messages = $validation->getErrorMessages();
                    $this->showErrorMessages($error_messages);
                    $this->error_login_count++;
                    return $this->login();
                }
            break;

            case 'password' :
                $validation = new PasswordValidation;
                $check = $validation->check($input);
                if(!$check) {
                    $error_messages = $validation->getErrorMessages();
                    $this->showErrorMessages($error_messages);
                    $this->error_login_count++;
                    return $this->login();
                }
                
                //パスワードチェック
                if($this->user['password'] !== $input) {
                    echo 'エラー！パスワードが一致しません。' .PHP_EOL;
                    $this->error_login_count++;
                    return $this->login();
                }
            break;

            case self::MENU :
                $validation = new MenuValidation;
                $check = $validation->check($input);
                if (!$check) {
                    $error_messages = $validation->getErrorMessages();
                    $this->showErrorMessages($error_messages);
                    return $this->main();
                }
            break;

            case self::DEPOSIT :
                $validation = new MoneyValidation;
                $check = $validation->check($input);
                if (!$check) {
                    $error_messages = $validation->getErrorMessages();
                    $this->showErrorMessages($error_messages);
                    $this->error_money_count++;
                    return $this->input(self::DEPOSIT);
                }
                
                if (self::MONEY_MAX < ($input + $this->user[self::BALANCE])) {
                    $this->error_money_count++;
                    echo 'エラー！10,000,000円までしかお預かり出来ません。' . PHP_EOL;
                    echo '残高｜¥ ';
                    echo number_format($this->user[self::BALANCE]) . PHP_EOL;
                    echo '入金可能額｜¥ ';
                    echo number_format(self::MONEY_MAX - $this->user[self::BALANCE]) . PHP_EOL;
                    return $this->input(self::DEPOSIT);
                }
            break;
            
            case self::WITHDRAW :
                $validation = new MoneyValidation;
                $check = $validation->check($input);
                if (!$check) {
                    $error_messages = $validation->getErrorMessages();
                    $this->showErrorMessages($error_messages);
                    $this->error_money_count++;
                    return $this->input(self::WITHDRAW);
                }
                
                if (($this->user[self::BALANCE] - $input) < 0) {
                    $this->error_money_count++;
                    echo 'エラー！残高を超えています。' . PHP_EOL;
                    echo '残高｜¥ ';
                    echo number_format($this->user[self::BALANCE]) . PHP_EOL;
                    return $this->input(self::WITHDRAW);
                }
                
                if (self::WITHDRAW_MAX < $input) {
                    $this->error_money_count++;
                    echo 'エラー！出金限度額（50万）を超えています。' . PHP_EOL;
                    return $this->input(self::WITHDRAW);
                }
            break;
        }

        return $input;
    }

    //エラーメッセージ表示
    private function showErrorMessages($error_messages) {
        foreach($error_messages as $error_message) {
            echo $error_message . PHP_EOL;
        }
    }

    //残高照会
    private function atmShow()
    {
        echo date('Y-m-d | 残高 |¥ ');
        echo number_format($this->user[self::BALANCE]) .PHP_EOL;
        return $this->main();
    }

    //入金
    private function atmDeposit()
    {
        if ($this->user[self::BALANCE] === self::MONEY_MAX) {
            echo 'これ以上お預かり出来ません：お預かり限度額（1,000万）' . PHP_EOL;
            return $this->main();
        }

        echo '入金額を入力してください。' . PHP_EOL;
        $deposit_money = $this->input(self::DEPOSIT);
        $this->user[self::BALANCE] += $deposit_money;
        echo number_format($deposit_money);
        echo '円お預かりしました。' . PHP_EOL;
        return $this->main();
    }

    //出金
    private function atmWithdraw()
    {
        echo '出金額を入力してください。' . PHP_EOL;
        $withdraw_money = $this->input(self::WITHDRAW);
        $this->user[self::BALANCE] -= $withdraw_money;
        echo number_format($withdraw_money);
        echo '円です。取り忘れにご注意ください。' . PHP_EOL;
        return $this->main();
    }

    //終了
    private function atmEnd()
    {
        echo 'ご利用、ありがとうございました。' . PHP_EOL;
        $this->login = self::LOGIN_STATUS_FALSE;
        exit();
    }
}

$atm = new Atm();
$atm->main();


?>
