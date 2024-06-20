<script type='text/javascript' src="https://cdnjs.cloudflare.com/ajax/libs/jquery-ajaxtransport-xdomainrequest/1.0.1/jquery.xdomainrequest.min.js"></script>

<script>
    jQuery('table td').css('text-align','unset');

    // auto update amount for wallet recharge
    function update_wallet_recharge_amount(e){
        let wallet = document.getElementById("wallet_0");
        if(isNaN(e)){
            wallet.setAttribute('data-amount', e.value);
        }
        else{
            wallet.setAttribute('data-amount', e);
        }
    }

    function update_gift_recharge_amount(e){
        let gift = document.getElementById("gift_0");
        gift.setAttribute('data-amount', e.value);
    }

    function book(e, beneficiary=null, gift_message=null){
        <?php if ( !is_user_logged_in() ) : ?>
            window.location.href = "<?php echo wp_login_url( get_permalink() ); ?>";
        <?php else: ?>
            const post_details = e.id.split("_");
            const post_amount = <?php //echo $post_amount; ?> jQuery(e).attr('data-amount');
            const typex = post_details[0] + "";
            
            let seats = 1;
            let seats_input = "input#" + post_details[1];
            if(jQuery(seats_input).length) { seats = jQuery(seats_input).val(); }
            else { seats = 1; }
            const cart_url = "<?php echo get_site_url(); ?>" + "/custom-cart/";
            
            var post_form = jQuery('<form action="' + cart_url + '" method="post">' +
            // _ to prevent route conflict with post type form submit
            '<input type="hidden" name="post_type" value="_' + post_details[0] + '" />' +
            '<input type="hidden" name="post_id" value="' + post_details[1] + '" />' +
            '<input type="hidden" name="post_amount" value="' + post_amount + '" />' +
            '<input type="hidden" name="post_seats" value="' + seats + '" />' +
            '<input type="hidden" name="post_beneficiary" value="' + beneficiary + '" />' +
            '<input type="hidden" name="post_gift_message" value="' + gift_message + '" />' +
            '</form>');

            jQuery('body').append(post_form);
            post_form.submit();
        <?php endif; ?>
    } // function book()

    function gift(e){
        <?php if ( !is_user_logged_in() ) : ?>
            window.location.href = "<?php echo wp_login_url( get_permalink() ); ?>";
        <?php else: ?>            
            const post_amount = jQuery(e).attr('data-amount');
            if(post_amount <= 0) {
                alert("Please enter a valid amount"); return;
            }

            const validateEmail = (email) => {
                return email.match(
                    /^(([^<>()[\]\\.,;:\s@\"]+(\.[^<>()[\]\\.,;:\s@\"]+)*)|(\".+\"))@((\[[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\])|(([a-zA-Z\-0-9]+\.)+[a-zA-Z]{2,}))$/
                );
            };
            const bnfcry = jQuery('#ben_email').val();
            const cstmsg = jQuery('#ben_msg').val();
            if(validateEmail(bnfcry)){
                // ajax
                // console.log("Success"); return;

                jQuery.ajax({
                    type: "POST",
                    url: "<?php echo get_site_url()."/wp-content/themes/betheme-child/chkeml.php"; ?>",
                    data: { 
                        ben_email: bnfcry 
                    },
                    success: function(data){
                        // console.log(data);
                        var v = JSON.parse(data);
                        if(v.code == 200){
                            book(e, beneficiary=bnfcry, gift_message=cstmsg); // passing the event variable as the target element's event for function call
                        }
                        else{
                            alert(v.msg); return;
                        }
                    },
                    complete: function() {
                        //alert('gift card sent');
                    }
                });
            }
            else{
                alert("Please enter a valid Email"); return;
            }
        <?php endif; ?>
    } // function gift()

    function start_payment_gateway(e, beneficiary=null, gift_message=null){
        <?php if ( !is_user_logged_in() ) : ?>
            window.location.href = "<?php echo wp_login_url( get_permalink() ); ?>";
        <?php else: ?>
        
        const post_details = e.id.split("_");
        const post_amount = <?php //echo $post_amount; ?> jQuery(e).attr('data-amount');
        const post_seats = jQuery(e).attr('data-seats');

        jQuery.ajax({
            type: "POST",
            url: "<?php echo get_site_url()."/wp-content/themes/betheme-child/hesabe.php"; ?>",
            data:{
                post_type: post_details[0],
                post_id: post_details[1],
                post_amount: post_amount,
                post_seats: post_seats,
                post_beneficiary: beneficiary,
                post_gift_message: gift_message
            },
            beforeSend: function() {
                
            },
            success: function(data){
                // success
                //console.log(data);
                var v = JSON.parse(data);
                if(v.code == 200){
                    if(v.redirect != ""){
                        let link = v.redirect + "/payment?data=" + v.paymentData;
                        window.location.href = link;
                    }
                }
                if(v.code == 404){
                    alert("Invalid details");
                }
            }
        });
        <?php endif; ?>     
    } // start_payment_gateway()
    <?php 
        $token = bin2hex(random_bytes(20)); 
        session_start();
        $_SESSION['wallet_pay_token'] = $token; 
    ?>
    function pay_with_wallet(e, beneficiary=null, gift_message=null){
        <?php if ( !is_user_logged_in() ) : ?>
            window.location.href = "<?php echo wp_login_url( get_permalink() ); ?>";
        <?php else: ?>
        
        const post_details = e.id.split("_");
        const post_amount = <?php //echo $post_amount; ?> jQuery(e).attr('data-amount');
        <?php /* TODO: patch security loophole */ ?>
        const post_token = '<?php echo $token; ?>';
        const post_seats = jQuery(e).attr('data-seats');

        jQuery.ajax({
            type: "POST",
            url: "<?php echo get_site_url()."/wp-content/themes/betheme-child/pay_with_wallet.php"; ?>",
            data:{
                post_type: post_details[0],
                post_id: post_details[1],
                post_amount: post_amount,
                post_token: post_token,
                post_seats: post_seats,
                post_beneficiary: beneficiary,
                post_gift_message: gift_message
            },
            beforeSend: function() {
                
            },
            success: function(data){
                // success
                //console.log(data);
                var v = JSON.parse(data);
                if(v.code == 200){
                    // edit page html
                    jQuery('#title').text("Payment Success!");
                    jQuery('#text').text("Your Payment with wallet was Successful");
                    let balance = <?php echo isset($balance) ? $balance : 0; ?>;
                    console.log("old balance" + balance);
                    balance = balance - post_amount;
                    jQuery('#wallet_balance').text(balance);
                    jQuery('button').hide();

                    // add button to redirect to post
                    let html = "<center><span><a href='" + <?php get_site_url() ?> + "/my-account'>My Account</a> &nbsp;&nbsp;&nbsp; <a href='" + <?php get_site_url() ?> + "/terms-and-conditions/'>Terms</a></span></center>";
                    $("#payment_cart").append(html);
                }
                else if(v.code == 404){
                   alert("Invalid details. Please refresh the page");
                }
                else if(v.code == 401){
                   alert("Invalid details. Please refresh the page");
                }
                else{
                    alert("Technical issues detected. Please try again after some time");
                }
            }
        });
        <?php endif; ?>
        <?php //session_destroy(); ?>     
    } // pay_with_wallet()
</script>