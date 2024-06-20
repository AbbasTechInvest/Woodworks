<?php get_header(); ?>

    <div id="primary" class="content-area">
		<main id="main" class="site-main" role="main">
            <div class="container" style="max-width: 600px; padding: 25px;">
            <h2>Catering Request #<?php the_ID(); ?></h2>
            <table>
                <thead>
                    <tr><th colspan="2" style="background:#419bd5">Request Details</th></tr>
                </thead>
                <tbody>
                    <tr><td>Name</td><td><?php the_field('catering_name'); ?></td></tr>
                    <tr><td>Date</td><td><?php echo date("D, d M Y",strtotime(get_field('catering_date'))); ?></td></tr>
                    <tr><td>Time</td><td><?php echo date("h:i a",strtotime(get_field('catering_time'))); ?></td></tr>
                    <tr><td>Whatsapp Number</td><td><?php the_field('catering_whatsapp_number'); ?></td></tr>
                    <tr><td>Number of Participants</td><td><?php the_field('catering_number_of_participants'); ?></td></tr>
                    <tr><td>Address</td>
                        <td>
                            <?php echo get_field('catering_session_address_building') ."<br>". get_field('catering_session_address_street') ."<br>"; ?>
                            <?php echo get_field('catering_session_address_city') .", ". get_field('catering_session_address_governorate'); ?>
                            <?php // TODO: display current site country ?>
                        </td>
                    </tr>
                    <tr><td>Session Status</td><td><?php $catering_status = get_field('catering_status'); if($catering_status==null) { echo "Requested"; } else { echo $catering_status; } ?></td></tr>
                    <tr><td>Fee</td><td><?php $fee=get_field('catering_fee'); if(!empty($fee)) {echo $fee;} else {echo "Not set";} ?></td></tr>
                    <tr><td>Payment Status</td>
                        <td>
                            <?php 
                                $payment_status = get_field('catering_payment_status');
                                if($payment_status == null){
                                    $payment_status = "Unpaid";
                                }
                            ?>
                            <?php if($payment_status=="Unpaid" && $fee > 0 && $catering_status == 'Scheduled') : ?>
                                <button onclick="book(this)" data-amount="<?php echo $fee ?>" id="catering_<?php echo get_the_ID(); ?>">Pay Now</button>  
                            <?php else : echo $payment_status; ?>
                            <?php endif; ?>
                        </td>
                    </tr>
                    <tr><td>My Notes</td><td><?php the_field('catering_client_notes'); ?></td></tr>
                </tbody>
            </table>
        </main><!-- #main -->
	</div><!-- #primary -->
    <?php require_once "pay_script.php"; ?>

<?php get_footer(); ?>