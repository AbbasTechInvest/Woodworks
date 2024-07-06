<?php get_header(); ?>

    <div id="primary" class="content-area">
		<main id="main" class="site-main" role="main">
            <div class="container" style="max-width: 700px; padding: 25px">
                <h2><?php the_title(); ?></h2>
                <img src="<?php the_post_thumbnail_url(); ?>" width="100%" />
                <table>
                    <thead>
                        <tr><th colspan="2" style="background:#419bd5">Class Details</th></tr>
                    </thead>
                    <tbody>
                        <?php 
                            $schedule = get_field('multiclass_recurring_dates');
                            $start_date = strtotime($schedule[0]['multiclass_session_date']);
                            $last_date = end($schedule);
                            $last_date = strtotime($last_date['multiclass_session_date_end']);
                            $current_date = strtotime("now");
                            // close booking once program day is reached
                            if($current_date > $start_date){
                                //update_field('multiclass_booking_enabled', 'Closed');
                                
                                // autoarchive after program completion
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
                            $booked_seats = 0;
                            if( have_rows('multiclass_participant_details') ){
                                while( have_rows('multiclass_participant_details') ) { 
                                    the_row();
                                    $seat = get_sub_field('multiclass_participant_num_seats');
                                    if(!$seat){ $seat = 1; }
                                    $booked_seats += $seat;
                                }
                            }
                            $booking_limit = $location_capacity - $booked_seats;
                            $user_participated = false;
                        ?>
                        <tr><td>Booking</td><td><?php echo $is_open; ?></td></tr>
                        <tr><td>Work Type</td><td><?php the_field('multiclass_type'); ?></td></tr>
                        <tr><td>Work Subtype</td><td><?php the_field('multiclass_subtype'); ?></td></tr>
                        <tr><td>Amount</td><td><?php the_field('multiclass_fee'); ?> KD</td></tr>
                        <?php 
                            $eventat_link = (get_field("multiclass_eventat")) ? get_field("multiclass_eventat") : false; 
                            if($eventat_link){
                                echo "<tr><td>Eventat Link</td><td>";
                                echo "<a class='button button_size_2' href='{$eventat_link}' target='_blank'><img src='".get_site_url()."/wp-content/uploads/2024/05/event.png' width='50px' /></a>";
                                echo "</td></tr>";
                            }
                        ?>
                        <?php
                            $tags = get_the_tags();
                            if($tags) : $i=0; ?>
                                <tr><td><small>Tags</small></td><td>
                            <?php
                                foreach($tags as $tag) : ?>
                                    <small class=""><?php if ($i>0) { echo " | "; } echo $tag->name; $i++; ?></small>
                                <?php endforeach; ?>
                                </td></tr>
                            <?php endif;
                        ?>
                        <tr><td>Booking Status</td><td>
                            <?php 
                                if( $rows ) :
                                    $current_user_id = get_current_user_id();
                                    foreach( $rows as $row ) :
                                        // return type is user array
                                        $participantID = $row['multiclass_participant_id']['ID'];
                                        // current user is enrolled
                                        if($participantID == $current_user_id){
                                            echo "<span style='color:#6e2a36'>Already Enrolled</span>";
                                            $user_participated = true;
                                            break; 
                                        }
                                    endforeach;
                                    
                                    if(!$user_participated && "Open" == $is_open): ?>
                                        <input type=number id="<?php echo get_the_ID(); ?>" max="<?php echo $booking_limit; ?>" onchange="set_price(this)" min="1" placeholder="1" step="1" style="display: inline-block; width:70px; margin-bottom: 0;" />
                                        <button onclick="book(this)" data-amount="<?php the_field('multiclass_fee'); ?>" id="multiclass_<?php echo get_the_ID(); ?>">Enroll Now</button>
                                    <?php elseif(!$user_participated): echo "Booking Closed"; ?>
                                    <?php endif; ?>
                            <?php else : // no one has yet booked the program ?>
                                <?php if("Open" == $is_open): ?>
                                        <input type=number id="<?php echo get_the_ID(); ?>" max="<?php echo $booking_limit; ?>" onchange="set_price(this)" min="1" placeholder="1" step="1" style="display: inline-block; width:70px; margin-bottom: 0;" />
                                    <button class="button button_size_2" onclick="book(this)" data-amount="<?php the_field('multiclass_fee'); ?>" id="multiclass_<?php echo get_the_ID(); ?>"><span class="button_label">Enroll Now</span></button>
                                <?php else: echo "Booking Closed"; ?>
                                <?php endif; ?>
                            <?php endif; ?>
                        </td>
                    </tbody>
                </table>
                <table>
                    <thead><tr><th style="background:#419bd5">Description</th></tr></thead>
                    <tbody><tr><td><?php the_content(); ?></td></tr></tbody>
                </table>
                <table>
                    <thead><tr><th colspan="5" style="background:#419bd5">Class Schedule</th></tr></thead>
                    <tbody>
                        <tr>
                            <td><b>Date</b></td>
                            <td><b>Time</b></td>
                            <td><b>End Time</b></td>
                            <td><b>Location</b></td>
                            <?php /* <td><b>Instructor</b></td> */ ?>
                        </tr>
                        <?php $i=1; while( have_rows('multiclass_recurring_dates') ) : the_row(); ?>
                            <tr>
                                <td><?php echo date("D, d M Y",strtotime(get_sub_field('multiclass_session_date'))); ?></td>
                                <td><?php echo date("h:i a",strtotime(get_sub_field('multiclass_session_date'))); ?></td>
                                <td><?php echo date("h:i a",strtotime(get_sub_field('multiclass_session_date_end'))); ?></td>
                                <td><?php echo get_field('location_name', get_sub_field('multiclass_session_location')); ?></td>
                                <?php /* <td><?php echo get_field('instructor_name', get_sub_field('multiclass_instructor')); ?></td> */ ?>
                            </tr>
                        <?php $i++; endwhile; ?>
                    </tbody>
                </table>
                <?php 
                    // display participants record for admin user only
                    if ( current_user_can( 'administrator' ) ) : ?>
                    <div id="participants">
                        <table>
                            <thead>
                                <tr><th colspan="6" style="background:#419bd5">Class Participant Details <span title="Download Participants list" style="float: right;"><img src="<?php echo get_site_url() ?>/wp-content/uploads/2024/02/download.png" alt="share" onclick="createPDF()" width="15px" /></span></th></tr>
                                <tr><td>#</td><td>Name</td><td>Whatsapp</td><td>Email</td><td>Invoice</td><td>Seats</td></tr>
                            </thead>
                            <tbody>
                            <?php if( have_rows('multiclass_participant_details') ) : $i=1;
                                while( have_rows('multiclass_participant_details') ) : the_row(); ?>
                                    <tr>
                                        <td><?php echo $i; $participant_user = get_sub_field('multiclass_participant_id'); ?></td>
                                        <td><?php $u = get_sub_field('multiclass_participant_name'); echo "<a href='".get_site_url()."/wp-admin/user-edit.php?user_id=".$participant_user['ID']."'>$u</a>"; ?></td>
                                        <td><?php echo get_sub_field('multiclass_participant_whatsapp'); ?></td>
                                        <td><?php echo get_sub_field('multiclass_participant_email'); ?></td>
                                        <td><?php $pay_id = get_sub_field('multiclass_participant_payment'); echo "<a href='".get_site_url()."/wp-admin/post.php?post=$pay_id&action=edit'>".get_the_title($pay_id)."</a>"; ?></td>
                                        <td><?php echo get_sub_field('multiclass_participant_num_seats'); ?></td>
                                    </tr>
                                <?php $i++; endwhile; ?>
                            <?php else: ?>
                                <tr><td colspan="6" style="">No Participants yet.</td></tr>
                            <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                <?php endif; ?>
            </div>
        </main><!-- #main -->
	</div><!-- #primary -->
    <?php require_once "pay_script.php"; ?>
    <script>
        function set_price(e){
            const seats = e.value;
            const booking_limit = <?php echo $booking_limit; ?>;
            const id = "#multiclass_" + e.id
            if(seats > booking_limit || seats < 1 || isNaN(seats)){
                e.value = 1;
                jQuery(id).attr('data-amount', <?php the_field('multiclass_fee'); ?>);
                return;
            }
            jQuery(id).attr('data-amount', seats * <?php the_field('multiclass_fee'); ?>);
        }

        function createPDF() {
            var sTable = document.getElementById('participants').innerHTML;

            var style = "<style>";
            style = style + "table {width: 100%;font: 17px Calibri;}";
            style = style + "table, th, td {border: solid 1px #DDD; border-collapse: collapse;";
            style = style + "padding: 5px 5px;text-align: center;}";
            style = style + "</style>";

            // CREATE A WINDOW OBJECT.
            var win = window.open('', '', 'height=700,width=700');

            win.document.write('<html><head>');
            win.document.write('<title>Recurring Class #<?php echo get_the_ID(); ?> Participants list</title>');   // <title> FOR PDF HEADER.
            win.document.write(style);          // ADD STYLE INSIDE THE HEAD TAG.
            win.document.write('</head>');
            win.document.write('<body>');
            win.document.write(sTable);         // THE TABLE CONTENTS INSIDE THE BODY TAG.
            win.document.write('</body></html>');

            win.document.close(); 	// CLOSE THE CURRENT WINDOW.

            win.print();    // PRINT THE CONTENTS.
        }
    </script>
<?php get_footer(); ?>