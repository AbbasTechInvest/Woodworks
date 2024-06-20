<?php
/**
 * template for video archive
*/

$current_user_id = get_current_user_id(); ?>
        <section class="section mcb-section mfn-default-section mcb-section-8a18bfb36  no-margin-h no-margin-v equal-height" style="margin-bottom:5vh; max-width:600px;">
            <div class="mcb-background-overlay"></div>
            <div class="section_wrapper mfn-wrapper-for-wraps mcb-section-inner mcb-section-inner-8a18bfb36">
                <div class="wrap mcb-wrap mcb-wrap-c36aebd86 one tablet-one laptop-one mobile-one valign-top clearfix" data-desktop-col="one" data-laptop-col="laptop-one" data-tablet-col="tablet-one" data-mobile-col="mobile-one" style="padding:;background-color:;border-radius: 20px; overflow: hidden; max-height:600px">
                    <div class="mcb-wrap-inner mcb-wrap-inner-c36aebd86 mfn-module-wrapper mfn-wrapper-for-wraps">
                        <div class="mcb-wrap-background-overlay"></div>
                        <div class="column mcb-column mcb-item-98d99719b one-second laptop-one-second tablet-one-second mobile-one column_column" style="max-height:305px">
                            <div class="mcb-column-inner mfn-module-wrapper mcb-column-inner-98d99719b mcb-item-column-inner">
                                <div class="column_attr mfn-inline-editor clearfix bg-cover" style="background-image:url('<?php the_post_thumbnail_url(); ?>');background-repeat:no-repeat;background-position:center;">
                                    <hr class="no_line" style="margin: 0 auto 550px auto">
                                </div>
                            </div>
                        </div>
                        <div class="column mcb-column mcb-item-0ad5e35f9 one-second laptop-one-second tablet-one-second mobile-one column_column" style="">
                            <div class="mcb-column-inner mfn-module-wrapper mcb-column-inner-0ad5e35f9 mcb-item-column-inner" style="">
                                <div class="column_attr mfn-inline-editor clearfix " style="background-color:#1f1a4e;padding:20px 8%;">
                                    <a href="<?php the_permalink(); ?>"><h2 style="color:#fffbf3;"><?php the_title(); ?></h2></a>
                                    <p style="color:#fffbf3;">
                                    
                                    <br>Fee: 
                                    <b>
                                        <?php
                                            $video_type = get_field('video_type');
                                            if($video_type == "Free"){
                                                echo "<small class=''>Not Charged</small>";
                                            }
                                            else{
                                                the_field('video_fee');
                                            }
                                        ?>
                                    </b>
                                    <br><b>
                                    <?php
                                        $tags = get_the_tags();
                                        if ($tags):
                                            $i=0;
                                            foreach ($tags as $tag): ?>
                                                <small class="">
                                                    <?php
                                                        if ($i>0) { echo " | "; }
                                                        echo $tag->name;
                                                        $i++;
                                                    ?>
                                                </small>
                                            <?php endforeach;
                                        endif;
                                    ?></b></p>
                                    <?php
                                        if($video_type == "Paid"):
                                            $rows = get_field('video_purchaser_details');
                                            $user_purchased = false;

                                            if ($rows):
                                                foreach ($rows as $row):
                                                    // return type is user array
                                                    $participantID = $row['video_purchaser_id']['ID'];
                                                    // current user has purchased the video
                                                    if ($participantID == $current_user_id) {
                                                        echo "<a href='".get_permalink()."' class='button button_size_2'><span class='button_label'>View Video</span></a>";
                                                        $user_purchased = true;
                                                        break;
                                                    }
                                                endforeach;

                                                if (!$user_purchased): ?>
                                                    <button class="button button_size_2" onclick="book(this)" data-amount="<?php the_field('video_fee'); ?>" id="video_<?php echo get_the_ID(); ?>"><span class="button_label">Buy Now</span></button>
                                                <?php endif; ?>
                                            <?php else: // no one has yet purchased the video ?>
                                                <button class="button button_size_2" onclick="book(this)" data-amount="<?php the_field('video_fee'); ?>" id="video_<?php echo get_the_ID(); ?>"><span class="button_label">Buy Now</span></button>
                                            <?php endif; ?>
                                        <?php else: echo "<a href='".get_permalink()."' class='button button_size_2'><span class='button_label'>View Video</span></a>"; ?>
                                        <?php endif;
                                    ?>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>