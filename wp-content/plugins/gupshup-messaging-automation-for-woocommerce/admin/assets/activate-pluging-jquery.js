( function ( $ ) {
	WorkflowPluging = {
		init() {
			
			$( document ).on(
				'click',
				'.gupshup-switch-grid',
				WorkflowPluging.toggle_activate_template_confirmation_on_grid
			);
			
		},

		toggle_activate_template_on_grid($switch, state, id) {
			let css = state === 'on' ? 'red' : 'green';
			let new_state = state === 'on' ? 'off' : 'on';
			$.post(
				ajaxurl,
				{
					action: 'activate_workflow_on_table',
					id,
					state,
					_wpnonce: gupshup_gs_activation_vars.guphsup_nonce_action
				},
				function ( response ) {
					if(response.success){
					$( '#gupshup_activate_workflow' ).val(
						new_state === 'on' ? 1 : 0
					);
						$('#gupshup_total_workflow_count').html(response.data.workflow_count_response.total_count);
						$('#gupshup_active_workflow_count').html(response.data.workflow_count_response.active_count);
						$switch.attr( 'gupshup-gs-workflow-switch', new_state );
					}
					else{
						css='red';
					}
					$( '.gupshup_response_msg' ).remove();

					$(
						"<span class='gupshup_response_msg'> " +
							response.data.toggle_response +
							' </span>'
					)
						.insertAfter( $switch )
						.delay( 2000 )
						.fadeOut()
						.css( 'color', css );
				}
			);
		},
		toggle_activate_template_confirmation_on_grid() {
			const $switch = $( this );
			let id = $( this ).attr( 'id' );
			let state = $switch.attr( 'gupshup-gs-workflow-switch' );
			let confirmation_status = state === 'on' ? confirm('Are sure you want to deactivate the workflow ?') :confirm('Are sure you want to activate the workflow ?');
			if(confirmation_status){
				WorkflowPluging.toggle_activate_template_on_grid($switch, state, id);
			}
			
		}
		
	};

	$( function () {
		WorkflowPluging.init();
	} );
} )( jQuery );
