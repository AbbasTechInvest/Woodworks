<?php
    // init post details
	include('../../../wp-load.php');
    if(!isset($_POST['post_type'])){
        die("{'code':'404', 'msg':'post_type not sent'}");
    }
    if(!isset($_POST['post_id'])){
        die("{'code':'404', 'msg':'post_id not sent'}");
    }
    if(!isset($_POST['post_amount'])){
        die("{'code':'404', 'msg':'post_amount not sent'}");
    }

    $post_seats = 1;
    if(isset($_POST['post_seats'])){ $post_seats = $_POST['post_seats']; }

    $post_beneficiary = '';
    if(isset($_POST['post_beneficiary'])){ $post_beneficiary = $_POST['post_beneficiary']; }

    $gift_message = '';
    if(isset($_POST['post_gift_message'])){ $gift_message = $_POST['post_gift_message']; }

    $post_type = $_POST['post_type'];
    
    // post ID is beneficiary email if post type is gift
    $post_id = ("gift"==$post_type) ? $post_beneficiary : $_POST['post_id']; //$_POST['post_id'];

    $post_amount = number_format((float) $_POST['post_amount'], 3, '.', '');


    // init user details
    $current_user = wp_get_current_user();
    $user_id = $current_user->ID;
    $user_name = $current_user->user_login;
    $email = $current_user->user_email;
    $user_whatsapp = get_field('user_whatsapp', "user_".$current_user->ID);

    error_log("user #$user_id initiaitng payment for $post_type - $post_id");

    // init hesabe class
    $hesabe = new WC_Hesabe();
    $merchantCode = $hesabe->settings['merchantCode'];
    $sandbox = $hesabe->settings['sandbox'];
    $secretKey = $hesabe->settings['secretKey'];
    $ivKey = $hesabe->settings['ivKey'];
    $accessCode = $hesabe->settings['accessCode'];
    $currencyConvert = (!empty($hesabe->settings['currencyConvert']) && 'yes' === $hesabe->settings['currencyConvert']) ? true : false;
    if ($sandbox == 'yes') {
        $apiUrl = WC_HESABE_TEST_URL;
    } else {
        $apiUrl = WC_HESABE_LIVE_URL;
    }

    $notify_url = home_url('/handlepay');

    // Prepare the invoice id
    $invoice_id = "INV/". date('dmY') . "/" . $user_id . "/" . rand(1000,10000) . "/" . $post_seats;
    // session_start();
    // $_SESSION['invoice_id'] = $invoice_id;
    // session_destroy();

    // Encryption class
    $crypto = new WC_Hesabe_Crypt();

    // modify variable for gift custom message
    $email_with_message = $user_email;
    if("gift"==$post_type){
        $email_with_message .= "|$gift_message";
    }

    $post_values = array(
        "merchantCode" => $merchantCode,
        "amount" => $post_amount,
        "responseUrl" => $notify_url,
        "failureUrl" => $notify_url,
        "paymentType" => 0,
        "version" => '2.0',
        "orderReferenceNumber" => $invoice_id,
        "variable1" => $post_type,
        "variable2" => $post_id, // is beneficiary email if post type is gift
        "variable3" => $user_id,
        "variable4" => $user_whatsapp,
        "variable5" => $email_with_message, // modified email to concatenate gift message
        "name" => $user_name,
        "mobile_number" => $user_whatsapp,
        "description" => "Payment for ".ucfirst($post_type) . " #$post_id x $post_seats"
    );

    $post_values['email'] = $user_email;
    
    $post_values['currency'] = 'KWD';
    if ($currencyConvert && get_woocommerce_currency() !== 'KWD') {
        $post_values['currency'] = get_woocommerce_currency();
    }

    $post_string = json_encode($post_values);

    $encrypted_post_string = $crypto->encrypt($post_string, $secretKey, $ivKey);
    $encrypted_post_string = 'data=' . $encrypted_post_string;

    $header = array();
    $header[] = 'accessCode: ' . $accessCode;
    $header[] = 'Access-Control-Allow-Origin: http://localhost:80'; // development

    $checkOutUrl = $apiUrl . '/checkout';

    $curl = curl_init($checkOutUrl);

    curl_setopt($curl, CURLOPT_HEADER, 1);
    curl_setopt($curl, CURLOPT_HTTPHEADER, $header);
    curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, 1);
    curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($curl, CURLOPT_FORBID_REUSE, 1);
    curl_setopt($curl, CURLOPT_FRESH_CONNECT, 1);
    curl_setopt($curl, CURLOPT_POST, 1);
    curl_setopt($curl, CURLOPT_CONNECTTIMEOUT, 12);
    curl_setopt($curl, CURLOPT_TIMEOUT, 30);
    curl_setopt($curl, CURLOPT_POSTFIELDS, $encrypted_post_string);
    $post_response = curl_exec($curl);
    curl_close($curl); // close curl object
    
    list($responsheader, $responsebody) = explode("\r\n\r\n", $post_response, 2);

    $decrypted_post_response = $crypto->decrypt($responsebody, $secretKey, $ivKey);

    $decode_response = json_decode($decrypted_post_response);

    if ($decode_response->status != 1 || !(isset($decode_response->response->data))) {
        $responseMessage = "We can not complete order at this moment, Error Code: " . $decode_response->code . " Details : " . $decode_response->message;
        echo $responseMessage;
        exit;
    }
    $paymentData = $decode_response->response->data;
    //header('Location:' . $apiUrl . '/payment?data=' . $paymentData);
    $response_to_ajax = array("code" => 200, "redirect" => $apiUrl, "paymentData" => $paymentData);
    echo json_encode($response_to_ajax);
    exit;
?>