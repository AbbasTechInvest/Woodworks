<?php
/**
 * The template is for customised products.
 * Template Name: Custom Product
 */

get_header(); ?>

<div class="container" style="margin-bottom:5vh">
    <div class="wrap mcb-wrap mcb-wrap-a0a9525cc one tablet-one laptop-one mobile-one valign-top clearfix"
        data-desktop-col="one" data-laptop-col="laptop-one" data-tablet-col="tablet-one" data-mobile-col="mobile-one"
        style="padding:;background-color:">
        <div class="mcb-wrap-inner mcb-wrap-inner-a0a9525cc mfn-module-wrapper mfn-wrapper-for-wraps">
            <div class="mcb-wrap-background-overlay"></div>
            <div class="column mcb-column mcb-item-30126a0aa one laptop-one tablet-one mobile-one column_column"
                style="">
                <div class="mcb-column-inner mfn-module-wrapper mcb-column-inner-30126a0aa mcb-item-column-inner">
                    <div class="column_attr mfn-inline-editor clearfix align_center" style="">
                        <h2>Your Customised Product Requests</h2>
                    </div>
                </div>
            </div>
            <div class="column mcb-column mcb-item-f683dd4ac one laptop-one tablet-one mobile-one column_blog" style="">
                <div class="mcb-column-inner mfn-module-wrapper mcb-column-inner-f683dd4ac mcb-item-blog-inner">
                    <div class="column_filters">
                        <div class="blog_wrapper isotope_wrapper clearfix">
                            <div class="posts_group lm_wrapper col-3 photo2">
                                <?php if(is_user_logged_in()): ?>
                                    <?php
                                        global $current_user;
                                        wp_get_current_user();

                                        $paged = ( get_query_var( 'paged' ) ) ? get_query_var( 'paged' ) : 1;

                                        $args = array(  
                                            'post_type' => 'custom-product',
                                            'post_status' => array('publish', 'pending', 'draft'),
                                            'author__in' => array($current_user->ID),
                                            'posts_per_page'=> 10,
                                            'paged' => $paged
                                        );
                                
                                        $nows_date = strtotime("now");

                                        $the_query = new WP_Query($args);

                                        if ($the_query->have_posts()):
                                            while ($the_query->have_posts()): $the_query->the_post(); ?>
                                                <?php if("pending" == get_post_status()): ?>
                                                    <?php
                                                        $post_date = strtotime(get_the_date());
                                                        $diff = $nows_date - $post_date;
                                                        $ARCHIVE_LIMIT = 60*60*24; // seconds in a day
                                                        if($diff > $ARCHIVE_LIMIT){
                                                            $post_status = "archived";
                                                            
                                                            $update_post = array(
                                                                'post_type' => 'custom-product',
                                                                'ID' => get_the_ID(),
                                                                'post_status' => $post_status
                                                            );
                                                            // this post will be hidden on next time page visit
                                                            $statusTest = wp_update_post($update_post);
                                                        }
                                                    ?>
                                                    <?php get_template_part( 'content', 'custom_product_archive' ); ?>
                                                <?php else: ?>
                                                    <?php get_template_part( 'content', 'custom_product_archive' ); ?>
                                                <?php endif; ?>
                                            <?php endwhile; ?>
                                        <?php else: ?>
                                            <div class="container" style="max-width: 750px; padding: 25px; margin-bottom:70px">
                                                <h3><?php esc_html_e("You don't have any active requests now."); ?></h3>
                                                <table>
                                                    <thead>
                                                        <tr><th colspan="6" style="background:#f3d8bb">Your Customization Requests</th></tr>
                                                    </thead>
                                                    <tbody>
                                                        <tr><td colspan="6">No records to show</td></tr>
                                                    </tbody>
                                                </table>
                                            </div>
                                        <?php endif; ?>
                                    <?php $num_pages = $the_query->max_num_pages; ?>
                                    <?php require_once "pagination.php"; ?>
                                <?php else: ?>
                                    <div class="container" style="max-width: 750px; padding: 25px; margin-bottom:70px">
                                        <h3><?php esc_html_e("Please login to view your requests."); ?></h3>
                                        <table>
                                            <thead>
                                                <tr><th colspan="6" style="background:#f3d8bb">Your Customization Requests</th></tr>
                                            </thead>
                                            <tbody>
                                                <tr><td colspan="6">No records to show</td></tr>
                                            </tbody>
                                        </table>
                                    </div>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
    <?php require_once "pay_script.php"; ?>

<?php echo do_shortcode('[elementor-template id="1876"]'); ?>
<?php echo do_shortcode('[elementor-template id="1678"]'); ?>
<?php get_footer(); ?>