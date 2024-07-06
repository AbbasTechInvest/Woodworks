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

    // TODO: Remember this
    // post ID is beneficiary email if post type is gift
    $post_id = ("gift"==$post_type) ? $post_beneficiary : $_POST['post_id']; //$_POST['post_id'];

    $post_amount = number_format((float) $_POST['post_amount'], 3, '.', '');

    // init user details
    $user = wp_get_current_user();
    $user_id = $user->ID;
    $user_name = $user->user_login;
    $email = $user->user_email;
    $user_whatsapp = get_field('user_whatsapp', "user_".$user->ID);

    // TODO: change email addresses in Production
    $ADMIN_EMAIL = "info@itswework.com";
    $RECEIVER_EMAIL = $user->user_email; 
    $SENDER_EMAIL = "info@techinvestkw.com";

    $site = get_site_url();

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
    // TODO: Note that for post type gift, post-id is beneficiary-id
    if("gift" == $post_type){
        $beneficiary_user = get_user_by('email', $post_beneficiary); // $post_id is beneficiary email in this case
        $beneficiary_user_id = $beneficiary_user->ID;

        $balance = get_field('user_wallet_balance', "user_$beneficiary_user_id");
        $balance += $post_amount;
        update_field('user_wallet_balance', $balance, "user_{$beneficiary_user_id}");
        error_log("Invoice: #{$invoice_id}, Beneficiary User: {$beneficiary_user->ID}, Beneficiary Name: {$beneficiary_user->first_name} {$beneficiary_user->last_name}, Recharge amount: {$post_amount}, Beneficiary Wallet Balance: {$balance}");
        
        // send email to beneficiary
        $headers = array('Content-Type: text/html; charset=UTF-8', "From: {$SENDER_EMAIL}");
        $subject = "You have received a GIFT! | Woodworks";

        $body = <<<EOD
                <!DOCTYPE html>
                <html lang='en'>
                <head>
                    <meta charset='UTF-8'>
                    <meta name='viewport' content='width=device-width, initial-scale=1.0'>
                    <title>Payment Confirmation</title>
                    <style>
                        body {
                            font-family: Arial, sans-serif;
                            line-height: 1.6;
                            color: #333;
                            max-width: 600px;
                            margin: 0 auto;
                            padding: 20px;
                        }
                        .container {
                            background-color: #f9f9f9;
                            border: 1px solid #ddd;
                            border-radius: 5px;
                            padding: 20px;
                        }
                        .msg{
                            background-color: #fcfcfc;
                            border: 1px solid #000;
                            max-width: 400px; 
                            padding: 20px;
                        }
                        h1 {
                            color: #1f1a4e;
                        }
                        a {
                            color: #0066cc;
                            text-decoration: none;
                        }
                        a:hover {
                            text-decoration: underline;
                        }
                        p{
                            text-align: start;
                        }
                    </style>
                </head>
                <body>
                    <center>
                    <div class='container'>
                        <div class='msg'>
                            <img src='{$site}/wp-content/uploads/2024/03/cropped-single_logo-removebg-preview.png' width='100' alt='Logo' />
                            <h1>Congratulations {$beneficiary_user->first_name}!</h1>
                EOD;

                $body .= "<p>" . esc_html(ucfirst($user->first_name ?? 'A user')) . " " . esc_html(ucfirst($user->last_name ?? '')) . " has gifted " . esc_html($post_amount) . " KD to your wallet balance.</p>";
                $body .= "<p>You can view your wallet balance <a href='{$site}/my-account/wallet'>here</a></p>";
                $body .= "<p>There is a special message for you from " . esc_html(ucfirst($user->first_name ?? 'the sender')) . ":</p>";
                $body .= "<div class='container'>" . wp_kses_post($post_gift_message) . "</div>";

                $body .= <<<EOD
                            <p>Best Regards,<br>WeWork Team</p>
                        </div>
                    </div>
                    </center>
                </body>
                </html>
                EOD;

        wp_mail($post_beneficiary, $subject, $body, $headers); // $post_id is the beneficiary email
    }


    // 3. Display frontend message
    // Define the styles
    $styles = "
    <style scoped>
        table {width: 100%; border-collapse: collapse;}
        th {background-color: #1f1a4e; color: white; padding: 25px;}
        td {padding: 15px; border: 1px solid #ccc;}
        .invoice_container {max-width: 700px; padding: 25px;}
        .center {text-align: center;}
    </style>
    ";

    // Start building the HTML content
    $content = $styles . '
    <div class="invoice_container">
        <div class="center">
            <img src="' . $site . '/wp-content/uploads/2024/03/cropped-single_logo-removebg-preview.png" width="100" />
            <h2>Your Payment was successful</h2>
        </div>
        <table class="center">
            <thead><tr><th colspan="2">Payment Details</th></tr></thead>
            <tbody>
                <tr>
                    <td><strong>Applicant Name</strong></td>
                    <td>' . $user->first_name . ' ' . $user->last_name . '</td>
                </tr>';

        // Add permalink exception for wallet recharge
        if ($post_beneficiary !== $post_id) {
            // other post types
            $content .= '
                <tr>
                    <td><strong>Program Details</strong></td>
                    <td><a href="' . get_post_permalink($post_id) . '">' . get_the_title($post_id) . '</a></td>
                </tr>';
        } else { // post_id is beneficiary email for gift post type
            $content .= '
                <tr>
                    <td><strong>Beneficiary Email</strong></td>
                    <td>' . $post_beneficiary . '</td>
                </tr>
                <tr>
                    <td><strong>Gift Message</strong></td>
                    <td>' . $post_gift_message . '</td>
                </tr>';
        }


    if (in_array($post_id, array("class", "multiclass"))) {
        $content .= '
            <tr>
                <td><strong>Booked Seats</strong></td>
                <td>' . $booked_seats . '</td>
            </tr>';
    }

    $content .= '
            <tr>
                <td><strong>Amount</strong></td>
                <td>' . number_format((float)$post_amount, 3, '.', '') . '</td>
            </tr>
            <tr>
                <td><strong>Payment Type</strong></td>
                <td>Wallet</td>
            </tr>
            <tr>
                <td><strong>Invoice No.</strong></td>
                <td>' . $invoice_id . '</td>
            </tr>
            <tr>
                <td><strong>Transaction Date</strong></td>
                <td>' . date("Y-m-d H:i:s") . '</td>
            </tr>
            <tr>
                <td><strong>Order Note</strong></td>
        <td>Wallet Payment Successful</td>
            </tr>
            </tbody>
        </table>
        <br>
        <div class="center">
            <span><a href="' . $site . '"/my-account">My Account</a> &nbsp;&nbsp;&nbsp; <a href="' . $site . '/terms-and-conditions/">Terms</a></span>
        </div>
    </div>';


    // 4. create pdf report
    require_once('tcpdf/tcpdf.php');
    $pdf = new TCPDF(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);
    $pdf->SetMargins(PDF_MARGIN_LEFT, PDF_MARGIN_TOP, PDF_MARGIN_RIGHT);
    $pdf->SetTitle($invoice_id);
    $pdf->SetAuthor('Itswework');
    $pdf->AddPage();
    $pdf->writeHTML($content, true, false, true, false, '');
    
    $pdf_name = str_replace('/', '_', $invoice_id) . ".pdf";
    $pdf_content = $pdf->Output($pdf_name, 'S');
    $pdf_path = get_stylesheet_directory() . "/invoices/{$pdf_name}";

    // Save the PDF content to a file
    file_put_contents($pdf_path, $pdf_content);
    $attachments = array($pdf_path);


    // 5. Notify admin for received payment
    $headers = array('Content-Type: text/html; charset=UTF-8', "From: {$SENDER_EMAIL}");
    $subject = "{$invoice_id} {$user->user_login} paid for {$post_type} # {$post_id}";
    
    if("gift" !== $post_type){
        $body = "Dear Admin,<br>{$user->user_login} has paid for a {$post_type} <a href='{$site}";
        $body .= "/wp-admin/post.php?post={$post_id}&action=edit'>";
        $body .= " #{$post_id}</a>";
    }
    else{
        $body = "Dear Admin,<br>{$user->user_login} has gifted {$post_amount} to {$post_beneficiary}"; // $post_id is beneficiary email
    }
    
    wp_mail($ADMIN_EMAIL, $subject, $body, $headers, $attachments);


    // 6. Send email to customer
    $headers = array('Content-Type: text/html; charset=UTF-8', "From: {$SENDER_EMAIL}");
    $subject = "{$invoice_id} Payment Confirmation | WeWork";

    $body = "
            <!DOCTYPE html>
            <html lang='en'>
            <head>
                <meta charset='UTF-8'>
                <meta name='viewport' content='width=device-width, initial-scale=1.0'>
                <title>Payment Confirmation</title>
                <style>
                    body {
                        font-family: Arial, sans-serif;
                        line-height: 1.6;
                        color: #333;
                        max-width: 600px;
                        margin: 0 auto;
                        padding: 20px;
                    }
                    .container {
                        background-color: #f9f9f9;
                        border: 1px solid #ddd;
                        border-radius: 5px;
                        padding: 20px;
                    }
                    .msg{
                        background-color: #fcfcfc;
                        border: 1px solid #000;
                        max-width: 400px; 
                        padding: 20px;
                    }
                    h1 {
                        color: #1f1a4e;
                    }
                    a {
                        color: #0066cc;
                        text-decoration: none;
                    }
                    a:hover {
                        text-decoration: underline;
                    }
                    p{
                        text-align: start;
                    }
                </style>
            </head>
            <body>
            <center>
            <div class='container'>
                <div class='msg'>
                    <img src='" . esc_url($site . "/wp-content/uploads/2024/03/cropped-single_logo-removebg-preview.png") . "' width='100' />
                    <h1>Payment Confirmation</h1>
                    <p>Dear " . esc_html($user->first_name) . ",</p>
                    <p>The payment for " . esc_html(ucfirst($post_type)) . " has been purchased using Wallet Balance.</p>";

                // gift post id is always 0
                if("gift" !== $post_type){
                    $body .= "<p>You can view your booking <a href='" . esc_url(get_post_permalink($post_id)) . "'>here</a></p>";
                } else { // gift
                    $body .= "<p>Your gift amount has been sent to " . esc_html($post_beneficiary) . ".</p>";
                }
                
                $body .= "<p>You can view your wallet balance <a href='" . esc_url($site . "/my-account/wallet") . "'>here</a>.</p>";
                $body .= "<p>Amount deducted from Wallet Balance: <span class='amount'>" . esc_html($post_amount) . "</span></p>";

                $body .= "
                        <p>Thank you!</p>
                        <p>Best Regards,<br>WeWork Team</p>
                    </div>
                </div>
                </center>
            </body>
            </html>
            ";

    wp_mail($RECEIVER_EMAIL, $subject, $body, $headers, $attachments);

    // Clean up: Delete the temporary PDF file
    unlink($pdf_path);

    // update wallet balance
    $balance = get_field('user_wallet_balance', "user_{$user->ID}");
    $balance = $balance + $post_amount;
    update_field('user_wallet_balance', $balance, "user_{$user_id}");
    error_log("Invoice: #{$invoice_id}, ID: {$user->ID}, Username: {$user->user_login}, Deducted from Wallet: {$post_amount}, Wallet Balance: {$balance}");
    
    // cleanup
    unset($_SESSION['wallet_pay_token']);
    session_destroy();

    // exit
    die('{"code":200, "msg":"Payment Successful!"}');
