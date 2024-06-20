<?php
	include('../../../wp-load.php');
    // validate token to enter script
    if(!isset($_POST['post_token'])){
        die("{'code':'401', 'msg':'Unauthorised Access'}");
    }
    $token = $_POST['post_token'];
    session_start();
    if(!isset($_SESSION['wallet_pay_token'])){
        die("{'code':'402', 'msg':'Unauthorised Access'}");
    }
    // mismatched token
    if($token != $_SESSION['wallet_pay_token']){
        unset($_SESSION['wallet_pay_token']);
        die("{'code':'403', 'msg':'Unauthorised Access'}");
    }

    // init post details
    if(!isset($_POST['post_type'])){
        die("{'code':'404', 'msg':'post_type not sent'}");
    }
    if(!isset($_POST['post_id'])){
        die("{'code':'404', 'msg':'post_id not sent'}");
    }
    if(!isset($_POST['post_amount'])){
        die("{'code':'404', 'msg':'post_amount not sent'}");
    }

    $booked_seats = 1;
    if(isset($_POST['post_seats']) && ((int)$_POST['post_seats']) > 1){ $booked_seats = $_POST['post_seats']; }

    $post_beneficiary = '';
    if(isset($_POST['post_beneficiary'])){ $post_beneficiary = $_POST['post_beneficiary']; }

    $post_gift_message = '';
    if(isset($_POST['post_gift_message'])){ $post_gift_message = $_POST['post_gift_message']; }

    // trim extra _ added in form
    $post_type = $_POST['post_type'];

    // post ID is beneficiary email if post type is gift
    $post_id = ("gift"==$post_type) ? $post_beneficiary : $_POST['post_id']; //$_POST['post_id'];

    $post_amount = number_format((float) $_POST['post_amount'], 3, '.', '');

    // init user details
    $user = wp_get_current_user();
    $user_id = $user->ID;
    $user_name = $user->user_login;
    $email = $user->user_email;
    $user_whatsapp = get_field('user_whatsapp', "user_".$user->ID);

    // payment flow
    error_log("user #$user_id initiaitng payment for $post_type - $post_id using Wallet");

    // Prepare the invoice id
    $invoice_id = "INV/". date('dmY') . "/" . $user_id . "/" . rand(1000,10000) . "/" . $booked_seats;

    $gift_desc = ("gift" == $post_type) ? "\nBalance gifted to $post_beneficiary" : "";
    
    // TODO: add validation to check if payment record already exists
    
    // insert payment record
    $payment_record_id = wp_insert_post(array(
        'post_title'=>$invoice_id, 
        'post_type'=>'payments', 
        'post_content'=> "Wallet Token: ". $_SESSION['wallet_pay_token']. $gift_desc,
        'post_status'=>'private'
    ));

    update_field('payments_status', "CAPTURED", $payment_record_id);
    update_field('payments_amount', $post_amount, $payment_record_id);
    update_field('payments_date', date("Y-m-d H:i:s"), $payment_record_id);
    update_field('payments_type', $post_type, $payment_record_id);
    update_field('payments_type_id', $post_id, $payment_record_id);
    update_field('payments_user', $user_id, $payment_record_id);
    update_field('payments_user_whatsapp', $user_whatsapp, $payment_record_id);
    update_field('payments_user_email', $email, $payment_record_id);
    update_field('payments_method', "Wallet", $payment_record_id);

    // 2. Create participant record in the post type
    // 2.1 class
    if("class" == $post_type){
        $rows = get_field('class_participant_details', $post_id);
        $record_exists = false;
        if( $rows ) {
            $record_exists = false;
            foreach( $rows as $row ) {
                if($row['class_participant_id']['ID'] == $user_id){
                    $record_exists = true;
                    break;
                }
            }
        }
        if($record_exists){
            // TODO: create flow for exception
            $order_note = "Please check your order. There seems to be a duplicate booking.";
        }
        else{
            $row = array(
                //'class_participant_invoice_id' => $invoice_id,
                'class_participant_payment' => $payment_record_id,
                'class_participant_num_seats' => $booked_seats,
                'class_participant_id' => $user->ID,
                'class_participant_name' => $user->first_name." ".$user->last_name,
                'class_participant_whatsapp' => $user_whatsapp,
                'class_participant_email' => $email
            );
            
            add_row('class_participant_details', $row, $post_id);
            //echo "<br>added subrow to class<br>";
        }
    }

    // 2.2 multiclass
    if("multiclass" == $post_type){
        $rows = get_field('multiclass_participant_details', $post_id);
        $record_exists = false;
        if( $rows ) {
            $record_exists = false;
            foreach( $rows as $row ) {
                if($row['multiclass_participant_id']['ID'] == $user_id){
                    $record_exists = true;
                    break;
                }
            }
        }
        if($record_exists){
            // TODO: create flow for exception
            $order_note = "Please check your order. There seems to be a duplicate booking.";
        }
        else{
            $row = array(
                //'multiclass_participant_invoice_id' => $invoice_id,
                'multiclass_participant_payment' => $payment_record_id,
                'multiclass_participant_num_seats' => $booked_seats,
                'multiclass_participant_id' => $user->ID,
                'multiclass_participant_name' => $user->first_name." ".$user->last_name,
                'multiclass_participant_whatsapp' => $user_whatsapp,
                'multiclass_participant_email' => $email
            );
            
            add_row('multiclass_participant_details', $row, $post_id);
            //echo "<br>added subrow to multiclass<br>";
        }
    }

    // 2.3 private-session
    if("private-session" == $post_type){
        update_field('private_session_payment_status','Paid', $post_id);
        update_field('private_session_payment_id', $payment_record_id, $post_id);
        //echo "<br>Private session Paid<br>";
    }

    // 2.4 catering
    if("catering" == $post_type){
        update_field('catering_payment_status','Paid', $post_id);
        update_field('catering_payment_id', $payment_record_id, $post_id);
        //echo "<br>Catering session Paid<br>";
    }

    // 2.5 custom-product
    if("custom-product" == $post_type){
        update_field('cp_payment_status','Paid', $post_id);
        update_field('cp_payment_id', $payment_record_id, $post_id);
        //echo "<br>Custom Product Request Paid<br>";
    }

    // 2.6 video
    if("video" == $post_type){
        $rows = get_field('video_purchaser_details', $post_id);
        $record_exists = false;
        if( $rows ) {
            $record_exists = false;
            foreach( $rows as $row ) {
                if($row['video_purchaser_id']['ID'] == $user_id){
                    $record_exists = true;
                    break;
                }
            }
        }
        if($record_exists){
            // TODO: create flow for exception
            $order_note = "Please check your order. There seems to be a duplicate purchase.";
        }
        else{
            $row = array(
                //'video_purchaser_invoice_id' => $invoice_id,
                'video_purchaser_payment' => $payment_record_id,
                'video_purchaser_id' => $user->ID,
                'video_purchaser_name' => $user->first_name." ".$user->last_name,
                'video_purchaser_whatsapp' => $user_whatsapp,
                'video_purchaser_email' => $email
            );
            
            add_row('video_purchaser_details', $row, $post_id);
            //echo "<br>added subrow to video<br>";
        }
    }

    // 2.7 prework
    if("prework" == $post_type){
        $rows = get_field('prework_participant_details', $post_id);
        $record_exists = false;
        if( $rows ) {
            $record_exists = false;
            foreach( $rows as $row ) {
                if($row['prework_participant_id']['ID'] == $user_id){
                    $record_exists = true;
                    break;
                }
            }
        }
        if($record_exists){
            // TODO: create flow for exception
            $order_note = "Please check your order. There seems to be a duplicate booking.";
        }
        else{
            $row = array(
                //'prework_participant_invoice_id' => $invoice_id,
                'prework_participant_payment' => $payment_record_id,
                'prework_participant_num_seats' => $booked_seats,
                'prework_participant_id' => $user->ID,
                'prework_participant_name' => $user->first_name." ".$user->last_name,
                'prework_participant_whatsapp' => $user_whatsapp,
                'prework_participant_email' => $email
            );
            
            add_row('prework_participant_details', $row, $post_id);
            //echo "<br>added subrow to prework<br>";
        }
    }

    // 2.8 gift
    // For post type gift, post-id is beneficiary-id
    if("gift" == $post_type){
        $beneficiary_user = get_user_by('email', $post_id);
        $beneficiary_user_id = $beneficiary_user->ID;

        $balance = get_field('user_wallet_balance', "user_$beneficiary_user_id");
        $balance += $post_amount;
        update_field('user_wallet_balance', $balance, "user_{$beneficiary_user_id}");
        error_log("Invoice: #{$invoice_id}, Beneficiary User: {$beneficiary_user->ID}, Beneficiary Name: {$beneficiary_user->first_name} {$beneficiary_user->last_name}, Recharge amount: {$post_amount}, Beneficiary Wallet Balance: {$balance}");
        
        // send email to beneficiary
        $subject = "You have received a GIFT! | Woodworks";
        $body = "Congratulations {$beneficiary_user->first_name}!<br>";
        $body .= ucfirst($user->first_name);
        $body .= " has gifted {$post_amount} KD to your wallet balance.";
        $body .= "<br>You can view your wallet balance <a href='";
        $body .= get_site_url();
        $body .= "/my-account/wallet'>here</a><br>";
        $body .= $post_gift_message;
		$headers = array('Content-Type: text/html; charset=UTF-8', 'From: info@techinvestkw.com');

        wp_mail($post_id, $subject, $body, $headers);
    }

    // 3. Send email to customer
    $subject = "#{$invoice_id} Payment Confirmation | Woodworks";
    // make exception to permalink for wallet recharge
    $body = "Dear {$user->first_name},<br>The payment for ";
    $body .= ucfirst($post_type);
    $body .= " has been purchased using Wallet Balance.";
    $body .= "<br>You can view your booking <a href='";
    $body .= get_post_permalink($post_id);
    $body .= "'>here</a><br>";
    $body .= "<br>Amount deducted from Wallet Balance is {$post_amount}<br>";
    $url = $_SERVER['SERVER_NAME'];
    $headers = array('Content-Type: text/html; charset=UTF-8', 'From: info@techinvestkw.com');

    wp_mail($email, $subject, $body, $headers);

    // 4. Send Whatsapp notification for payment
    // TODO: Integrate Whatsapp Plugin
    // TODO: Attach pdf invoice

    // 5. Notify admin for received payment
    $subject = "#{$invoice_id} {$user->first_name} paid for {$post_type} #{$post_id}";
    $url = $_SERVER['SERVER_NAME'];
    $body = "Dear Admin,<br>{$user->first_name} {$user->last_name} has paid for <a href='";
    $body .= get_site_url();
    $body .= "'/wp-admin/post.php?post={$post_id}&action=edit'>";
    $body .= ucfirst($post_type);
    $body .= " #{$post_id}</a>";
    $headers = array('Content-Type: text/html; charset=UTF-8', 'From: info@techinvestkw.com');
    $ADMIN_EMAIL = "abbas.kagdi@tech.com.kw"; // TODO: change in Production
    wp_mail($ADMIN_EMAIL, $subject, $body, $headers);

    // update wallet balance
	$balance = get_field('user_wallet_balance', "user_{$user->ID}");
    $balance -= $post_amount;
    update_field('user_wallet_balance', $balance, "user_{$user->ID}");
    error_log("Invoice: #{$invoice_id}, User: {$user->ID}, Name: {$user->first_name}  {$user->last_name}, Deducted from Wallet: {$post_amount}, Wallet Balance: {$balance}");
    
    // cleanup
    unset($_SESSION['wallet_pay_token']);
    session_destroy();

    // exit
    die('{"code":200, "msg":"Payment Successful!"}');
