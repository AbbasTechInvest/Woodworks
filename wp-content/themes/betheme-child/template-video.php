<?php
/**
 * The template is for video purchase.
 * Template Name: Video
 */

get_header(); ?>
<div style="padding:12vw; margin-bottom:5vh; background:url('<?php echo site_url(); ?>/wp-content/uploads/2024/02/pexels-vanessa-loring-7869442.jpg'); background-size:cover; repeat:no-repeat;">

</div>

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
                        <h2>Tutorial Videos</h2>
                        <p style="text-align:initial; font-size:1.2em; ">Introducing our Tutorial Videos<br>

                                Enhance your skills and knowledge with our carefully curated tutorial videos, covering wide range of topics, ensuring there's something for everyone.
<br>
                                Our expert instructors provide step-by-step guidance to help you master new techniques and concepts.
<br>
                                Explore our collection today and unlock your potential with our tutorial videos.
                        </p>
                    </div>
                </div>
            </div>
            <div class="column mcb-column mcb-item-f683dd4ac one laptop-one tablet-one mobile-one column_blog" style="">
                <div class="mcb-column-inner mfn-module-wrapper mcb-column-inner-f683dd4ac mcb-item-blog-inner">
                    <div class="column_filters">
                        <div class="blog_wrapper isotope_wrapper clearfix">
                            <div class="posts_group lm_wrapper col-3 photo2">
                                <?php
                                    // The Query.
                                    $args = array(
                                        "post_type" => "video",
                                        'posts_per_page' => 10,
                                        'paged' => $paged
                                    );
                                    $the_query = new WP_Query($args);

                                    // The Loop.
                                    if ($the_query->have_posts()):
                                        while ($the_query->have_posts()):
                                            $the_query->the_post(); ?>

                                            <?php get_template_part( 'content', 'video_archive_alt' ); ?>
                                        
                                        <?php endwhile; ?>
                                        <?php $num_pages = $the_query->max_num_pages; ?>
                                        <?php require_once "pagination.php"; ?>
                                    <?php else:
                                        esc_html_e('No videos found.');
                                    endif;
                                    // Restore original Post Data.
                                    wp_reset_postdata();
                                ?>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<?php require_once "pay_script.php"; ?>

<?php
echo do_shortcode('[elementor-template id="1876"]');
echo do_shortcode('[elementor-template id="1678"]');
get_footer();
