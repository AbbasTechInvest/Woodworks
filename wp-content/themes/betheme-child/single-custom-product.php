<?php get_header(); ?>

    <div id="primary" class="content-area">
		<main id="main" class="site-main" role="main">
            <div class="container" style="max-width: 600px; padding: 25px">
            <h2>Custom Product Request #<?php the_ID(); ?></h2>
            <img src="<?php the_post_thumbnail_url(); ?>" width="100%" />
            <table>
                <thead>
                    <tr><th colspan="2" style="background:#419bd5">Request Details</th></tr>
                </thead>
                <tbody>
                    <tr><td>Name</td><td><?php the_title(); ?></td></tr>
                    <tr><td>Requested On</td><td><?php echo date("D, d M Y",strtotime(get_the_date())); ?></td></tr>
                    <tr><td>Whatsapp Number</td><td><?php the_field('cp_client_whatsapp'); ?></td></tr>
                    <tr><td>My Request</td><td><?php the_field('cp_client_request'); ?></td></tr>
                    <tr><td>Request Status</td><td><?php $status = get_field('cp_request_status'); if($status==null) { echo "Requested"; } else { echo $status; } ?></td></tr>
                    <tr><td>Fee</td><td><?php $fee=get_field('cp_fee'); if(!empty($fee)) {echo $fee;} else {echo "Not set";} ?></td></tr>
                    <tr><td>Payment Status</td>
                        <td>
                            <?php 
                                $payment_status = get_field('cp_payment_status');
                                if($payment_status == null){
                                    $payment_status = "Unpaid";
                                }
                            ?>
                            <?php if($payment_status=="Unpaid" && $fee > 0 && $status == 'Scheduled') : ?>
                                <button onclick="book(this)" data-amount="<?php echo $fee ?>" id="custom-product_<?php echo get_the_ID(); ?>">Pay Now</button>  
                            <?php else : echo $payment_status; ?>
                            <?php endif; ?>
                        </td>
                    </tr>
                </tbody>
            </table>
        </main><!-- #main -->
	</div><!-- #primary -->
    <?php require_once "pay_script.php"; ?>

<?php get_footer(); ?>