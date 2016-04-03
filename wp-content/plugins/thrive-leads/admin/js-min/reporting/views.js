/*! Thrive Leads - The ultimate Lead Capture solution for wordpress - 2016-03-02
* https://thrivethemes.com 
* Copyright (c) 2016 * Thrive Themes */
var ThriveLeads=ThriveLeads||{};ThriveLeads.views=ThriveLeads.views||{},jQuery(function(){ThriveLeads.views.Reporting=Backbone.View.extend({el:jQuery("#tve-reporting"),$chart:jQuery("#tve-report-chart"),table_data:{},events:{"change #report_type":"changeChart","click .tve-submit-report-type":"changeChart","change .tve-chart-source-select":"changeChart","change .tve-chart-interval-select":"changeChart","change .tve-referral-type-select":"changeChart","change .tve-tracking-type-select":"changeChart","change .tve-source-type-select":"changeChart","change .tve-chart-date-select":"changeDate","click .tve-leads-clear-cache":"clearCacheStats","click .tl-inbound-link-builder":"inboundLinkBuilder","click .calendar-trigger-icon":"triggerDatepicker"},validateDate:function(a,b){"start"===a&&this.start_date!==b&&(this.start_date=b,this.changeChart()),"end"===a&&this.end_date!==b&&(this.end_date=b,this.changeChart())},changeDate:function(a){var b,c,d,e=new Date,f=1;switch("undefined"!=typeof a&&(f=parseInt(jQuery(a.target).val())),d=e.getFullYear()+"-"+("0"+(e.getMonth()+1)).slice(-2)+"-"+("0"+e.getDate()).slice(-2),this.$el.find(".tve-date-filter").hide(),f){case ThriveLeadsConst.date_intervals.last_7_days:b=new Date(e.getTime()-6048e5),c=b.getFullYear()+"-"+("0"+(b.getMonth()+1)).slice(-2)+"-"+("0"+b.getDate()).slice(-2);break;case ThriveLeadsConst.date_intervals.last_30_days:b=new Date(e.getTime()-2592e6),c=b.getFullYear()+"-"+("0"+(b.getMonth()+1)).slice(-2)+"-"+("0"+b.getDate()).slice(-2);break;case ThriveLeadsConst.date_intervals.this_month:c=e.getFullYear()+"-"+("0"+(e.getMonth()+1)).slice(-2)+"-01";break;case ThriveLeadsConst.date_intervals.last_month:c=e.getFullYear()+"-"+("0"+e.getMonth()).slice(-2)+"-01",d=e.getFullYear()+"-"+("0"+e.getMonth()).slice(-2)+"-30";break;case ThriveLeadsConst.date_intervals.this_year:c=e.getFullYear()+"-01-01";break;case ThriveLeadsConst.date_intervals.last_year:c=e.getFullYear()-1+"-01-01",d=e.getFullYear()-1+"-12-31";break;case ThriveLeadsConst.date_intervals.last_12_months:c=e.getFullYear()-1+"-"+("0"+(e.getMonth()+1)).slice(-2)+"-"+("0"+e.getDate()).slice(-2);break;case ThriveLeadsConst.date_intervals.custom_date_range:return void this.$el.find(".tve-date-filter").show()}this.start_date=c,this.end_date=d,this.$el.find("#tve-report-start-date").pickadate("picker").set("select",c),this.$el.find("#tve-report-end-date").pickadate("picker").set("select",d),this.changeChart()},triggerDatepicker:function(a){var b=jQuery(a.target),c=jQuery(b.attr("data-activates"));return jQuery(c).trigger("click"),!1},initialize:function(){var a=this;this.hide_source_views=["ConversionRate","ListGrowth","CumulativeListGrowth"],this.$form=this.$el.find("form"),this.$el.find(".tve_load_annotation").change(jQuery.proxy(this.load_annotation_change,this)),this.$el.find("#tve-report-start-date").pickadate({format:"yyyy-mm-dd",format_submit:"yyyy-mm-dd",onClose:function(){a.validateDate("start",a.$el.find("#tve-report-start-date").val())}}),this.$el.find("#tve-report-end-date").pickadate({format:"yyyy-mm-dd",format_submit:"yyyy-mm-dd",onClose:function(){a.validateDate("end",a.$el.find("#tve-report-end-date").val())}}),this.pagination=new ThriveLeads.views.Pagination({total:1,collection:{}}),this.changeDate()},load_annotation_change:function(){var a=jQuery(".tve_load_annotation"),b={action:"thrive_leads_backend_ajax",route:"globalSettingsAPI",field:a.attr("name"),value:a.is(":checked")?1:0};jQuery.post(ajaxurl,b).always(jQuery.proxy(this.changeChart,this))},changeChart:function(){var a=this;"undefined"!=typeof this.chart&&this.chart.showLoading(),jQuery.ajax({url:ThriveLeads.ajaxurl("action=thrive_leads_backend_ajax&route=report"),data:this.getData(),dataType:"json",success:function(b){("undefined"==typeof a.chartType||a.chartType&&a.chartType!==a.getChartType())&&a.drawChart(b.chart_data,b.chart_title),b.table_data&&(a.table_data=b.table_data),a.updateChart(b.chart_data,b.chart_title,b.chart_x_axis,b.chart_y_axis),a.getViewForType()}})},drawChart:function(a,b){return this.chartType=this.getChartType(),"undefined"!=typeof a&&0===a.length&&"none"!==this.chartType?this.$el.find(".tve-chart-overlay").show():this.$el.find(".tve-chart-overlay").hide(),"none"===this.chartType?void this.$el.find("#tve-report-chart").hide():void("line"===this.chartType||"areaspline"===this.chartType?(-1!==this.hide_source_views.indexOf(this.getReportType())?this.$el.find(".tve-chart-source").hide():this.$el.find(".tve-chart-source").show(),this.$el.find("#tve-report-chart").show(),this.$el.find(".tve-chart-interval").show(),this.chart=new ThriveLeads.LineChart({title:b,data:a,renderTo:"tve-report-chart",type:this.chartType})):"pie"===this.chartType&&(this.$el.find(".tve-chart-source").hide(),this.$el.find(".tve-chart-interval").hide(),this.$el.find("#tve-report-chart").show(),this.chart=new ThriveLeads.PieChart({title:b,data:a,renderTo:"tve-report-chart"})))},updateChart:function(a,b,c,d){return"undefined"==typeof this.chart&&this.drawChart(a),this.displayCustomFields(),"none"===this.chartType?(jQuery(".tve-chart-source").show(),jQuery(".tve-chart-interval").hide(),void jQuery("#tve-report-chart").html("")):(this.chart.set("data",a),this.chart.set("title",b),this.chart.set("x_axis",c),this.chart.set("y_axis",d),void this.chart.redraw())},displayCustomFields:function(){"undefined"!=typeof data&&0===data.length&&"none"!==this.chartType?this.$el.find(".tve-chart-overlay").show():this.$el.find(".tve-chart-overlay").hide(),"areaspline"==this.chartType?this.$el.find("#tve-chart-annotations").show():this.$el.find("#tve-chart-annotations").hide(),"LeadReferral"===this.getReportType()?this.$el.find(".tve-referral-type").show():this.$el.find(".tve-referral-type").hide(),"LeadTracking"===this.getReportType()?this.$el.find(".tve-tracking-type").show():this.$el.find(".tve-tracking-type").hide(),"LeadSource"===this.getReportType()?this.$el.find(".tve-source-type").show():this.$el.find(".tve-source-type").hide(),-1!==this.hide_source_views.indexOf(this.getReportType())?this.$el.find(".tve-chart-source").hide():this.$el.find(".tve-chart-source").show()},getChartType:function(){switch(this.getReportType()){case"Conversion":case"ConversionRate":case"CumulativeConversion":return"line";case"ListGrowth":case"CumulativeListGrowth":return"areaspline";case"ComparisonChart":return"pie";case"LeadReferral":case"LeadTracking":case"LeadSource":return"none"}},getViewForType:function(){switch(ThriveLeads.objects.titleChanger.replaceFirst(this.getPageTitle()),this.getReportType()){case"Conversion":case"CumulativeConversion":case"ListGrowth":case"CumulativeListGrowth":return"undefined"==typeof ThriveLeads.objects.ConversionView?ThriveLeads.objects.ConversionView=new ThriveLeads.views.ConversionReportList({collection:ThriveLeads.objects.ConversionReport,pagination:this.pagination,report_count_data:this.table_data.count_table_data}):(this.pagination.pageCount=Math.ceil(this.table_data.count_table_data/this.pagination.itemsPerPage),this.pagination.total_items=this.table_data.count_table_data,this.pagination.collection=ThriveLeads.objects.ConversionView.collection,this.pagination.changePage(null,1)),ThriveLeads.objects.ConversionView;case"ConversionRate":return"undefined"==typeof ThriveLeads.objects.ConversionRateView?ThriveLeads.objects.ConversionRateView=new ThriveLeads.views.ConversionRateReportList({collection:ThriveLeads.objects.ConversionRateReport,pagination:this.pagination,report_count_data:this.table_data.count_table_data,report_average_rate:this.table_data.average_rate}):(ThriveLeads.objects.ConversionRateView.extra_fields={average_rate:this.table_data.average_rate},this.pagination.pageCount=Math.ceil(this.table_data.count_table_data/this.pagination.itemsPerPage),this.pagination.total_items=this.table_data.count_table_data,this.pagination.collection=ThriveLeads.objects.ConversionRateView.collection,this.pagination.changePage(null,1)),ThriveLeads.objects.ConversionRateView;case"ComparisonChart":return"undefined"==typeof ThriveLeads.objects.ComparisonChartView?ThriveLeads.objects.ComparisonChartView=new ThriveLeads.views.ComparisonReportList({collection:ThriveLeads.objects.ComparisonReport,pagination:this.pagination,table_data:this.table_data}):(this.pagination.empty(),ThriveLeads.objects.ComparisonChartView.collection.reset(this.table_data),ThriveLeads.objects.ComparisonChartView.render()),ThriveLeads.objects.ComparisonChartView;case"LeadReferral":return"undefined"==typeof ThriveLeads.objects.LeadReferralView?ThriveLeads.objects.LeadReferralView=new ThriveLeads.views.LeadReferralReportList({collection:ThriveLeads.objects.LeadReferralReport,pagination:this.pagination,report_count_data:this.table_data.count_table_data}):(this.pagination.pageCount=Math.ceil(this.table_data.count_table_data/this.pagination.itemsPerPage),this.pagination.total_items=this.table_data.count_table_data,this.pagination.collection=ThriveLeads.objects.LeadReferralView.collection,this.pagination.changePage(null,1)),ThriveLeads.objects.LeadReferralView;case"LeadTracking":return"undefined"==typeof ThriveLeads.objects.LeadTrackingView?ThriveLeads.objects.LeadTrackingView=new ThriveLeads.views.LeadTrackingReportList({collection:ThriveLeads.objects.LeadTrackingReport,pagination:this.pagination,report_count_data:this.table_data.count_table_data}):(this.pagination.pageCount=Math.ceil(this.table_data.count_table_data/this.pagination.itemsPerPage),this.pagination.total_items=this.table_data.count_table_data,this.pagination.collection=ThriveLeads.objects.LeadTrackingView.collection,this.pagination.changePage(null,1)),ThriveLeads.objects.LeadTrackingView;case"LeadSource":return"undefined"==typeof ThriveLeads.objects.LeadSourceView?ThriveLeads.objects.LeadSourceView=new ThriveLeads.views.LeadSourceReportList({collection:ThriveLeads.objects.LeadSourceReport,pagination:this.pagination,report_count_data:this.table_data.count_table_data}):(this.pagination.pageCount=Math.ceil(this.table_data.count_table_data/this.pagination.itemsPerPage),this.pagination.total_items=this.table_data.count_table_data,this.pagination.collection=ThriveLeads.objects.LeadSourceView.collection,this.pagination.changePage(null,1)),ThriveLeads.objects.LeadSourceView}},getData:function(){return this.$form.serialize()},getReportType:function(){return this.$el.find("#report_type").val()},getPageTitle:function(){return this.$el.find("#report_type option:selected").text()},render:function(){this.$el.find(".tve-setting-change").on("change",_.bind(this.globalSetting,this))},inboundLinkBuilder:function(){TVE_Dash.modal(ThriveLeads.views.lightbox.InboundLink,{title:ThriveLeads["const"].translations.InboundLinkBuilder,"max-width":"80%",width:750,collection:ThriveLeads.objects.groups,model:new ThriveLeads.models.InboundLink})},toggleGlobalSettings:function(a){return jQuery(a.currentTarget).parents(".tve-global-settings").toggleClass("tve-expanded"),!1},globalSetting:function(a){var b=jQuery(a.currentTarget),c={action:"thrive_leads_backend_ajax",route:"globalSettingsAPI",field:b.attr("name"),value:b.attr("value")};b.is('input[type="checkbox"]')&&!b.is(":checked")&&(c.value=0),this.globalSettings[c.field]=c.value,jQuery.post(ajaxurl,c)},clearCacheStats:function(){TVE_Dash.showLoader(),jQuery.post(ajaxurl,{action:"thrive_leads_backend_ajax",route:"clearCacheStatistics"},function(){location.reload()})}}),ThriveLeads.views.Pagination=Backbone.View.extend({el:jQuery(".tl-pagination"),template:TVE_Dash.tpl("pagination/view"),events:{"click a.page":"changePage"},currentPage:1,pageCount:1,pagesToDisplay:2,itemsPerPage:50,total_items:0,order_by:"",order_dir:"",collection:null,initialize:function(a){this.pageCount=Math.ceil(a.total/this.itemsPerPage),this.collection=a.collection},changePage:function(a,b){TVE_Dash.showLoader();var c="undefined"==typeof b?jQuery(a.currentTarget).attr("value"):parseInt(b),d=this.getFormData(),e=this;return d+="&"+jQuery.param({type:"table",page:c,itemsPerPage:this.itemsPerPage,order_by:this.order_by,order_dir:this.order_dir}),jQuery.ajax({url:ThriveLeads.ajaxurl("action=thrive_leads_backend_ajax&route=report"),data:d,dataType:"json",success:function(a){e.currentPage=c,e.collection.reset(a),e.render(),TVE_Dash.hideLoader()}}),!1},getFormData:function(){return jQuery("#tve-reporting form").serialize()},empty:function(){this.$el.empty()},render:function(){return this.$el.html(this.template({currentPage:parseInt(this.currentPage),pageCount:parseInt(this.pageCount),pagesToDisplay:parseInt(this.pagesToDisplay),total_items:parseInt(this.total_items),itemsPerPage:this.itemsPerPage})),this}}),ThriveLeads.views.ReportingTableBase=Backbone.View.extend({el:jQuery("#tve-report-meta"),report_count_data:0,pagination:{},itemView:"",extra_fields:{},initialize:function(a){this.listenTo(this.collection,"reset",this.render),this.pagination=a.pagination,this.pagination.pageCount=Math.ceil(a.report_count_data/this.pagination.itemsPerPage),this.pagination.total_items=a.report_count_data,this.pagination.collection=this.collection,this.pagination.changePage(null,1),this.$el.html(this.template(this.extra_fields))},changeOrder:function(a){var b=jQuery(a.currentTarget).attr("data-order-by");if(b==this.pagination.order_by)switch(this.pagination.order_dir){case"DESC":this.pagination.order_dir="ASC";break;case"ASC":this.pagination.order_dir="",this.pagination.order_by="";break;case"":this.pagination.order_dir="DESC"}else this.pagination.order_by=b,this.pagination.order_dir="DESC";this.pagination.changePage(null,1)},addOne:function(a){var b=new ThriveLeads.views[this.itemView]({model:a});this.$el.find("#tve-table-items").append(b.render().el)},displayItems:function(){this.collection.each(this.addOne,this)},emptyList:function(){this.$el.find("#tve-table-items .tvd-collection-item").empty()},render:function(){return this.$el.html(this.template(this.extra_fields)),this.$el.find(".tve-table-sortable").on("click",jQuery.proxy(this.changeOrder,this)),this.displayItems(),this}}),ThriveLeads.views.ConversionReportItem=ThriveLeads.views.Base.extend({tagName:"li",className:"tvd-collection-item",template:TVE_Dash.tpl("reporting/conversion-report/item")}),ThriveLeads.views.ConversionReportList=ThriveLeads.views.ReportingTableBase.extend({template:TVE_Dash.tpl("reporting/conversion-report/list"),itemView:"ConversionReportItem"}),ThriveLeads.views.ConversionRateReportItem=ThriveLeads.views.Base.extend({tagName:"li",className:"tvd-collection-item",template:TVE_Dash.tpl("reporting/conversion-rate-report/item")}),ThriveLeads.views.ConversionRateReportList=ThriveLeads.views.ReportingTableBase.extend({template:TVE_Dash.tpl("reporting/conversion-rate-report/list"),itemView:"ConversionRateReportItem",extra_fields:{},initialize:function(a){this.extra_fields={average_rate:a.report_average_rate},ThriveLeads.views.ReportingTableBase.prototype.initialize.call(this,a)}}),ThriveLeads.views.ComparisonReportItem=ThriveLeads.views.Base.extend({tagName:"li",className:"tvd-collection-item",template:TVE_Dash.tpl("reporting/comparison-report/item")}),ThriveLeads.views.ComparisonReportList=ThriveLeads.views.ReportingTableBase.extend({template:TVE_Dash.tpl("reporting/comparison-report/list"),itemView:"ComparisonReportItem",initialize:function(a){a.pagination.empty(),this.collection.reset(a.table_data),this.render()}}),ThriveLeads.views.LeadReferralReportItem=ThriveLeads.views.Base.extend({tagName:"li",className:"tvd-collection-item",template:TVE_Dash.tpl("reporting/lead-referral-report/item")}),ThriveLeads.views.LeadReferralReportList=ThriveLeads.views.ReportingTableBase.extend({template:TVE_Dash.tpl("reporting/lead-referral-report/list"),itemView:"LeadReferralReportItem"}),ThriveLeads.views.LeadTrackingReportItem=ThriveLeads.views.Base.extend({tagName:"li",className:"tvd-collection-item",template:TVE_Dash.tpl("reporting/lead-tracking-report/item")}),ThriveLeads.views.LeadTrackingReportList=ThriveLeads.views.ReportingTableBase.extend({template:TVE_Dash.tpl("reporting/lead-tracking-report/list"),itemView:"LeadTrackingReportItem",displayItems:function(){this.collection.each(this.addOne,this);var a=jQuery(".tve-tracking-type-select option:selected").val();"all"!=a&&this.$el.find(".tve-tracking-field").not('[data-field-display="'+a+'"]').remove()}}),ThriveLeads.views.LeadSourceReportItem=ThriveLeads.views.Base.extend({tagName:"li",className:"tvd-collection-item",template:TVE_Dash.tpl("reporting/lead-source-report/item")}),ThriveLeads.views.LeadSourceReportList=ThriveLeads.views.ReportingTableBase.extend({template:TVE_Dash.tpl("reporting/lead-source-report/list"),itemView:"LeadSourceReportItem"})});