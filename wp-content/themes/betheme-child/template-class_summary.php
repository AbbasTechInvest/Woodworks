<?php
/**
 * The template is for class purchase history.
 * Template Name: Class_history
 */

get_header(); ?>


    <?php if(is_user_logged_in()) {
        // The Query.
        $args = array(
            "post_type" => "class",
            'posts_per_page' => 10,
            'paged' => $paged,
            'post_status' => array("publish", "archived"), 
            'orderby' => 'publish_date',
            'order' => 'DESC'
        );
        $the_query = new WP_Query($args);
        $upcoming_classes = []; 
        $completed_classes = [];
        $current_user_id = get_current_user_id();
        $i = 0;
        $j = 0;

        // The Loop.
        if ($the_query->have_posts()) {
            while ($the_query->have_posts()) {
                $the_query->the_post();

                $user_participated = false;
                $rows = get_field('class_participant_details');
                $booking_date = 0;
                if(!$rows) { $rows = []; }
                if( $rows ) {
                    foreach( $rows as $row ) :
                        // return type is user array
                        $participantID = $row['class_participant_id']['ID'];
                        // current user is enrolled
                        if($participantID == $current_user_id){
                            $user_participated = true;
                            $booking_date = get_field('payments_date', $row['class_participant_payment']);
                            break; 
                        }
                    endforeach;
                }
                if($user_participated) {
                    $booking_status = get_field('class_booking_enabled');
                    $x = [];
                    $x['ID'] = get_the_ID();
                    $x['permalink'] = get_permalink();
                    $x['title'] = get_the_title();
                    $x['date'] = date('d-M-Y', strtotime(get_field('class_date')));
                    $x['fee'] = get_field('class_fee');
                    $x['booking_date'] = $booking_date;
                    $x['booking_status'] = $booking_status;
                    
                    if("Open" == $booking_status){
                        $upcoming_classes[$i] = $x; ++$i;
                    }
                    else{
                        $completed_classes[$j] = $x; ++$j;
                    }
                }
            }
        }
        // Restore original Post Data.
        wp_reset_postdata();
    } ?>

<?php echo do_shortcode('[elementor-template id="2334"]'); ?>
<div class="container" style="margin-bottom:5vh">
    <div class="wrap mcb-wrap mcb-wrap-a0a9525cc one tablet-one laptop-one mobile-one valign-top clearfix"
        data-desktop-col="one" data-laptop-col="laptop-one" data-tablet-col="tablet-one" data-mobile-col="mobile-one">
        <div class="mcb-wrap-inner mcb-wrap-inner-a0a9525cc mfn-module-wrapper mfn-wrapper-for-wraps">
            <div class="mcb-wrap-background-overlay"></div>
            <div class="column mcb-column mcb-item-30126a0aa one laptop-one tablet-one mobile-one column_column"
                style="">
                <div class="mcb-column-inner mfn-module-wrapper mcb-column-inner-30126a0aa mcb-item-column-inner">
                    <div class="column_attr mfn-inline-editor clearfix align_center" style="">
                        <h2><?php _e('Class Purchase History'); ?></h2>
                    </div>
                </div>
            </div>
            <div class="column mcb-column mcb-item-f683dd4ac one laptop-one tablet-one mobile-one column_blog" style="">
                <div class="mcb-column-inner mfn-module-wrapper mcb-column-inner-f683dd4ac mcb-item-blog-inner">
                    <div class="column_filters">
                        <div class="blog_wrapper isotope_wrapper clearfix">
                            <div class="posts_group lm_wrapper col-3 photo2">
                                <div style="padding: 5px;">
                                    <?php if(is_user_logged_in()) : ?>
                                        <?php if($i + $j > 0): ?>
                                            <?php if($upcoming_classes): ?>
                                                <table>
                                                    <thead>
                                                        <tr style="text-align:center;"><th colspan="5" style="background:#419bd5;">Upcoming Class Details</th></tr>
                                                        <tr>
                                                            <td><b>Class Name</b></td>
                                                            <td><b>Date</b></td>
                                                            <td><b>Fee</b></td>
                                                            <td><b>Booking date</b></td>
                                                            <td><b>Session Status</b></td>
                                                        </tr>
                                                    </thead>
                                                    <tbody>
                                                        <?php foreach ($upcoming_classes as $upcoming_class): ?>
                                                            <tr>
                                                                <td><a href="<?php echo $upcoming_class['permalink']; ?>" target="_blank"><?php echo $upcoming_class['title']; ?></a></td>
                                                                <td><?php echo $upcoming_class['date']; ?></td>
                                                                <td><?php echo $upcoming_class['fee']; ?></td>
                                                                <td><?php echo $upcoming_class['booking_date']; ?></td>
                                                                <td><?php echo $upcoming_class['booking_status']; ?></td>
                                                            </tr>
                                                        <?php endforeach; ?>
                                                    </tbody>
                                                </table>
                                            <?php endif; ?>
                                            <?php if($completed_classes): ?>
                                                <table>
                                                    <thead>
                                                        <tr style="text-align:center;"><th colspan="5" style="background:#419bd5;">Completed Class Details</th></tr>
                                                        <tr>
                                                            <td><b>Class Name</b></td>
                                                            <td><b>Date</b></td>
                                                            <td><b>Fee</b></td>
                                                            <td><b>Booking date</b></td>
                                                            <td><b>Session Status</b></td>
                                                        </tr>
                                                    </thead>
                                                    <tbody>
                                                        <?php foreach ($completed_classes as $completed_class): ?>
                                                            <tr>
                                                                <td><a href="<?php echo $upcoming_class['permalink']; ?>" target="_blank"><?php echo $completed_class['title']; ?></a></td>
                                                                <td><?php echo $completed_class['date']; ?></td>
                                                                <td><?php echo $completed_class['fee']; ?></td>
                                                                <td><?php echo $completed_class['booking_date']; ?></td>
                                                                <td><?php echo $completed_class['booking_status']; ?></td>
                                                            </tr>
                                                        <?php endforeach; ?>
                                                    </tbody>
                                                </table>
                                            <?php endif; ?>
                                        <?php else : ?>
                                            <h4>There are no enrolled programs under your account.</h4>
                                            <table>
                                                <thead><tr style="text-align:center;"><th colspan="5" style="background:#419bd5;">Class Details</th></tr></thead>
                                                <tbody><tr><td colspan="5">No records to show</td></tr></tbody>
                                            </table>
                                        <?php endif; ?>
                                    <?php else: ?>
                                        <table>
                                            <thead><tr style="text-align:center;"><th colspan="5" style="background:#419bd5;">Class Details</th></tr></thead>
                                            <tbody><tr><td colspan="5">Please login to view your purchase history</td></tr></tbody>
                                        </table>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<style> .mcb-column-inner { margin: unset !important; } table th, table td { text-align: unset !important } </style>
<?php
echo do_shortcode('[elementor-template id="1876"]');
echo do_shortcode('[elementor-template id="1678"]');
get_footer();
