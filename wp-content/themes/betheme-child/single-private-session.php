<?php get_header(); ?>

    <div id="primary" class="content-area">
		<main id="main" class="site-main" role="main">
            <div class="container" style="max-width: 600px; padding: 25px">
            <h2>Private Session Request #<?php the_ID(); ?></h2>
            <table>
                <thead>
                    <tr><th colspan="2" style="background:#419bd5">Request Details</th></tr>
                </thead>
                <tbody>
                    <tr><td>Name</td><td><?php the_field('private_session_client_name'); ?></td></tr>
                    <tr><td>Date</td><td><?php echo date("D, d M Y",strtotime(get_field('private_session_date'))); ?></td></tr>
                    <tr><td>Time</td><td><?php echo date("h:i a",strtotime(get_field('private_session_time'))); ?></td></tr>
                    <tr><td>Whatsapp Number</td><td><?php the_field('private_session_whatsapp_number'); ?></td></tr>
                    <tr><td>Number of Participants</td><td><?php the_field('private_session_number_of_participants'); ?></td></tr>
                    <tr><td>Session Status</td><td><?php $status = get_field('private_session_status'); if($status==null) { echo "Requested"; } else { echo $status; } ?></td></tr>
                    <tr><td>Fee</td><td><?php $fee=get_field('private_session_fee'); if(!empty($fee)) {echo $fee;} else {echo "Not set";} ?></td></tr>
                    <tr><td>Payment Status</td>
                        <td>
                            <?php 
                                $payment_status = get_field('private_session_payment_status');
                                if($payment_status == null){
                                    $payment_status = "Unpaid";
                                }
                            ?>
                            <?php if($payment_status=="Unpaid" && $fee > 0 && $status == 'Scheduled') : ?>
                                <button onclick="book(this)" data-amount="<?php echo $fee ?>" id="private-session_<?php echo get_the_ID(); ?>">Pay Now</button>  
                            <?php else : echo $payment_status; ?>
                            <?php endif; ?>
                        </td>
                    </tr>
                    <tr><td>My Notes</td><td><?php the_field('private_session_client_notes'); ?></td></tr>
                </tbody>
            </table>
        </main><!-- #main -->
	</div><!-- #primary -->
    <?php require_once "pay_script.php"; ?>

<?php get_footer(); ?>