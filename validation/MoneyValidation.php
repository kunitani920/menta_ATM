<?php
//ver2.BaseValidationを継承。
require_once 'BaseValidation.php';

class MoneyValidation extends BaseValidation {
    public function check($input) {
        if($input === '') {
            $msg = 'エラー！入力が確認出来ませんでした。';
            $this->addErrorMessage($msg);
        }

        if(!is_numeric($input)) {
            $msg = 'エラー！金額は数字で入力してください。';
            $this->addErrorMessage($msg);
        }

        if ($input <= 0) {
            $msg = 'エラー！１以上が必要です。【例】10000';
            $this->addErrorMessage($msg);
            return false;
        }

        return true;
    }
}

?>