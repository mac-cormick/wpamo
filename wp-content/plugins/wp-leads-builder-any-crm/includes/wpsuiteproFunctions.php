<?php

/******************************************************************************************
 * Copyright (C) Smackcoders 2016 - All Rights Reserved
 * Unauthorized copying of this file, via any medium is strictly prohibited
 * Proprietary and confidential
 * You can contact Smackcoders at email address info@smackcoders.com.
 *******************************************************************************************/
if ( ! defined( 'ABSPATH' ) )
        exit; // Exit if accessed directly

if(!defined('sugarEntry') || !sugarEntry)
{
        define('sugarEntry', TRUE);
	include_once(SM_LB_PRO_DIR.'lib/nusoap/nusoap.php');
}
class mainCrmHelper{
	public $username;
	public $accesskey;
	public $url;
	public $result_emails;
	public $result_ids;
	public function __construct()
	{
		$WPCapture_includes_helper_Obj = new WPCapture_includes_helper_PRO();
		$activateplugin = $WPCapture_includes_helper_Obj->ActivatedPlugin;
		if(isset($_REQUEST['crmtype']))
		{
			$crmtype = sanitize_text_field( $_REQUEST['crmtype'] );
			$SettingsConfig = get_option("wp_{$crmtype}_settings");
		}
		else
		{
			$SettingsConfig = get_option("wp_{$activateplugin}_settings");
		}
		$this->username = isset($SettingsConfig['username']) ? $SettingsConfig['username'] : '';
		$this->accesskey = isset($SettingsConfig['password']) ? $SettingsConfig['password'] : '';
		$this->url = isset($SettingsConfig['url']) ? $SettingsConfig['url'] : '';
	}

	public function login()
	{
		$client = new nusoapclient($this->url.'/soap.php?wsdl',true);
		$user_auth = array(
				'user_auth' => array(
				'user_name' => $this->username,
				'password' => md5($this->accesskey),
				'version' => '0.1'
			),
			'application_name' => 'wp-sugar-pro'
		);
		$login = $client->call('login',$user_auth);
		$session_id = $login['id'];
		$client_array = array( 'login' => $login , 'session_id' => $session_id , "clientObj" => $client );
		return $client_array;
	}

	public function testlogin( $url , $username , $password )
	{		
		$this->url = $url;
		$this->username = $username;
		$this->accesskey = $password;
		$login = $this->login();
		return $login;
	}

	public function getCrmFields( $module )
	{
                $client_array = $this->login();
		$client = $client_array['clientObj'];
		$recordInfo = $client->call('get_module_fields', array('session' => $client_array['session_id'], 'module_name' => $module));
		$config_fields = array();
                if(isset($recordInfo))
                {
                        $j=0;
                        $module = $recordInfo['module_name'];
                        $AcceptedFields = Array( 'text' => 'text' , 'bool' => 'boolean', 'enum' => 'picklist' , 'varchar' => 'string' , 'url' => 'url' , 'phone' => 'phone' , 'multienum' => 'multipicklist' , 'radioenum' => 'radioenum', 'currency' => 'currency' ,'date' => 'date' , 'datetime' => 'date' , 'int' => 'text' , 'decimal' => 'text' , 'currency_id' => 'text' );
                        for($i=0;$i<count($recordInfo['module_fields']);$i++)
                        {
                                if(array_key_exists($recordInfo['module_fields'][$i]['type'], $AcceptedFields)){
                                        if(($recordInfo['module_fields'][$i]['type'] == 'enum') || ($recordInfo['module_fields'][$i]['type'] == 'multienum') || ($recordInfo['module_fields'][$i]['type'] == 'radioenum')){
                                                $optionindex = 0;
                                                $picklistValues = array();
                                                foreach($recordInfo['module_fields'][$i]['options'] as $option)
                                                {
                                                        $picklistValues[$optionindex]['label'] = $option['name'] ;
                                                        $picklistValues[$optionindex]['value'] = $option['value'];
                                                        $optionindex++;
                                                }
                                                $recordInfo['module_fields'][$i]['type'] = Array ( 'name' => $AcceptedFields[$recordInfo['module_fields'][$i]['type']] , 'picklistValues' => $picklistValues );
                                        }
                                        else
                                        {
                                                $recordInfo['module_fields'][$i]['type'] = Array( 'name' => $AcceptedFields[$recordInfo['module_fields'][$i]['type']]);
                                        }
                                        $config_fields['fields'][$j] = $recordInfo['module_fields'][$i];
                                        $config_fields['fields'][$j]['order'] = $j;
                                        $config_fields['fields'][$j]['publish'] = 1;
                                        $config_fields['fields'][$j]['display_label'] = trim($recordInfo['module_fields'][$i]['label'], ':');
                                        if($recordInfo['module_fields'][$i]['required'] == 1)
                                        {

                                                $config_fields['fields'][$j]['wp_mandatory'] = 1;
                                                $config_fields['fields'][$j]['mandatory'] = 2;
                                        }
                                        else
                                        {
                                                $config_fields['fields'][$j]['wp_mandatory'] = 0;
                                        }
                                        $j++;
                                }
                        }
                        $config_fields['check_duplicate'] = 0;
                        $config_fields['isWidget'] = 0;
                        $users_list = $this->getUsersList();
                        $config_fields['assignedto'] = $users_list['id'][0];
                        $config_fields['module'] = $module;
		}
		return $config_fields;
	}

