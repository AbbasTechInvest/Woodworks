<?php 
require_once("../../../wp-load.php");
if(isset($_POST['ben_email'])){
    try{
        if(email_exists($_POST['ben_email'])){
            $current_user = wp_get_current_user();
            $email = $current_user->user_email;

            if($email == $_POST['ben_email']){
                die(json_encode(["code"=>422, "msg"=>"You cannot gift balance to youself"]));
            }
            else{
                // only acceptable condition
                die(json_encode(["code"=>200, "msg"=>"Exists"]));
            }
        }
        else{
            die(json_encode(["code"=>400, "msg"=>"This email does not exist"]));
        }
    }
    catch(Exception $e) {
        die(json_encode(["code"=>500, "msg"=>$e->getMessage()]));
    }
}
else{
    die(json_encode(["code"=>404, "msg"=>"Not Found"]));
}

