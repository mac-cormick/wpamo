<?php

/******************************************************************************************
 * Copyright (C) Smackcoders 2016 - All Rights Reserved
 * Unauthorized copying of this file, via any medium is strictly prohibited
 * Proprietary and confidential
 * You can contact Smackcoders at email address info@smackcoders.com.
 *******************************************************************************************/
if ( ! defined( 'ABSPATH' ) )
        exit; // Exit if accessed directly

/*
Cases : 
1) CreateNewFieldShortcode		Will create new field shortcode
2) FetchCrmFields			Will Fetch crm fields from the the crm
3) FieldSwitch				Enable/Disable single field
4) DuplicateSwitch			Change Duplicate handling settings 
5) MoveFields				Change the order of the fields
6) MandatorySwitch			Make Mandatory or Remove Mandatory
7) SaveDisplayLabel			Save Display Label
8) SwitchMultipleFields			Enable/Disable multiple fields
9) SwitchWidget				Enable/Disable widget  form
10) SaveAssignedTo			Save Assignee of the form leads 
11) CaptureAllWpUsers			Capture All wp users
*/

class OverallFunctionsPRO {

	public function CheckFetchedDetails()
	{
		global $wpdb;
		$HelperObj = new WPCapture_includes_helper_PRO();
		$module = $HelperObj->Module;
		$moduleslug = $HelperObj->ModuleSlug;
		$activatedplugin = $HelperObj->ActivatedPlugin;
		$activatedpluginlabel = $HelperObj->ActivatedPluginLabel;
		$SettingsConfig = get_option("wp_{$activatedplugin}_settings");
		$shortcodeObj = new CaptureData();
		$leadsynced = $shortcodeObj->selectFieldManager( $activatedplugin , 'Leads' );
		$users = get_option('crm_users');
		$usersynced = false;
		if( is_array($users[$activatedplugin]) && count( $users[$activatedplugin] ) > 0 )
		{
			$usersynced = true;
		}
		$content = "";
		$flag = true;
		if( !$leadsynced || !$usersynced )
		{
			$content = __( "Please configure your CRM in the CRM Configuration" , "wp-leads-builder-any-crm-pro"  );
			$flag = false;
		}
		$return_array = array( 'content' => "$content" , 'status' => $flag );
		return $return_array;
	}

	public function CreateNewFieldShortcode( $crmtype , $module ){
		global $crmdetailsPRO;
		$module = $module;
		$moduleslug = rtrim( strtolower($module) , "s");
		$tmp_option = "smack_{$crmtype}_{$moduleslug}_fields-tmp";
		if(!function_exists("generateRandomStringActivate"))
		{
			function generateRandomString($length = 10) {
				$characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
				$randomString = '';
				for ($i = 0; $i < $length; $i++) {
					$randomString .= $characters[rand(0, strlen($characters) - 1)];
				}
				return $randomString;
			}
		}
		$list_of_shorcodes = Array();
		$shortcode_present_flag = "No";
		$config_fields = get_option($tmp_option);
		$options = "smack_fields_shortcodes";
		$config_contact_shortcodes = get_option($options);
		if(is_array($config_contact_shortcodes))
		{
			foreach($config_contact_shortcodes as $shortcode => $values)
			{
				$list_of_shorcodes[] = $shortcode;
			}
		}

		for($notpresent = "no" ; $notpresent == "no"; )
		{
			$random_string = generateRandomString(5);
			if(in_array($random_string, $list_of_shorcodes))
			{
				$shortcode_present_flag = 'Yes';
			}
			if($shortcode_present_flag != 'yes')
			{
				$notpresent = 'yes';
			}
		}
		$options = $tmp_option;
		return $random_string;
	}

