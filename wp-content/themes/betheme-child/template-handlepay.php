<?php 
/**
 * The template is for handling Hesabe payment response.
 * Template Name: handlepay
 */

$site = get_site_url();
$content = "<a href='{$site}'>HOME</a>";

if(is_user_logged_in()){
    if(isset($_GET['data'])){
        $paymentData = $_GET['data'];

        // TODO: change email addresses in Production
        $ADMIN_EMAIL = "info@itswework.com";
        $SENDER_EMAIL = "info@techinvestkw.com";

        // init hesabe class
        $hesabe = new WC_Hesabe();
        $merchantCode = $hesabe->settings['merchantCode'];
        $sandbox = $hesabe->settings['sandbox'];
        $secretKey = $hesabe->settings['secretKey'];
        $ivKey = $hesabe->settings['ivKey'];
        $accessCode = $hesabe->settings['accessCode'];

        // init encryption class
        $crypto = new WC_Hesabe_Crypt();

        $decrypted_post_response = $crypto->decrypt($paymentData, $secretKey, $ivKey);
        $data = json_decode($decrypted_post_response);

        // log payment response data
        error_log(print_r($data, true));

        $order_note = $data->message;
        $response = $data->response;

        $user = get_userdata($response->variable3);
        $booked_seats = substr($response->orderReferenceNumber, strrpos($response->orderReferenceNumber, '/') + 1);
        $RECEIVER_EMAIL = $user->user_email;

        if(!$booked_seats) { $booked_seats = 1; }

        // payment success
        if($data->status){            
            if($response->method == 1) { $payments_method = "KNET"; } elseif ($response->method == 2) { $payments_method = "MPGS"; } else{ $payments_method = "Wallet"; }
            
            // 1. Create payment record
            // TODO: add validation to check if payment record already exists
        
            $payment_record_id = wp_insert_post(array(
                'post_title'=>$response->orderReferenceNumber, 
                'post_type'=>'payments', 
                'post_content'=>$decrypted_post_response,
                'post_status'=>'private'
            ));

            update_field('payments_status', $response->resultCode, $payment_record_id);
            update_field('payments_amount', $response->amount, $payment_record_id);
            update_field('payments_date', $response->paidOn, $payment_record_id);
            // wallet recharge post id is always 0
            if($response->variable2){
                update_field('payments_type', $response->variable1, $payment_record_id);
            }
            else{
                update_field('payments_type', null, $payment_record_id);
            }
            update_field('payments_type', $response->variable1, $payment_record_id);
            update_field('payments_type_id', $response->variable2, $payment_record_id);
            update_field('payments_user', $response->variable3, $payment_record_id);
            update_field('payments_user_whatsapp', $response->variable4, $payment_record_id);
            update_field('payments_user_email', current(explode("|", $response->variable5)), $payment_record_id); // for gift post type, the email and custom message are concatinated with | charecter. Make sure emails do not contain | charecter
            update_field('payments_method', $payments_method, $payment_record_id);

            $balance = get_field('user_wallet_balance', 'user_'.$user->ID);
            error_log("Invoice: #{$response->orderReferenceNumber}, User: {$user->ID}, Name: {$user->user_login}, Payment amount: {$response->amount}, Wallet Balance: {$balance}");


            // 2. Create participant record in the post type
            // 2.1 class
            if("class" == $response->variable1){
                $rows = get_field('class_participant_details', $response->variable2);
                $record_exists = false;
                if( $rows ) {
                    $record_exists = false;
                    foreach( $rows as $row ) {
                        if($row['class_participant_id']['ID'] == $response->variable3){
                            $record_exists = true;
                            break;
                        }
                    }
                }
                if($record_exists){
                    // TODO: create flow for exception
                    // make curl request to verify payment with hesabe
                    $order_note = "Please check your order. There seems to be a duplicate booking.";
                }
                else{
                    $row = array(
                        //'class_participant_invoice_id' => $response->orderReferenceNumber,
                        'class_participant_payment' => $payment_record_id,
                        'class_participant_num_seats' => $booked_seats,
                        'class_participant_id' => $user->ID,
                        'class_participant_name' => $user->first_name." ".$user->last_name,
                        'class_participant_whatsapp' => $response->variable4,
                        'class_participant_email' => $response->variable5
                    );
                    
                    add_row('class_participant_details', $row, $response->variable2);
                    //echo "<br>added subrow to class<br>";
                }
            }

            // 2.2 multiclass
            if("multiclass" == $response->variable1){
                $rows = get_field('multiclass_participant_details', $response->variable2);
                $record_exists = false;
                if( $rows ) {
                    $record_exists = false;
                    foreach( $rows as $row ) {
                        if($row['multiclass_participant_id']['ID'] == $response->variable3){
                            $record_exists = true;
                            break;
                        }
                    }
                }
                if($record_exists){
                    // TODO: create flow for exception
                    // make hesabe call to verify payment
                    $order_note = "Please check your order. There seems to be a duplicate booking.";
                }
                else{
                    $row = array(
                        //'multiclass_participant_invoice_id' => $response->orderReferenceNumber,
                        'multiclass_participant_payment' => $payment_record_id,
                        'multiclass_participant_num_seats' => $booked_seats,
                        'multiclass_participant_id' => $user->ID,
                        'multiclass_participant_name' => $user->first_name." ".$user->last_name,
                        'multiclass_participant_whatsapp' => $response->variable4,
                        'multiclass_participant_email' => $response->variable5
                    );
                    
                    add_row('multiclass_participant_details', $row, $response->variable2);
                    //echo "<br>added subrow to multiclass<br>";
                }
            }

            // 2.3 private-session
            if("private-session" == $response->variable1){
                update_field('private_session_payment_status','Paid', $response->variable2);
                update_field('private_session_payment_id', $payment_record_id, $response->variable2);
                //echo "<br>Private session Paid<br>";
            }

            // 2.4 catering
            if("catering" == $response->variable1){
                update_field('catering_payment_status','Paid', $response->variable2);
                update_field('catering_payment_id', $payment_record_id, $response->variable2);
                //echo "<br>Catering session Paid<br>";
            }

            // 2.5 custom-product
            if("custom-product" == $response->variable1){
                update_field('cp_payment_status','Paid', $response->variable2);
                update_field('cp_payment_id', $payment_record_id, $response->variable2);
                //echo "<br>Custom Product Request Paid<br>";
            }

            // 2.6 video
            if("video" == $response->variable1){
                $rows = get_field('video_purchaser_details', $response->variable2);
                $record_exists = false;
                if( $rows ) {
                    $record_exists = false;
                    foreach( $rows as $row ) {
                        if($row['video_purchaser_id']['ID'] == $response->variable3){
                            $record_exists = true;
                            break;
                        }
                    }
                }
                if($record_exists){
                    // TODO: create flow for exception
                    // make curl request to verify payment with hesabe
                    $order_note = "Please check your order. There seems to be a duplicate booking.";
                }
                else{
                    $row = array(
                        //'video_purchaser_invoice_id' => $response->orderReferenceNumber,
                        'video_purchaser_payment' => $payment_record_id,
                        'video_purchaser_id' => $user->ID,
                        'video_purchaser_name' => $user->first_name." ".$user->last_name,
                        'video_purchaser_whatsapp' => $response->variable4,
                        'video_purchaser_email' => $response->variable5
                    );
                    
                    add_row('video_purchaser_details', $row, $response->variable2);
                    //echo "<br>added subrow to video<br>";
                }
            }

            // 2.7 wallet
            // wallet recharge post id is always 0
            if(!$response->variable2){
                $balance = get_field('user_wallet_balance', 'user_'.$user->ID);
                $balance += $response->amount;
                update_field('user_wallet_balance', $balance, 'user_'.$user->ID);
                error_log("Invoice: #{$response->orderReferenceNumber}, User: {$user->ID}, Name: {$user->user_login}, Recharge amount: {$response->amount}, Wallet Balance: {$balance}");
            }

            // 2.8 prework
            if("prework" == $response->variable1){
                $rows = get_field('prework_participant_details', $response->variable2);
                $record_exists = false;
                if( $rows ) {
                    $record_exists = false;
                    foreach( $rows as $row ) {
                        if($row['prework_participant_id']['ID'] == $response->variable3){
                            $record_exists = true;
                            break;
                        }
                    }
                }
                if($record_exists){
                    // TODO: create flow for exception
                    // make curl request to verify payment with hesabe
                    $order_note = "Please check your order. There seems to be a duplicate booking.";
                }
                else{
                    $row = array(
                        //'prework_participant_invoice_id' => $response->orderReferenceNumber,
                        'prework_participant_payment' => $payment_record_id,
                        'prework_participant_num_seats' => $booked_seats,
                        'prework_participant_id' => $user->ID,
                        'prework_participant_name' => $user->first_name." ".$user->last_name,
                        'prework_participant_whatsapp' => $response->variable4,
                        'prework_participant_email' => $response->variable5
                    );
                    
                    add_row('prework_participant_details', $row, $response->variable2);
                    //echo "<br>added subrow to prework<br>";
                }
            }

            // 2.9 gift
            // For post type gift, post-id is beneficiary-id
            if("gift" == $response->variable1){
                $beneficiary_user = get_user_by('email', $response->variable2);
                $beneficiary_user_id = $beneficiary_user->ID;

                $gift_message = explode("|", $response->variable5);
                $gift_message = end($gift_message);

                $balance = get_field('user_wallet_balance', "user_{$beneficiary_user_id}");
                $balance += $response->amount;
                update_field('user_wallet_balance', $balance, "user_{$beneficiary_user_id}");
                error_log("Invoice: #{$response->orderReferenceNumber}, Beneficiary User: {$beneficiary_user->ID}, Beneficiary Name: {$beneficiary_user->first_name} {$beneficiary_user->last_name}, Beneficiary Recharge amount: {$response->amount}, Beneficiary Wallet Balance: {$balance}");
                
                // send email to beneficiary
                $headers = array('Content-Type: text/html; charset=UTF-8', "From: {$SENDER_EMAIL}");
                $subject = "You have received a GIFT! | WeWork";
                

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
                            <img src='{$site}/wp-content/uploads/2024/03/cropped-single_logo-removebg-preview.png' width='100' />
                            <h1>Congratulations ". $beneficiary_user->first_name . "!</h1>";
                            
                            $body .= "<p>" . esc_html(ucfirst($user->first_name)) ." ". esc_html(ucfirst($user->last_name)) . " has gifted {$response->amount} KD to your wallet balance.";
                            $body .= "<br>You can view your wallet balance <a href='{$site}/my-account/wallet'>here</a><br>";
                            $body .= "There is a special message for you from " . esc_html(ucfirst($user->first_name));
                            $body .= "<div class='container'>{$gift_message}</div>"; // gift message
                            $body .= "
                            <p>Best Regards,<br>WeWork Team</p>
                        </div>
                    </div>
                    </center>
                </body>
                </html>
                ";
    
                wp_mail($beneficiary_user->user_email, $subject, $body, $headers);
            }


            // 3. Display frontend message
            // Define the styles
            $styles = '
            <style scoped>
                table {width: 100%; border-collapse: collapse;}
                th {background-color: #1f1a4e; color: white; padding: 25px;}
                td {padding: 15px; border: 1px solid #ccc;}
                .invoice_container {max-width: 700px; padding: 25px;}
                .center {text-align: center;}
            </style>
            ';

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
            if ($response->variable2) {
                if ("gift" == $response->variable1) {
                    $gift_message = explode("|", $response->variable5);
                    $gift_message = end($gift_message);
                    
                    $content .= '
                        <tr>
                            <td><strong>Beneficiary Email</strong></td>
                            <td>' . $response->variable2 . '</td>
                        </tr>
                        <tr>
                            <td><strong>Gift Message</strong></td>
                            <td>' . $gift_message . '</td>
                        </tr>';
                } else { // other post types
                    $content .= '
                        <tr>
                            <td><strong>Program Details</strong></td>
                            <td><a href="' . get_post_permalink($response->variable2) . '">' . get_the_title($response->variable2) . '</a></td>
                        </tr>';
                }
            } else { // wallet
                $balance = get_field('user_wallet_balance', 'user_' . $user->ID);
                $content .= '
                    <tr>
                        <td><strong>Wallet Balance</strong></td>
                        <td>' . number_format((float)$balance, 3, '.', '') . ' ' . get_woocommerce_currency_symbol() . '</td>
                    </tr>';
            }

            if (in_array($response->variable1, array("class", "multiclass"))) {
                $content .= '
                    <tr>
                        <td><strong>Booked Seats</strong></td>
                        <td>' . (string) $booked_seats . '</td>
                    </tr>';
            }

            $content .= '
                    <tr>
                        <td><strong>Amount</strong></td>
                        <td>' . number_format((float)$response->amount, 3, '.', '') . '</td>
                    </tr>
                    <tr>
                        <td><strong>Payment Type</strong></td>
                        <td>' . $payments_method . '</td>
                    </tr>
                    <tr>
                        <td><strong>Invoice No.</strong></td>
                        <td>' . $response->orderReferenceNumber . '</td>
                    </tr>
                    <tr>
                        <td><strong>Transaction Date</strong></td>
                        <td>' . $response->paidOn . '</td>
                    </tr>
                    <tr>
                        <td><strong>Order Note</strong></td>
                        <td>' . $order_note . '</td>
                    </tr>
                    </tbody>
                </table>
                <br>
                <div class="center">
                    <span><a href="' . $site . '/my-account">My Account</a> &nbsp;&nbsp;&nbsp; <a href="' . $site . '/terms-and-conditions/">Terms</a></span>
                </div>
            </div>';


            // 4. create pdf report
            require_once('tcpdf/tcpdf.php');
            $pdf = new TCPDF(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);
            $pdf->SetMargins(PDF_MARGIN_LEFT, PDF_MARGIN_TOP, PDF_MARGIN_RIGHT);
            $pdf->SetTitle($response->orderReferenceNumber);
            $pdf->SetAuthor('Itswework');
            $pdf->AddPage();
            $pdf->writeHTML($content, true, false, true, false, '');
            // $pdf_content = $pdf->Output("$response->orderReferenceNumber.pdf", 'D');
            $pdf_name = str_replace('/', '_', $response->orderReferenceNumber) . ".pdf";
            //$pdf_content = $pdf->Output("invoice1", 'F');
            $pdf_content = $pdf->Output($pdf_name, 'S');
            $pdf_path = get_stylesheet_directory() . "/invoices/{$pdf_name}";

            // Save the PDF content to a file
            file_put_contents($pdf_path, $pdf_content);
            $attachments = array($pdf_path);


            // 5. Notify admin for received payment
            $headers = array('Content-Type: text/html; charset=UTF-8', "From: {$SENDER_EMAIL}");

            $subject = "{$response->orderReferenceNumber} {$user->user_login} paid for {$response->variable1} #{$response->variable2}";
            
            $body = "Dear Admin,<br>{$user->user_login} has paid for a {$response->variable1} <a href='{$site}";
            $body .= "/wp-admin/post.php?post={$response->variable2}&action=edit'>";
            $body .= " #{$response->variable2}</a>";
            
            wp_mail($ADMIN_EMAIL, $subject, $body, $headers, $attachments);

            
            // 6. Send email to customer
            $headers = array('Content-Type: text/html; charset=UTF-8', "From: {$SENDER_EMAIL}");

            $subject = "{$response->orderReferenceNumber} Payment Confirmation | WeWork";

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
                        <img src='{$site}/wp-content/uploads/2024/03/cropped-single_logo-removebg-preview.png' width='100' />
                        <h1>Payment Confirmation</h1>
                        <p>Dear " . esc_html($user->first_name) . ",</p>
                        <p>The payment for " . esc_html(ucfirst($response->variable1)) . " has been received.</p>";

                // wallet recharge post id is always 0
                if ($response->variable1 !== "gift") {
                    $body .= "<p>You can view your booking <a href='" . esc_url(get_post_permalink($response->variable2)) . "'>here</a>.</p>";
                } else {
                    $body .= "<p>Your gift amount has been sent to " . esc_html(current(explode("|", $response->variable5))) . ".</p>";
                    $body .= "<p>You can view your wallet balance <a href='" . esc_url($site . "/my-account/wallet") . "'>here</a>.</p>";
                }

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
        }
        else{
            if(isset($_GET['data'])){
                unset($_GET['data']);
            }
            $content = "<center>
                <img src='{$site}/wp-content/uploads/2024/03/cropped-single_logo-removebg-preview.png' width='100px' />
                <h2>Your Payment was Failed!</h2>
                {$content}
            </center>";
            error_log("Payment failed for userid:{$user->ID} name:{$user->user_login} {$user->last_name} on {$response->variable1} #{$response->variable2}, Whatsapp: {$response->variable4}");
        }
    }
    else{
        $content = "<center><h2>Unauthorized Access</h2>{$content}</center>";
        error_log("Someone tried to pay without encrypted hesaeb url");
    }
}
else{
    if(isset($_GET['data'])){
        unset($_GET['data']);
    }
    $content = "<center><h2>Unauthorized Access</h2>{$content}</center>";
    error_log("Someone tried to pay while being logged out");
}

?>

<?php 
session_start();
$_SESSION['content'] = $content;
wp_redirect( "{$site}/receipt" );
exit();