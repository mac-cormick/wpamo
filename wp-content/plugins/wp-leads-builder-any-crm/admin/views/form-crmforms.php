<?php
if ( ! defined( 'ABSPATH' ) )
        exit; // Exit if accessed directly
?>

<div class="mt30">
<div class="panel" style="width:98%;">
<div class="panel-body">
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
$page = $_REQUEST['page'];
//print_r($_REQUEST);
$result = $OverallFunctionsPROObj->CheckFetchedDetails();
if( !$result['status'] )
{
	$display_content = "<br>". $result['content']." to create Forms <br><br>";
	echo "<div style='font-weight:bold;  color:red; font-size:16px;text-align:center'> $display_content </div>";
}
else
{
	global $crmdetailsPRO;
	global $attrname;
	global $migrationmap;
	global $wpdb;
	global $lb_crmm;
	require_once( SM_LB_PRO_DIR."includes/class_lb_manage_shortcodes.php" );
	$HelperObj = new WPCapture_includes_helper_PRO();
	$module = $HelperObj->Module;
	$moduleslug = $HelperObj->ModuleSlug;
	$activatedplugin = $HelperObj->ActivatedPlugin;
	$activatedpluginlabel = $HelperObj->ActivatedPluginLabel;
	//print_r($activatedplugin);
	$active_plugins_hook = get_option( "active_plugins" );
	$redirect_to = "no";
	switch ($activatedplugin) {
		case 'wpzohopluspro':
		case 'wpzohopro':
			if( !in_array( "wp-zoho-crm/index.php" , $active_plugins_hook) )       {
				$redirect_to = 'yes';
			}
		break;
		case 'freshsales':
			if( !in_array( "wp-freshsales/index.php" , $active_plugins_hook) )       {
				$redirect_to = 'yes';
			}
		break;
		case 'wpsalesforcepro':
			if( !in_array( "wp-salesforce/index.php" , $active_plugins_hook) )       {
				$redirect_to = 'yes';
			}
		break;
		case 'wptigerpro':
			if( !in_array( "wp-tiger/index.php" , $active_plugins_hook) )       {
				$redirect_to = 'yes';
			}
		break;
		case 'wpsugarpro':
			if( !in_array( "wp-sugar-free/index.php" , $active_plugins_hook) )       {
				$redirect_to = 'yes';
			}
		break;
		
		default:
			# code...
			break;
	}
	if($redirect_to == 'yes'){
		echo "<div style='  font-size:16px;text-align:center'> Please <a href='admin.php?page=wp-leads-builder-any-crm'>configure </a> your CRM </div>";
		die();
	 //wp_safe_redirect('admin.php?page=wp-leads-builder-any-crm'); exit();
	}
	$lb_crmm->setActivatedPluginLabel($activatedpluginlabel);
	$plugin_url= SM_LB_PRO_DIR;
	$lb_crmm->setPluginsUrl($plugin_url);
	$onAction= 'onCreate';
	$siteurl= site_url();
	$crm_users = get_option("crm_users");
	$users_detail = array();
	foreach( $crm_users[$activatedplugin]['id'] as $key => $value )
	{
		$users_detail[$value] = array( 'user_name' => $crm_users[$activatedplugin]['user_name'][$key] , 'first_name' => $crm_users[$activatedplugin]['first_name'][$key] , 'last_name' => $crm_users[$activatedplugin]['last_name'][$key]  );
	}


		$content1 = "";
		$content1 .= "<div class='leads-builder-heading col-md-12 mb20'>".__('Forms and Shortcodes' , "wp-leads-builder-any-crm" )." ( {$crmdetailsPRO[$activatedplugin]['Label']} )  </div>
			<div class='wp-common-crm-content'>
			<table style='margin-right:20px;margin-bottom:20px;border: 1px solid #dddddd;'>
				<tr style='border-top: 1px solid #dddddd;'>
				</tr>
				<tr class='smack-crm-pro-highlight smack-crm-pro-alt' style='border-top: 1px solid #dddddd;'>
					<th class='smack-crm-free-list-view-th' style='width: 300px;'>".__('Shortcode / Title' , 'wp-leads-builder-any-crm' )."</th>
					<th class='smack-crm-free-list-view-th' style='width: 200px;'>".__('Assignee' , 'wp-leads-builder-any-crm' )."</th>
					<th class='smack-crm-free-list-view-th' style='width: 200px;'>".__('Module' , 'wp-leads-builder-any-crm' )."</th>
					<th class='smack-crm-free-list-view-th' style='width: 200px;'>".__('Thirdparty' , 'wp-leads-builder-any-crm' )."</th>			

					<th class='smack-crm-free-list-view-th' style='width: 200px;'>".__('Actions' , 'wp-leads-builder-any-crm' )."</th>
				</tr>";
						
			$shortcodemanager = $wpdb->get_results("select *from wp_smackleadbulider_shortcode_manager where crm_type = '{$activatedplugin}'");
			$assign_helper = new mainCrmHelper();
			$assignto = $assign_helper->getUsersList();
			if($assignto==""){
				wp_redirect('admin.php?page=lb-crmconfig'); die();
			}
			foreach($shortcodemanager as $shortcode_fields)
			{
				$content1 .= "<tr>";
				$shortcode_name = "[" . $shortcode_fields->crm_type . "-web-form name='" . $shortcode_fields->shortcode_name . "']";

				if( $shortcode_fields->assigned_to == "Round Robin" )
				{
					$assigned_to = "Round Robin";
				}
				else
				{
					$assigned_to=$assignto['first_name'][0]." ".$assignto['last_name'][0];
				}
				$oldshortcodename = "";
				$oldshortcode_reveal_html = "";
				$oldshortcode_html = "";
				if( $shortcode_fields->old_shortcode_name != NULL )
				{
					$oldshortcodename = $shortcode_fields->old_shortcode_name;
					$oldshortcode_reveal_html = "<p><a style='cursor:pointer;' id='oldshortcodename_reveal{$shortcode_fields->shortcode_id}' onclick='jQuery(\"#oldshortcodename\"+{$shortcode_fields->shortcode_id}).show(); jQuery(\"#oldshortcodename_reveal\"+{$shortcode_fields->shortcode_id}).hide(); '> Click here to reveal old shortcode </a></p>";
					$oldshortcode_html = "<p style='display:none;' id='oldshortcodename{$shortcode_fields->shortcode_id}'> $oldshortcodename </p>";
				}

				$content1 .= "<td class='smack-crm-pro-highlight' style='border-top: 1px solid #dddddd; text-align: center'>" . $shortcode_name . "$oldshortcode_reveal_html $oldshortcode_html</td>";
				$content1 .= "<td class='smack-crm-pro-highlight' style='border-top: 1px solid #dddddd; text-align: center'>" . $assigned_to . "</td>";
				$content1 .= "<td class='smack-crm-pro-highlight' style='border-top: 1px solid #dddddd; text-align: center'>" . $shortcode_fields->module . "</td>";
				$content1 .= "<td class='smack-crm-pro-highlight' style='border-top: 1px solid #dddddd; text-align: center'> None </td>";
				$content1 .= "<td class='smack-crm-pro-highlight' style='border-top: 1px solid #dddddd; text-align:center;'>";
				$content1 .= "<a href='#' onclick='create_form(\"Editshortcode\" , \"$shortcode_fields->module\" , \"$shortcode_fields->shortcode_name\",\"$activatedplugin\")'> <i class='icon-pencil2'></i> </a>";
				$content1 .= "<i style='margin-left:2px;' class='icon-trash2'title='Upgrade to PRO' disabled></i>";
				$content1 .= "</td>";
				$content1 .= "</tr>";
			}
		
			//Codes for getting Thirdparty existing forms
			$existing_content = '';
			$save_gravity_form_id = array();
			$gravity_option_name = $activatedplugin."_wp_gravity";
                        $list_of_shortcodes = $wpdb->get_results( $wpdb->prepare( "select option_name from {$wpdb->prefix}options where option_name like %s" , "$gravity_option_name%" ) );
                        if( !empty( $list_of_shortcodes ))
                        {
                                foreach( $list_of_shortcodes as $list_key => $list_val )
                                {
                                        $shortcode_name = $list_val->option_name;
                                        $form_id = explode( $gravity_option_name , $shortcode_name );
                                        $save_gravity_form_id[] = $form_id[1];
					
                                }
                        }
			foreach( $save_gravity_form_id as $grav_val )
			{
				$get_config = get_option($gravity_option_name."".$grav_val);
				$exist_module = $get_config['third_module'];
				$exist_assignee = $get_config['thirdparty_assignedto_name'];
				$get_form_title = $wpdb->get_results( $wpdb->prepare( "select title from {$wpdb->prefix}rg_form where id=%d" , $grav_val ) );
				$gravity_form_title = $get_form_title[0]->title;
				$third_plugin = $get_config['third_plugin'];
				if(isset($get_config['tp_roundrobin'])) {
					$third_roundrobin = $get_config['tp_roundrobin'];
				} else {
					$third_roundrobin = "";
				}

				$existing_content .= "<tr>
				<td class='smack-crm-pro-highlight' style='border-top: 1px solid #dddddd; text-align: center'> $gravity_form_title</td>
				<td class='smack-crm-pro-highlight' style='border-top: 1px solid #dddddd; text-align: center'> $exist_assignee</td>				
				<td class='smack-crm-pro-highlight' style='border-top: 1px solid #dddddd; text-align: center'> $exist_module</td>
				<td class='smack-crm-pro-highlight' style='border-top: 1px solid #dddddd; text-align: center'> Gravity Form</td>
				<td class='smack-crm-pro-highlight' style='border-top: 1px solid #dddddd; text-align: center'>"; 
				$existing_content .= "<a href='#' onclick='return show_map_config(\"$exist_module\" , \"$gravity_form_title\" , \"$grav_val\" , \"$third_plugin\" , \"$third_roundrobin\")'> <i class='icon-pencil2'></i> </a>";
                                $existing_content .= "<i style='margin-left:2px;' class='icon-trash2'></i>";

				$existing_content .="</td></tr>";
			}

		//NINJA MAPPED FIELDS
			$save_ninja_form_id = array();
			$ninja_option_name = $activatedplugin."_wp_ninja";
                        $list_of_shortcodes = $wpdb->get_results( $wpdb->prepare( "select option_name from {$wpdb->prefix}options where option_name like %s" , "$ninja_option_name%" ) );
                        if( !empty( $list_of_shortcodes ))
                        {
                                foreach( $list_of_shortcodes as $list_key => $list_val )
                                {
                                        $shortcode_name = $list_val->option_name;
                                        $form_id = explode( $ninja_option_name , $shortcode_name );
                                        $save_ninja_form_id[] = $form_id[1];
					
                                }
                        }

			foreach( $save_ninja_form_id as $ninja_val )
			{
				$get_config = get_option($ninja_option_name."".$ninja_val);
				$exist_module = $get_config['third_module'];
				$exist_assignee = $get_config['thirdparty_assignedto_name'];
				$get_form_title = $wpdb->get_results( $wpdb->prepare( "select title from {$wpdb->prefix}nf3_forms where id=%d" , $ninja_val ) );
				$ninja_form_title = $get_form_title[0]->title;
				$third_plugin = $get_config['third_plugin'];
				if(isset($get_config['tp_roundrobin'])) {
                                        $third_roundrobin = $get_config['tp_roundrobin'];
                                } else {
                                        $third_roundrobin = "";
                                }

				$existing_content .= "<tr><td class='smack-crm-pro-highlight' style='border-top: 1px solid #dddddd; text-align: center'> $ninja_form_title</td>
				<td class='smack-crm-pro-highlight' style='border-top: 1px solid #dddddd; text-align: center'> $exist_assignee</td>				
				<td class='smack-crm-pro-highlight' style='border-top: 1px solid #dddddd; text-align: center'> $exist_module</td>
				<td class='smack-crm-pro-highlight' style='border-top: 1px solid #dddddd; text-align: center'> Ninja Forms</td>
				<td class='smack-crm-pro-highlight' style='border-top: 1px solid #dddddd; text-align: center'>"; 
				$existing_content .= "<a href='#' onclick='return show_map_config(\"$exist_module\" , \"$ninja_form_title\" , \"$ninja_val\" , \"$third_plugin\" , \"$third_roundrobin\")'> <i class='icon-pencil2'></i></a>";
                                $existing_content .= "<a href='#' onclick='return delete_map_config(\"$third_plugin\" , \"$ninja_val\");' style='margin-left:2px;'> <i class='icon-trash2'></i> </a>";

				$existing_content .="</td></tr>";
			}
		//CONTACT FORM MAPPING
		
			$save_contact_form_id = array();
			$contact_option_name = $activatedplugin."_wp_contact";
                        $list_of_shortcodes = $wpdb->get_results( $wpdb->prepare( "select option_name from {$wpdb->prefix}options where option_name like %s" , "$contact_option_name%" ) );
                        if( !empty( $list_of_shortcodes ))
                        {
                                foreach( $list_of_shortcodes as $list_key => $list_val )
                                {
                                        $shortcode_name = $list_val->option_name;
                                        $form_id = explode( $contact_option_name , $shortcode_name );
                                        $save_contact_form_id[] = $form_id[1];
					
                                }
                        }

			foreach( $save_contact_form_id as $contact_val )
			{
				$get_config = get_option($contact_option_name."".$contact_val);
				$exist_module = $get_config['third_module'];
				$exist_assignee = $get_config['thirdparty_assignedto_name'];
				$get_form_title = $wpdb->get_results( $wpdb->prepare( "select post_title from $wpdb->posts where post_type=%s and ID=%d" , 'wpcf7_contact_form' , $contact_val ) );

				$contact_form_title = $get_form_title[0]->post_title;
				$third_plugin = $get_config['third_plugin'];
				if(isset($get_config['tp_roundrobin'])) {
				$third_roundrobin = $get_config['tp_roundrobin'];
				} else {
				$third_roundrobin = "";
				}

				$existing_content .= "<tr>
				<td class='smack-crm-pro-highlight' style='border-top: 1px solid #dddddd; text-align: center'> $contact_form_title</td>
				<td class='smack-crm-pro-highlight' style='border-top: 1px solid #dddddd; text-align: center'> $exist_assignee</td>			
				<td class='smack-crm-pro-highlight' style='border-top: 1px solid #dddddd; text-align: center'> $exist_module</td>	
				<td class='smack-crm-pro-highlight' style='border-top: 1px solid #dddddd; text-align: center'> Contact Form7</td>
				<td class='smack-crm-pro-highlight' style='border-top: 1px solid #dddddd; text-align: center'>"; 
				$existing_content .= "<a href='#' onclick='return show_map_config(\"$exist_module\" , \"$contact_form_title\" , \"$contact_val\" , \"$third_plugin\" , \"$third_roundrobin\")'> <i class='icon-pencil2'></i> </a>";
                                $existing_content .= "<a href='#' onclick='return delete_map_config(\"$third_plugin\" , \"$contact_val\" );' style='margin-left:2px;'> <i class='icon-trash2'></i> </a>";

				$existing_content .="</td></tr>";
			}
	
		$content1 .= $existing_content;	
		$content1 .= "</table></div>";
	echo $content1;
?> 
<div class="col-md-12 text-center">
     <div class="col-md-4">
	     <input class="smack-btn smack-btn-primary btn-radius"  type="submit" value="<?php echo esc_attr__('Create Lead Form' , "wp-leads-builder-any-crm" ); ?>" disabled/>
	 </div>    
     <div class="col-md-4">
	     <input class="smack-btn smack-btn-primary btn-radius"  type="submit" value="<?php echo esc_attr__('Create Contact Form' , "wp-leads-builder-any-crm" ); ?>" disabled/>
	 </div>    
     <div class="col-md-4">
	     <input class="smack-btn smack-btn-primary btn-radius"  type="button" id="thirdparty_map" value="<?php echo esc_attr__('Use Existing Form' , "wp-leads-builder-any-crm" ); ?>" disabled />
	 </div> 
</div>
<div class="col-md-12 text-center">
     <div class="col-md-4">
		<a href ="https://www.smackcoders.com/wp-leads-builder-any-crm-pro.html" class="free-notice" style="float:left;margin-top:-1px;margin-left:36%;text-color:red" target="_blank"><h6 style="color:red"><?php echo esc_html__("Upgrade To Pro");?></h6>
		</a>
     </div>
     <div class="col-md-4">
                <a href ="https://www.smackcoders.com/wp-leads-builder-any-crm-pro.html" class="free-notice" style="float:left;margin-top:-1px;margin-left:36%;" target="_blank"><h6 style="color:red"><?php echo esc_html__("Upgrade To Pro");?></h6>
		</a>
     </div>
     <div class="col-md-4">
                <a href ="https://www.smackcoders.com/wp-leads-builder-any-crm-pro.html" class="free-notice" style="float:left;margin-top:-1px;margin-left:36%;" target="_blank"><h6 style="color:red"><?php echo esc_html__("Upgrade To Pro");?></h6>
		</a>
    </div>
</div>

<div id="loading-image" style="display: none; background:url(<?php echo esc_url(WP_PLUGIN_URL);?>/wp-leads-builder-any-crm/assets/images/ajax-loaders.gif) no-repeat center"><?php echo esc_html__('' , "wp-leads-builder-any-crm"  ); ?> </div>

<?php
}
?>
   </div>
 </div>
</div>   
