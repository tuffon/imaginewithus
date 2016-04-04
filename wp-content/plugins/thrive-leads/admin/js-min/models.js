/*! Thrive Leads - The ultimate Lead Capture solution for wordpress - 2016-03-23
* https://thrivethemes.com 
* Copyright (c) 2016 * Thrive Themes */
var ThriveLeads=ThriveLeads||{};ThriveLeads.models=ThriveLeads.models||{},ThriveLeads.collections=ThriveLeads.collections||{},function(){function a(a){return"undefined"!=typeof a&&null!==a?!0:!1}Backbone.emulateHTTP=!0,Backbone.ssajax=function(){var a=Array.prototype.slice.call(arguments,0);return void 0===a[0].data&&(a[0].data={}),a[0].data.security="security",a[0].type="POST",a[0].data.action="thrive_leads_backend_ajax",-1===a[0].url.indexOf("route=")&&(a[0].data.route=ThriveLeads.router.routes[Backbone.history.fragment]),delete ThriveLeads.ajax_no_route,Backbone.$.ajax.apply(Backbone.$,a)},ThriveLeads.collections.Variations=Backbone.Collection.extend({determineControl:function(){this.each(function(a,b){a.set("is_control",0===b)})}}),ThriveLeads.models.Group=Backbone.Model.extend({idAttribute:"ID",defaults:function(){return{ID:"",post_title:"",impressions:"0",conversions:"0",conversion_rate:"N/A",order:0,active_tests:new ThriveLeads.collections.Tests,completed_tests:new ThriveLeads.collections.Tests,form_types:_.extend(ThriveLeads["const"].default_form_types),details_expanded:!1,display_on_mobile:"Yes",display_status:"Yes",has_display_settings:0}},validate:function(a){var b={},c=!0;return a.post_title.length<=0&&(b.post_title=ThriveLeads["const"].translations.GroupNameRequired,c=!1),c?void 0:b},initialize:function(){var a=new ThriveLeads.collections.FormTypes;a.reset(this.get("form_types")),a.each(this.setFormTypeParent,this),this.set("form_types",a),this.listenTo(this,"change:ID",this.idChanged)},idChanged:function(){this.get("form_types").each(this.setFormTypeParent,this)},setFormTypeParent:function(a){a.set("post_parent",this.get("ID"))},url:function(){return ThriveLeads.ajaxurl("action=thrive_leads_backend_ajax&route=groupAPI&ID="+this.get("ID"))},ableForTest:function(){var a=0;return this.get("form_types").each(function(b){!b.get("active_test").get("id")&&b.get("variations").length>=1&&a++}),a>=2},decreaseStatistics:function(a){var b=this.get("impressions")-parseInt(a.get("impressions")),c=this.get("conversions")-parseInt(a.get("conversions"));this.set({impressions:b,conversions:c,conversion_rate:ThriveLeads.conversion_rate(b,c)}),a instanceof ThriveLeads.models.FormVariation?this.get("form_types").findWhere({ID:parseInt(a.get("post_parent"))}).decreaseStatistics(a):a instanceof ThriveLeads.models.FormType&&this.get("form_types").findWhere({ID:parseInt(a.get("ID"))}).set({impressions:0,conversions:0,conversion_rate:ThriveLeads.conversion_rate(0,0)})},ajaxGetEmptyVariations:function(){var a=this;return jQuery.ajax({url:a.url()+"&get_empty_variations=1",dataType:"json"})}}),ThriveLeads.collections.Groups=Backbone.Collection.extend({model:ThriveLeads.models.Group,comparator:function(a){return a.get("order")},getTotalVariations:function(){var a=0;return this.each(function(b){b.get("form_types").each(function(b){a+=b.get("variations").length})}),a}}),ThriveLeads.objects.groups=new ThriveLeads.collections.Groups,ThriveLeads.models.TestItem=Backbone.Model.extend({idAttribute:"id",defaults:function(){return{id:"",name:"",form_type:"",impressions:0,conversions:0,conversion_rate:0,percentage_improvement:0,beat_original:0,trigger:""}},url:function(){return ThriveLeads.ajaxurl("action=thrive_leads_backend_ajax&route=testItemAPI&id="+this.get("id"))},getConversionRate:function(){var a=parseInt(this.get("unique_impressions"))?parseInt(this.get("conversions"))/parseInt(this.get("unique_impressions"))*100:0;return a=ThriveLeads.roundNumber(a,3),a=a.toFixed(2)},getPercentageImprovementColor:function(){var a=parseFloat(this.get("percentage_improvement"));return isNaN(a)?"":0>a?ThriveLeads["const"].CHART_RED:a>0?ThriveLeads["const"].CHART_GREEN:""}}),ThriveLeads.models.TestChartModel=ThriveLeads.models.Group.extend({idAttribute:"ID",defaults:function(){return{interval:"day",chart_data:[],chart_title:"",chart_x_axis:[],chart_y_axis:""}},url:function(){return ThriveLeads.ajaxurl("action=thrive_leads_backend_ajax&route=chartAPI&chartType=testChart&ID="+this.get("ID")+"&interval="+this.get("interval"))}}),ThriveLeads.collections.TestItems=Backbone.Collection.extend({model:ThriveLeads.models.TestItem,getTotals:function(){var a=this.getTotalImpressions(),b=this.getTotalConversions(),c=this.getConversionRate();return{impressions:a,conversions:b,conversions_rate:c}},getTotalImpressions:function(){var a=0;return _.each(this.models,function(b){a+=parseInt(b.get("unique_impressions"))}),a},getTotalConversions:function(){var a=0;return _.each(this.models,function(b){a+=parseInt(b.get("conversions"))}),a},getConversionRate:function(){var a=0!==this.getTotalImpressions()?this.getTotalConversions()/this.getTotalImpressions()*100:0;return a=a.toFixed(2)},getCandleStickData:function(){var a=[];_.each(this.models,function(b){var c=parseInt(b.get("conversion_rate")),d=parseInt(b.get("unique_impressions"));if(isNaN(c)||isNaN(d)||0===d)a.push([0,0]);else{var e=Math.round(100*Math.sqrt(c*(100-c)/d))/100;a.push([c-e,c+e])}});var b=ThriveLeads["const"].CHART_RED,c=ThriveLeads["const"].CHART_GREY,d=ThriveLeads["const"].CHART_GREEN,e=a[0],f=[],g=[];return _.each(a,function(a){var h=[],i=[];a[0]<e[0]?(i.push(b),h.push(a[0]),a[1]<e[0]?h.push(a[1]):a[1]<e[1]?(i.push(c),h.push(e[0]),h.push(a[1])):e[1]<a[1]&&(i.push(c),i.push(d),h.push(e[0]),h.push(e[1]),h.push(a[1]))):a[0]<e[1]?(i.push(c),h.push(a[0]),a[1]<=e[1]?h.push(a[1]):e[1]<a[1]&&(i.push(d),h.push(e[1]),h.push(a[1]))):e[1]<a[1]&&(i.push(d),h.push(a[0]),h.push(a[1])),f.push(h),g.push(i)}),{chartData:f,chartColors:g}},getHighestRateItem:function(){var a,b=0;return this.each(function(c){parseFloat(c.getConversionRate())>=b&&(a=c.get("id"),b=parseFloat(c.getConversionRate()))}),0===b?void 0:a}}),ThriveLeads.models.FormTypeBase=Backbone.Model.extend({ajaxGetEmptyVariations:function(){return jQuery.ajax({url:this.url()+"&get_empty_variations=1",dataType:"json"})},resetStatistics:function(a){var b=_.extend({type:"post",dataType:"json",url:ThriveLeads.ajaxurl("action=thrive_leads_backend_ajax&route=formTypeAPI&ID="+this.get("ID")+"&custom_action=reset_statistics")},a);return jQuery.ajax(b)},getPHPCode:function(){return this.get("ID")?"<?php if (function_exists('tve_leads_form_display')) { tve_leads_form_display("+this.get("post_parent")+", "+this.get("ID")+"); } ?>":""}}),ThriveLeads.models.FormType=ThriveLeads.models.FormTypeBase.extend({idAttribute:"ID",defaults:function(){return{ID:"",tve_form_type:"",post_parent:0,post_title:"",impressions:"0",conversions:"0",conversion_rate:"N/A",variations:new ThriveLeads.collections.FormVariations,variations_archived:new ThriveLeads.collections.FormVariations,parent:{},active_test:null,completed_tests:new ThriveLeads.collections.Tests,has_frequency_settings:!1,has_position_settings:!1,has_animation_settings:!1,display_on_mobile:1,display_status:1}},parse:function(a,b){return a&&a.variations&&(this.get("variations")instanceof ThriveLeads.collections.FormVariations?(this.get("variations").reset(a.variations),a.variations=this.get("variations")):a.variations=new ThriveLeads.collections.FormVariations(a.variations)),a&&a.variations_archived&&(this.get("variations_archived")instanceof ThriveLeads.collections.FormVariations?(this.get("variations_archived").reset(a.variations_archived),a.variations_archived=this.get("variations_archived")):a.variations_archived=new ThriveLeads.collections.FormVariations(a.variations_archived)),a&&a.completed_tests&&(this.get("completed_tests")instanceof ThriveLeads.collections.Tests?(this.get("completed_tests").reset(a.completed_tests),a.completed_tests=this.get("completed_tests")):a.completed_tests=new ThriveLeads.collections.Tests(a.completed_tests)),a},url:function(){return ThriveLeads.ajaxurl("action=thrive_leads_backend_ajax&route=formTypeAPI&ID="+this.get("ID"))},initialize:function(){if(!(this.get("variations")instanceof Backbone.Collection)){var a=new ThriveLeads.collections.FormVariations(this.get("variations"));this.set("variations",a)}!this.get("active_test")||this.get("active_test")instanceof Backbone.Model?this.set("active_test",new ThriveLeads.models.Test):this.set("active_test",new ThriveLeads.models.Test(this.get("active_test")))},setVariationParent:function(a){a.set("parent",this)},decreaseStatistics:function(a){var b=this.get("impressions")-parseInt(a.get("impressions")),c=this.get("conversions")-parseInt(a.get("conversions"));this.set({impressions:b,conversions:c,conversion_rate:ThriveLeads.conversion_rate(b,c)})}}),ThriveLeads.collections.FormTypes=Backbone.Collection.extend({model:ThriveLeads.models.FormType}),ThriveLeads.models.FormVariationBase=Backbone.Model.extend({defaults:function(){return{key:"",post_parent:0,post_title:"",post_status:"publish",trigger:"page_load",trigger_nice_name:"",trigger_config:{},impressions:"0",conversions:"0",conversion_rate:"N/A",parent:{},hide_buttons:!1,is_control:!0,has_frequency_settings:!1,has_position_settings:!1,has_animation_settings:!1,display_frequency:"0",display_position:"",display_animation:"",display_frequency_nice_name:"",display_position_nice_name:"",display_animation_nice_name:""}},idAttribute:"key",url:function(){return ThriveLeads.ajaxurl("action=thrive_leads_backend_ajax&route=formVariationAPI&key="+this.get("key")+"&form_type="+this.get("post_parent"))},resetStatistics:function(a){var b=_.extend({type:"post",dataType:"json",url:ThriveLeads.ajaxurl("action=thrive_leads_backend_ajax&route=formVariationAPI&key="+this.get("key")+"&form_type="+this.get("post_parent")+"&custom_action=reset_statistics")},a);return jQuery.ajax(b)}}),ThriveLeads.models.FormVariation=ThriveLeads.models.FormVariationBase.extend({initialize:function(){}}),ThriveLeads.collections.FormVariations=ThriveLeads.collections.Variations.extend({model:ThriveLeads.models.FormVariation}),ThriveLeads.models.Test=Backbone.Model.extend({idAttribute:"id",defaults:function(){return{id:"",item_ids:[],auto_win_min_conversions:100,auto_win_min_duration:14,auto_win_chance_original:95,test_type:ThriveLeads["const"].variation_test_type}},validate:function(a){var b=!0,c={};return a.title.length<=0&&(b=!1,c.title=ThriveLeads["const"].translations.TestTitleRequired),a.auto_win_enabled&&(a.auto_win_min_conversions.length<=0?(b=!1,c.auto_win_min_conversions=ThriveLeads["const"].translations.AutoWinMinConversionsRequired):(isNaN(a.auto_win_min_conversions)||a.auto_win_min_conversions<0)&&(b=!1,c.auto_win_min_conversions=ThriveLeads["const"].translations.PositiveIntegerNumber),a.auto_win_min_duration.length<=0?(b=!1,c.auto_win_min_duration=ThriveLeads["const"].translations.AutoWinMinDurationRequired):(isNaN(a.auto_win_min_duration)||a.auto_win_min_duration<0)&&(b=!1,c.auto_win_min_duration=ThriveLeads["const"].translations.PositiveIntegerNumber),a.auto_win_chance_original.length<=0?(b=!1,c.auto_win_chance_original=ThriveLeads["const"].translations.AutoWinChanceOriginalRequired):(isNaN(a.auto_win_chance_original)||a.auto_win_chance_original>100||a.auto_win_chance_original<0)&&(b=!1,c.auto_win_chance_original=ThriveLeads["const"].translations.PositivePercentNumber)),a.form_types&&a.form_types.length<2&&(b=!1,c.form_types=ThriveLeads["const"].translations.MinimumTwoFormTypesAreRequired),b?void 0:c},setAsGroupType:function(){this.set("test_type",ThriveLeads["const"].group_test_type)},setAsShortcodeType:function(){this.set("test_type",ThriveLeads["const"].shortcode_test_type)},setAsTwoStepLightboxType:function(){this.set("test_type",ThriveLeads["const"].two_step_lightbox_test_type)},setAsVariationType:function(){this.set("test_type",ThriveLeads["const"].variation_test_type)},url:function(){return ThriveLeads.ajaxurl("action=thrive_leads_backend_ajax&route=testAPI&id="+this.get("id"))},isRunning:function(){return this.get("status")===ThriveLeads["const"].test_status.running}}),ThriveLeads.collections.Tests=Backbone.Collection.extend({model:ThriveLeads.models.Test}),ThriveLeads.models.Template=Backbone.Model.extend({defaults:{name:"",description:"",hangers:""},initialize:function(a,b){this.set("hangers",new Backbone.Collection([a.show_group_options,a.hide_group_options]))}}),ThriveLeads.collections.Templates=Backbone.Collection.extend({model:ThriveLeads.models.Template}),ThriveLeads.models.Filter=Backbone.Model.extend({defaults:{cssClass:"",identifier:"",label:""}}),ThriveLeads.collections.Filters=Backbone.Collection.extend({model:ThriveLeads.models.Filter}),ThriveLeads.models.Option=Backbone.Model.extend({defaults:{label:"",isChecked:!1,id:"",type:null},validate:function(a){return a.label.length?void 0:(alert("Empty links are not accepted !"),"just return something")},toggle:function(){this.set("isChecked",!this.get("isChecked"))},check:function(){this.set("isChecked",!0)},uncheck:function(){this.set("isChecked",!1)}}),ThriveLeads.collections.Options=Backbone.Collection.extend({model:ThriveLeads.models.Option,countCheckedOptions:function(){var a=0;return this.each(function(b){a+=b.get("isChecked")?1:0}),a}}),ThriveLeads.models.Tab=Backbone.Model.extend({defaults:function(){return{identifier:"",label:"",isActive:!1,actions:[],filters:[]}},initialize:function(a){this.set("options",new ThriveLeads.collections.Options(a.options)),this.set("filters",new ThriveLeads.collections.Filters(a.filters))},getTabIdentifierFromTabId:function(a){return a.replace("tve_leads_tab_","")},getTabIdFromIdentifier:function(){return"tve_leads_"+(this.get("exclusion")?"hide":"show")+"_tab_"+this.get("identifier")},getTabContentIdentifier:function(){return"tve_leads_tab_content_"+this.get("identifier")},countCheckedOptions:function(){if("others"===this.get("identifier")){var a=0;return this.get("options").each(function(b){("direct_url"===b.get("type")||b.get("isChecked"))&&a++}),a}return this.get("options").countCheckedOptions()},uncheckAll:function(){var a=[],b=this.get("options");b.each(function(b){b.set("isChecked",!1),"direct_url"===b.get("type")&&a.push(b)}),_.forEach(a,function(a){b.remove(a)})}}),ThriveLeads.collections.Tabs=Backbone.Collection.extend({model:ThriveLeads.models.Tab}),ThriveLeads.models.Hanger=Backbone.Model.extend({defaults:function(){return{identifier:"",tabs:""}},initialize:function(a,b){this.set("tabs",new ThriveLeads.collections.Tabs(a.tabs))},countCheckedOptions:function(){var a=0;return this.get("tabs").each(function(b){a+=b.countCheckedOptions()}),a},uncheckAll:function(){this.get("tabs").each(function(a){a.uncheckAll()})},getDataForSave:function(){var a={tabs:[]};return this.get("tabs").each(function(b,c){a.tabs[c]=[],b.get("options").each(function(b){(b.get("isChecked")||"direct_url"==b.get("type"))&&a.tabs[c].push(b.get("id"))})}),a}}),ThriveLeads.collections.Hangers=Backbone.Collection.extend({model:ThriveLeads.models.Hanger,uncheckAll:function(){this.each(function(a){a.uncheckAll()})}}),ThriveLeads.models.Shortcode=ThriveLeads.models.FormTypeBase.extend({idAttribute:"ID",defaults:function(){return{ID:"",post_title:"",post_parent:"0",impressions:"0",conversions:"0",conversion_rate:"N/A",variations:new ThriveLeads.collections.ShortcodeVariations,active_test:null,content_locking:0,shortcode_code:"",variations_archived:new ThriveLeads.collections.FormVariations,completed_tests:new ThriveLeads.collections.Tests}},url:function(){return ThriveLeads.ajaxurl("action=thrive_leads_backend_ajax&route=shortcodeAPI&ID="+this.get("ID"))},validate:function(a){var b={},c=!0;return a.post_title.length<=0&&(b.post_title=ThriveLeads["const"].translations.ShortcodeNameRequired,c=!1),c?void 0:b},initialize:function(){this.get("active_test")&&this.set("active_test",new ThriveLeads.models.Test(this.get("active_test"))),!this.get("variations")||this.get("variations")instanceof ThriveLeads.collections.ShortcodeVariations||this.set("variations",new ThriveLeads.collections.ShortcodeVariations(this.get("variations")))},setFormTypeParent:function(a){a.set("post_parent",this.get("ID"))},parse:function(a,b){return a&&a.variations&&(a.variations=new ThriveLeads.collections.ShortcodeVariations(a.variations)),a&&a.variations_archived&&(a.variations_archived=new ThriveLeads.collections.ShortcodeVariations(a.variations_archived)),a&&a.completed_tests&&(this.get("completed_tests")instanceof ThriveLeads.collections.Tests?(this.get("completed_tests").reset(a.completed_tests),a.completed_tests=this.get("completed_tests")):a.completed_tests=new ThriveLeads.collections.Tests(a.completed_tests)),a},getCode:function(){if(this.get("shortcode_code").length)return this.get("shortcode_code");var a=1==this.get("content_locking")?"[thrive_lead_lock id='"+this.get("ID")+"']Hidden Content[/thrive_lead_lock]":"[thrive_leads id='"+this.get("ID")+"']";return this.set("shortcode_code",a),this.get("shortcode_code")},decreaseStatistics:function(a){var b=this.get("impressions")-parseInt(a.get("impressions")),c=this.get("conversions")-parseInt(a.get("conversions"));this.set({impressions:b,conversions:c,conversion_rate:ThriveLeads.conversion_rate(b,c)})}}),ThriveLeads.collections.Shortcode=Backbone.Collection.extend({model:ThriveLeads.models.Shortcode,getTotalVariations:function(){var a=0;return this.each(function(b){a+=b.get("variations").length}),a}}),ThriveLeads.models.ShortcodeVariation=ThriveLeads.models.FormVariationBase.extend({initialize:function(){}}),ThriveLeads.collections.ShortcodeVariations=ThriveLeads.collections.Variations.extend({model:ThriveLeads.models.ShortcodeVariation}),ThriveLeads.models.TwoStepLightbox=ThriveLeads.models.FormTypeBase.extend({idAttribute:"ID",defaults:function(){return{ID:"",post_title:"",impressions:"0",conversions:"0",conversion_rate:"N/A",variations:new ThriveLeads.collections.TwoStepLightboxVariations,variations_archived:new ThriveLeads.collections.FormVariations,active_test:null,completed_tests:new ThriveLeads.collections.Tests}},url:function(){return ThriveLeads.ajaxurl("action=thrive_leads_backend_ajax&route=twoStepLightBoxAPI&ID="+this.get("ID"))},initialize:function(){this.get("active_test")&&this.set("active_test",new ThriveLeads.models.Test(this.get("active_test"))),!this.get("variations")||this.get("variations")instanceof ThriveLeads.collections.TwoStepLightboxVariations||this.set("variations",new ThriveLeads.collections.TwoStepLightboxVariations(this.get("variations")))},validate:function(a){var b={},c=!0;return a.post_title.length<=0&&(b.post_title=ThriveLeads["const"].translations.TwoStepLightboxNameRequired,c=!1),c?void 0:b},setFormTypeParent:function(a){a.set("post_parent",this.get("ID"))},parse:function(a,b){return a&&a.variations&&(a.variations=new ThriveLeads.collections.TwoStepLightboxVariations(a.variations)),a&&a.variations_archived&&(a.variations_archived=new ThriveLeads.collections.TwoStepLightboxVariations(a.variations_archived)),a&&a.completed_tests&&(this.get("completed_tests")instanceof ThriveLeads.collections.Tests?(this.get("completed_tests").reset(a.completed_tests),a.completed_tests=this.get("completed_tests")):a.completed_tests=new ThriveLeads.collections.Tests(a.completed_tests)),a},getCode:function(){return"[thrive_2step id='"+this.get("ID")+"']Trigger goes here[/thrive_2step]"},decreaseStatistics:function(a){var b=this.get("impressions")-parseInt(a.get("impressions")),c=this.get("conversions")-parseInt(a.get("conversions"));this.set({impressions:b,conversions:c,conversion_rate:ThriveLeads.conversion_rate(b,c)})}}),ThriveLeads.collections.TwoStepLightbox=Backbone.Collection.extend({model:ThriveLeads.models.TwoStepLightbox,getTotalVariations:function(){var a=0;return this.each(function(b){a+=b.get("variations").length}),a}}),ThriveLeads.models.TwoStepLightboxVariation=ThriveLeads.models.FormVariationBase.extend({defaults:function(){return{key:"",post_parent:0,post_title:"",post_status:"publish",trigger:"click",trigger_nice_name:"",trigger_config:{},impressions:"0",conversions:"0",conversion_rate:"N/A",parent:{},hide_buttons:!1,is_control:!0,has_frequency_settings:!1,has_position_settings:!1,has_animation_settings:!1,display_frequency:"0",display_position:"",display_animation:"",display_frequency_nice_name:"",display_position_nice_name:"",display_animation_nice_name:""}},initialize:function(){}}),ThriveLeads.collections.TwoStepLightboxVariations=ThriveLeads.collections.Variations.extend({model:ThriveLeads.models.TwoStepLightboxVariation}),ThriveLeads.models.OneClickSignup=ThriveLeads.models.FormTypeBase.extend({idAttribute:"ID",defaults:function(){return{ID:"",post_title:"",impressions:"0",conversions:"0",conversion_rate:"N/A",active_test:null,post_link:"",redirect_url:{},api_connections:{},signups:"0"}},url:function(){return ThriveLeads.ajaxurl("action=thrive_leads_backend_ajax&route=oneClickSignupAPI&ID="+this.get("ID"))},validate:function(a){var b={},c=!0;return a.post_title.length<=0&&(b.post_title=ThriveLeads["const"].translations.OneClickSignupNameRequired,c=!1),c?void 0:b},setFormTypeParent:function(a){a.set("post_parent",this.get("ID"))},getCode:function(){return this.get("post_link")+"&tl_name=[name]&tl_email=[email]"},decreaseStatistics:function(){this.set({signups:"0"})},isDataValid:function(){var b=!0,c=this.get("redirect_url"),d=jQuery.map(this.get("api_connections"),function(a){return a});return b=a(c)&&a(c.type)?"single-redirect"==c.type?c.single&&""!=c.single?!0:!1:c.before&&""!=c.before||c.during&&""!=c.during||c.after&&""!=c.after_event?!0:!1:!1,a(d)&&0!==d.length||(b&=!1),b}}),ThriveLeads.collections.OneClickSignup=Backbone.Collection.extend({model:ThriveLeads.models.OneClickSignup}),ThriveLeads.models.OneClickSignupVariation=ThriveLeads.models.FormVariationBase.extend({defaults:function(){return{key:"",post_parent:0,post_title:"",post_status:"publish",trigger:"click",trigger_nice_name:"",trigger_config:{},impressions:"0",conversions:"0",conversion_rate:"N/A",parent:{},hide_buttons:!1,is_control:!0,has_frequency_settings:!1,has_position_settings:!1,has_animation_settings:!1,display_frequency:"0",display_position:"",display_animation:"",display_frequency_nice_name:"",display_position_nice_name:"",display_animation_nice_name:""}},initialize:function(){}}),ThriveLeads.models.Message=Backbone.Model.extend({defaults:function(){return{text:"text",status:"success"}}}),ThriveLeads.collections.Messages=Backbone.Collection.extend({model:ThriveLeads.models.Message}),ThriveLeads.objects.messages=new ThriveLeads.collections.Messages,ThriveLeads.models.PageTitle=Backbone.Model.extend({defaults:function(){return{parts:[],separator:" ‹ ",default_title:""}},initialize:function(){this.set("parts",this.get("default_title").split(this.get("separator")))},prepend:function(a,b,c){"undefined"!=typeof b&&b&&this.get("parts").length>1&&this.get("parts").shift(),"undefined"!=typeof c&&c&&this.set("parts",this.get("default_title").split(this.get("separator"))),this.get("parts").unshift(a),this.trigger("title_change",this.getTitle())},replaceFirst:function(a){return this.prepend(a,!0)},getTitle:function(){return this.get("parts").join(this.get("separator"))}}),ThriveLeads.models.InboundLink=Backbone.Model.extend({defaults:function(){return{base_url:"",target_all:"1",groups:[],form_type:0,period_type:0,period_days:""}},getLink:function(){var a=this.get("base_url");a+=-1!==a.indexOf("?")?"":"?",a+="?"==a.substr(-1)?"":"&",a+="tl_inbound=1","0"!=this.get("target_all")&&(a+="&tl_target_all="+this.get("target_all"));var b=this.get("groups");b.forEach(function(b){a+="&tl_groups[]="+b}),"0"!=this.get("form_type")&&(a+="&tl_form_type="+this.get("form_type")),"0"!=this.get("period_type")&&(a+="&tl_period_type="+this.get("period_type"));var c=this.get("period_days");return""!=c&&(a+="&tl_period_days="+c),a}}),ThriveLeads.models.Breadcrumbs=Backbone.Model.extend({defaults:function(){return{name:"",url:"",last:0}}}),ThriveLeads.collections.BreadcrumbsCollection=Backbone.Collection.extend({model:ThriveLeads.models.Breadcrumbs})}();