<?php
/**
 * template for class archive
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
                <p style="color:#fffbf3;">
                <?php /* Instructor: 
                    <b>
                        <?php $instructor = get_field('instructor_name', get_field('multiclass_instructor'));
                        if ($instructor) {
                            echo $instructor;
                        } else {
                            echo "Not Assigned";
                        } ?>
                    </b>
                */ ?>
                <?php while( have_rows('multiclass_recurring_dates') ) : the_row(); ?>
                    Starts from <b><?php echo date('D, d M Y', strtotime(get_sub_field('multiclass_session_date'))) ?></b>
                    <?php /* <br>Time <b><?php echo date('h:i a', strtotime(get_sub_field('multiclass_session_date'))) ?></b> */ ?>
                    <?php break;
                endwhile; ?>


                <br>Fee: <b><?php the_field('multiclass_fee') ?></b>
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
                    $schedule = get_field('multiclass_recurring_dates');
                    $start_date = strtotime($schedule[0]['multiclass_session_date']);
                    $last_date = end($schedule);
                    $last_date = strtotime($last_date['multiclass_session_date_end']);
                    $current_date = strtotime("now");
                    // close booking once program day is reached
                    if($current_date > $start_date){
                        //update_field('multiclass_booking_enabled', 'Closed');

                        // autoarchive in one day past program completion
                        if($current_date > $last_date){
                            $update_post = array(
                                'post_type' => 'multiclass',
                                'ID' => get_the_ID(),
                                'post_status' => "archived"
                            );
                            //$statusTest = wp_update_post($update_post);
                        }
                    }

                    // close booking once location's capacity is reached
                    // multiclass capacity is taken from the first session's location capacity
                    $location_id = $schedule[0]['multiclass_session_location'];
                    $location_capacity = (int) get_field('location_seating_capacity', $location_id);

                    $rows = get_field('multiclass_participant_details');
                    if(!$rows) { $rows = []; }
                    $current_number_of_participants = count($rows);
                    if($current_number_of_participants >= $location_capacity){
                        //update_field('multiclass_booking_enabled', 'Closed');
                    }

                    $is_open = get_field('multiclass_booking_enabled'); 
                    $user_participated = false;

                    if( $rows ) :
                        $current_user_id = get_current_user_id();
                        foreach( $rows as $row ) :
                            // return type is user array
                            $participantID = $row['multiclass_participant_id']['ID'];
                            // current user is enrolled
                            if($participantID == $current_user_id){
                                echo "<p class=''><small style='color:#6e2a36'>Already Enrolled</small></p>";
                                $user_participated = true;
                                break; 
                            }
                        endforeach;
                        
                        if(!$user_participated && "Open" == $is_open): ?>
                            <a class="button button_size_2" onclick="book(this)" data-amount="<?php the_field('multiclass_fee'); ?>" id="multiclass_<?php echo get_the_ID(); ?>"><span class="button_label">Enroll Now</span></a>
                        <?php elseif(!$user_participated): echo "<a class='button button_size_2' href='#' style='background: #000;' disabled><span class='button_label'>Fully Booked</span></a>"; ?>
                        <?php endif; ?>
                <?php else : // no one has yet booked the program ?>
                    <?php if("Open" == $is_open): ?>
                        <a class="button button_size_2" onclick="book(this)" data-amount="<?php the_field('multiclass_fee'); ?>" id="multiclass_<?php echo get_the_ID(); ?>"><span class="button_label">Enroll Now</span></a>
                    <?php else: echo "<a class='button button_size_2' href='#' style='background: #000;' disabled><span class='button_label'>Fully Booked</span></a>"; ?>
                    <?php endif; ?>
                <?php endif; ?>

                <?php 
                    $eventat_link = (get_field("multiclass_eventat")) ? get_field("multiclass_eventat") : false; 
                    if($eventat_link){
                        echo "<a class='button button_size_2' href='{$eventat_link}' target='_blank'><img src='".get_site_url()."/wp-content/uploads/2024/05/event.png' width='50px' /></a>";
                    }
                ?>
            </div>
        </div>
    </div>
</article>