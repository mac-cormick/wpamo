<?php 
/******************************************************************************************
 * Copyright (C) Smackcoders 2016 - All Rights Reserved
 * Unauthorized copying of this file, via any medium is strictly prohibited
 * Proprietary and confidential
 * You can contact Smackcoders at email address info@smackcoders.com.
 *******************************************************************************************/
if ( ! defined( 'ABSPATH' ) )
        exit; // Exit if accessed directly

class CaptureData {

	function selectFieldManager( $crmtype = "" , $module = "" )
	{
		global $wpdb;
		$sql = "select *from wp_smackleadbulider_field_manager";
		$fields = $wpdb->get_results($wpdb->prepare( " $sql where crm_type =%s and module_type =%s" , $crmtype,$module ) );
		if( count( $fields ) > 0 ) {
			return true;
		} else {
			return false;
		}
	}
	
	//Delete fields
	function DeleteFields($crmtype , $module , $check_deleted_fields)
	{
		//Delete fields from field_manager table
		global $wpdb;
		$get_shortcodes = array();
		$get_shortcodes = $wpdb->get_results($wpdb->prepare("select * from wp_smackleadbulider_shortcode_manager where module =%s and crm_type =%s" , $module , $crmtype) );

		foreach($check_deleted_fields as $del_key => $del_value)
		{
			$get_field_id = $wpdb->get_results($wpdb->prepare("select field_id from wp_smackleadbulider_field_manager where field_name=%s and crm_type=%s and module_type=%s" , $del_value , $crmtype , $module));
			$crm_field_id = $get_field_id[0]->field_id;
			$wpdb->delete('wp_smackleadbulider_field_manager', array('field_name' => $del_value , 'crm_type'=> $crmtype , 'module_type' => $module ) , array('%s' , '%s' , '%s'));

			//Delete fields from fields manager table
			foreach( $get_shortcodes as $key => $shortcodedata )
			{
				$fields = array();
				//$shortcodename = $shortcodedata->shortcode_name;
				$shortcode_id = $shortcodedata->shortcode_id;
				$wpdb->delete('wp_smackleadbulider_form_field_manager', array('field_id' => $crm_field_id , 'shortcode_id' => $shortcode_id ) , array('%s' , '%d'));

			}
		}
	}

	function fieldManager($data , $module )
	{
		global $wpdb;
		$field_name = $data['name'];
		$field_label = $data['label'];
		$field_type = $data['type'];
		$module_type = $data['module'];
		$field_mandatory = $data['mandatory'];
		$crm_type = $data['crmtype'];
		$base_model = $data['base_model'];
		$field_sequence = $data['sequence'];
		$field_values = $data['field_values'];
		
		$fields = $wpdb->get_results( $wpdb->prepare( "select *from wp_smackleadbulider_field_manager where field_name=%s and module_type=%s and crm_type=%s and base_model=%s", $field_name, $module, $crm_type, $base_model ) );
		if(count($fields) == 0  )
		{
			$fields = $wpdb->insert( 'wp_smackleadbulider_field_manager' , array( 'field_name' => "$field_name", 'field_label' => "$field_label", 'field_type' => "$field_type", 'field_values' => "$field_values", 'module_type' => "$module_type", 'field_mandatory' => $field_mandatory, 'crm_type' => "$crm_type", 'field_sequence' => $field_sequence, 'base_model' => "$base_model") );
		}
		else {
			$fields = $wpdb->update( 'wp_smackleadbulider_field_manager' , array( 'field_label' => "$field_label", 'field_type' => "$field_type", 'field_values' => "$field_values", 'field_mandatory' => "$field_mandatory", 'field_sequence' => "$field_sequence", 'base_model' => "$base_model"), array( 'field_name' => "$field_name", 'module_type' => "$module_type", 'crm_type' => "$crm_type" ) );
		}
	}

