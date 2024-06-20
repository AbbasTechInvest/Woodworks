<?php
/**
 * The template is for class purchase.
 * Template Name: Class
 */

get_header(); ?>



<div class="container" style="margin-bottom:3vh; margin-top:5vh;">
    <div class="wrap mcb-wrap mcb-wrap-a0a9525cc one tablet-one laptop-one mobile-one valign-top clearfix"
        data-desktop-col="one" data-laptop-col="laptop-one" data-tablet-col="tablet-one" data-mobile-col="mobile-one"
        style="padding:;background-color:">
        <div class="mcb-wrap-inner mcb-wrap-inner-a0a9525cc mfn-module-wrapper mfn-wrapper-for-wraps">
            <div class="mcb-wrap-background-overlay"></div>
            <div class="column_attr mfn-inline-editor clearfix align_center" style="">
                <h2 style="font-size:2.5em;"><?php _e('Classes'); ?></h2>
            </div>
            <div class="column mcb-column mcb-item-f683dd4ac one laptop-one tablet-one mobile-one column_blog" style="">
                <div class="mcb-column-inner mfn-module-wrapper mcb-column-inner-f683dd4ac mcb-item-blog-inner">
                    <div class="column_filters">
                        <div class="blog_wrapper isotope_wrapper clearfix">
                            <div class="posts_group lm_wrapper col-3 photo2">
                                <!-- <div id="primary" class="content-area">
    <main id="main" class="site-main" role="main">
        <h2 class="container" style="margin-top:5vh; margin-bottom:2vh"><?php //the_title(); ?></h2>
        <div class="container" style="margin-bottom:5vh"> -->
                                <?php
                                // The Query.
                                $args = array(
                                    "post_type" => "class",
                                    'posts_per_page' => 10,
                                    'paged' => $paged,
                                    'post_status' => "publish"
                                );
                                $the_query = new WP_Query($args);

                                // The Loop.
                                if ($the_query->have_posts()):
                                    while ($the_query->have_posts()):
                                        $the_query->the_post(); ?>

                                        <?php get_template_part('content', 'class_archive_alt'); ?>

                                    <?php endwhile; ?>
                                    <?php $num_pages = $the_query->max_num_pages; ?>
                                    <?php require_once "pagination.php"; ?>
                                <?php else: ?>
                                    <div class="container" style="max-width: 750px; padding: 25px; margin-bottom:70px">
                                        <h3>There are no active classes currently.</h3>
                                        <table>
                                            <thead>
                                                <tr><th colspan="6" style="background:#419bd5">Class Sessions</th></tr>
                                            </thead>
                                            <tbody>
                                                <tr><td colspan="6">No records to show</td></tr>
                                            </tbody>
                                        </table>
                                    </div>
                                <?php endif;
                                // Restore original Post Data.
                                wp_reset_postdata();
                                ?>
                                <!-- </div>
    </main>
</div> -->

                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="column mcb-column mcb-item-30126a0aa one laptop-one tablet-one mobile-one column_column"
                style="">
                <div class="mcb-column-inner mfn-module-wrapper mcb-column-inner-30126a0aa mcb-item-column-inner">
                    
                    <div><u><b>Wood Work:</b></u> A one hour session where participants are first given a detailed demonstration on how to assemble the wood pieces provided, in order to build the chosen project. They are then encouraged to build their items individually, applying what they learnt. Each participant takes their finished project home with them after the workshop.</div><br>
                    <div><u><b>Craft Work:</b></u> We offer a variety of art activities, through which we aim to help children explore and use their creativity. Teaching them that when it comes to art, the fun is all in the process. We are constantly creating new and varied projects using all kinds of materials! Each participant takes their finished project home with them after the workshop.</div><br>
                </div>
            </div>
        </div>
    </div>
</div>
<?php require_once "pay_script.php"; ?>

<?php
echo do_shortcode('[elementor-template id="2334"]');
echo do_shortcode('[elementor-template id="1876"]');
echo do_shortcode('[elementor-template id="1678"]');
get_footer();
