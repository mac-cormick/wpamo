<?php

if ( ! defined( 'ABSPATH' ) )
        exit; // Exit if accessed directly
  global $wpdb;
  $siteurl = site_url();
  $siteurl = esc_url( $siteurl );
  $active_plugin = get_option('WpLeadBuilderProActivatedPlugin');
  //Check Shortcode available
  $check_shortcode = $wpdb->get_results( $wpdb->prepare("select shortcode_name from wp_smackleadbulider_shortcode_manager where crm_type=%s", $active_plugin));
  $check_field_manager = $wpdb->get_results( $wpdb->prepare("select field_name from wp_smackleadbulider_field_manager where crm_type=%s", $active_plugin));
  $count_shortcode=0;
        $count_shortcode = count($check_shortcode);
  if( !empty( $check_field_manager)){
         if( $count_shortcode>1 ){
      $shortcode_available = 'yes';
    }else{
      $shortcode_available = 'no';
    }
  }else{
    $shortcode_available = 'yes';
  }
  echo "<input type='hidden' id='check_shortcode_availability' value='$shortcode_available'>";
  echo "<input type='hidden' id='count_shortcode' value='$count_shortcode'>";
//END

  $config = get_option("wp_{$active_plugin}_settings");

  $smack_active_plugin = get_option('WpLeadBuilderProActivatedPlugin');
  if($smack_active_plugin == 'wpzohopro'){
    $smack_TFA_label = 'TFA_zoho_authtoken';
    $smack_CRM_label = 'Zoho CRM Settings';
  }
  else{
    $smack_TFA_label = 'TFA_zoho_plus_authtoken';
    $smack_CRM_label = 'Zoho CRM Plus Settings';
  }
  if( $config == "" )
  {
          $config_data = 'no';
  }
  else
  {
          $config_data = 'yes';
  }
?>
<div class="mt20">
 <div class="form-group col-md-5 col-md-offset-7">    
        <div class="col-md-6">
            <label id="inneroptions" class="leads-builder-crm"><?php echo esc_html__("Select the CRM you use" , "wp-leads-builder-any-crm" ); ?></label>
        </div>
        <div class="col-md-5">          
            <?php $ContactFormPluginsObj = new ContactFormPROPlugins();echo $ContactFormPluginsObj->getPluginActivationHtml();
      ?>
        </div>
</div><!-- form group close -->
</div>  
<div class="clearfix"></div>      
<div class="">
  <div class="panel" style="width:99%;">
    <div class="panel-body">
  <img src="<?php echo SM_LB_DIR?>assets/images/zohocrm-logo.png" width=150 height=35>
    <input type="hidden" id="get_config" value="<?php echo $config_data ?>" >
    <input type="hidden" id="revert_old_crm_pro" value="<?php echo $smack_active_plugin;?>">
    <span id="save_config" style="font:14px;width:200px;">
    </span>
<form id="smack-zoho-settings-form"  method="post">
    <input type="hidden" name="smack-zoho-settings-form" value="smack-zoho-settings-form" />
  <input type="hidden" id="plug_URL" value="<?php echo esc_url(SM_LB_URL);?>" />
  <!-- <div class="wp-common-crm-content" style="width:900px;float: left;"> -->
<!-- <div class="form-group">
   <div class="col-md-3 col-md-offset-3">
       <label id="inneroptions" class="leads-builder-heading"><?php echo esc_html__("Select the CRM you use" , "wp-leads-builder-any-crm" ); ?></label>
    </div>
     <div class="col-md-3">
        <?php $ContactFormPluginsObj = new ContactFormPROPlugins();echo $ContactFormPluginsObj->getPluginActivationHtml();
      ?>
  </div>
</div> -->
<div class="clearfix"></div>
<hr>
<div class="mt30"> 
    <div class="form-group col-md-12">  
       <label id="inneroptions" class="leads-builder-heading"><?php echo $smack_CRM_label;?>
       </label>
    </div>
</div>
<div class="clearfix"></div>
<div class="mt20">    
<div class="form-group col-md-12">
   <div class="col-md-3">
      <label id="innertext" class="leads-builder-label"> <?php echo esc_html__('Username' , 'wp-leads-builder-any-crm' ); ?> </label>
    </div>
    <div class="col-md-4">   
        <input type='text' class='smack-vtiger-settings form-control' name='username' id='smack_host_username' value="<?php echo sanitize_text_field($config['username']) ?>"/>
    </div>    
</div>
<div class="form-group col-md-12">
   <div class="col-md-3">
      <label id="innertext" class="leads-builder-label"> <?php echo esc_html__('Password', 'wp-leads-builder-any-crm' ); ?> </label>
    </div>
    <div class="col-md-4">   
        <input type='password' class='smack-vtiger-settings form-control' name='password' id='smack_host_access_key' value="<?php echo sanitize_text_field($config['password']) ?>"/>
    </div>    