	public static function doFieldAjaxAction()
	{
		$crmtype = isset($_REQUEST['crmtype']) ? sanitize_text_field($_REQUEST['crmtype']) : "";
		$module = isset($_REQUEST['module']) ? sanitize_text_field($_REQUEST['module']) : "";
		$module_options = $module;
		$options = sanitize_text_field($_REQUEST['option']);
		$onAction = sanitize_text_field($_REQUEST['onAction']);
		$siteurl = site_url();
		$HelperObj = new WPCapture_includes_helper_PRO();
		$moduleslug = $HelperObj->ModuleSlug;
		$activatedplugin = $HelperObj->ActivatedPlugin;
		$activatedpluginlabel = $HelperObj->ActivatedPluginLabel;
		$content = '';
		$FunctionsObj = new mainCrmHelper();
		$tmp_option = "smack_{$activatedplugin}_{$moduleslug}_fields-tmp";
		if($onAction == 'onEditShortCode');
		{
			$original_options = "smack_{$activatedplugin}_fields_shortcodes";
			$original_config_fields = get_option($original_options);
		}
		$SettingsConfig = get_option("wp_{$activatedplugin}_settings");
		if($onAction == 'onCreate')
		{
			$config_fields = get_option($options);
		}
		else
		{
			$config_fields = get_option($options);
		}
		$FieldCount = 0;
		if(isset($config_fields['fields']))
		{
			$FieldCount = count($config_fields['fields']);
		}

		if(isset($config_fields)){
			$error[0] = 'no fields';
		}
		switch($_REQUEST['doaction'])
		{
			case "GetAssignedToUser":
				$Functions = new mainCrmHelper();
				echo $Functions->getUsersListHtml();
				break;
			case "CheckformExits":
				include(SM_LB_PRO_DIR.'includes/class_lb_manage_shortcodes.php');
				$fields = new ManageShortcodesActions();
				$all_fields = $fields->ManageFields($_REQUEST['shortcode'], $_REQUEST['crmtype'], $_REQUEST['module'], $_REQUEST['bulkaction'], $_REQUEST['chkarray'], $_REQUEST['labelarray'], $_REQUEST['orderarray']);
				$moduleslug = rtrim( strtolower($module) , "s");
				$config_fields = get_option( "smack_{$crmtype}_{$moduleslug}_fields-tmp" );
				if( !isset($config_fields['fields'][0]) )
					die( "Not synced" );
				else
					die( "Synced" );
				break;
			case "GetTemporaryFields":
				$moduleslug = rtrim( strtolower($module) , "s");
				$config_fields = get_option( "smack_{$crmtype}_{$moduleslug}_fields-tmp" );
				if($options != 'getSelectedModuleFields')
				{
					include(SM_LB_PRO_DIR.'templates/crm-fields-form.php');
				}
				break;
			case "FetchCrmFields":
				$moduleslug = rtrim( strtolower($module) , "s");
				$config_fields = $FunctionsObj->getCrmFields( $module );
				$seq = 1;
				$field_details = $current_fields = $existing_fields = array();
				foreach($config_fields['fields'] as $fkey => $fval) {
					$field_details['name'] = $fval['name'];
					$field_details['label'] = $fval['label'];
					$field_details['type'] = isset($fval['type']['name']) ? $fval['type']['name'] : "";
					$field_details['field_values'] = null;
					if(! empty( $fval['type']['picklistValues'] ) ) {
						$field_details['field_values'] = serialize($fval['type']['picklistValues']);
					}
					$field_details['module'] = $module;
					if( isset($fval['mandatory']) && $fval['mandatory'] == 2 )
						$field_details['mandatory'] = 1;
					else
						$field_details['mandatory'] = 0;
					$field_details['crmtype'] = $crmtype;
					$field_details['sequence'] = $seq;
					$field_details['base_model'] = null;
					if(isset($fval['base_model']))
						$field_details['base_model'] = $fval['base_model'];
					$seq++;

					if($field_details['label']=='Date of Birth')
					{
						$field_details['type']='date';
					}
					$DataObj = new CaptureData();
					$DataObj->fieldManager( $field_details , $module );
					$DataObj->updateShortcodeFields( $field_details , $module );
					$current_fields[] = $field_details['name'];
				}
				if($options != 'getSelectedModuleFields')
				{
					include(SM_LB_PRO_DIR.'templates/display-log.php');
				}
				global $wpdb;
				$get_existing_fields = $wpdb->get_results( $wpdb->prepare("select field_name from wp_smackleadbulider_field_manager where module_type =%s and crm_type =%s", $module, $crmtype) );
				foreach($get_existing_fields as $ex_key => $ex_val){
					$existing_fields[] = $ex_val->field_name;
				}
				if(!empty($existing_fields))
				{
					$check_deleted_fields = array();
					$check_deleted_fields = array_diff($existing_fields , $current_fields);
					if(!empty($check_deleted_fields))
					{
						//Delete fields from table
						$DataObj = new CaptureData();
						$DataObj->DeleteFields( $crmtype , $module , $check_deleted_fields );
					}
				}
				//Update Current Fields
				$options = "smack_{$crmtype}_{$moduleslug}_fields-tmp";
				update_option($options, $config_fields);
				$options = "smack_fields_shortcodes";
				$edit_config_fields = get_option($options);
				$edit_config_fields[sanitize_text_field($_REQUEST['shortcode'])] = $config_fields;
				update_option($options, $edit_config_fields);
				break;
			case "FetchAssignedUsers":
				$HelperObj = new WPCapture_includes_helper_PRO();
				$module = $HelperObj->Module;
				$moduleslug = $HelperObj->ModuleSlug;
				$activatedplugin = $HelperObj->ActivatedPlugin;
				$activatedpluginlabel = $HelperObj->ActivatedPluginLabel;
				$FunctionsObj = new mainCrmHelper();
				$crmusers = get_option( 'crm_users' );
				$users = $FunctionsObj->getUsersList();
				$crmusers[$activatedplugin] = $users;
				update_option('crm_users', $crmusers);
				$content .='<h5>Assigned Users:</h5>';
				$firstname = '';
				foreach($users['first_name'] as $assignusers)
				{
					$firstname .= $assignusers."<br>";
				}
				echo $content;
				echo $firstname; die;
				break;
			default:
				break;
		}
	}

