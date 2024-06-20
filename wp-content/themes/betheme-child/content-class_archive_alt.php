<?php
/**
 * template for class archive alt
*/

$current_user_id = get_current_user_id(); 

$start_date = strtotime(get_field('class_date'));
$end_date = strtotime(get_field('class_date_end'));
$current_date = strtotime("now");
// close booking once program day is reached
if($current_date > $start_date){
    update_field('class_booking_enabled', 'Closed');

    // autoarchive after class completion
    if($current_date > $end_date){
        $update_post = array(
            'post_type' => 'class',
            'ID' => get_the_ID(),
            'post_status' => "archived"
        );
        //$statusTest = wp_update_post($update_post);
    }
}

$rows = get_field('class_participant_details');

// close booking once location's capacity is reached
$location_capacity = (int) get_field('location_seating_capacity', get_field('class_location'));
if(!$rows) { $rows = []; }
$current_number_of_participants = count($rows);
if($current_number_of_participants >= $location_capacity){
    update_field('class_booking_enabled', 'Closed');
}

$is_open = get_field('class_booking_enabled');
$user_participated = false;

?>


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
                <p style="color:#1f1a4e;"><?php /* Instructor: 
                    <b>
                        <?php $instructor = get_field('instructor_name', get_field('class_instructor'));
                        if ($instructor) {
                            echo $instructor;
                        } else {
                            echo "Not Assigned";
                        } ?>
                    </b> */ ?>
                
                Date: <b><?php echo date('D, d M Y', strtotime(get_field('class_date'))) ?></b>
                <br>Time: <b><?php echo date("h:i a", $start_date) . " - " . date("h:i a", $end_date); ?></b>
                <br>Fee: <b><?php the_field('class_fee') ?></b>
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
                    if ($rows):
                        foreach ($rows as $row):
                            // return type is user array
                            $participantID = $row['class_participant_id']['ID'];
                            // current user is enrolled
                            if ($participantID == $current_user_id) {
                                echo "<a class='button button_size_2' href='#' style='background: #000;' disabled><span class='button_label'>Already Enrolled</span></a>";
                                $user_participated = true;
                                break;
                            }
                        endforeach;
                        
                        if (!$user_participated && "Open" == $is_open): ?>
                            <button class="button button_size_2" onclick="book(this)" data-amount="<?php the_field('class_fee'); ?>" id="class_<?php echo get_the_ID(); ?>"><span class="button_label">Enroll Now</span></button>
                        <?php elseif (!$user_participated):
                            echo "<a class='button button_size_2' href='#' style='background: #000;' disabled><span class='button_label'>Fully Booked</span></a>"; ?>
                        <?php endif; ?>
                    <?php else: // no one has yet booked the program ?>
                        <button class="button button_size_2" onclick="book(this)" data-amount="<?php the_field('class_fee'); ?>" id="class_<?php echo get_the_ID(); ?>"><span class="button_label">Enroll Now</span></button>
                    <?php endif; ?>

                    <?php 
                        $eventat_link = (get_field("class_eventat")) ? get_field("class_eventat") : false; 
                        if($eventat_link){
                            echo "<a class='button button_size_2' href='{$eventat_link}' target='_blank'><img src='".get_site_url()."/wp-content/uploads/2024/05/event.png' width='50px' /></a>";
                        }
                    ?>
                  
            </div>
        </div>
    </div>
</article>