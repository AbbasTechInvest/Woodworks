( function ( $ ) {
    var days=30;
    var chartObj;
    var color='rgb(92,176,230)';
	DashboardAnalytics = {
            init() {
               $( document ).on(
                    'click',
                    '#gupshup_time_filter_button',
                    DashboardAnalytics.handle_gupshup_time_filter_button
                );

            },
            handle_gupshup_time_filter_button(event){
                let filterDaystext= event.target.value;
                let filterDaysValue=filterDaystext.split(' ')[0];
                days=filterDaysValue;
                DashboardAnalytics.gupshup_workflow_run_chart();
            },
            initiate_workflow_run_chart(){
                var ctx = document.getElementById("gupshup_my_chart").getContext("2d");
                $(".gupshup_hollow_circle").css('color',color);
                var chartData = {
                    labels: [],
                    datasets: [{
                        data: [],
                        fill: true,
                        borderColor: color,
                        tension: 0.1
                    }]
                };
                chartObj = new Chart(ctx, {
                    type: "line",
                    data:chartData,
			        options: {
                        plugins: {
                            legend: {
                                display: false,                                        
                            },
                            tooltip: {
                                usePointStyle:true,
                            }
                        },
                        scales: {
                            y: {
                              beginAtZero: true, // set to true if you want the y-axis to start at zero
                              ticks: {
                                stepSize: 1 // set the step size to 1 to display only integer numbers
                              }
                            }
                        }
                    }
			    });
            },
            generateMostRunWorkflow(mostRunWorkflowData){
                let most_run_workflow_html="";
                if(mostRunWorkflowData.length==0){
                    most_run_workflow_html="<div style='padding:10px'>No data available</div>";
                }
                
                for(let workflowData of mostRunWorkflowData){
                    most_run_workflow_html += "<div class='gupshup_dashboard_box_inside'>";
                    most_run_workflow_html += "<div style='color:"+color+"; padding-left:10px;padding-top:15px; padding-bottom:5px;font-size:18px'>"
                    most_run_workflow_html += "<a style='text-decoration:none;' href='"+workflowData.redirect_workflow_url+"'>"+workflowData.workflow_name+"</a>"
                    most_run_workflow_html += "</div>"
                    most_run_workflow_html += "<div style='padding-left:10px; padding-bottom:15px;font-size:15px;color:#bdbfc1'>"
                    most_run_workflow_html += "Last run "+ (Math.floor(parseInt(workflowData.last_run)/24)>0?Math.floor(parseInt(workflowData.last_run)/24)+" days ":"") +parseInt(workflowData.last_run)%24+" hours ago";
                    most_run_workflow_html += "</div>"
                    most_run_workflow_html += "</div>"
                }
                $('#gupshup_most_workflow_run_table').html(most_run_workflow_html);
            },
            gupshup_workflow_run_chart() {
            $("#gupshup-overlay").fadeIn(300);　
            $.post(
				ajaxurl,
				{
					action: 'gupshup_workflow_run_chart_action',
                    days,
					security: null
				},
				function ( response ) {
					if(response.success){
                        var labelsData=[];
                        var countData=[];
						var totalCount=0;
                        let serverResponse = response.data;
                        for(var chartData of serverResponse.workflow_count_data){
                            labelsData.push(chartData['time'] );
							countData.push(chartData['workflow_run_count']);
                            totalCount = totalCount + parseInt(chartData['workflow_run_count']);
						}
                        chartObj.data.labels=labelsData;
                        chartObj.data.datasets.forEach((dataset) => {
                            dataset.data=countData;
                        });
                        chartObj.options.plugins.tooltip.callbacks.label=function(context) {
                            let label = context.raw || '0';
                            if (label) {
                                label += ' workflows';
                            }
                            return label;
                        };
                        chartObj.options.plugins.tooltip.callbacks.afterLabel=function(context) {
                            let messages_sent_count = ((serverResponse.workflow_count_data)[context.dataIndex]).messages_sent;
                            let afterLabel = messages_sent_count || '0';
                            if (afterLabel) {
                                afterLabel += ' messages submitted';
                            }
                            return afterLabel;
                        };
                        chartObj.update();
                        
                        $('#gupshup_chart_workflow_run_count').html(totalCount)
                        $('#gupshup_most_workflow_run_table').html(totalCount)
                        DashboardAnalytics.generateMostRunWorkflow(serverResponse.most_run_workflow_data);
                        let totalMessagesSent =  serverResponse.total_messages_sent_data|| '0'
                        $('#gupshup_dashboard_total_messages_sent').html(totalMessagesSent);
                        $('[id="gupshup_time_filter_button"]').each(function(){
                            if((($(this).val()).split(' '))[0]==days){
                                $(this).attr('style',"background :#FFFFFF !important; float:right");
                            }
                            else{
                                $(this).css('background','#E7E7E7');
                            }
                        });
                        $(".gupshup_chart_box").css('display','block');
                    }
					
					
				}
			).done(function() {
                setTimeout(function(){
                  $("#gupshup-overlay").fadeOut(300);
                },500);
            });
		},
	};
    
	$( function () {
        DashboardAnalytics.init();
        if(document.getElementById("gupshup_my_chart")){
            DashboardAnalytics.initiate_workflow_run_chart();
            DashboardAnalytics.gupshup_workflow_run_chart();
        }
	} );
} )( jQuery );