	public function update_formtitle( $shortcode , $tp_title , $tp_formtype ) 
	{
		global $wpdb;
		switch( $tp_formtype )	
		{
			case 'contactform':
			$get_checkid = $wpdb->get_results("select thirdpartyid from wp_smackformrelation where  shortcode='{$shortcode}' and thirdparty='contactform'");
			if(isset($get_checkid[0])) {
				$checkid = $get_checkid[0]->thirdpartyid;
			} else {
				$checkid = "";
			}
			if( !empty( $checkid ))
			{	
				$wpdb->update( $wpdb->posts , array('post_title' => $tp_title ) , array( 'ID' => $checkid ) );	
			}

			break;
		}
		return;
	}

	public function doNoFieldAjaxAction()
	{
		global $wpdb,$lb_crmm;
		$HelperObj = new WPCapture_includes_helper_PRO();
		$module = $HelperObj->Module;
		$moduleslug = $HelperObj->ModuleSlug;
		$activatedplugin = $HelperObj->ActivatedPlugin;
		$activatedpluginlabel = $HelperObj->ActivatedPluginLabel;
		$SettingsConfig = get_option("wp_{$activatedplugin}_settings");
		$shortcodeObj = new CaptureData();
		switch($_REQUEST['doaction'])
		{
			case "SaveFormSettings":
				$shortcode_name = sanitize_text_field($_REQUEST['shortcode']);
				$thirdparty_title = sanitize_text_field( $_REQUEST['thirdparty_title'] );
				$thirdparty_form_type = sanitize_text_field( $_REQUEST['thirdparty_form_type'] );
				if($thirdparty_form_type != 'none'){
					update_option( $shortcode_name , $thirdparty_title);
					update_option( 'Thirdparty_'.$shortcode_name , $thirdparty_form_type);
				}
				if( $thirdparty_title != "" )
				{
					$this->update_formtitle($shortcode_name , $thirdparty_title , $thirdparty_form_type );
				}
				$shortcodedata['module'] =  $module;
				$shortcodedata['crm_type'] =  $activatedplugin;
				$shortcodedata['name'] = $shortcode_name;
				$shortcodedata['type'] = sanitize_text_field($_REQUEST['formtype']);
				//$shortcodedata['assignto'] = sanitize_text_field($_REQUEST['assignedto']);
				$shortcodedata['errormesg'] = sanitize_text_field($_REQUEST['errormessage']);
				$shortcodedata['successmesg'] = sanitize_text_field($_REQUEST['successmessage']);
				$shortcodedata['duplicate_handling'] = sanitize_text_field($_REQUEST['duplicate_handling']);
				if( sanitize_text_field($_REQUEST['enableurlredirection']) == "true" )
				{
					$shortcodedata['isredirection'] = 1;
				}
				else
				{
					$shortcodedata['isredirection'] = 0;
				}
				$shortcodedata['urlredirection'] = sanitize_text_field($_REQUEST['redirecturl']);
				if( sanitize_text_field($_REQUEST['enablecaptcha']) == "true" )
				{
					$shortcodedata['captcha'] = 1;
				}
				else
				{
					$shortcodedata['captcha'] = 0;
				}
				$shortcodeObj->formShorcodeManager( $shortcodedata , "edit" );
				break;
			}
	}
}

