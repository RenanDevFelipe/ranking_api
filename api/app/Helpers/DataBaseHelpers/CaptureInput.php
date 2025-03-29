<?php 

class GetFileContens {
    public function FileContets(){
        $input = file_get_contents("php://input");
        $data = json_decode($input, true);

        return $data;
    }
}

?>