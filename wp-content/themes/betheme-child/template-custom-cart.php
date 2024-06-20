<?php
/**
 * The template is a custom payment cart for custom posttypes used in woodworks
 * Template Name: custom-cart
 */


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

    $post_seats = 1;
    if(isset($_POST['post_seats'])){ $post_seats = $_POST['post_seats']; }

    $post_beneficiary = '';
    if(isset($_POST['post_beneficiary'])){ $post_beneficiary = $_POST['post_beneficiary']; }
    $post_gift_message = '';
    if(isset($_POST['post_gift_message'])){ $post_gift_message = $_POST['post_gift_message']; }

    // trim extra _ added in form
    $post_type = ltrim($_POST['post_type'], "_");
    $post_id = $_POST['post_id'];
    //$post_amount = number_format((float) $_POST['post_amount'], 3, '.', '');
    $post_amount = $_POST['post_amount'];

    $user = wp_get_current_user();
	$balance = get_field('user_wallet_balance', 'user_'.$user->ID);

    $sufficient_balance = false;
    if($post_amount <= $balance){
        $sufficient_balance = true;
    }
?>


<?php get_header(); ?>
    <center style='margin-top: 3%'>
        <img src="<?php echo get_site_url(); ?>/wp-content/uploads/2024/03/cropped-single_logo-removebg-preview.png" width='100px' />
        <h2 id="title">Payment Details</h2>
        <span style="color:red;">Do not refresh this page!</span>
    </center>
    <div class="container" id="payment_cart" style="max-width: 700px; padding: 25px; margin-bottom:5%;">            
        <table>
            <thead><tr><th id="text" colspan="6" style="background:#419bd5">Please review these details before payment</th></tr></thead>
            <tbody>
                <tr>
                    <td><b>Payment For</b></td>
                    <td><?php echo $post_type; if($post_beneficiary != "null"){ echo " to ".get_user_by("email", $post_beneficiary)->user_nicename; echo " &lt;$post_beneficiary&gt;"; } ?></td>
                </tr>
                <tr>
                    <td><b>Amount to be Paid</b></td>
                    <td><?php echo number_format((float)$post_amount, 3, '.', '') ." ". get_woocommerce_currency_symbol(); ?></td>
                </tr>
                <tr>
                    <td><b>Current Wallet Balance</b></td>
                    <td><span id="wallet_balance"><?php echo number_format((float)$balance, 3, '.', ''); ?></span> <?php echo get_woocommerce_currency_symbol(); ?></td>
                </tr>
                <?php if($post_gift_message != "null"): ?>
                <tr>
                    <td><b>Gift Message</b></td>
                    <td><?php echo wordwrap($post_gift_message,50,"<br>\n") ?></td>
                </tr>
                <?php endif; ?>
                <?php if($post_seats > 1): ?>
                <tr>
                    <td><b>Number of Seats</b></td>
                    <td><?php echo $_POST['post_seats']; ?></td>
                </tr>
                <?php endif; ?>
            </tbody>
        </table>
        <center>
            <button onclick="start_payment_gateway(this, beneficiary = '<?php echo $post_beneficiary; ?>', gift_message = '<?php echo $post_gift_message; ?>')" data-amount="<?php echo $post_amount; ?>" id="<?php echo $post_type . "_" . $post_id;?>" data-seats="<?php echo $post_seats; ?>">Pay Now</button>
            <?php if($sufficient_balance && $post_type != "wallet"): ?>
                <button onclick="pay_with_wallet(this, beneficiary = '<?php echo $post_beneficiary; ?>', gift_message = '<?php echo $post_gift_message; ?>')" data-amount="<?php echo $post_amount; ?>" id="<?php echo $post_type . "_" . $post_id; ?>" data-seats="<?php echo $post_seats; ?>">Pay with Wallet</button>
            <?php endif; ?>
            <br><br><span><a href="<?php echo get_site_url(); ?>/my-account">My Account</a> &nbsp;&nbsp;&nbsp; <a href="<?php echo get_site_url(); ?>/terms-and-conditions/">Terms</a></span>
        </center>
    </div>
    <?php require_once "pay_script.php"; ?>
<?php get_footer();
