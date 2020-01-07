<?php

class IdValidation {
    public function check($input) {
        if (is_numeric($input)) {
            return true;
        }

        return false;
    }
}

?>