	public function getUsersList()
	{
		$user_details = array();
		$client_array = $this->login();
		$client = $client_array['clientObj'];
		$recordInfo = $client->call('user_list', array('user_name' => $this->username, 'password' => md5($this->accesskey)));
		$userindex = 0;
		if(is_array($recordInfo))
		foreach($recordInfo as $record)
		{
			$user_details['user_name'][$userindex] = $record['user_name'];
			$user_details['id'][$userindex] = $record['id'];
			$user_details['first_name'][$userindex] = $record['first_name'];
			$user_details['last_name'][$userindex] = $record['last_name'];
			$userindex++;
		}
                return $user_details;
	}
	
	public function getUsersListHtml( $shortcode = "" )
	{
		$HelperObj = new WPCapture_includes_helper_PRO();
		$module = $HelperObj->Module;
		$moduleslug = $HelperObj->ModuleSlug;
		$activatedplugin = $HelperObj->ActivatedPlugin;
		$activatedpluginlabel = $HelperObj->ActivatedPluginLabel;
		$formObj = new CaptureData();
                if(isset($shortcode) && ( $shortcode != "" ))
                {
                        $config_fields = $formObj->getFormSettings( $shortcode );  // Get form settings 
                }
		$users_list = get_option('crm_users');
		$users_list = $users_list[$activatedplugin];
		$html = "";
		$html = '<select class="selectpicker form-control" name="assignedto" id="assignedto">';
                $content_option = "";
                if(isset($users_list['user_name']))
                for($i = 0; $i < count($users_list['user_name']) ; $i++)
                {
			$content_option.="<option id='{$users_list['id'][$i]}' value='{$users_list['id'][$i]}'";

			if($users_list['id'][$i] == $config_fields->assigned_to)
			{
				$content_option.=" selected";

			}
			$content_option.=">{$users_list['first_name'][$i]} {$users_list['last_name'][$i]}</option>";
		}
		$content_option .= "<option id='owner_rr' value='Round Robin'";
                if( $config_fields->assigned_to == 'Round Robin' )
                {
                        $content_option .= "selected";
                }
                $content_option .= "> Round Robin </option>";

		$html .= $content_option;
		$html .= "</select> <span style='padding-left:15px; color:red;' id='assignedto_status'></span>";
		return $html;
	}

	public function getAssignedToList()
	{
		$users_list = $this->getUsersList();

		for($i = 0; $i < count($users_list['user_name']) ; $i++)
                {
			$user_list_array[$users_list['id'][$i]] = $users_list['first_name'][$i] ." ". $users_list['last_name'][$i];
		}

		return $user_list_array;
	}
	
        public function mapUserCaptureFields( $user_firstname , $user_lastname , $user_email )
        {
                $post = array();
                $post['first_name'] = $user_firstname;
                $post['last_name'] = $user_lastname;
                $post[$this->duplicateCheckEmailField()] = $user_email;
                return $post;
        }

	public function assignedToFieldId()
	{
		return "assigned_user_id";
	}

        public function createRecordOnUserCapture( $module , $module_fields )
        {
		return $this->createRecord( $module , $module_fields );

	}

