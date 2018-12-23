<?php

/******************************************************************************************
 * Copyright (C) Smackcoders 2016 - All Rights Reserved
 * Unauthorized copying of this file, via any medium is strictly prohibited
 * Proprietary and confidential
 * You can contact Smackcoders at email address info@smackcoders.com.
 *******************************************************************************************/
if ( ! defined( 'ABSPATH' ) )
        exit; // Exit if accessed directly

$siteurl = site_url();
$siteurl = esc_url( $siteurl );
$config = get_option("wp_thirdpartyplugin_settings");
$active_plugin = get_option('WpLeadBuilderProActivatedPlugin');
/* define the plugin folder url */
define('WP_LB_PLUGIN_URL', plugin_dir_url(__FILE__));

?>

</script>
<?php
        $Thirdparty_plugin = get_option( "WpLeadThirdPartyPLugin" );            
?>
<input type="hidden" id="third_plugin_value" value='<?php echo $Thirdparty_plugin ;?>'>
<div>
<!--  Start -->
    <form id="smack-thirdparty-settings-form"  method="post">
        <input type="hidden" name="smack-thirdparty-settings-form" value="smack-thirdparty-settings-form" />
        <input type="hidden" id="plug_URL" value="<?php echo esc_url(SM_LB_URL);?>" />
        <!-- <div class="wp-common-crm-content" style="width: 800px;float: left;">
        </div> -->
    
<script>
jQuery( "#dialog-modal" ).hide();
</script>
    <span id="Fields" style="margin-right:20px;"></span>
    </form>
<!-- End-->
</div>

<div id="loading-image" style="display: none; background:url(<?php echo esc_url(WP_LB_PLUGIN_URL);?>/wp-leads-builder-any-crm/assets/images/ajax-loaders.gif) no-repeat center"><?php echo esc_html__('' , 'wp-leads-builder-any-crm'  ); ?> </div>

<?php
    echo '<br>';   
    $captcha_config = get_option( "wp_captcha_settings" );
    $captcha = SM_LB_URL ;
?>
<div>
  <div class="panel" style="width:99%;">
    <div class="panel-body">

<div class = "captcha" style="margin-left:20px;"> 
    <form id="smack-<?php echo $active_plugin;?>-captcha-form"  method="post">
    <input type="hidden" name="smack-<?php echo $active_plugin;?>-captcha-form" value="smack-<?php echo $active_plugin;?>-captcha-form" />
         
         <div class="form-group">
         <label id="inneroptions" class="leads-builder-heading" ><?php echo esc_html__('Debug and Notification ' , 'wp-leads-builder-any-crm' );?> </label>
        </div>
    <div class="form-group col-md-12">     
         <div class="col-md-3">
         <label id="innertext" class="leads-builder-label" ><?php echo esc_html__('Which log do you need?' , 'wp-leads-builder-any-crm' );?> </label>
        </div>
        <div class="col-md-2">
         <span id="circlecheck">
            <select class="selectpicker form-control" data-live-search="false" name="emailcondition" id="emailcondition" onchange="enablesmackemail(this.id)">
                <option value="none" id='smack_email'
                <?php if(isset($captcha_config['emailcondition']) && $captcha_config['emailcondition'] == 'none'){
                echo "selected=selected";
                }?>>None
                </option>
                <option value = "success" id='successemailcondition'<?php if(isset($captcha_config['emailcondition']) && $captcha_config['emailcondition'] == 'success'){
                echo "selected=selected";
                }?>>Success
                </option>
                <option value="failure"  id = 'failureemailcondition' disabled
                <?php if(isset($captcha_config['emailcondition']) && $captcha_config['emailcondition'] == 'failure'){
                echo "selected=selected";
                }?>>Failure
                </option>
                <option value="both" id = 'bothemailcondition' disabled
                <?php if(isset($captcha_config['emailcondition']) && $captcha_config['emailcondition'] == 'both'){ echo "selected=selected";
                }?>>Both
                </option>
                </select>
        </span>
        </div>
    </div>
     
     <div class="form-group col-md-12">
       <div class="col-md-3">
            <label id="innertext" class="leads-builder-label"> <?php echo esc_html__('Specify Email', "wp-leads-builder-any-crm" ); ?> </label>
        </div>
        <div class="col-md-4">
        <input type='text' class='smack-vtiger-settings form-control' name='email' id='email' value="<?php if(isset($captcha_config['email'])) { echo sanitize_email($captcha_config['email']); } ?>" <?php if( isset( $captcha_config['emailcondition']) && $captcha_config['emailcondition'] == 'none' ){ ?> disabled="disabled" <?php } ?> />
        </div>
    </div>

    <div class="form-group col-md-12">
        <div class="col-md-3">
            <label id="innertext" class="leads-builder-label"><?php echo esc_html__('Enable Debug mode ' , 'wp-leads-builder-any-crm' ); ?></label>
        </div>
        <div class="col-md-4">
             <div class="switch">
             	    <!-- tfa button -->
        <input id="debugmode" type='checkbox' class="tgl tgl-skewed noicheck smack-vtiger-settings-text" name='debugmode' <?php if(isset($captcha_config['debugmode']) && sanitize_text_field($captcha_config['debugmode']) == 'on') { echo "checked=checked"; } ?> onclick="debugmod(this.id)" />
        <label id="innertext" data-tg-off="OFF" data-tg-on="ON" for="debugmode"  class="tgl-btn" style="font-size: 16px;" >
        </label>
        <!-- tfa btn End -->

             </div>   
        </div>
    </div>

    <div class="form-group col-md-12">
         <div class="col-md-3">
             <label id="inneroptions" class="leads-builder-label"><?php echo esc_html__("Do you want to enable the captcha " , "wp-leads-builder-any-crm" ); ?> <?php echo str_repeat( '&nbsp' , 2 ); ?>  </label>
        </div>
        <div class="col-md-4">        
             <span id="circlecheck">
             <input type='radio'  name='smack_recaptcha' id='smack_recaptcha_no'  value="no"
             <?php if(isset($captcha_config['smack_recaptcha']) && $captcha_config['smack_recaptcha']=='no' || !isset($captcha_config['smack_recaptcha'])) { echo "checked"; } ?> onclick="showOrHideRecaptchaPRO('no');"> 
             <label for="smack_recaptcha_no"  id="innertext"  class="leads-builder-label mr10"> <?php echo esc_html__('No' , 'wp-leads-builder-any-crm' ); ?>
             </label>
             <input type='radio'  name='smack_recaptcha' id='smack_recaptcha_yes'  value="yes"<?php if(isset($captcha_config['smack_recaptcha']) && $captcha_config['smack_recaptcha']=='yes') { echo "checked"; } ?> onclick="showOrHideRecaptchaPRO('yes');">
             <label for="smack_recaptcha_yes"  id="innertext"  class="leads-builder-label"> <?php echo esc_html__('Yes' , 'wp-leads-builder-any-crm' ); ?>
             </label>
             </span>
         </div>
    </div>          


