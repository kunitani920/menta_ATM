<?php

class MoneyValidation {
    public function check($input) {
        if ($input <= 0) {
            //空白もここでエラーになる
            return false;
        }

        return true;
    }
}

?>