<?php 

class ResponseHelper {
    public static function jsonResponse($data) {
        header("Content-Type: application/json");
        echo json_encode($data);
        exit;
    }
}

?>