<?php
/**
 * template for video archive alt
*/

$current_user_id = get_current_user_id(); ?>


<article class="post post-item isotope-item clearfix post-742 type-post status-publish format-standard has-post-thumbnail hentry category-uncategorized" style="">
    <div class="image_frame post-photo-wrapper scale-with-grid images_only">
        <div class="image_wrapper">
            <a href="<?php the_permalink(); ?>">
                <div class="mask"></div>
                <?php /* https://stackoverflow.com/a/51447865/21151561 */ ?>
                <img src="<?php echo get_the_post_thumbnail_url(get_the_ID(), 'thumbnail'); ?>" class="scale-with-grid wp-post-image" alt="" decoding="async" style="aspect-ratio: 1 / 1; object-fit: cover;">
            </a>
        </div>
    </div>
    <div class="post-desc-wrapper bg- has-custom-bg" style="background-color: #419bd5">
        <div class="post-desc" style="padding:25px 25px;">
            <div class="post-head"></div>
            <div class="post-title">
                <h4 class="entry-title" itemprop="headline" style="font-size:1.2em;"><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a></h4>
                <p style="color:#1f1a4e;">Fee: 
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
                    ?></b>
                </p>
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
</article>