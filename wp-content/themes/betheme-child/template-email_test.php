<?php

/**
 * The template is for testing email with attachment.
 * Template Name: email_test
 */

 add_action('wp_head', 'wpse_43672_wp_head');
 function wpse_43672_wp_head(){
     //Close PHP tags 
     ?>
     <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-EVSTQN3/azprG1Anm3QDgpJLIm9Nao0Yz1ztcQTwFspd3yD65VohhpuuCOmLASjC" crossorigin="anonymous">
     <?php //Open PHP tags
 }

 $content = "<div class=\"container\" style=\"max-width: 700px; padding: 25px\">
 <div style=\"text-align:center\">
     <img src=\"".get_site_url()."/wp-content/uploads/2023/11/Steel-Blue-Line-Construction-Logo-1.png\" width=\"100px\" />
     <h2>Your Payment was successful</h2>
 </div>
 <table border=\"1\" style=\"border-collapse:collapse\">
     <thead><tr><th colspan=\"2\" style=\"background:#f3d8bb\">Your Private Session Requests</th></tr></thead>
     <tbody>
     <tr>
         <td>Applicant Name</td>
         <td>Mike Ross</td>
     </tr>
     </tbody>
</table>"; 


 require_once('tcpdf/tcpdf.php');
 $pdf = new TCPDF(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);
 $pdf->SetMargins(PDF_MARGIN_LEFT, PDF_MARGIN_TOP, PDF_MARGIN_RIGHT);
 $pdf->SetTitle("Invoice.pdf");
 $pdf->SetAuthor('Woodworks');
 $pdf->AddPage();
 $pdf->writeHTML($content, true, false, true, false, '');
 // $pdf_content = $pdf->Output("$response->orderReferenceNumber.pdf", 'D');
 $pdf_content = $pdf->Output(get_stylesheet_directory() .'\file.pdf', 'F');





// recipient email address
$to = "recipient@example.com";

// subject of the email
$subject = "Email with Attachment";

// message body
$message = "This is a sample email with attachment.";

// from
$from = "sender@example.com";

$content = file_get_contents(get_stylesheet_directory() ."\\file.pdf"); // reading the file

$encoded_content = chunk_split(base64_encode($content));
$boundary = md5("random"); // define boundary with a md5 hashed value

//header
$headers = "MIME-Version: 1.0\r\n"; // Defining the MIME version
$headers .= "From:".$from."\r\n"; // Sender Email
$headers .= "Reply-To: ".$to."\r\n"; // Email address to reach back
$headers .= "Content-Type: multipart/mixed;"; // Defining Content-Type
$headers .= "boundary = $boundary\r\n"; //Defining the Boundary
        
//plain text
$body = "--$boundary\r\n";
$body .= "Content-Type: text/plain; charset=ISO-8859-1\r\n";
$body .= "Content-Transfer-Encoding: base64\r\n\r\n";
$body .= chunk_split(base64_encode($message));
        
//attachment
$body .= "--$boundary\r\n";
$body .="Content-Type: application/pdf; name=".$name."\r\n";
$body .="Content-Disposition: attachment; filename=".$name."\r\n";
$body .="Content-Transfer-Encoding: base64\r\n";
$body .="X-Attachment-Id: ".rand(1000, 99999)."\r\n\r\n";

// send email
if (mail($to, $subject, $body, $headers)) {
    echo "<div class='container bg-danger'><p class='text-center alert alert-success'>Email with attachment sent successfully.</p></div>";
} else {
    echo "Failed to send email with attachment.";
}


