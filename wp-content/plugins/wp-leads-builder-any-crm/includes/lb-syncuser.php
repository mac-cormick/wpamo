<?php

/******************************************************************************************
 * Copyright (C) Smackcoders 2016 - All Rights Reserved
 * Unauthorized copying of this file, via any medium is strictly prohibited
 * Proprietary and confidential
 * You can contact Smackcoders at email address info@smackcoders.com.
 *******************************************************************************************/
if ( ! defined( 'ABSPATH' ) )
        exit; // Exit if accessed directly

class SyncUserActions {

    public function __construct()
    {
    }

    public function ModuleMapping( $user_fields,$config_fields,$option)
    {
	$HelperObj = new WPCapture_includes_helper_PRO();
       	$Activated_plugin = $HelperObj->ActivatedPlugin;
	$config = get_option("smack_{$Activated_plugin}_user_capture_settings");
	if(!empty($user_fields)) {
	$add_user_array = update_option("smack_{$Activated_plugin}_userfields_capture_settings",$user_fields);}
	$module = $config['user_sync_module'];
	$request = "";
       	$data = $this->UserModuleMapping( $user_fields , $config_fields, $module ,$option  );
       	return $data;
    }


    public function UserModuleMapping( $user_fields , $config_fields , $module , $option )
    {
	    // return an array of name value pairs to send data to the template
	    $data = array();
		   if(!empty($config_fields)) { 
		   foreach( $config_fields as $REQUESTS_KEY => $REQUESTS_VALUE )
		    {
			    $data['REQUEST'][$REQUESTS_KEY] = $REQUESTS_VALUE;
		    }}

	    $data['config_fields'] = $config_fields;
	    $data['HelperObj'] = new WPCapture_includes_helper_PRO();
	    $data['module'] = $module ;
	    $data['moduleslug'] = rtrim( strtolower($module) , "s");
	    $data['activatedplugin'] = $data['HelperObj']->ActivatedPlugin;
	    $data['activatedpluginlabel'] = $data['HelperObj']->ActivatedPluginLabel;
	    $data['plugin_dir']= SM_LB_PRO_DIR;
	    $data['plugins_url'] = SM_LB_DIR;
	    $data['siteurl'] = site_url();
	    if($option == "update") 
	    {
		    $this->saveUserMapping($data);
		    $data['display'] = "<p class='display_success'> Settings Saved Successfully</p>";
	    }
	    $activated_plugin = get_option( "WpLeadBuilderProActivatedPlugin" );
	    $data['UserModuleMapping'] = get_option("User{$data['activatedplugin']}{$data['module']}ModuleMapping");;
	    $CaptureDataObj = new CaptureData();
	    $leadFields = $CaptureDataObj->get_crmfields_by_settings( $data['activatedplugin'] , $data['module'] );
	    $data['fields'] = $leadFields;
	    return $data;
    }

    public function saveUserMapping( $data )
    {
	    $activated_plugin = get_option( "WpLeadBuilderProActivatedPlugin" );
	    $userfield = get_option("smack_{$activated_plugin}_userfields_capture_settings");
	    $module_field = get_option("smack_{$activated_plugin}_mappedfields_capture_settings");
	    $mapfields = array();
	    foreach($userfield as $key => $value)
	    {
		$mapfields[] = $key;
	    }
	    $combined_fields = array_combine($mapfields,$module_field);
	    update_option( "User{$activated_plugin}{$data['module']}ModuleMapping" , $combined_fields );
    }

    public function executeView($request)
    {
	    // return an array of name value pairs to send data to the template
	    $data = array();
	    foreach( $request as $key => $REQUESTS )
	    {
		    foreach( $REQUESTS as $REQUESTS_KEY => $REQUESTS_VALUE )
		    {
			    $data['REQUEST'][$REQUESTS_KEY] = $REQUESTS_VALUE;
		    }
	    }

	    $data['HelperObj'] = new WPCapture_includes_helper_PRO();
	    $data['module'] = $data['HelperObj']->Module;
	    $data['moduleslug'] = $data['HelperObj']->ModuleSlug;
	    $data['activatedplugin'] = $data['HelperObj']->ActivatedPlugin;
	    $data['activatedpluginlabel'] = $data['HelperObj']->ActivatedPluginLabel;
	    $data['plugin_dir']= SM_LB_PRO_DIR;
	    $data['plugins_url'] = SM_LB_DIR;
	    $data['siteurl'] = site_url();
	    if( isset($data['REQUEST']["smack-{$data['activatedplugin']}-user-capture-settings-form"]) )
	    {
		    $this->saveSettingArray($data);
	    }
	    return $data;
    }

	public function saveSettingArray($data)
	{
		$HelperObj = $data['HelperObj'];
		$module = $HelperObj->Module;
		$moduleslug = $HelperObj->ModuleSlug;
		$activatedplugin = $HelperObj->ActivatedPlugin;
		$activatedpluginlabel = $HelperObj->ActivatedPluginLabel;
		$fieldNames = array(
			'smack_user_capture' => __('Capture Registering User'),
			'smack_capture_duplicates' => __('Capture Duplicate users'),
			'user_sync_module' => __('User Sync Module'),
		);

		foreach ($fieldNames as $field => $value){
			if(isset($data['REQUEST'][$field]))
			{
				$config[$field] = $data["REQUEST"][$field];
			}
			else
			{
				$config[$field] = "";
			}
		}
		update_option("smack_{$activatedplugin}_user_capture_settings", $config);
	}
}