	function updateFormSubmitStatuses( $submit_parameters , $shortcodename )
	{
		global $wpdb;
		$submit_parameters['failure_count'] = $submit_parameters['total'] - $submit_parameters['success'];

		$update_form_submits = $wpdb->get_results("update wp_smackleadbulider_shortcode_manager set submit_count = '{$submit_parameters['total']}' , success_count = '{$submit_parameters['success']}' , failure_count = '{$submit_parameters['failure_count']}' where shortcode_name = '$shortcodename'");
	}

	function updateShortcodeFields( $data , $module  ) {
		global $wpdb;
		$field_name      = $data['name'];
		$field_label     = $data['label'];
		$field_type      = $data['type'];
		$module_type     = $data['module'];
		$field_mandatory = $data['mandatory'];

		$publish = 0;
		if ( $field_mandatory == 1 ) {
			$publish = 1;
		}

		$crm_type = $data['crmtype'];
		$field_sequence = $data['sequence'];
		$field_values   = $data['field_values'];
		$get_shortcodes = array();
		$get_shortcodes = $wpdb->get_results( $wpdb->prepare( "select * from wp_smackleadbulider_shortcode_manager where module = %s and crm_type = %s", $module, $crm_type ) );
		if(empty($get_shortcodes)) {
			//require_once(SM_LB_PRO_DIR . "includes/".$crm_type."Functions.php");
			//$FunctionsObj = new mainCrmHelper();
			if(!class_exists('mainCrmHelper')){
        	if($crm_type = 'wpzohopro'){
	        		$ob = new ZohoCrmSmLBHelper();
	        		$FunctionsObj = $ob->setEventObj();
	        	}
	        	elseif($crm_type = 'freshsales'){
	        		$ob = new FsalesSmLBAdmin();
	        		$FunctionsObj = $ob->setEventObj();
	        	}	
	        	elseif($crm_type = 'wpsalesforcepro'){
	        		$ob = new SforceSmLBAdmin();
	        		$FunctionsObj = $ob->setEventObj();
	        	}				
	        	elseif($crm_type = 'wptigerpro'){
	        		$ob = new VtigerCrmSmLBHelper();
	        		$FunctionsObj = $ob->setEventObj();
	        	}
	        	elseif($crm_type = 'wpsugarpro'){
	        		$ob = new SugarFreeSmLBAdmin();
	        		$FunctionsObj = $ob->setEventObj();
	        	}
	        }
	        else{
	        	$FunctionsObj = new mainCrmHelper();
	        }
			$get_userslist = $FunctionsObj->getUsersList();
			$first_userid = $get_userslist['id'][0];
			$shortcode_list = array('post', 'widget');
			foreach($shortcode_list as $shortcodeName) {
				$wpdb->insert( 'wp_smackleadbulider_shortcode_manager', array(
					'shortcode_name'  => "$shortcodeName",
					'form_type'       => "$shortcodeName",
					'error_message'   => "",
					'success_message' => "",
					'is_redirection'  => "",
					'url_redirection' => "",
					'google_captcha'  => "",
					'module'          => "$module",
					'crm_type'        => "$crm_type",
					'Round_Robin'     => "$first_userid"
				) );
			}
		}
		$get_field_manager = $wpdb->get_results( $wpdb->prepare( "select * from wp_smackleadbulider_field_manager where module_type = %s and field_name = %s and crm_type = %s", $module, $field_name, $crm_type ) );
		if(!empty($get_shortcodes)) {
			foreach ( $get_shortcodes as $key => $shortcodedata ) {
				$fields        = array();
				$shortcodename = $shortcodedata->shortcode_name;
				$shortcode_id  = $shortcodedata->shortcode_id;

				$fields   = $wpdb->get_results( $wpdb->prepare("select ffm.* , sm.* from wp_smackleadbulider_form_field_manager as ffm inner join wp_smackleadbulider_field_manager as fm on fm.field_id = ffm.field_id inner join wp_smackleadbulider_shortcode_manager as sm on sm.shortcode_id = ffm.shortcode_id where fm.field_name = %s and fm.module_type = %s and shortcode_name = %s and sm.crm_type = %s", $field_name, $module, $shortcodename, $crm_type));
				$rel_id   = isset( $fields[0] ) ? $fields[0]->rel_id : "";
				$field_id = isset( $get_field_manager[0] ) ? $get_field_manager[0]->field_id : "";

				if ( $crm_type == $shortcodedata->crm_type && $module_type == $module ) {
					if ( count( $fields ) == 0 ) {
						$query = $wpdb->query("insert into wp_smackleadbulider_form_field_manager( field_id, shortcode_id, display_label, custom_field_type, custom_field_values, wp_field_mandatory, form_field_sequence, state ) VALUES ('$field_id', '$shortcode_id', '$field_label', '$field_type', '$field_values', $field_mandatory, $field_sequence, $publish )");
					} else {
						$state = "";
						if ( $field_mandatory == 1 ) {
							$field_mandatory = 1;
							$state           = ", state = '1'";
						}

						$query = $wpdb->query("update wp_smackleadbulider_form_field_manager set wp_field_mandatory = '$field_mandatory' {$state} , custom_field_values = '$field_values' where rel_id = '{$rel_id}'");

						if ( $field_type == 'picklist' || $field_type == 'multipicklist' ) {
							$wpdb->update( 'wp_smackleadbulider_form_field_manager', array( 'custom_field_values' => $field_values ), array( 'rel_id' => $rel_id ) );
						}
					}
				}
			}
		}
	}