class AjaxActionsClassPRO
{
	public static function adminAllActionsPRO()
	{
		$OverallFunctionObj = new OverallFunctionsPRO();
		if( isset($_REQUEST['operation']) && (sanitize_text_field($_REQUEST['operation']) == "NoFieldOperation") ) {
			$OverallFunctionObj->doNoFieldAjaxAction( );
		} else {
			$OverallFunctionObj->doFieldAjaxAction();
		}
		die;
	}
}

add_action('wp_ajax_adminAllActionsPRO', array( "AjaxActionsClassPRO" , 'adminAllActionsPRO' ));

class CapturingProcessClassPRO
{
	function CaptureFormFields( $globalvariables )
	{
		global $wpdb;
		$HelperObj = new WPCapture_includes_helper_PRO();
		$module = $HelperObj->Module;
		$moduleslug = $HelperObj->ModuleSlug;
		$activatedplugin = $HelperObj->ActivatedPlugin;
		$activatedpluginlabel = $HelperObj->ActivatedPluginLabel;
		$duplicate_inserted = $duplicate_cancelled = $duplicate_updated = 0;
		$module = $globalvariables['formattr']['module'];
		$post = $globalvariables['post'];
		$FunctionsObj = new mainCrmHelper();
		$emailfield = $FunctionsObj->duplicateCheckEmailField();
		$shortcode_name = $globalvariables['attrname'];
		$enable_round_robin = $wpdb->get_var( $wpdb->prepare( "select assigned_to from wp_smackleadbulider_shortcode_manager where shortcode_name =%s" , $shortcode_name ) );	
		if( $enable_round_robin == 'Round Robin' || $enable_round_robin == '')	
		{
			$assignedto_old = $wpdb->get_var( $wpdb->prepare( "select Round_Robin from wp_smackleadbulider_shortcode_manager where shortcode_name =%s and crm_type=%s" , $shortcode_name, $activatedplugin ) );
		}
	
		if(is_array($post))
		{
			foreach($post as $key => $value)
			{
				if(($key != 'moduleName') && ($key != 'submitcontactform') && ($key != 'submitcontactformwidget') && ($key != '') && ($key != 'submit'))
				{
					$module_fields[$key] = $value;
					if($key == $emailfield)
					{
						$email_field_present = "yes";
						$user_email = $value;
					}
				}
			}
		}
		if( $enable_round_robin != 'Round Robin' && $enable_round_robin != '' )
		{
			$module_fields[$FunctionsObj->assignedToFieldId()] = $globalvariables['assignedto'];
		}
		else
		{
			$module_fields[$FunctionsObj->assignedToFieldId()] = $assignedto_old;
		}
		unset($module_fields['formnumber']);
		unset($module_fields['IsUnreadByOwner']);
		// print_r($module_fields);
		// die();
		
		//Check both module and Skip
                $duplicate_option_check = $globalvariables['formattr']['duplicate_handling'];
                if($duplicate_option_check == 'skip_both' ){
                        $CheckEmailResult_Leads = $FunctionsObj->checkEmailPresent('Leads' , $post[$emailfield]);
                        $CheckEmailResult_Contacts = $FunctionsObj->checkEmailPresent('Contacts' , $post[$emailfield]);

                        if( $CheckEmailResult_Leads == 1 || $CheckEmailResult_Contacts == 1 )
                        {
                                $CheckEmailResult = 1;
                        }
                }else{
                	if(isset($post[$emailfield]))
                        $CheckEmailResult = $FunctionsObj->checkEmailPresent($module , $post[$emailfield]);
                    else
                    	$CheckEmailResult = "";
                }

		if(($CheckEmailResult == 1) && ($duplicate_option_check =='skip' || $duplicate_option_check == 'skip_both'))
		{
			$duplicate_cancelled++;
		}
		else
		{
			$result_id = $FunctionsObj->result_ids;
			$result_emails = $FunctionsObj->result_emails;
			if($globalvariables['formattr']['duplicate_handling'] == 'update')
			{
				foreach( $result_emails as $key => $email )
				{
					if( ($email == $user_email) && ( $user_email != "" ) )
					{
						$ids_present = $result_id[$key];
						$email_present = "yes";
					}
				}
				//      Update Code here
				if(isset($email_present) && ($email_present == "yes"))
				{
					$record = $FunctionsObj->updateRecord( $module , $module_fields , $ids_present );

					if($record['result'] == "success")
					{
						$duplicate_updated++;
						$data = "/$module entry is added./";
					}
				}
				else
				{
					$record = $FunctionsObj->createRecord( $module , $module_fields);
					if($record['result'] == "success")
					{
						$duplicate_inserted++;
						$data = "/$module entry is added./";
						if( $enable_round_robin == 'Round Robin' )
                                                {
							$new_assigned_val = self::getRoundRobinOwner( $assignedto_old );	
							$wpdb->update( 'wp_smackleadbulider_shortcode_manager' , array( 'Round_Robin' => $new_assigned_val ) , array( 'shortcode_name' => $shortcode_name ) );
                                                }
					}
				}
			}
			else
			{
				$record = $FunctionsObj->createRecord( $module , $module_fields);
				$data = "failure";
				if($record['result'] == "success")
				{
					$duplicate_inserted++;
					$data = "/$module entry is added./";
	
					if( $enable_round_robin == 'Round Robin' )
                                                {
							$new_assigned_val = self::getRoundRobinOwner( $assignedto_old );
							$wpdb->update( 'wp_smackleadbulider_shortcode_manager' , array( 'Round_Robin' => $new_assigned_val ) , array( 'shortcode_name' => $shortcode_name ) );
                                                }
				}
			}
		}
		return $data;
	}