<div class='leads-captcha'>
<div id="recaptcha_public_key"
    <?php 
        if(isset($captcha_config['smack_recaptcha']) && $captcha_config['smack_recaptcha']=='no' || !isset($captcha_config['smack_recaptcha']))
                {
                        echo 'style="display:none"';
                }
                else
                {
                        echo 'style="display:block;margin-top:18px;"';
                }?>
>    <div class="form-group col-md-12">
	<div style="margin-left:33%; color:red"> Upgrade to PRO</div>
         <div class="col-md-3">
             <label id="innertext" class="leads-builder-label"><?php echo esc_html__('Google Recaptcha Public Key' , 'wp-leads-builder-any-crm' ); ?>  </label>
         </div>
    
         <div class="col-md-4">
             <input type='text' 
             class='smack-vtiger-settings-text form-control' placeholder='<?php echo esc_attr__('Enter your recaptcha public key here', 'wp-leads-builder-any-crm' ); ?>' name='recaptcha_public_key' id='smack_public_key' value="" disabled/>
         </div>
     </div>     
    
    <div>
         <div style ="padding-left:20px; position:relative;top:-9px;">
        <a class="tooltip"  href="#">
        <img src="<?php echo SM_LB_DIR; ?>assets/images/help.png"><span class="tooltipPostStatus">
        <img src="<?php echo SM_LB_DIR; ?>assets/images/callout.gif" class="callout">
        <?php echo __('Enter your recaptcha public key here.', 'wp-leads-builder-any-crm' ); ?>
        <img style="margin-top: 6px;float:right;" src="<?php echo SM_LB_DIR; ?>assets/images/help.png">
        </span>
        </a>
        </div>
     </div>
</div><!-- recaptcha public key div close -->


<div id="recaptcha_private_key" <?php
    if(isset($captcha_config['smack_recaptcha']) && $captcha_config['smack_recaptcha']=='no' || !isset($captcha_config['smack_recaptcha']))
    {
    echo 'style="display:none"';
    }
    else
    {
        echo 'style="display:block;margin-top:13px"';
    }
    ?>
>
    <div class="form-group col-md-12">
         <div class="col-md-3">
             <label id="innertext" class="leads-builder-label"><?php echo esc_html__("Google Recaptcha Private Key", "wp-leads-builder-any-crm" ); ?></label><?php echo str_repeat( '&nbsp;' , 50 ); ?>
         </div>    
         <div class="col-md-4">
             <input type='text'  
             class='smack-vtiger-settings-text form-control' placeholder='<?php echo esc_attr__("Enter your recaptcha private key here" , "wp-leads-builder-any-crm" ); ?>' name='recaptcha_private_key' id='smack_private_key' value="" disabled/>
         </div>
     </div>     

     <div style ="padding-left:14px; position:relative;top:-12px;">
     <a class="tooltip"  href="#">
     <img src="<?php echo SM_LB_DIR; ?>assets/images/help.png">
     <span class="tooltipPostStatus">
     <img src="<?php echo SM_LB_DIR; ?>assets/images/callout.gif" class="callout">
     <?php echo __("Enter your recaptcha private key here." , "wp-leads-builder-any-crm" ); ?>
     <img style="margin-top: 6px;float:right;" src="<?php echo SM_LB_DIR; ?>assets/images/help.png">
     </span> </a>
    </div>                 
</div><!-- recaptcha private key div close -->
</div><!--leads captcha div close -->

     <div class="col-md-offset-11">
         <input type="hidden" name="posted" value="<?php echo 'posted';?>">
        <input type="button" value="<?php echo esc_attr__('Save' , 'wp-leads-builder-any-crm' );?>" onclick="save_captcha_key();" id="innersave" class="smack-btn smack-btn-primary btn-radius" />
     </div>   

    </div>
  </div>
</div>    