	function formShorcodeManager($shortcodedata , $mode = "create")
	{
		global $wpdb;
		$shortcode_name = $shortcodedata['name'];
		$form_type = $shortcodedata['type'];
		$assigned_to = $shortcodedata['assignto'];
		$error_message = $shortcodedata['errormesg'];
		$success_message = $shortcodedata['successmesg'];
		$is_redirection = $shortcodedata['isredirection'];
		$url_redirection = $shortcodedata['urlredirection'];
		$google_captcha = $shortcodedata['captcha'];
		$module = $shortcodedata['module'];
		$crm_type = $shortcodedata['crm_type'];
		$duplicate_handling = $shortcodedata['duplicate_handling'];
		if($crm_type == 'wpzohopluspro'){
                       $crm_type = 'wpzohopro';
                       $temp = 'wpzohopluspro';
               }

        if(!class_exists('mainCrmHelper')){
        	if($crm_type = 'wpzohopro'){
        		$ob = new ZohoCrmSmLBHelper();
        		$FunctionsObj = $ob->setEventObj();
        	}	
        	elseif($crm_type = 'freshsales'){
        		$ob = new FsalesSmLBAdmin();
        		$FunctionsObj = $ob->setEventObj();
        	}	
        	elseif($crm_type = 'wpsalesforcepro'){
	        	$ob = new SforceSmLBAdmin();
	        	$FunctionsObj = $ob->setEventObj();
	        }	
	        elseif($crm_type = 'wptigerpro'){
	        		$ob = new VtigerCrmSmLBHelper();
	        		$FunctionsObj = $ob->setEventObj();
	        }
	        elseif($crm_type = 'wpsugarpro'){
	        		$ob = new SugarFreeSmLBAdmin();
	        		$FunctionsObj = $ob->setEventObj();
	        }
         }
        else{
        	$FunctionsObj = new mainCrmHelper();
        }

		//require_once(SM_LB_PRO_DIR . "includes/".$crm_type."Functions.php");
		
		$get_userslist = $FunctionsObj->getUsersList();
		$first_userid = $get_userslist['id'][0];

		if($crm_type = 'wpzohopro' && !empty($temp)){
                       $crm_type = $temp;
               }

		if( $mode == "create" )
		{
			$shortcodemanager = $wpdb->insert( 'wp_smackleadbulider_shortcode_manager' , array( 'shortcode_name' => "$shortcode_name" , 'form_type' => "$form_type" , 'error_message' => "$error_message" , 'success_message' => "$success_message" , 'is_redirection' => "$is_redirection" , 'url_redirection' => "$url_redirection" , 'google_captcha' => "$google_captcha" , 'module' => "$module" , 'crm_type' => "$crm_type" , 'Round_Robin' => "$first_userid") );
		}
		else
		{
			$shortcodemanager = $wpdb->update( 'wp_smackleadbulider_shortcode_manager' , array( 'form_type' => "$form_type" , 'error_message' => "$error_message" , 'success_message' => "$success_message" , 'is_redirection' => $is_redirection , 'url_redirection' => $url_redirection, 'google_captcha' => $google_captcha , 'duplicate_handling' => "$duplicate_handling") , array( 'shortcode_name' => "$shortcode_name" ) );

		}
		$lastid = $wpdb->insert_id;
		return $lastid;	
	}

