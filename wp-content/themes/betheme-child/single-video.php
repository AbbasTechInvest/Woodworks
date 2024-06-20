<?php 
    get_header(); 

    $user_purchased = false;
    $current_user_id = get_current_user_id();
    $short_description = get_field('video_short_description');
    $video_type = get_field('video_type');
    $video_url = get_field('video_vimeo_link');
    $rows = get_field('video_purchaser_details');

    $is_free = 0;
    if ($video_type == "Free"){
        $is_free = 1; 
    }
    elseif ($rows){
        foreach ($rows as $row){
            // return type is user array
            $participantID = $row['video_purchaser_id']['ID'];
            // current user has purchased the video
            if ($participantID == $current_user_id) {
                $user_purchased = true; 
            }
        }
    }
?>

    <div class="section_wrapper mcb-section-inner" style="padding-right:10%; padding-left:10%; margin-bottom:0">
        <div class="wrap mcb-wrap mcb-wrap-m2kan4x59 one  valign-top clearfix" data-col="one" style="">
            <div class="mcb-wrap-inner">
                <div class="column mcb-column mcb-item-a5lxzum8b one-fourth column_hover_color" style="padding:0.5vh;">
                    <div class="hover_color align_"
                        style="background-color:#ffbf3d;border-color:;border-radius:16px;margin:5px;"
                        ontouchstart="this.classList.toggle('hover');">
                        <div class="hover_color_bg"
                            style="background-color:#ffcb62;border-color:;border-width:;border-radius:15px;">
                            <div class="hover_color_wrapper" style="padding:50px 30px 35px;"><img class="scale-with-grid"
                                    src="<?php echo get_site_url(); ?>/wp-content/uploads/2024/02/answer_5732163.png" width="100px" alt="">
                                <hr class="no_line" style="margin: 0 auto 20px auto">

                                <p class="big" style="font-size:1.5em;"><span style="color:#300843;">Choose a Course</span></p>
                                <!-- <p><span style="color:#300843;">Challenge yourself to make the next big thing.</span> -->
                                </p>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="column mcb-column mcb-item-bi70y8x6a one-fourth column_hover_color" style="padding:0.5vh;">
                    <div class="hover_color align_"
                        style="background-color:#ff4444;border-color:;border-radius:16px;margin:5px;"
                        ontouchstart="this.classList.toggle('hover');">
                        <div class="hover_color_bg"
                            style="background-color:#ff6060;border-color:;border-width:;border-radius:15px;">
                            <div class="hover_color_wrapper" style="padding:50px 30px 35px;"><img class="scale-with-grid"
                                    src="<?php echo get_site_url(); ?>/wp-content/uploads/2024/02/payment_10106313.png" width="100px" alt="">
                                <hr class="no_line" style="margin: 0 auto 20px auto">

                                <p class="big" style="font-size:1.5em;">Pay the Amount</p>
                                <!-- <p>Be punctual to get the best out of the session.</p> -->
                            </div>
                        </div>
                    </div>
                </div>
                <div class="column mcb-column mcb-item-3qydimjjs one-fourth column_hover_color" style="padding:0.5vh;">
                    <div class="hover_color align_"
                        style="background-color:#732c94;border-color:;border-radius:16px;margin:5px;"
                        ontouchstart="this.classList.toggle('hover');">
                        <div class="hover_color_bg"
                            style="background-color:#5a2274;border-color:;border-width:;border-radius:15px;">
                            <div class="hover_color_wrapper" style="padding:50px 30px 35px;"><img class="scale-with-grid"
                                    src="<?php echo get_site_url(); ?>/wp-content/uploads/2024/02/play_440796.png" width="100px" alt="">
                                <hr class="no_line" style="margin: 0 auto 20px auto">

                                <p class="big" style="font-size:1.5em;">View the Tutorial</p>
                                <!-- <p>Take care of yourself and your belongings.</p> -->
                            </div>
                        </div>
                    </div>
                </div>
                <div class="column mcb-column mcb-item-h8mfji2tv one-fourth column_hover_color" style="padding:0.5vh;">
                    <div class="hover_color align_"
                        style="background-color:#ff0d47;border-color:;border-radius:16px;margin:5px;"
                        ontouchstart="this.classList.toggle('hover');">
                        <div class="hover_color_bg"
                            style="background-color:#ff3465;border-color:;border-width:;border-radius:15px;">
                            <div class="hover_color_wrapper" style="padding:50px 30px 35px;"><img class="scale-with-grid"
                                    src="<?php echo get_site_url(); ?>/wp-content/uploads/2024/02/hand_7371078.png" width="100px" alt="">
                                <hr class="no_line" style="margin: 0 auto 20px auto">

                                <p class="big" style="font-size:1.5em;">Practice Along</p>
                                <!-- <p>Hurry Up! limited seats are available.</p> -->
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>


    <div class="container" style="max-width: 1200px; padding: 25px">

        <div class="elementor-element elementor-element-4317939 elementor-widget elementor-widget-woocommerce-breadcrumb" data-id="4317939" data-element_type="widget" data-widget_type="woocommerce-breadcrumb.default">
            <div class="elementor-widget-container" style="display: flex;justify-content: center;margin:4%">
                <!-- <link rel="stylesheet" href="<?php //echo get_site_url(); ?>/wp-content/plugins/elementor-pro/assets/css/widget-woocommerce.min.css" /> -->
                <nav class="woocommerce-breadcrumb" aria-label="Breadcrumb">
                    <a href="<?php echo site_url(); ?>/">Home</a>&nbsp;/&nbsp;
                    <a href="<?php echo site_url(); ?>/video">Tutorial Videos</a>
                    &nbsp;/&nbsp;<?php the_title(); ?></a>
                </nav>
            </div>
        </div>

        <?php if ($is_free): ?>
                <h2><?php the_title() ?></h2>
            </div>
            <div style="background:#000;">
                <div class="container" style="padding:3% 5%">
                    <div style="padding:56.25% 0 0 0;position:relative;">
                        
                        <?php /* smaple url structure: https://player.vimeo.com/video/903738885 */?>
                        <iframe src="<?php echo $video_url; ?>" frameborder="0" allow="autoplay; fullscreen; picture-in-picture" style="position:absolute;top:0;left:0;width:100%;height:100%;" title="<?php the_title() ?>"></iframe>
                    </div>
                </div>
            </div>
            <script src="https://player.vimeo.com/api/player.js"></script>
            <div><?php /* Open div tag for valid syntax */ ?>
        
        <?php elseif ($user_purchased): ?>
                <h2><?php the_title() ?></h2>
            </div>
            <div style="background:#000;">
                <div class="container" style="padding:3% 5%">
                    <div style="padding:56.25% 0 0 0;position:relative;">
                        
                        <?php /* smaple url structure: https://player.vimeo.com/video/903738885 */?>
                        <iframe src="<?php echo $video_url; ?>" frameborder="0" allow="autoplay; fullscreen; picture-in-picture" style="position:absolute;top:0;left:0;width:100%;height:100%;" title="<?php the_title() ?>"></iframe>
                    </div>
                </div>
            </div>
            <script src="https://player.vimeo.com/api/player.js"></script>
            <div><?php /* Open div tag for valid syntax */ ?>

        <?php else: ?>
            <?php get_template_part('content', 'video_single'); ?>

        <?php endif; ?>
    </div>
<?php require_once "pay_script.php"; ?>

<?php if (!$user_purchased): ?>
    <?php echo do_shortcode('[elementor-template id="1876"]'); ?>
    <?php echo do_shortcode('[elementor-template id="1678"]'); ?>
<?php endif; ?>
<?php get_footer(); ?>