<?php
//ver2.BaseValidationを継承。
require_once 'BaseValidation.php';

class IdValidation extends BaseValidation {
    public function check($input) {
        if($input === '') {
            $msg = 'エラー！入力が確認出来ませんでした。';
            $this->addErrorMessage($msg);
        }
        
        if (!is_numeric($input)) {
            $msg = 'エラー！IDは数字です。';
            $this->addErrorMessage($msg);
        }

        //ユーザーリストに存在するidかチェック
        if(!User::checkUserList($input)) {
            $msg = 'エラー！そのIDは存在しません。';
            $this->addErrorMessage($msg);
        }

        //エラーがある場合
        if($msg) {
            return false;
        }

        return true;
    }
}

?>