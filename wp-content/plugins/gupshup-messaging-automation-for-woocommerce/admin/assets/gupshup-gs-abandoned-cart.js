( function ( $ ) {
	let timer;
	const abandonedCart = {
		init() {
			$( document ).on(
				'change',
				'#billing_phone, input.input-text, textarea.input-text, select',
				abandonedCart.added_cart_data
            );

			$( document.body ).on( 'updated_checkout', function () {
				abandonedCart.added_cart_data();
			} );

        },

		validate_phone_no( gup_phone ) {
			if((gup_phone.replace(/\D/g,'')).length>=10 && (gup_phone.replace(/\D/g,'')).length<=13){
                return true;
            }
		},

		added_cart_data() {
            let gup_phone = jQuery( '#billing_phone' ).val();
			const gup_country = jQuery( '#billing_country' ).val();
			const phoneRegex = /^(?:\+?\d{1,3}[\s-]?)?(?:\(\d{3}\)|\d{3})[\s-]?\d{3}[\s-]?\d{4}$/;
            if ( typeof gup_phone === 'undefined' || gup_phone === null || typeof gup_country === 'undefined' || gup_country === null) {
				return;
			}
			clearTimeout( timer );
            if (phoneRegex.test(gup_phone)) {
				
				//If Phone is valid
                const gup_email = jQuery( '#billing_email' ).val();
				const gup_name = jQuery( '#billing_first_name' ).val();
				const gup_surname = jQuery( '#billing_last_name' ).val();
				const gup_city = jQuery( '#billing_city' ).val();

				//Other fields used for "Remember user input" function
				const gup_billing_company = jQuery( '#billing_company' ).val();
				const gup_billing_address_1 = jQuery(
					'#billing_address_1'
				).val();
				const gup_billing_address_2 = jQuery(
					'#billing_address_2'
				).val();
				const gup_billing_state = jQuery( '#billing_state' ).val();
				const gup_billing_postcode = jQuery(
					'#billing_postcode'
				).val();
				const gup_shipping_first_name = jQuery(
					'#shipping_first_name'
				).val();
				const gup_shipping_last_name = jQuery(
					'#shipping_last_name'
				).val();
				const gup_shipping_company = jQuery(
					'#shipping_company'
				).val();
				const gup_shipping_country = jQuery(
					'#shipping_country'
				).val();
				const gup_shipping_address_1 = jQuery(
					'#shipping_address_1'
				).val();
				const gup_shipping_address_2 = jQuery(
					'#shipping_address_2'
				).val();
				const gup_shipping_city = jQuery( '#shipping_city' ).val();
				const gup_shipping_state = jQuery( '#shipping_state' ).val();
				const gup_shipping_postcode = jQuery(
					'#shipping_postcode'
				).val();
				const gup_order_comments = jQuery( '#order_comments' ).val();

				const data = {
					action: 'gupshup_gs_save_abandoned_cart_data',
					gup_email,
					gup_name,
					gup_surname,
					gup_phone,
					gup_country,
					gup_city,
					gup_billing_company,
					gup_billing_address_1,
					gup_billing_address_2,
					gup_billing_state,
					gup_billing_postcode,
					gup_shipping_first_name,
					gup_shipping_last_name,
					gup_shipping_company,
					gup_shipping_country,
					gup_shipping_address_1,
					gup_shipping_address_2,
					gup_shipping_city,
					gup_shipping_state,
					gup_shipping_postcode,
					gup_order_comments,
                    security: gupshup_gs_cart_vars.gupshup_nonce_action,
					_wpnonce: gupshup_gs_cart_vars.gupshup_nonce_action,
				};
                	jQuery.post(
                        gupshup_gs_cart_vars.ajaxurl,
						data, 
						function () {
							// success response
						}
					);
                
			} else {

			}
		},
	};

	abandonedCart.init();
} )( jQuery );
