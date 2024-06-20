<?php
/**
 * template for class archive
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
                                <div class="column_attr mfn-inline-editor clearfix " style="background-color:#f2634d;padding:20px 8%;">
                                    <a href="<?php the_permalink(); ?>"><h2 style="color:#fffbf3; margin-top:5vh;"><?php the_title(); ?></h2></a>
                                    <p style="color:#fffbf3;"><?php /* Instructor: 
                                        <b>
                                            <?php $instructor = get_field('instructor_name', get_field('class_instructor'));
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
                                            update_field('multiclass_booking_enabled', 'Closed');

                                            // autoarchive in one day past program completion
                                            if($current_date > $last_date){
                                                $update_post = array(
                                                    'post_type' => 'multiclass',
                                                    'ID' => get_the_ID(),
                                                    'post_status' => "archived"
                                                );
                                                $statusTest = wp_update_post($update_post);
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
                                            update_field('multiclass_booking_enabled', 'Closed');
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
                                                    echo "<p class=''><small class=''>Already Enrolled</small></p>";
                                                    $user_participated = true;
                                                    break; 
                                                }
                                            endforeach;
                                            
                                            if(!$user_participated && "Open" == $is_open): ?>
                                                <button class="button button_size_2" onclick="book(this)" data-amount="<?php the_field('multiclass_fee'); ?>" id="multiclass_<?php echo get_the_ID(); ?>"><span class="button_label">Enroll Now</span></button>
                                            <?php elseif(!$user_participated): echo "<p class=''><small class=''>Booking Closed</small></p>"; ?>
                                            <?php endif; ?>
                                    <?php else : // no one has yet booked the program ?>
                                        <?php if("Open" == $is_open): ?>
                                            <button class="button button_size_2" onclick="book(this)" data-amount="<?php the_field('multiclass_fee'); ?>" id="multiclass_<?php echo get_the_ID(); ?>"><span class="button_label">Enroll Now</span></button>
                                        <?php else: echo "Booking Closed"; ?>
                                        <?php endif; ?>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>