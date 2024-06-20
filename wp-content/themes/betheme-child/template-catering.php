<?php get_header(); ?>

	<div id="primary" class="content-area">
		<main id="main" class="site-main" role="main">

        <?php echo do_shortcode('[elementor-template id="2334"]'); ?>
        <?php if(is_user_logged_in()): ?>
            <?php
                global $current_user;
                wp_get_current_user();

                $paged = ( get_query_var( 'paged' ) ) ? get_query_var( 'paged' ) : 1;

                $args = array(  
                    'post_type' => 'catering',
                    'post_status' => array('publish', 'pending'),
                    'author__in' => array($current_user->ID),
                    'posts_per_page'=> 10,
                    'paged' => $paged
                );

                $nows_date = strtotime("now");
                
                $the_query = new WP_Query($args);

                if ($the_query->have_posts()): ?>

                <div class="container" style="max-width: 700px; padding: 25px">
                    <table>
                        <thead>
                            <tr><th colspan="6" style="background:#419bd5">Your Outdoor Session Requests</th></tr>
                            <tr>
                                <td><b>Name</b></td>
                                <td><b>Date</b></td>
                                <td><b>Participants</b></td>
                                <td><b>Session Status</b></td>
                                <td><b>Fee</b></td>
                                <td><b>Pay Status</b></td>
                            </tr>
                        </thead>
                        <tbody>
                            <?php 
                                while ($the_query->have_posts()): $the_query->the_post(); ?>
                                    <?php if("pending" == get_post_status()): ?>
                                        <?php
                                            $post_date = strtotime(get_the_date());
                                            $diff = $nows_date - $post_date;
                                            $ARCHIVE_LIMIT = 60*60*24; // seconds in a day
                                            if($diff > $ARCHIVE_LIMIT){
                                                $post_status = "archived";
                                                
                                                $update_post = array(
                                                    'post_type' => 'catering',
                                                    'ID' => get_the_ID(),
                                                    'post_status' => $post_status
                                                );
                                                // this post will be hidden on next time page visit
                                                $statusTest = wp_update_post($update_post);
                                            }
                                        ?>
                                        <tr>
                                            <td><a href="<?php the_permalink(); ?>"><?php the_field('catering_name'); ?></a></td>
                                            <td><?php echo date("D, d M Y",strtotime(get_field('catering_date'))); ?></td>
                                            <td><?php the_field('catering_number_of_participants'); ?></td>
                                            <td>Requested</td>
                                            <td>Not Set</td>
                                            <td>Unpaid</td>
                                        </tr>
                                    <?php else: ?>
                                        <tr>
                                            <td><a href="<?php the_permalink(); ?>"><?php the_field('catering_name'); ?></a></td>
                                            <td><?php echo date("D, d M Y",strtotime(get_field('catering_date'))); ?></td>
                                            <td><?php the_field('catering_number_of_participants'); ?></td>
                                            <td><?php $catering_status = get_field('catering_status'); if($catering_status==null) { echo "Requested"; } else { echo $catering_status; } ?></td>
                                            <td><?php $fee = get_field('catering_fee'); if($fee){ echo $fee; } else { $fee = 0; echo "Not Set"; } ?></td>
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
                                    <?php endif; ?>
                                <?php endwhile;
                                wp_reset_postdata(); 
                            ?>
                        </tbody>
                    </table>
                    <?php $num_pages = $the_query->max_num_pages; ?>
                    <?php require_once "pagination.php"; ?>
                </div>

                <?php else: ?>
                    <div class="container" style="max-width: 750px; padding: 25px; margin-bottom:70px">
                        <h2>You do not have any active requests currently.</h2>
                        <table>
                            <thead>
                                <tr><th colspan="6" style="background:#f3d8bb">Your Outdoor Session Requests</th></tr>
                            </thead>
                            <tbody>
                                <tr><td colspan="6">No records to show</td></tr>
                            </tbody>
                        </table>
                    </div>
                <?php endif; ?>

            <?php else: ?>
                <div class="container" style="max-width: 750px; padding: 25px; margin-bottom:70px">
                    <h2>Please login to view your requests</h2>
                    <table>
                        <thead>
                            <tr><th colspan="6" style="background:#f3d8bb">Your Outdoor Session Requests</th></tr>
                        </thead>
                        <tbody>
                            <tr><td colspan="6">No records to show</td></tr>
                        </tbody>
                    </table>
                </div>
        <?php endif; ?>

		</main><!-- #main -->
	</div><!-- #primary -->
    <?php require_once "pay_script.php"; ?>

<?php echo do_shortcode('[elementor-template id="1876"]'); ?>
<?php echo do_shortcode('[elementor-template id="1678"]'); ?>
<?php get_footer(); ?>