	public function createRecord( $module , $module_fields )
	{

		$client_array = $this->login();
		$client = $client_array['clientObj'];
		$fieldvalues = array();
		foreach($module_fields as $key => $value)
		{
			$fieldvalues[] = array('name' => $key, 'value' => $value);
		}
		$set_entry_parameters = array(
			 //session id
			 "session" => $client_array['session_id'],
			 //The name of the module from which to retrieve records.
			 "module_name" =>  $module,
			 //Record attributes
			 "name_value_list" => $fieldvalues,
		);
		$response = $client->call('set_entry',  $set_entry_parameters , $this->url );
		if(isset($response['id']))
		{
			$data['result'] = "success";
			$data['failure'] = 0;
		}
		else
		{
			$data['result'] = "failure";
			$data['failure'] = 1;
			$data['reason'] = "failed adding entry";
		}
		return $data;
	}

	public function createEcomRecord($module, $module_fields , $order_id )
        {
		$client_array = $this->login();
		$client = $client_array['clientObj'];
		$fieldvalues = array();
		foreach($module_fields as $key => $value)
		{
			$fieldvalues[] = array('name' => $key, 'value' => $value);
		}
		$set_entry_parameters = array(
			 //session id
			 "session" => $client_array['session_id'],
			 //The name of the module from which to retrieve records.
			 "module_name" =>  $module,
			 //Record attributes
			 "name_value_list" => $fieldvalues,
		);

                if( $module == 'Leads' || $module == 'Contacts' )
                {
                        $response = $client->call('set_entry',  $set_entry_parameters , $this->url );
                }

                global $wpdb;
                if(isset( $response['id'] ))
                {
                        $data['result'] = "success";
                        $data['failure'] = 0;

                        if( $module == "Leads" )
                        {
                                $crm_id = $response['id'];
                                $my_leadid = $crm_id;
                                $crm_name = 'wpsugarpro';
                                if( is_user_logged_in() )
                                {
                                        $user_id = get_current_user_id();
                                        $is_user = 1;
                                }else
                                {
                                        $user_id = 'guest';
                                        $is_user = 0;
                                }
                                $lead_no = $crm_id;
                                $wpdb->insert( 'wp_smack_ecom_info' , array( 'crmid' => $crm_id , 'crm_name' => $crm_name , 'wp_user_id' => $user_id , 'is_user' => $is_user , 'lead_no' => $my_leadid , 'order_id' => $order_id ) );
                        }
                        if( $module == 'Contacts' )
                        {
                                $crm_id = $response['id'];
				$crm_name = 'wpsugarpro';
                                $my_contactid = $crm_id;
                                if( is_user_logged_in() )
                                {
                                        $user_id = get_current_user_id();
                                        $is_user = 1;
                                }else
                                {
                                        $user_id = '';
                                        $is_user = 0;
                                }
                                $contact_no = $crm_id;
                                $wpdb->insert( 'wp_smack_ecom_info' , array( 'crmid' => $crm_id , 'crm_name' => $crm_name , 'wp_user_id' => $user_id , 'is_user' => $is_user , 'contact_no' => $my_contactid , 'order_id' => $order_id ) );

                        }
                }
                else
                {
                        $data['result'] = "failure";
                        $data['failure'] = 1;
                        $data['reason'] = "failed adding entry";
                }
                return $data;
        }


