( function ( $ ) {
	var selfserveChannelDetails;
	var enterpriseChannelDetails;
	WorkflowAdmin = {
		init() {
			$( document ).on(
				'change',
				'#gupshup_template_category',
				WorkflowAdmin.handle_category_change
			);
			$( document ).on(
				'change',
				'#gupshup_template_id',
				WorkflowAdmin.handle_template_change
			);
			$( document ).on(
				'input',
				'#gupshup_template_variable_text',
				WorkflowAdmin.handle_gupshup_template_variable_text
			);
			$( document ).on(
				'change',
				'#gupshup_template_variable_dropdown',
				WorkflowAdmin.handle_gupshup_template_variable_dropdown
			);
			$( document ).on(
				'change',
				'#gupshup_trigger_type',
				WorkflowAdmin.handle_gupshup_trigger_type
			);
			$( document ).on(
				'input',
				'#gupshup_template_header_variable_text',
				WorkflowAdmin.handle_gupshup_template_header_variable_text
			);
			$( document ).on(
				'input',
				'#gupshup_template_header_variable_dropdown',
				WorkflowAdmin.handle_gupshup_template_header_variable_dropdown
			);
			$( document ).on(
				'change',
				'#gupshup_channel_type',
				WorkflowAdmin.handle_gupshup_channel_change
			);
			$( document ).on(
				'change',
				'#gupshup_workflow_timing_unit',
				WorkflowAdmin.handle_gupshup_workflow_timing_unit
			);
			$( document ).on(
				'change',
				'#gupshup_workflow_time_option_block',
				WorkflowAdmin.handle_gupshup_workflow_time_option_block
			);
		},

		handle_gupshup_business_no(){
			var selected_channel_type = $('#gupshup_channel_type').val();
			if(selected_channel_type=='self-serve'){
				$("#gupshup_business_no").prop('required', true);
				$("#gupshup_business_no").closest(".example-class").css('display','');
			}
			else if(selected_channel_type=='enterprise'){
				$("#gupshup_business_no").removeAttr('required');
				$("#gupshup_business_no").closest(".example-class").css('display','none');
			}
		},

		handle_gupshup_channel_change(){
			var selected_channel_type = $(this).val();
			if(selected_channel_type=='self-serve'){
				enterpriseChannelDetails={
					'gupshup_user_id':$('#gupshup_user_id').val(),
					'gupshup_channel_name':$('#gupshup_channel_name').val(),
					'gupshup_password':$('#gupshup_password').val(),
					'gupshup_business_no':$('#gupshup_business_no').val(),
				}
				$('label[for=gupshup_user_id]').text('App name *');
				$("#gupshup_user_id_help_text").html('The name of the app created in your Gupshup account. E.g., demoapp. If you don’t have an app created yet, log in to your Gupshup.io account and navigate to Dashboard > WhatsApp. Then, click on the + icon and click the Access API button. Enter the name of the app without spaces or any special characters and finish the rest of the procedures.')
				$('label[for=gupshup_password]').text('API key *');
				$("#gupshup_password_help_text").html('To get the API key of the app specified,  log in to your Gupshup.io account  and navigate to Dashboard and click the Settings icon beside the app name. Scroll down the window and look for the API key under the Request code snippet.')
				if(selfserveChannelDetails !=null){
					$("#gupshup_user_id").val(selfserveChannelDetails.gupshup_user_id);
					$("#gupshup_channel_name").val(selfserveChannelDetails.gupshup_channel_name);
					$("#gupshup_password").val(selfserveChannelDetails.gupshup_password);
					$("#gupshup_business_no").val(selfserveChannelDetails.gupshup_business_no);
				}
				else{
					$("#gupshup_user_id").val('');
					$("#gupshup_channel_name").val('');
					$("#gupshup_password").val('');
					$("#gupshup_business_no").val('');
				}
				$("#gupshup_business_no").prop('required', true);
				$("#gupshup_business_no").closest(".example-class").css('display','');
			}
			else if(selected_channel_type=='enterprise'){
				selfserveChannelDetails={
					'gupshup_user_id':$('#gupshup_user_id').val(),
					'gupshup_channel_name':$('#gupshup_channel_name').val(),
					'gupshup_password':$('#gupshup_password').val(),
					'gupshup_business_no':$('#gupshup_business_no').val(),
				}
				$('label[for=gupshup_user_id]').text('User ID *');
				$("#gupshup_user_id_help_text").html('The HSM user ID of your gupshup enterprise account.')
				$('label[for=gupshup_password]').text('Password *');
				$("#gupshup_password_help_text").html('The password associated with the HSM user ID.')
				if(enterpriseChannelDetails !=null){
					$("#gupshup_user_id").val(enterpriseChannelDetails.gupshup_user_id);
					$("#gupshup_channel_name").val(enterpriseChannelDetails.gupshup_channel_name);
					$("#gupshup_password").val(enterpriseChannelDetails.gupshup_password);
					$("#gupshup_business_no").val(enterpriseChannelDetails.gupshup_business_no);
				}
				else{
					$("#gupshup_user_id").val('');
					$("#gupshup_channel_name").val('');
					$("#gupshup_password").val('');
					$("#gupshup_business_no").val('');
				}
				$("#gupshup_business_no").removeAttr('required');
				$("#gupshup_business_no").closest(".example-class").css('display','none');
			}
		},
		handle_category_change() {
			var dataurl = gupshup_gs_action_vars.get_data_php_url;
			var selected_category = $(this).val();
			var template_data  = gupshup_gs_action_vars.template_data;
			var template_id_html ='';
			template_id_html+= '<option value="">--Select Template--</option>';
			for(let template of template_data){
				if(template.template_type==selected_category){
					template_id_html+='<option value="'+template['template_id']+'">'+template['template_name']+'</option>';
				}
			}
			$("#gupshup_template_id").html(template_id_html);
			$("#gupshup_template_message_detail").html('');
			$("#gupshup_template_header_detail").html('');
			$("#gupshup_template_footer_detail").html('');
			$("#gupshup_template_header").val('');
			$("#gupshup_template_footer").val('');
			$("#gupshup_template_button_type").val('');
			$("#gupshup_template_message").val('');
			$("#gupshup_workflow_table").html('');
			$("#gupshup_template_media_url").val('');
			$("#gupshup_template_media_url").removeAttr('required');
			$("#gupshup_action_media_url_block").css("display","none");
			if(selected_category !='TEXT' && selected_category !=''){
				var pattern;
				var title;
				switch (selected_category){
					case 'IMAGE':
						pattern = "(http|https)://.*\\.(jpg|jpeg|png)(\\?[\\w\\-@?^=%&/~+#]*)?";
						title = 'Insert any .jpg, .jpeg or .png Image URL';
						break;
					case 'DOCUMENT':
						pattern = "(http|https)://.*\\.(pdf)(\\?[\\w\\-@?^=%&/~+#]*)?";
						title = 'Insert any .pdf Document URL';
						break;
					case 'VIDEO':
						pattern = "(http|https)://.*\\.(mp4)(\\?[\\w\\-@?^=%&/~+#]*)?";
						title = 'Insert any .mp4 Document URL';
						break;
					default:
						pattern = '(http|https)://.*$';
						break;
				}
				$("#gupshup_action_media_url_block").css("display","");
				$("#gupshup_template_media_url").prop('required', true);
				$("#gupshup_template_media_url").prop('pattern',  pattern);
				$("#gupshup_template_media_url").prop('title', title);
			}
		},
		handle_template_change() {
			let dataurl = gupshup_gs_action_vars.get_data_php_url;
			let selected_template_id = $(this).val();
			let template_data  = gupshup_gs_action_vars.template_data;
			let template_detail_html ='';
			let template_table_html ='';
			let selectedTemplate;
			$("#gupshup_template_header").val('');
			$("#gupshup_template_footer").val('');
			$("#gupshup_template_button_type").val('');
			$("#gupshup_template_header_detail").html('');
			$("#gupshup_template_footer_detail").html('');
			$("#gupshup_template_message_detail").html('');
			
			for(let template of template_data){
				if(template.template_id==selected_template_id){
					selectedTemplate = template;
				}
			}
			
			if(selectedTemplate.template_header!=null){
				$("#gupshup_template_header").val(selectedTemplate.template_header);
				$("#gupshup_template_header_detail").html('<b>'+selectedTemplate.template_header+'</b></br>');
			}
			$("#gupshup_template_message").val(selectedTemplate.template_body);
			$("#gupshup_template_message_detail").text(selectedTemplate.template_body).html();
			if(selectedTemplate.template_footer!=null){
				$("#gupshup_template_footer").val(selectedTemplate.template_footer);
				$("#gupshup_template_footer_detail").html(selectedTemplate.template_footer);
			}
			if(selectedTemplate.template_button_type!=null){
				$("#gupshup_template_button_type").val(selectedTemplate.template_button_type);
			}
			let trigger = $("#gupshup_trigger_type").val();
			let triggerPostTypeData = gupshup_gs_action_vars.trigger_post_types_data;
			let variableFieldData = gupshup_gs_action_vars.variable_fields_data;
			let variableFieldArray;
			if(trigger!=null && trigger!=''){
				let triggerPostType = triggerPostTypeData[trigger];
				variableFieldArray = variableFieldData[triggerPostType];
			}

			let re = /{{\s*([^}]+)\s*}}/g;
			let headerbody = selectedTemplate.template_header,
			headerVariableList = [],
			itemHeader;
			if(headerbody!=null){
				while (itemHeader = re.exec(headerbody))
            	{
                	headerVariableList.push("{{"+itemHeader[1]+"}}");
            	}
			}
			if(headerVariableList!=null && headerVariableList.length>0){
				template_table_html+='<tr>';
				template_table_html+='<th>Header *</th>';
				template_table_html+='<td><div class="variable-table-td-div">';
				for(let headerVariableIndex in headerVariableList){
					template_table_html+='<span class="variable-table-span-variable-name">'+headerVariableList[headerVariableIndex]+' </span>';
					template_table_html+='<input class="form-control" type="hidden" name="gupshup_template_header_variable_name[]" id="gupshup_template_header_variable_name" data-index='+headerVariableIndex+' value="'+headerVariableList[headerVariableIndex]+'" />'
						template_table_html+='<span class="variable-table-span-variable-text"><input class="variable-table-input-variable-text" required type="text" name="gupshup_template_header_variable_text[]" id="gupshup_template_header_variable_text" data-index='+headerVariableIndex+' placeholder="Enter content for '+headerVariableList[headerVariableIndex]+'"></span>';
							template_table_html+='<span class="variable-table-span-or"> or </span>';
							template_table_html+='<span class="variable-table-span-variable-dropdown">';
								template_table_html+='<select class="variable-table-input-variable-dropdown" name="gupshup_template_header_variable_dropdown[]" id="gupshup_template_header_variable_dropdown" data-index='+headerVariableIndex+' >';
									template_table_html+='<option value=""> --Select Variable-- </option>';
									for(let variableDropdown in variableFieldArray){
										template_table_html+='<option value="'+variableDropdown+'" >'+variableDropdown+'</option>';
									}
								template_table_html+='</select>';
							template_table_html+='</span><br/>';
				}
				template_table_html+='</div></td>';
				template_table_html+='</tr>';
			}
			let templateBody = selectedTemplate.template_body,
            variableList=[],
            item;
            while (item = re.exec(templateBody))
            {
                variableList.push("{{"+item[1]+"}}");
            }
			if(variableList!=null && variableList.length>0){
				
				template_table_html+='<tr>';
				template_table_html+='<th>Body *</th>';
				template_table_html+='<td><div class="variable-table-td-div">';
				for(let variableIndex in variableList){
					template_table_html+='<span class="variable-table-span-variable-name">'+variableList[variableIndex]+' </span>';
					template_table_html+='<input class="form-control" type="hidden" name="gupshup_template_variable_name[]" id="gupshup_template_variable_name" data-index='+variableIndex+' value="'+variableList[variableIndex]+'" />'
						template_table_html+='<span class="variable-table-span-variable-text"><input class="variable-table-input-variable-text" required type="text" name="gupshup_template_variable_text[]" id="gupshup_template_variable_text" data-index='+variableIndex+' placeholder="Enter content for '+variableList[variableIndex]+'"></span>';
							template_table_html+='<span class="variable-table-span-or"> or </span>';
							template_table_html+='<span class="variable-table-span-variable-dropdown">';
								template_table_html+='<select class="variable-table-input-variable-dropdown" name="gupshup_template_variable_dropdown[]" id="gupshup_template_variable_dropdown" data-index='+variableIndex+'>';
									template_table_html+='<option value=""> --Select Variable-- </option>';
									for(let variableDropdown in variableFieldArray){
										template_table_html+='<option value="'+variableDropdown+'" >'+variableDropdown+'</option>';
									}
								template_table_html+='</select>';
							template_table_html+='</span><br/>';
				}

				template_table_html+='</div></td>';
				template_table_html+='</tr>';
			}
			$("#gupshup_workflow_table").html(template_table_html);
		},

		toggle_activate_template() {
			const $switch = $( this ),
			state = $switch.attr( 'gupshup-gs-workflow-switch' );
			const new_state = state === 'on' ? 'off' : 'on';
			$( '#gupshup_activate_workflow' ).val(
				new_state === 'on' ? 1 : 0
			);
			$switch.attr( 'gupshup-gs-workflow-switch', new_state );
		},
		handle_gupshup_trigger_type(){
			let selected_trigger_type = $(this).val();
			let triggerPostTypeData = gupshup_gs_action_vars.trigger_post_types_data;
			let variableFieldData = gupshup_gs_action_vars.variable_fields_data;
			let schedulingTriggers = gupshup_gs_action_vars.scheduling_triggers;
			let variableFieldArray;
			if(selected_trigger_type!=null && selected_trigger_type!=''){
				let triggerPostType = triggerPostTypeData[selected_trigger_type];
				variableFieldArray = variableFieldData[triggerPostType];
			}
			
			let template_variable_dropdown_html;
			template_variable_dropdown_html+='<option value=""> --Select Variable-- </option>';
			for(let variableDropdown in variableFieldArray){
				template_variable_dropdown_html+='<option value="'+variableDropdown+'" >'+variableDropdown+'</option>';
			}
			$('[id="gupshup_template_variable_dropdown"]').html(template_variable_dropdown_html);
			$('[id="gupshup_template_header_variable_dropdown"]').html(template_variable_dropdown_html);
			if((gupshup_gs_action_vars.trigger_help_text)[selected_trigger_type] != null){
				$("#gupshup_trigger_span_help_text").html((gupshup_gs_action_vars.trigger_help_text)[selected_trigger_type]);
			}
			$("#gupshup_workflow_time_option_block").css("display","none");
			$("#gupshup_is_scheduled").val(null);
			if(schedulingTriggers.includes(selected_trigger_type)){
				$("#gupshup_workflow_time_option_block").css("display","");
			}
			$("#gupshup_workflow_timing_block").css("display","none");
			if(selected_trigger_type=="Abandoned Cart"){
				$("#gupshup_workflow_timing_block").css("display","");
			}
		},
		handle_gupshup_template_variable_text(){
			let templateMessage = $("#gupshup_template_message").val();
			let elementData = $(this);
			let index = elementData.attr('data-index');
			let template_message_detail_html = templateMessage;
			let templateBody = templateMessage,
            variableList=[],
            re = /{{\s*([^}]+)\s*}}/g,
            item;
            while (item = re.exec(templateBody))
            {
                variableList.push("{{"+item[1]+"}}");
            }
			$('[id="gupshup_template_variable_text"]').each(function(){
				let dataIndex = $(this).attr('data-index')
				if($(this).val()!=null && $(this).val()!=''){
					template_message_detail_html = template_message_detail_html.replace(variableList[dataIndex],$(this).val())
				}
			});
			$('[id="gupshup_template_variable_dropdown"]').each(function(){
					if($(this).attr('data-index')==index){
						$(this).val('');
					}
			});
			$("#gupshup_template_message_detail").text(template_message_detail_html).html();
		},

		handle_gupshup_template_variable_dropdown(){
			let templateMessage = $("#gupshup_template_message").val();
			let elementData = $(this);
			let index = elementData.attr('data-index');
			let template_message_detail_html = templateMessage;
			let templateBody = templateMessage,
            variableList=[],
            re = /{{\s*([^}]+)\s*}}/g,
            item;
            while (item = re.exec(templateBody))
            {
                variableList.push("{{"+item[1]+"}}");
            }
			$('[id="gupshup_template_variable_text"]').each(function(){
				let dataIndex = $(this).attr('data-index')
					if(dataIndex==index){
						$(this).val(elementData.val());
						if(elementData.val() !=null && elementData.val()!=''){
							template_message_detail_html = template_message_detail_html.replace(variableList[dataIndex],elementData.val())
						}
					}
					else{
						if($(this).val()!=null && $(this).val()!=''){
							template_message_detail_html = template_message_detail_html.replace(variableList[dataIndex],$(this).val())
						}
					}
					
			});
			$("#gupshup_template_message_detail").text(template_message_detail_html).html();			
		},
		handle_gupshup_template_header_variable_text(){
			let templateHeader = $("#gupshup_template_header").val();
			let elementData = $(this);
			let index = elementData.attr('data-index');
			let template_header_detail_html = templateHeader,
			headerVariableList=[],
            re = /{{\s*([^}]+)\s*}}/g,
            item;
            while (item = re.exec(templateHeader))
            {
                headerVariableList.push("{{"+item[1]+"}}");
            }
			$('[id="gupshup_template_header_variable_text"]').each(function(){
				let dataIndex = $(this).attr('data-index')
				if($(this).val()!=null && $(this).val()!=''){
					template_header_detail_html = template_header_detail_html.replace(headerVariableList[dataIndex],$(this).val())
				}
			});
			$('[id="gupshup_template_header_variable_dropdown"]').each(function(){
					if($(this).attr('data-index')==index){
						$(this).val('');
					}
			});
			$("#gupshup_template_header_detail").html('<b>'+template_header_detail_html+'</b>');
		},
		handle_gupshup_template_header_variable_dropdown(){
			let templateHeader = $("#gupshup_template_header").val();
			let elementData = $(this);
			let index = elementData.attr('data-index');
			let template_header_detail_html = templateHeader,
			headerVariableList=[],
            re = /{{\s*([^}]+)\s*}}/g,
            item;
            while (item = re.exec(templateHeader))
            {
                headerVariableList.push("{{"+item[1]+"}}");
            }
			$('[id="gupshup_template_header_variable_text"]').each(function(){
				let dataIndex = $(this).attr('data-index')
					if(dataIndex==index){
						$(this).val(elementData.val());
						if(elementData.val() !=null && elementData.val()!=''){
							template_header_detail_html = template_header_detail_html.replace(headerVariableList[dataIndex],elementData.val())
						}
					}
					else{
						if($(this).val()!=null && $(this).val()!=''){
							template_header_detail_html = template_header_detail_html.replace(headerVariableList[dataIndex],$(this).val())
						}
					}
					
			});
			$("#gupshup_template_header_detail").html('<b>'+template_header_detail_html+'</b>');			
		},
		handle_gupshup_workflow_timing_unit(){
			if($("#gupshup_workflow_timing_unit").val()==='days'){

				$("#gupshup_workflow_timing").attr('max',3);
			}
			else if($("#gupshup_workflow_timing_unit").val()=='hours'){
				$("#gupshup_workflow_timing").attr('max',72);
			}
			else{
				$("#gupshup_workflow_timing").attr('max',4320);
			}
			$('#gupshup_age_span_help_text').html('Age of selected trigger (In '+$("#gupshup_workflow_timing_unit").val()+'). min 1 and max '+$("#gupshup_workflow_timing").attr('max')+' '+$("#gupshup_workflow_timing_unit").val()) ;
		},
		handle_gupshup_workflow_time_option_block(event){
			let eventValue = event.target.value;
			$("#gupshup_workflow_timing_block").css("display","none");
			$("#gupshup_workflow_timing").removeAttr('required');
			if(eventValue == true){
				$("#gupshup_workflow_timing_block").css("display","");
				$("#gupshup_workflow_timing").prop("required",true);
			}
		}
	};

	$( function () {
		WorkflowAdmin.init();
		if(document.getElementById("gupshup_business_no")){
            WorkflowAdmin.handle_gupshup_business_no();
        }
	} );
} )( jQuery );