</div>
<!-- TWO FACTOR AUTHENTICATION -->
<div class="form-group col-md-12">
   <div class="col-md-3">
      <label id="innertext" class="leads-builder-label"><?php echo esc_html__("Two Factor Authentication" , SM_LB_SLUG ); ?></label>
    </div>
    <div class="col-md-4">   
        <!--<input type='checkbox' class="smack-vtiger-settings cmn-toggle cmn-toggle-yes-no" name='TFA_check' id='TFA_check' <?php if(isset($config['TFA_check']) && sanitize_text_field($config['TFA_check']) == 'on') { echo "checked=checked"; }  ?> onclick="enablesmackTFA(this.id)" /> 
        <label class="TFA_check" for="TFA_check" id="innertext" data-on="On" data-off="Off" ></label>
    <input type="hidden" id="TFA_check"  > -->
        <!-- tfa button -->
        <!--<input id="tfa-btn" type='checkbox' class="tgl tgl-skewed noicheck" name='tfa-btn'>
        <label data-tg-off="OFF" data-tg-on="ON" for="tfa-btn"  class="tgl-btn" style="font-size: 16px;" >
        </label>-->
        <!-- tfa btn End -->
  <!-- tfa button -->
        <input id="TFA_check" type='checkbox' class="tgl tgl-skewed noicheck smack-vtiger-settings" name='TFA_check' <?php if(isset($config['TFA_check']) && sanitize_text_field($config['TFA_check']) == 'on') { echo "checked=checked"; }  ?> onclick="enablesmackTFA(this.id)" />
        <label id="innertext" data-tg-off="OFF" data-tg-on="ON" for="TFA_check"  class="tgl-btn TFA_check" style="font-size: 16px;" >
        </label>
        <!-- tfa btn End -->
    </div>    
</div>
<div class="form-group col-md-12">
   <div class="col-md-3" id="TFA_tr_show_hide">
      <label id="innertext" class="leads-builder-label"> <?php echo esc_html__("Specify Authtoken" , SM_LB_SLUG ); ?></label>
    </div>
    <div class="col-md-4">   
        <input type="text" id="TFA_authkey" class="form-control" onblur="TFA_Authkey_Save(this.value)" value="<?php echo get_option($smack_TFA_label);?>" <?php if( !isset( $config['TFA_check'] ) || sanitize_text_field($config['TFA_check']) != 'on' ){ ?> disabled="disabled" <?php } ?> >
<!--</div> wp-common-crm-content div end -->
<div style="margin-left:195px">
    <div class="tooltip"  style="margin-top:-24px">
    <?php echo $help ?>
        <span class="tooltipPostStatus" style="width:300px;">
            <h5>Zoho AuthToken</h5>Generate and Specify the TFA AuthToken from Zoho Accounts.
          <a target="_blank" href="https://www.zoho.com/crm/help/api/using-authentication-token.html">Refer Zoho Help
          </a>
        </span>
    </div>
</div>
    </div>    
</div>
</div>
    <input type="hidden" name="posted" value="<?php echo 'posted';?>">
    <input class="smack_settings_input_text" type="hidden" id="authkey" name="authkey" value="" />
    <input type="hidden" id="site_url" name="site_url" value="<?php echo esc_attr($siteurl) ;?>">
  <input type="hidden" id="active_plugin" name="active_plugin" value="<?php echo esc_attr($active_plugin); ?>">
  <input type="hidden" id="leads_fields_tmp" name="leads_fields_tmp" value="smack_wpzohopro_leads_fields-tmp">
  <input type="hidden" id="contact_fields_tmp" name="contact_fields_tmp" value="smack_wpzohopro_contacts_fields-tmp">
<div class="col-md-offset-9"> 
        <span id="SaveCRMConfig">
          <input type="button" value="<?php echo esc_attr__('Save CRM Configuration' , 'wp-leads-builder-any-crm' );?>" id="save"  class="smack-btn smack-btn-primary btn-radius" onclick="saveCRMConfiguration(this.id);" />
        </span>
</div>
<!--</div> wp-common-crm-content  -->
</form>
<div id="loading-sync" style="display: none; background:url(<?php echo esc_url(WP_PLUGIN_URL);?>/wp-leads-builder-any-crm/assets/images/ajax-loaders.gif) no-repeat cente"><?php echo esc_html__('' , 'wp-leads-builder-any-crm' ); ?></div>
<div id="loading-image" style="display: none; background:url(<?php echo esc_url(WP_PLUGIN_URL);?>/wp-leads-builder-any-crm/assets/images/ajax-loaders.gif) no-repeat center"><?php echo esc_html__("" , "wp-leads-builder-any-crm" ); ?></div>
    </div>
  </div>
</div>    
