<?php
/**
 * template for custom product requests archive
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
                <h4 class="entry-title" itemprop="headline"><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a></h4>
                <p style="color:#fffbf3;">Requested On: <b><?php echo date("D, d M Y",strtotime(get_the_date())); ?></b>
                    <br>Request Status: <?php $status = get_field('cp_request_status'); if($status==null) { echo "Requested"; } else { echo $status; } ?></b>
                    <br>Fee: <b><?php $fee = get_field('cp_fee'); if($fee) { echo $fee; } else { $fee = 0; echo "Not Set"; } ?></b></p>
                    <?php if($fee): ?>
                        <b>
                        <?php 
                            $payment_status = get_field('cp_payment_status');
                            if($payment_status == null){
                                echo "Unpaid";
                            }
                        ?>
                        <?php if($payment_status=="Unpaid" && $fee > 0 && $status == 'Scheduled') : ?>
                            <button class="button button_size_2" onclick="book(this)" data-amount="<?php echo $fee ?>" id="custom-product_<?php echo get_the_ID(); ?>"><span class="button_label">Pay Now</span></button>  
                        <?php else : echo $payment_status; ?>
                        <?php endif; ?>
                        </b><br>
                    <?php endif; ?>
                </p>                  
            </div>
        </div>
    </div>
</article>