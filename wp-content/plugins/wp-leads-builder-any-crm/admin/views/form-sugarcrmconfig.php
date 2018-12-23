<?php

/******************************************************************************************
 * Copyright (C) Smackcoders 2016 - All Rights Reserved
 * Unauthorized copying of this file, via any medium is strictly prohibited
 * Proprietary and confidential
 * You can contact Smackcoders at email address info@smackcoders.com.
 *******************************************************************************************/
if ( ! defined( 'ABSPATH' ) )
        exit; // Exit if accessed directly
global $wpdb;
$siteurl = site_url();
$siteurl = esc_url( $siteurl );
$chkUpgrade = get_option('smack_wp_sugar_lead_fields');
$active_plugin = get_option('WpLeadBuilderProActivatedPlugin');
if($active_plugin != 'wpsuitepro'){
  update_option('PrevWpLeadBuilderProActivatedPlugin', $active_plugin);
}
$pre_plugin = get_option('PrevWpLeadBuilderProActivatedPlugin');
$pre_active_plugin = get_option('PrevWpLeadBuilderProActivatedPlugin');
$show = 'no';
$show_all_addon = 'no';

    switch ($pre_active_plugin) {
      case 'wptigerpro':
        $plugin = "Vtiger CRM";
        $add_on = "<a href='https://wordpress.org/plugins/wp-tiger/' target='blank' style='cursor:pointer;text-decoration:none' >WP Tiger</a>";
        $id_addon = 'vtiger';
        break;
      case 'wpzohopro':
      case 'wpzohopluspro':
        $plugin = "Zoho CRM";
        $add_on = "<a href='https://wordpress.org/plugins/wp-zoho-crm/' target='blank' style='cursor:pointer;text-decoration:none' >WP Zoho CRM</a>";
        $id_addon = 'zoho';
        break;
      case 'wpsalesforcepro':
        $plugin = "Sales Force";
        $add_on = "<a href='https://wordpress.org/plugins/wp-salesforce/' target='blank' style='cursor:pointer;text-decoration:none' >WP SalesForce</a>";
        $id_addon = 'sforce';
        break;
      case 'freshsales':
        $plugin = "Fresh Sales";
        $add_on = "<a href='https://wordpress.org/plugins/wp-freshsales/' target='blank' style='cursor:pointer;text-decoration:none' >WP FreshSales</a>";
        $id_addon = 'fsales';
        break;
      case 'wpsugarpro':
        $plugin = "Sugar CRM";
        $add_on = "<a href='https://wordpress.org/plugins/wp-sugar-free/' target='blank' style='cursor:pointe;text-decoration:noner' >WP Sugar Free</a>";
        $id_addon = 'sugar';
        break;
      default:
        $plugin = "Vtiger CRM";
        $add_on = "<a target='blank' style='cursor:pointer;text-decoration:none' >WP Tiger</a>";
        $id_addon = 'vtiger';
        break;
    }
    if (isset($_POST) && isset($_POST['crm_to_enable'])) {
 $crm_to_ac = $_POST['crm_to_enable'];
 switch ($crm_to_ac) {
   case 'vtiger':
         $hook = 'wp-tiger/index.php';
         break;
       case 'zoho':
         $hook = 'wp-zoho-crm/index.php';
         break; 
       case 'sforce':
         $hook = 'wp-salesforce/index.php';
         break;
       case 'fsales':
         $hook = 'wp-freshsales/index.php';
         break;
       case 'sugar':
         $hook = 'wp-sugar-free/index.php';
         break;
 }
 $dir = ABSPATH.'wp-content/plugins/'.$hook;
 if(is_file($dir)){
  include_once(ABSPATH.'wp-admin/includes/plugin.php');
   $activate = activate_plugin($hook);
   print_r($activate);
   if ( !is_wp_error( $activate ) ){
    echo "<script>location.reload();</script>";
   }
 }else{
  $show = 'show';
  //die();
 }
 
}
    if (isset($_REQUEST['page']) && ($_REQUEST['page'] == 'lb-crmconfig' || $_REQUEST['page'] == 'wp-leads-builder-any-crm') && $active_plugin != 'wpsuitepro') {
      $show_all_addon = 'show';
    ?>
       <div class="notice error is-dismissible" >
          <p style="font-size: 15px"> Kindly install/activate <?php echo $add_on; ?> addon to continue using Wordpress <?php echo $plugin; ?> Integration. This wont affect your any old data or configuration. </p>
          <?php if($show == 'show') { ?>
          <p style="color: red">Installation failed. Sorry! Could not create directory. kindly install  <?php echo $add_on; ?> Manually </p>
          <?php } ?>
           <p>
          <form method="post" action="">
          <input type="text" name="crm_to_enable" value="<?php echo $id_addon; ?>" hidden>
          <input type="submit"  class="smack-btn smack-btn-primary" value="Install Now"> 
          </form>
          </p>
      </div> 
     <?php  
     }