	/*
	Capture wordpress user on registration or creating a user from Wordpress Users
	*/
	//Register new user
	public static function capture_registering_users($user_id)
	{
		$posted_custom_fields = $_POST;
		$HelperObj = new WPCapture_includes_helper_PRO();
		$module = "Contacts";
		$moduleslug = $HelperObj->ModuleSlug;
		$activatedplugin = $HelperObj->ActivatedPlugin;
		$activatedpluginlabel = $HelperObj->ActivatedPluginLabel;
		$config_user_capture = get_option("smack_{$activatedplugin}_user_capture_settings");

//Code For RR
		$wp_active_crm = get_option( 'WpLeadBuilderProActivatedPlugin' );
		if( empty( $assignedto_old))
		{
			$get_first_usersync_owner = new mainCrmHelper();
			$get_first_user = $get_first_usersync_owner->getUsersList();
			$assignedto_old = $get_first_user['id'][0];
			$wp_assigneduser_config['usersync_rr_value'] = $assignedto_old;
			update_option( "smack_{$wp_active_crm}_usersync_assignedto_settings" , $wp_assigneduser_config );       
		}
			$duplicate_cancelled = 0;
			$duplicate_inserted = 0;
			$duplicate_updated = 0;
			$successful = 0;
			$failed = 0;
			$FunctionsObj = new mainCrmHelper();
			//$post = CapturingProcessClassPRO::mapRegisterUser( $module , $user_id , $posted_custom_fields , $assignedto_old );
			$user_email = "";
			$CheckEmailResult = array();
			$duplicate_option_check = $config_user_capture['smack_capture_duplicates'];
			$user_data = get_userdata( $user_id );
                        $user_email = $user_data->data->user_email;
                        $user_lastname = get_user_meta( $user_id, 'last_name', 'true' );
                        $user_firstname = get_user_meta( $user_id, 'first_name', 'true' );
                        if(empty($user_lastname))
                        {
                                $user_lastname = $user_data->data->display_name;
                        }
			$post = array();
			switch( $wp_active_crm)	{
			case 'wptigerpro':
				$post['firstname'] = $user_firstname;
		                $post['lastname'] = $user_lastname;
			break;

			case 'wpsugarpro':
			case 'wpsuitepro':
				$post['first_name'] = $user_firstname;
		                $post['last_name'] = $user_lastname;
			break;

			case 'wpzohopro':
			case 'wpzohopluspro':
				$post['First_Name'] = $user_firstname;
                		$post['Last_Name'] = $user_lastname;
			break;

			case 'wpsalesforcepro':
				$post['FirstName'] = $user_firstname;
                		$post['LastName'] = $user_lastname;	
			break;

			case 'freshsales':
				$post['first_name'] = $user_firstname;
		                $post['last_name'] = $user_lastname;
			break;
			}
			$post[$FunctionsObj->duplicateCheckEmailField()] = $user_email;	
			$record = $FunctionsObj->createRecord( $module , $post);
	}
}
