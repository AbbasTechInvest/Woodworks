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

        $order_note = $data->message;
        $response = $data->response;
        $user = get_userdata($response->variable3);
        $booked_seats = substr($response->orderReferenceNumber, strrpos($response->orderReferenceNumber, '/') + 1);

        // payment success
        if($data->status){            
            if($response->method == 1) { $payments_method = "KNET"; } elseif ($response->method == 2) { $payments_method = "MPGS"; } else{ $payments_method = "Wallet"; }
            
            // 1. Create payment record
            // $query = new WP_Query( array( 's' => $response->orderReferenceNumber ) );
            // if ( $query->have_posts() ) {
            //     var_dump($query);
            //     die();
            // }

            // TODO: add validation to check if payment record already exists
            // if ( post_exists( sanitize_title( $response->orderReferenceNumber ) ) ) {
            //     // payment record already exists, possibly page is reloaded.
            //     //die("<script>alert('Page Reloaded! Redirecting to Homepage.'); window.location.replace('{$site}');</script>");
            //     wp_redirect($site);
            // }
        
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
            error_log("Invoice: #$response->orderReferenceNumber, User: $user->ID, Name: $user->first_name $user->last_name, Payment amount: $response->amount, Wallet Balance: $balance");

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
                error_log("Invoice: #$response->orderReferenceNumber, User: $user->ID, Name: $user->first_name $user->last_name, Recharge amount: $response->amount, Wallet Balance: $balance");
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

                $balance = get_field('user_wallet_balance', "user_{$beneficiary_user_id}");
                $balance += $response->amount;
                update_field('user_wallet_balance', $balance, "user_{$beneficiary_user_id}");
                error_log("Invoice: #{$response->orderReferenceNumber}, Beneficiary User: {$beneficiary_user->ID}, Beneficiary Name: {$beneficiary_user->first_name} {$beneficiary_user->last_name}, Beneficiary Recharge amount: {$response->amount}, Beneficiary Wallet Balance: {$balance}");
                
                // send email to beneficiary
                $subject = "You have received a GIFT! | Woodworks";
                $body = "Congratulations {$beneficiary_user->first_name}!<br>";
                $body .= ucfirst($user->first_name);
                $body .= " has gifted {$response->amount} KD to your wallet balance.";
                $body .= "<br>You can view your wallet balance <a href='{$site}/my-account/wallet'>here</a><br>";
                $body .= explode("|", $response->variable5); // gift message
                $headers = array('Content-Type: text/html; charset=UTF-8', 'From: info@techinvestkw.com');
    
                wp_mail($beneficiary_user->user_email, $subject, $body, $headers);
            }

            // 3. Send email to customer
            $subject = "{$response->orderReferenceNumber} Payment Confirmation | Woodworks";
            // make exception to permalink for wallet recharge
            $body = "Dear $user->first_name,<br>The payment for ".ucfirst($response->variable1)." has been received.";
            
            // wallet recharge post id is always 0
            if($response->variable2){
                $body .= "<br>You can view your booking <a href='";
                $body .= get_post_permalink($response->variable2);
                $body .= "'>here</a><br>";
            }
            else{      
                if("gift" == $response->variable1){
                    $body .= "<br>Your gift amount has been sent to ";
                    $body .= current(explode("|", $response->variable5));
                } 
                else{
                    $body .= "<br>You can view your wallet balance <a href='{$site}/my-account/wallet'>here</a><br>";
                }         
            }
            $url = $_SERVER['SERVER_NAME'];
            $headers = array('Content-Type: text/html; charset=UTF-8', 'From: info@techinvestkw.com');

            wp_mail(current(explode("|", $response->variable5)), $subject, $body, $headers);

            // 4. Send Whatsapp notification for payment
            // TODO: Integrate Whatsapp Plugin
            // TODO: Attach pdf invoice

            // 5. Display frontend message
            $content = "<div class='container' style='max-width: 700px; padding: 25px'>
                <center>
                    <img src='{$site}/wp-content/uploads/2024/03/cropped-single-logo.jpg' width='100px' />
                    <h2>Your Payment was successful</h2>
                </center>
                <table>
                    <thead><tr><th colspan='2' style='background:#419bd5'>Payment Details</th></tr></thead>
                    <tbody>
                    <tr>
                        <td><b> Applicant Name</b></td>
                        <td>{$user->first_name} {$user->last_name}</td>
                    </tr>"; 
            /* Add permalink exception for wallet recharge */ 
            // wallet recharge post id is always 0
            if($response->variable2){
                if("gift" == $response->variable1){
                    $gift_message = explode("|", $response->variable5);
                    $gift_message = end($gift_message);
                    
                    $content .="
                    <tr>
                        <td><b>	Beneficiary Email </b></td>
                        <td>{$response->variable2}</td>
                    </tr>
                    <tr>
                        <td><b>	Gift Message </b></td>
                        <td>{$gift_message}</td>
                    </tr>"
                    ;
                }
                else { // other post types
                    $content .="
                        <tr>
                            <td><b>	Program Details </b></td>
                            <td><a href='";
                            $content .= get_post_permalink($response->variable2);
                            $content .= "'>";
                            $content .= get_the_title($response->variable2);
                            $content .= "</a></td>
                        </tr>";
                }
            }
            else{ // wallet
                $balance = get_field('user_wallet_balance', 'user_'.$user->ID);
                $content .="
                    <tr>
                        <td><b>	Wallet Balance </b></td>
                        <td>";
                        $content .= number_format((float)$balance, 3, '.', '');
                        $content .= " ";
                        $content .= get_woocommerce_currency_symbol();
                        $content .= "</td>
                    </tr>";

            }
            if (in_array($response->variable1, array("class", "multiclass"))){
                $content .="
                    <tr>
                        <td><b>	Booked Seats </b></td>
                        <td>{$booked_seats}</td>
                    </tr>";
            }
            $content .="
                    <tr>
                        <td><b>	Amount </b></td>
                        <td>";
                        $content .= number_format((float)$response->amount, 3, '.', '');
                        $content .= "</td>
                    </tr>
                    <tr>
                        <td><b> Payment Type</b></td>
                        <td>{$payments_method}</td>
                    </tr>
                    <tr>
                        <td><b>	Invoice No. </b></td>
                        <td>{$response->orderReferenceNumber}</td>
                    </tr>
                    <tr>
                        <td><b> Transaction Date </b></td>
                        <td>{$response->paidOn}</td>
                    </tr>	
                    <tr>
                        <td><b> Order Note </b></td>
                        <td>{$order_note}</td>
                    </tr>
                    </tbody>
                </table>
                <br>
                <center><span><a href='{$site}/my-account'>My Account</a> &nbsp;&nbsp;&nbsp; <a href='{$site}/terms-and-conditions/'>Terms</a></span></center>
            </div>";

            // 6. create pdf report
            require_once('tcpdf/tcpdf.php');
            $pdf = new TCPDF(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);
            $pdf->SetMargins(PDF_MARGIN_LEFT, PDF_MARGIN_TOP, PDF_MARGIN_RIGHT);
            $pdf->SetTitle($response->orderReferenceNumber);
            $pdf->SetAuthor('Woodworks');
            $pdf->AddPage();
            $pdf->writeHTML($content, true, false, true, false, '');
            // $pdf_content = $pdf->Output("$response->orderReferenceNumber.pdf", 'D');
            $pdf_name = str_replace('/', '_', $response->orderReferenceNumber) . ".pdf";
            //$pdf_content = $pdf->Output("invoice1", 'F');


            //$filename = get_stylesheet_directory() ."/invoices//". $response->orderReferenceNumber .".pdf";

            //$fileatt = $pdf->Output($filename, 'F');

            //$data = chunk_split( base64_encode(file_get_contents($filename)) );

            // 7. Notify admin for received payment
            //$headers = "From: noreply@".$url."\r\nMIME-Version: 1.0\r\nContent-Type: multipart/mixed; boundary='my-boundary'\r\n";
            //$headers = array("MIME-Version: 1.0", "Content-Type: multipart/mixed; boundary='my-boundary'", "From: info@techinvestkw.com");
            $headers = array('Content-Type: text/html; charset=UTF-8', "From: info@techinvestkw.com");

            $subject = "{$response->orderReferenceNumber} {$user->first_name} paid for {$response->variable1} #{$response->variable2}";
            $url = $_SERVER['SERVER_NAME'];
            
            //$body = "--my-boundary--\r\n";
            //$body .= "Content-Type: text/html; charset=UTF-8\r\n";
            $body .= "Dear Admin,<br>{$user->first_name} {$user->last_name} has paid for a {$response->variable1} <a href='{$site}";
            $body .= "/wp-admin/post.php?post={$response->variable2}&action=edit'>";
            $body .= " #{$response->variable2}</a>";
            //$body = "--my-boundary\r\n";
            //$body .= "Content-Type: application/pdf; name='$response->orderReferenceNumber.pdf'\r\n";
            //$body .= "Content-Transfer-Encoding: base64\r\n";
            //$body .= "\r\n" . $data . "\r\n";
            //$body .= "--my-boundary";

            //rename($source_file, $destination_file);
            $ADMIN_EMAIL = "abbas.kagdi@tech.com.kw"; // TODO: change in Production
            //$attachments = array(get_stylesheet_directory() . "/" . "invoices" . "/" . $pdf_name);
            $attachments = array("invoice1.pdf");
            //wp_mail($ADMIN_EMAIL, $subject, $body, $headers, $attachments);
            wp_mail($ADMIN_EMAIL, $subject, $body, $headers);





            // Create the PDF
            // $pdf = new TCPDF(...);
            // $pdfContent = $pdf->Output('', 'S'); // Return PDF as string

            // // Prepare headers and body
            // $headers = "From: sender@example.com\r\n";
            // $headers .= "MIME-Version: 1.0\r\n";
            // $headers .= "Content-Type: multipart/mixed; boundary=\"my-boundary\"\r\n";
            // $headers = array("MIME-Version: 1.0", "Content-Type: multipart/mixed; boundary='my-boundary'", "From: info@techinvestkw.com");

            // $body = "--my-boundary\r\n";
            // $body .= "Content-Type: text/plain; charset=UTF-8\r\n";
            // $body .= "\r\nThis email contains an attached report.\r\n";
            // $body .= "--my-boundary\r\n";
            // $body .= "Content-Type: application/pdf; name=\"report.pdf\"\r\n";
            // $body .= "Content-Transfer-Encoding: base64\r\n";
            // $body .= "\r\n" . base64_encode($pdfContent) . "\r\n";
            // $body .= "--my-boundary--";

            // Send email
            //wp_mail('recipient@example.com', 'My Report', $body, $headers);






            // $attachment = chunk_split($pdf_content);
        
            // // a random hash will be necessary to send mixed content
            // $separator = md5(time());
        
            // // carriage return type (RFC)
            // $eol = "\r\n";

            // $subject = "#$response->orderReferenceNumber $user->first_name paid for $response->variable1 #$response->variable2";
        
            // // main header (multipart mandatory)
            // $headers = "From: Admin <noreply@{$site}">" . $eol;
            // //$headers .= "MIME-Version: 1.0" . $eol;
            // $headers .= "Content-Type: multipart/mixed; boundary=\"" . $separator . "\"" . $eol;
            // //$headers .= "Content-Transfer-Encoding: 7bit" . $eol;
            // //$headers .= "This is a MIME encoded message." . $eol;
            // $headers = array("MIME-Version: 1.0", "Content-Type: multipart/mixed; boundary='my-boundary'", "From: info@techinvestkw.com");
        
            // // message
            // $message = "Dear Admin,<br>$user->first_name $user->last_name has paid for a {$response->variable1} <a href='{$site}/wp-admin/post.php?post=$response->variable2&action=edit'>#{$response->variable2}</a>";
            // $body = "--" . $separator . $eol;
            // $body .= "Content-Type: text/html; charset=\"utf-8\"" . $eol;
            // //$body .= "Content-Transfer-Encoding: 8bit" . $eol;
            // $body .= $message . $eol;
        
            // // attachment
            // $body .= "--" . $separator . $eol;
            // $body .= "Content-Type: application/octet-stream; name=\"" . $response->orderReferenceNumber . ".pdf\"" . $eol;
            // $body .= "Content-Transfer-Encoding: base64" . $eol;
            // $body .= "Content-Disposition: attachment" . $eol;
            // $body .= $pdf_content . $eol;
            // $body .= "--" . $separator . "--";

            // $ADMIN_EMAIL = "abbas.kagdi@tech.com.kw"; // TODO: change in Production
            // wp_mail($ADMIN_EMAIL, $subject, $body, $headers);
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
            error_log("Payment failed for userid:{$user->ID} name:{$user->first_name} {$user->last_name} on {$response->variable1} #{$response->variable2}, Whatsapp: {$response->variable4}");
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