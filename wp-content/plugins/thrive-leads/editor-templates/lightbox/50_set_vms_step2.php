<?php
$config = array(
	'email'    => 'Please enter a valid email address',
	'phone'    => 'Please enter a valid phone number',
	'required' => 'Name and Email fields are required'
)
/**
 * STATE EVENTS examples (important bits: data-tcb-events and the "tve_evt_manager_listen tve_et_click" classes
 *
 * -close lb:
 * <a href="#" data-tcb-events="|close_lightbox|" class="tve_evt_manager_listen tve_et_click">CLOSE THIS LIGHTBOX</a>
 *
 * -state switch example ( open_state_x, where x is the index in the _config / multi_step / states array:
 * <a href="#" data-tcb-events="|open_state_2|" class="tve_evt_manager_listen tve_et_click">open state 2</a>
 */
?>
<div class="thrv_wrapper thrv_contentbox_shortcode" data-tve-style="5">
	<div class="tve_cb tve_cb5 tve_white" style="margin-left: -20px;margin-right: -20px;margin-bottom: -20px;">
		<div class="tve_cb_cnt">
			<h2 class="tve_p_center rft" style="color: #666; font-size: 45px;margin-top: 20px;margin-bottom: 40px;">
				Solutions for <span class="bold_text">Smarter</span> Content Marketing
			</h2>
			<div class="thrv_wrapper thrv_columns tve_clearfix">
				<div class="tve_colm tve_oth">
					<div style="width: 197px;margin-top: 0;margin-bottom: -100px;"
					     class="thrv_wrapper tve_image_caption aligncenter">
                                 <span class="tve_image_frame">
                                    <img class="tve_image"
                                         src="<?php echo TVE_LEADS_URL . 'editor-templates/_form_css/images/50_set_image.png' ?>"
                                         style="width: 197px;"/>
                                </span>
					</div>
				</div>
				<div class="tve_colm tve_tth tve_lst">
					<h4 style="color: #666666; font-size: 25px;margin-bottom: 120px;margin-top: 0;">
						<font color="#6bd6e1">Sign up Below </font><br>
						to get access to your <span class="bold_text">FREE PRODUCT</span>
					</h4>
				</div>
			</div>
		</div>
	</div>
</div>
<div class="thrv_wrapper thrv_contentbox_shortcode" data-tve-style="5">
	<div class="tve_cb tve_cb5 tve_black" style="margin-left: -20px;margin-right: -20px;padding-bottom: 60px;">
		<div class="tve_cb_cnt">
			<div class="thrv_wrapper thrv_columns tve_clearfix" style="margin-top: 0;margin-bottom: 0;">
				<div class="tve_colm tve_oth">
					<p>&nbsp;</p>
				</div>
				<div class="tve_colm tve_tth tve_lst">
					<div
						class="thrv_wrapper thrv_lead_generation tve_clearfix thrv_lead_generation_horizontal tve_orange tve_3"
						data-inputs-count="3" data-tve-style="1" style="margin-top: -120px; margin-bottom: 0;">
						<div class="thrv_lead_generation_code" style="display: none;"></div>
						<input type="hidden" class="tve-lg-err-msg"
						       value="<?php echo htmlspecialchars( json_encode( $config ) ) ?>"/>

						<div class="thrv_lead_generation_container tve_clearfix">
							<div class="tve_lead_generated_inputs_container tve_clearfix">
								<div class="tve_lead_fields_overlay"></div>
								<div class=" tve_lg_input_container tve_lg_3 tve_lg_input">
									<input type="text" data-placeholder="" value="" name="name"
									       placeholder="Your name"/>
								</div>
								<div class=" tve_lg_input_container tve_lg_3 tve_lg_input">
									<input type="text" data-placeholder="" value="" name="email"
									       placeholder="Your Email Address"/>
								</div>
								<div class="tve_lg_input_container tve_submit_container tve tve_lg_3 tve_lg_submit">
									<button type="Submit">Send me the FREE DOWNLOAD</button>
								</div>
							</div>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>
</div>