	public function convertLead( $module , $lead_id , $order_id , $lead_no , $sales_order)
	{
		$client_array = $this->login();
                $client = $client_array['clientObj'];
                $fieldvalues = array();
		$fetch_lead_details = array(
                         "session" => $client_array['session_id'],
                         "module_name" =>  'Leads',
                         //Record attributes
                         "ids" => array( $lead_no ),
                );

		$get_lead_details = $client->call('get_entries', $fetch_lead_details);
		$lead_fields = $get_lead_details['entry_list'][0]['name_value_list'];


	$unset_array = array('assigned_user_name' , 'modified_by_name' , 'created_by_name' , 'id' , 'date_entered' , 'date_modified' );
	$i = 0;
	foreach( $lead_fields as $field_key => $field_value )
	{
		if( in_array( $field_value['name'] , $unset_array ))
		{
			
			unset( $lead_fields[$field_key]);
		}
	}

		$create_contact_parameters = array(
                         //session id
                         "session" => $client_array['session_id'],
                         //The name of the module from which to retrieve records.
                         "module_name" =>  'Contacts',
                         //Record attributes
                         "name_value_list" => $lead_fields,
                );

		$create_contact = $client->call( 'set_entry' , $create_contact_parameters , $this->url );
		$contact_id = $create_contact['id'];
		$fieldvalues = 	array(
					array('name' => 'id' , 'value' => $lead_no ),	
					array( 'name' => 'deleted' , 'value' => '1' )
				     );
                $delete_parameters = array(
                         //session id
                         "session" => $client_array['session_id'],
                         //The name of the module from which to retrieve records.
                         "module_name" =>  $module,
                         //Record attributes
                         "name_value_list" => $fieldvalues,
                );

                $delete_response = $client->call('set_entry',  $delete_parameters , $this->url );
		global $wpdb;
		$wpdb->update( 'wp_smack_ecom_info' , array('contact_no' => $contact_id) , array( 'order_id' => $order_id ) );	
	}
			
	public function updateRecord( $module , $module_fields , $ids_present )
	{
		$client_array = $this->login();
		$client = $client_array['clientObj'];
		$fieldvalues = array();
		$fieldvalues[] = array( 'name' => 'id', 'value' => $ids_present );
		foreach($module_fields as $key => $value)
		{
			$fieldvalues[] = array('name' => $key, 'value' => $value);
			if($key == 'email1')
			{
				$isemail_field_present = "yes";
			}
		}
		$set_entry_parameters = array(
			 //session id
			 "session" => $client_array['session_id'],
			 //The name of the module from which to retrieve records.
			 "module_name" =>  $module,
			 //Record attributes
			 "name_value_list" => $fieldvalues,
 		);
		$response = $client->call("set_entry", $set_entry_parameters, $this->url);
		if(isset($response['id']))
		{
			$data['result'] = "success";
			$data['failure'] = 0;
		}
		else
		{
			$data['result'] = "failure";
			$data['failure'] = 1;
			$data['reason'] = "failed updating entry";
		}
		return $data;
	} 

	public function checkEmailPresent( $module , $email )
	{
		$WPCapture_includes_helper_Obj = new WPCapture_includes_helper_PRO();
		$activateplugin = $WPCapture_includes_helper_Obj->ActivatedPlugin;
		$result_emails = array();
		$result_ids = array();
	        $email_present = "no";
		$module_table_name = strtolower($module);
		$client_array = $this->login();
		$client = $client_array['clientObj'];
                        $get_entries_count_parameters = array(
                             //Session id
                             'session' => $client_array['session_id'],
                             //The name of the module from which to retrieve records
                             'module_name' => $module,
                             //The SQL WHERE clause without the word "where".
                             'query' => "{$module_table_name}.id in (SELECT eabr.bean_id FROM email_addr_bean_rel eabr JOIN email_addresses ea ON (ea.id = eabr.email_address_id) WHERE eabr.deleted=0)",
                             //If deleted records should be included in results.
                //           'deleted' => false
                        );
                        $result = $client->call('get_entry_list', $get_entries_count_parameters);
                        $entry_list = $result['entry_list'];
                        foreach($entry_list as $entry)
                        {
                                foreach($entry['name_value_list'] as $field)
                                {
                                        if($field['name'] == 'last_name')
                                        {
                                                $result_lastnames[] = $field['value'];
                                        }
                                        if($field['name'] == 'email1')
                                        {
						if($email == $field['value'])
						{
							$email_present = 'yes';
						}
						$result_ids[] = $entry['id'];
                                                $result_emails[] = $field['value'];
                                                $result_emails1[] = $field['value'];
                                        }
                                        if($field['name'] == 'email2')
                                        {
						if($email == $field['value'])
						{
							$email_present = 'yes';
						}
						$result_ids[] = $entry['id'];
						$result_emails[] = $field['value'];
                                                $result_emails2[] = $field['value'];
                                        }
                                }
                        }
		$this->result_emails = $result_emails;
		$this->result_ids = $result_ids;
		if($email_present == 'yes')
		{
			return true;
		}
		else
		{
			return false;
		}
	}

        function duplicateCheckEmailField()
        {
                return "email1";
        }

}
