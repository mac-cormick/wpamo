<?php
/******************************************************************************************
 * Copyright (C) Smackcoders 2016 - All Rights Reserved
 * Unauthorized copying of this file, via any medium is strictly prohibited
 * Proprietary and confidential
 * You can contact Smackcoders at email address info@smackcoders.com.
 *******************************************************************************************/
if ( ! defined( 'ABSPATH' ) )
        exit; // Exit if accessed directly

require_once( SM_LB_PRO_DIR."includes/Functions.php" );
$OverallFunctionsPROObj = new OverallFunctionsPRO();
$result = $OverallFunctionsPROObj->CheckFetchedDetails();

if( !$result['status'] )
{
	$display_content = "<br>".$result['content']. " to configure WP User Sync <br><br>" ; 
        echo "<div style='font-weight:bold;  color:red;font-size:16px;text-align:center'> $display_content </div>";
}
else
{
$active_plugin = get_option('WpLeadBuilderProActivatedPlugin');
if( isset($_POST["smack_{$active_plugin}_user_capture_settings"]) ) {
	$data = new SyncUserActions();
	$data->saveSettingArray($_POST , $data['HelperObj']);
}
if( isset($data['display']) )
{
	echo $data['display'];
}
$config = get_option("smack_{$active_plugin}_user_capture_settings");
?>
<div class='clearfix'></div>
<div class='mt40'>
    <div class='panel' style='width:99%;'>
       <div class='panel-body'>
<?php 
	$custom_plugin = get_option( "custom_plugin" );
?>
<input type="hidden" id="custom_plugin_value" value='<?php echo $custom_plugin ;?>'>
<form id="smack-<?php echo sanitize_text_field($active_plugin);?>-user-capture-settings-form" action="" method="post">
<input type="hidden" name="smack-<?php echo  esc_attr($active_plugin);?>-user-capture-settings-form" value="smack-<?php echo  $active_plugin;?>-user-capture-settings-form" />

<input type="hidden" name="activated_crm" id="activated_crm" value="<?php echo $active_plugin; ?>">

<div class='leads-builder-heading col-md-12 mt10'><?php echo esc_html__("Capture WordPress users" , "wp-leads-builder-any-crm" ); ?> </div>
<div class='clearfix'></div>
<div class='mt20'>
<div class="form-group col-md-12">
       <div class="col-md-3">
            <label id="innertext"  class='leads-builder-label'><?php echo esc_html__( 'Select Plugin-Custom Fields ' , 'wp-leads-builder-any-crm' ); ?></label>
        </div>
        <div class="col-md-2">
        <?php $ContactFormPluginsObj = new ContactFormPROPlugins();
             echo $ContactFormPluginsObj->getCustomFieldPlugins();?>
        </div>
	<div style="margin-left:45%;color:red;font-size:17px">Upgrade to PRO</div>
</div>

<div class="form-group col-md-12">
       <div class="col-md-3">
            <label style = "" id="innertext" class='leads-builder-label' ><?php echo esc_html__("Sync Wordpress User as" , SM_LB_URL ); ?> </label>
        </div>
        <div class="col-md-2">
        <select name="choose_module" id="choose_module" class='selectpicker form-control' data-live-search='false' onchange="wpSyncSettingsPRO( this )">
				<option value="Leads" disabled 
				<?php
					if( isset( $config['user_sync_module']  ) && $config['user_sync_module'] == "Leads" )
					{
						echo "selected=selected";			
					}
				?>
				> Leads </option>
				<option value="Contacts" 
				<?php
					if( isset( $config['user_sync_module']  ) && $config['user_sync_module'] == "Contacts" )
                                        {
                                                echo "selected=selected";
                                        }
				?>
				> Contacts </option>
			</select>
        </div>
	<div class="col-md-1">
        <?php
                        $module_name = rtrim( $config['user_sync_module'] , "s" ) ;
                        ?>

                        <input type="button" value="<?php echo esc_attr__('Configure Mapping' , 'wp-leads-builder-any-crm' );?>" class="smack-btn smack-btn-primary btn-radius" disabled />
        </div>
</div>


<div class="form-group col-md-12">
       <div class="col-md-3">
            <label id="innertext" class='leads-builder-label'><?php echo esc_html__("On Duplicate Data" , "wp-leads-builder-any-crm" ); ?></label>
        </div>
        <div class="col-md-2">
        <select name="duplicate_handling" id="duplicate_handling" class='selectpicker form-control col-md-2' onchange="wpSyncDuplicateSettingsPRO( this )">
                                <option value="skip" disabled
				<?php
				if( isset( $config['smack_capture_duplicates'] ) && $config['smack_capture_duplicates'] == "skip"  ) 
				{
					echo "selected=selected";
				}
				?>
				> Skip </option>
				<option value="skip_both" disabled
                                <?php
                                if( isset( $config['smack_capture_duplicates'] ) && $config['smack_capture_duplicates'] == "skip_both"  )
                                {
                                        echo "selected=selected";
                                }
                                ?>
                                > Skip if already a Contact or Lead</option>
                                <option value="update" disabled

				<?php
                                if( isset( $config['smack_capture_duplicates'] ) && $config['smack_capture_duplicates'] == "update"  ) 
                                {
                                        echo "selected=selected";
                                }
                                ?>
				> Update </option>
				<?php 
				$activated_crm = get_option( 'WpLeadBuilderProActivatedPlugin' );
		                if($activated_crm != 'freshsales' || ($activated_crm == 'freshsales' && $config['user_sync_module'] != "Contacts")) { ?>
				<option value="create"
				<?php
                                if( isset( $config['smack_capture_duplicates'] ) && $config['smack_capture_duplicates'] == "create"  ) 
                                {
                                        echo "selected=selected";
                                }
                                ?>
				> Create </option>
				<?php } ?>
                        </select>
        </div>
</div>

<div><!-- wp user auto sync div start --> 
<input type="hidden" name="posted" value="<?php echo 'posted';?>"> <br>
<div class="form-group col-md-12">
       <div class="col-md-3">
            <label  id="innertext" class='leads-builder-label'><?php echo esc_html__("WP User Auto Sync" , "wp-leads-builder-any-crm" ); ?> <?php echo str_repeat( '&nbsp' , 13 );  ?></label>
        </div>
        <div class="col-md-2">
            <?php
		$switch_sync_button = get_option( "Sync_value_on_off" );
		$start = 1;
		$offset = 10;
		$wp_users_count = count( get_users() );		
		$check_sync_value = get_option( "Sync_value_on_off" );

		//Check mapping exist or not
		$activated_crm = get_option( 'WpLeadBuilderProActivatedPlugin' );
		$config_user_capture = get_option("smack_{$activated_crm}_user_capture_settings");
		$usersync_module = $config_user_capture['user_sync_module'];
		$check_mapping = get_option( "User{$activated_crm}{$usersync_module}ModuleMapping" );

		if( empty( $check_mapping ) )
		{
			$mapping_value = 'no';
		}
		else
		{
			$mapping_value = 'yes';
		}
	?>
	<input type="hidden" id="check_mapping_value" value="<?php echo $mapping_value ;?>">
	<input type="hidden" id='wp_start' value="0">
	<input type="hidden" id="wp_offset" value="10">
	<input type="hidden" id="wp_synced_count" value="0">
	<input type="hidden" id="wp_users_count" value="<?php echo $wp_users_count; ?>">
	<input type="hidden" id="site_url" value="<?php echo site_url(); ;?>">

                <!-- tfa button -->
        <input id="enableAutoSync" type='checkbox' class="tgl tgl-skewed noicheck smack-vtiger-settings" name='enableAutoSync' <?php if( $check_sync_value == "On" ) { echo "checked"; }  ?> onclick="enableWPUserAutoSync(this.id)" />
        <label id="innertext" data-tg-off="OFF" data-tg-on="ON" for="enableAutoSync"  class="tgl-btn enableAutoSync" style="font-size: 16px;" >
        </label>
        <!-- tfa btn End -->
        </div>
        <div class='col-md-1'>
             <input type="button"  id="OneTimeSync" class="OTMS smack-btn smack-btn-primary btn-radius" <?php if( $switch_sync_button == "On" ) { echo "disabled"; } ?> value="<?php echo esc_attr__('One Time Manual Sync ', 'wp-leads-builder-any-crm' );?>" class="button-secondary submit-add-to-menu innersave" disabled />
               </td>
        </div>
</div>
</div> <!-- wp-user auto syn div close -->
<div><!-- leads owner div start --> 

<?php
//Assign Leads And Contacts to User

$crm_users_list = get_option( 'crm_users' );
$assignedtouser_config = get_option( "smack_{$activated_crm}_usersync_assignedto_settings" );
$assignedtouser_config_leads = isset($assignedtouser_config['usersync_assign_leads']) ? $assignedtouser_config['usersync_assign_leads'] : '';
if(isset($assignedtouser_config['usersync_assign_contacts'])) {
$assignedtouser_config_contacts = $assignedtouser_config['usersync_assign_contacts'];
} else {
$assignedtouser_config_contacts = ""; }
$Assigned_users_list = $crm_users_list[$activated_crm];
switch( $activated_crm )
{
	case 'wpzohopro':
	case 'wpzohopluspro':
		$html_leads = "";
		$html_leads = '<select class="selectpicker form-control" name="usersync_assignedto_leads" id="usersync_assignedto_leads">';
		$content_option_leads = "";
		$content_option_leads = "<option id='select' value='--Select--'>--Select--</option>";
		/*if(isset($Assigned_users_list['user_name']))
			for($i = 0; $i < count($Assigned_users_list['user_name']) ; $i++)
			{
				$content_option_leads.="<option id='{$Assigned_users_list['user_name'][$i]}' value='{$Assigned_users_list['id'][$i]}'";
				if($Assigned_users_list['id'][$i] == $assignedtouser_config_leads )
				{
					$content_option_leads .=" selected";
				}
				$content_option_leads .=">{$Assigned_users_list['user_name'][$i]}</option>";
			}*/
		$content_option_leads .= "<option id='rr_usersync_owner' value='Round Robin' disabled";
		if( $assignedtouser_config_leads == 'Round Robin' )
		{
			$content_option_leads .= "selected";
		}
		$content_option_leads .= "> Round Robin</option>";
		$html_leads .= $content_option_leads;
		$html_leads .= "</select> <span style='padding-left:15px; color:red;' id='assignedto_status'></span>";
		break;
	case 'wptigerpro':
		$html_leads = "";
		$html_leads = '<select class="selectpicker form-control" name="usersync_assignedto_leads" id="usersync_assignedto_leads">';
		$content_option_leads = "";
		$content_option_leads = "<option id='select' value='--Select--'>--Select--</option>";
		/*if(isset($Assigned_users_list['user_name']))
			for($i = 0; $i < count($Assigned_users_list['user_name']) ; $i++)
			{
				$content_option_leads .="<option id='{$Assigned_users_list['id'][$i]}' value='{$Assigned_users_list['id'][$i]}'";
				if($Assigned_users_list['id'][$i] == $assignedtouser_config_leads)
				{
					$content_option_leads .=" selected";
				}
				$content_option_leads .=">{$Assigned_users_list['first_name'][$i]} {$Assigned_users_list['last_name'][$i]}</option>";
			}*/
		$content_option_leads .= "<option id='rr_usersync_owner' value='Round Robin' disabled";
		if( $assignedtouser_config_leads == 'Round Robin' )
		{
			$content_option_leads .= "selected";
		}
		$content_option_leads .= "> Round Robin</option>";
		$html_leads .= $content_option_leads;
		$html_leads .= "</select> <span style='padding-left:15px; color:red;' id='assignedto_status'></span>";
		break;
	case 'wpsugarpro':
	case 'wpsuitepro':
		$html_leads = "";
		$html_leads = '<select class="selectpicker form-control" name="usersync_assignedto_leads" id="usersync_assignedto_leads">';
		$content_option_leads = "";
		$content_option_leads = "<option id='select' value='--Select--'>--Select--</option>";
		/*if(isset($Assigned_users_list['user_name']))
			for($i = 0; $i < count($Assigned_users_list['user_name']) ; $i++)
			{
				$content_option_leads .="<option id='{$Assigned_users_list['id'][$i]}' value='{$Assigned_users_list['id'][$i]}'";
				if($Assigned_users_list['id'][$i] == $assignedtouser_config_leads)
				{
					$content_option_leads .=" selected";
				}
				$content_option_leads .=">{$Assigned_users_list['first_name'][$i]} {$Assigned_users_list['last_name'][$i]}</option>";
			}*/
		$content_option_leads .= "<option id='rr_usersync_owner' value='Round Robin'";
		if( $assignedtouser_config_leads == 'Round Robin' )
		{
			$content_option_leads .= "selected";
		}
		$content_option_leads .= "> Round Robin</option>";
		$html_leads .= $content_option_leads;
		$html_leads .= "</select> <span style='padding-left:15px; color:red;' id='assignedto_status'></span>";
		break;
	case 'wpsalesforcepro':
		$html_leads = "";
		$html_leads = '<select class="selectpicker form-control" name="usersync_assignedto_leads" id="usersync_assignedto_leads" >';
		$content_option_leads = "";
		$content_option_leads = "<option id='select' value='--Select--'>--Select--</option>";
		/*if(isset($Assigned_users_list['user_name']))
			for($i = 0; $i < count($Assigned_users_list['user_name']) ; $i++)
			{
				$content_option_leads .="<option id='{$Assigned_users_list['user_name'][$i]}' value='{$Assigned_users_list['id'][$i]}'";
				if($Assigned_users_list['id'][$i]== $assignedtouser_config_leads)
				{
					$content_option_leads .=" selected";
				}
				$content_option_leads .=">{$Assigned_users_list['user_name'][$i]}</option>";
			}*/
		$content_option_leads .= "<option id='rr_usersync_owner' value='Round Robin'";
		if( $assignedtouser_config_leads == 'Round Robin' )
		{
			$content_option_leads .= "selected";
		}
		$content_option_leads .= "> Round Robin</option>";
		$html_leads .= $content_option_leads ;
		$html_leads .= "</select> <span style='padding-left:15px; color:red;' id='assignedto_status'></span>";
		break;
	case 'freshsales':
		$html_leads = "";
		$html_leads = '<select class="selectpicker form-control" name="usersync_assignedto_leads" id="usersync_assignedto_leads" >';
		$content_option_leads = "";
		$content_option_leads = "<option id='select' value='--Select--'>--Select--</option>";
		/*if(isset($Assigned_users_list['last_name']))
			for($i = 0; $i < count($Assigned_users_list['last_name']) ; $i++)
			{
				$content_option_leads .="<option id='{$Assigned_users_list['last_name'][$i]}' value='{$Assigned_users_list['id'][$i]}'";
				if($Assigned_users_list['id'][$i]== $assignedtouser_config_leads)
				{
					$content_option_leads .=" selected";
				}
				$content_option_leads .=">{$Assigned_users_list['last_name'][$i]}</option>";
			}*/
		$content_option_leads .= "<option id='rr_usersync_owner' value='Round Robin'";
		if( $assignedtouser_config_leads == 'Round Robin' )
		{
			$content_option_leads .= "selected";
		}
		$content_option_leads .= "> Round Robin</option>";
		$html_leads .= $content_option_leads ;
		$html_leads .= "</select> <span style='padding-left:15px; color:red;' id='assignedto_status'></span>";
		break;
}
?>


<div class="form-group col-md-12">
       <div class="col-md-3">
            <div id='Lead_owner' style="width:250px;">
                 <label id="innertext" class='leads-builder-label'><?php echo esc_html__("Lead Owner" , "wp-leads-builder-any-crm" ); ?> <?php echo str_repeat( '&nbsp' , 26);  ?></label>
             </div>
             <div id='Contact_owner' style="width:250px;">    
                 <label id="innertext" class='leads-builder-label' ><?php echo esc_html__("Contact Owner" , "wp-leads-builder-any-crm" ); ?> <?php echo str_repeat( '&nbsp' , 20 );  ?></label>
             </div>    
        </div>
        <div class="col-md-2">
            <?php echo $html_leads; ?> 
        </div>
</div>

</div><!-- leads owner div close -->

<div><!-- wp-user note div start -->
<div class='col-md-12 form-group mt30'>
<div class='col-md-3'><label  id="inneroptions" class='leads-builder-heading'><?php echo esc_html__("NOTE" , "wp-leads-builder-any-crm" ); ?> </label></div></div>

<div class="form-group col-md-12">
       <div class="col-md-3">
            <label id="innertext" class='leads-builder-label'><?php echo esc_html__("WP User Auto Sync" , "wp-leads-builder-any-crm" ); ?></label>
            <span style="padding-left:9px;">: </span> 
        </div>
        <div class="col-md-8">
            <label id="innertext" class='leads-builder-label'>Automatically sync WordPress users to CRM When Creating Or Updating Users</label>
        </div>
</div>

<div class="form-group col-md-12">
       <div class="col-md-3">
             <label  id="innertext" class='leads-builder-label'><?php echo esc_html__("One Time Manual Sync " , "wp-leads-builder-any-crm" ); ?> <span style="padding-left:9px;">: </span> </label>
        </div>
        <div class="col-md-8">
             <label id="innertext" class='leads-builder-label'> Sync existing Wordpress users as Leads or Contacts </label>
        </div>
</div>
</div><!-- wp-user note div close -->
</div><!--hole label div close -->
</form>
<div id="loading-image" style="display: none; background:url(<?php echo WP_PLUGIN_URL;?>/wp-leads-builder-any-crm/assets/images/ajax-loaders.gif) no-repeat center"><?php echo esc_html__('' , 'wp-leads-builder-any-crm' ); ?></div>
</div>
</div>
</div>
<?php
}