$active_plugin = 'wpsuitepro';
//print_r($pre_plugin);

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
if($active_plugin == 'wpsugarpro' ){
  $LB_setting_name = "Sugar CRM Settings";
}else{
  $LB_setting_name = "Suite CRM Settings";
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
            <label id="inneroptions" class="leads-builder-crm"><?php echo esc_html__('Select the CRM you use ', "wp-leads-builder-any-crm" ); ?></label>
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
  <?php if( $active_plugin == 'wpsugarpro'){?>
  <img src="<?php echo SM_LB_DIR?>assets/images/sugarcrm-logo.png" width=168 height=42>
  <?php }else { ?>
  <img src="<?php echo SM_LB_DIR?>assets/images/suite-logo.png" width=168 height=25>
  <?php } ?>
    <input type="hidden" id="get_config" value="<?php echo $config_data ?>" >
    <input type="hidden" id="revert_old_crm_pro" value="wpsugarpro">
    <span id="save_config" style="font:14px;width:200px;">
    </span>
<form id="smack-sugar-settings-form" action="<?php echo esc_url($_SERVER['REQUEST_URI']); ?>" method="post">
    <input type="hidden" name="smack-sugar-settings-form" value="smack-sugar-settings-form" />
  <input type="hidden" id="plug_URL" value="<?php echo esc_attr(SM_LB_URL);?>" />
  <!-- <div class="wp-common-crm-content" style="width: 900px;float: left;"> -->
<!-- <div class="form-group">
   <div class="col-md-3 col-md-offset-3">
       <label id="inneroptions" class="leads-builder-heading"><?php echo esc_html__('Select the CRM you use ', "wp-leads-builder-any-crm" ); ?></label>
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
       <label id="inneroptions" class="leads-builder-heading"><?php echo $LB_setting_name; ?>
       </label>
    </div>
</div>
<div class="clearfix"></div>
<div class="mt20">    
<div class="form-group col-md-12">
   <div class="col-md-2 label-space">
       <label id="innertext" class="leads-builder-label"> <?php echo esc_html__('CRM Url' , "wp-leads-builder-any-crm" ); ?> </label>
    </div>
    <div class="col-md-8">   
        <input type='text' class='smack-vtiger-settings form-control' name='url' id='smack_sugar_host_address'  value="<?php echo isset($config['url']) ? esc_url($config['url']) : '' ?>"/>
    </div>    
</div>
<div class="form-group col-md-12">
   <div class="col-md-2 label-space">
       <label id="innertext" class="leads-builder-label"> <?php echo esc_html__('Username' , "wp-leads-builder-any-crm" ); ?> </label>
    </div>
    <div class="col-md-3"> 
        <input type='text' class='smack-vtiger-settings form-control' name='username' id='smack_host_username' value="<?php echo isset($config['username']) ? sanitize_text_field($config['username']) : '' ?>"/>
    </div>
    <div class="col-md-2 label-space">
       <label id="innertext" class="leads-builder-label"> <?php echo esc_html__('Password' , "wp-leads-builder-any-crm" ); ?> </label>
    </div>   
    <div class="col-md-3">
        <input type='password' class='smack-vtiger-settings form-control' name='password' id='smack_host_access_key' value="<?php echo isset($config['password']) ? sanitize_text_field($config['password']) : '' ?>"/>
    </div>    
</div>
</div>   
    <input type="hidden" name="posted" value="<?php echo 'posted';?>">
  <input type="hidden" id="site_url" name="site_url" value="<?php echo esc_attr($siteurl) ;?>">
    <input type="hidden" id="active_plugin" name="active_plugin" value="<?php echo esc_attr($active_plugin); ?>">
    <input type="hidden" id="leads_fields_tmp" name="leads_fields_tmp" value="smack_wpsugarpro_leads_fields-tmp">
    <input type="hidden" id="contact_fields_tmp" name="contact_fields_tmp" value="smack_wpsugarpro_contacts_fields-tmp">
<div class="col-md-offset-9">
  <span id="SaveCRMConfig">
            <input type="button" value="<?php echo esc_attr__('Save CRM Configuration' , "wp-leads-builder-any-crm" );?>" id="save" class="smack-btn smack-btn-primary btn-radius" onclick="saveCRMConfiguration(this.id);" />
        </span>
</div>
<!-- </div> wp-common-crm-content -->
</form>
<div id="loading-sync" style="display: none; background:url(<?php echo esc_url(WP_PLUGIN_URL);?>/wp-leads-builder-any-crm/assets/images/ajax-loaders.gif) no-repeat center"><?php echo esc_html__('' , 'wp-leads-builder-any-crm' ); ?></div>
<div id="loading-image" style="display: none; background:url(<?php echo esc_url(WP_PLUGIN_URL);?>/wp-leads-builder-any-crm/assets/images/ajax-loaders.gif) no-repeat center"><?php echo esc_html__('' , "wp-leads-builder-any-crm" );?></div>
    </div>
  </div>
</div>    