	function insertFormFieldManager( $shortcode_id, $field_id, $wp_field_mandatory, $state, $custom_field_type, $custom_field_values , $form_field_sequence, $display_label)
	{
		global $wpdb;

		$forms = $wpdb->insert( 'wp_smackleadbulider_form_field_manager', array( 'shortcode_id' => "$shortcode_id" , 'field_id' => "$field_id" , 'wp_field_mandatory' => "$wp_field_mandatory" , 'state' => "$state" , 'custom_field_type' => "$custom_field_type" , 'custom_field_values' => "$custom_field_values" , 'form_field_sequence' => "$form_field_sequence" , 'display_label' => "$display_label") );
	}

	function get_crmfields_by_settings($crmtype, $module)
	{
		global $wpdb;
		$fields = $wpdb->get_results($wpdb->prepare("select *from wp_smackleadbulider_field_manager where crm_type = %s and module_type = %s " , $crmtype , $module ) );
		return $fields;
	}

	function formfields_settings($shortcode_name)
	{
		global $wpdb;
		$active_crm = get_option("WpLeadBuilderProActivatedPlugin");
		$get_shortcode_id = $wpdb->get_results($wpdb->prepare("select shortcode_id from wp_smackleadbulider_shortcode_manager where shortcode_name = %s and crm_type = %s", $shortcode_name, $active_crm));
		$shortcode_id = $get_shortcode_id[0]->shortcode_id;
		$field = $wpdb->get_results("select * from wp_smackleadbulider_field_manager fm join wp_smackleadbulider_form_field_manager ffm ON ffm.field_id = fm.field_id join wp_smackleadbulider_shortcode_manager sm ON sm.shortcode_id = ffm.shortcode_id where sm.shortcode_id='{$shortcode_id}' group by fm.field_name order by ffm.form_field_sequence");
		$i = 0;
		$crmFields = array();
		foreach($field as $newfields)
		{
			$crmFields['fields'][$i]['field_id'] = $newfields->field_id;
			$crmFields['fields'][$i]['name'] = $newfields->field_name;
			if( $newfields->field_mandatory == 1 )
				$crmFields['fields'][$i]['mandatory'] = 2;//$newfields->wp_field_mandatory;
			else
				$crmFields['fields'][$i]['mandatory'] = 0;

			$crmFields['fields'][$i]['wp_mandatory'] = $newfields->wp_field_mandatory;
			$crmFields['fields'][$i]['order'] = $newfields->form_field_sequence;
			$crmFields['fields'][$i]['publish'] = $newfields->state;
			$crmFields['fields'][$i]['display_label'] = $newfields->display_label;
			$crmFields['fields'][$i]['label'] = $newfields->field_label;
			$crmFields['fields'][$i]['type'] = array( 'picklistValues' => @unserialize($newfields->custom_field_values) , 'name' => $newfields->custom_field_type , 'defaultValue' => $newfields->custom_field_values );
			$i++;
		}
		return $crmFields;
	} 

	function getFormSettings( $shortcodename = "" )
	{
		global $wpdb;
		$query = "";
		$where = "";
		if( $shortcodename != "" )
		{
			$where = " where shortcode_name = '$shortcodename'";
		}
		$query = "select * from wp_smackleadbulider_shortcode_manager";
		$sql = $query.$where;
		$results = $wpdb->get_results($sql);
		if( ( $shortcodename != "" ) && ( count( $results ) > 0 ) )
		{
			$return_results = $results[0];
			return $return_results;
		}
		else
		{
			return $results;
		}
	}
}
