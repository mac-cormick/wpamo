<?php

if ( ! defined( 'ABSPATH' ) )
        exit; // Exit if accessed directly

include_once(SM_LB_ZOHO_DIR.'lib/SmackZohoApi.php');
class mainCrmHelper{
	public $username;
	public $accesskey;
	public $authtoken;
	public $url;
	public $result_emails;
	public $result_ids;
	public $result_products;
	public function __construct()
	{
		$WPCapture_includes_helper_Obj = new WPCapture_includes_helper_PRO();
		$activateplugin = $WPCapture_includes_helper_Obj->ActivatedPlugin;
		$SettingsConfig = get_option("wp_{$activateplugin}_settings");
                if(isset($_REQUEST['crmtype']))
                {
                        $SettingsConfig = get_option("smackwp_{$_REQUEST['crmtype']}_settings");
                }
                else
                {
                        $SettingsConfig = get_option("wp_{$activateplugin}_settings");
                }
		$this->username = isset($SettingsConfig['username']) ? $SettingsConfig['username'] : '';
		$this->accesskey = isset($SettingsConfig['password']) ? $SettingsConfig['password'] : '';
		$this->url = "";
		$this->authtoken = isset($SettingsConfig['authtoken']) ? $SettingsConfig['authtoken'] : '';
	}

	public function login()
	{
		$client = new SmackZohoApi();
		return $client;
	}

	public function getAuthenticationKey( $username , $password )
	{
		$client = $this->login();
		$return_array = $client->getAuthenticationToken( $username , $password  );
		return $return_array;
	}

	public function getCrmFields( $module )
	{
                $client = $this->login();
		$WPCapture_includes_helper_Obj = new WPCapture_includes_helper_PRO();
                $activateplugin = $WPCapture_includes_helper_Obj->ActivatedPlugin;
		$SettingsConfig = get_option("wp_{$activateplugin}_settings");
		$this->authtoken = $SettingsConfig['authtoken'];
		$recordInfo = $client->APIMethod( $module , "getFields" , $this->authtoken );
		$config_fields = array();
		$AcceptedFields = Array( 'TextArea' => 'text' , 'Text' => 'string' , 'Email' => 'email' , 'Boolean' => 'boolean', 'Pick List' => 'picklist' , 'varchar' => 'string' , 'Website' => 'url' , 'Phone' => 'phone' , 'Multi Pick List' => 'multipicklist' , 'radioenum' => 'radioenum', 'Currency' => 'currency' , 'DateTime' => 'date' , 'datetime' => 'date' , 'Integer' => 'string' , 'BigInt' => 'string' , 'Double' => 'string');
		$j = 0;
		foreach($recordInfo['section'] as $section ){
			if(!empty($section['FL']))
			foreach($section['FL'] as $key => $fields )
			{
				if( ($key === '@attributes') )
				{
					if( $fields['req'] == 'true' )
					{
						$config_fields['fields'][$j]['wp_mandatory'] = 1;
						$config_fields['fields'][$j]['mandatory'] = 2;
					}
					else
					{
						$config_fields['fields'][$j]['wp_mandatory'] = 0;
					}
                                        if(($fields['type'] == 'Pick List') || ($fields['type'] == 'Multi Pick List') || ($fields['type'] == 'Radio')){
                                                $optionindex = 0;
                                                $picklistValues = array();
                                                foreach($fields['val'] as $option)
                                                {
                                                        $picklistValues[$optionindex]['label'] = $option ;
                                                        $picklistValues[$optionindex]['value'] = $option;
                                                        $optionindex++;
                                                }
                                                $config_fields['fields'][$j]['type'] = Array ( 'name' => $AcceptedFields[$fields['type']] , 'picklistValues' => $picklistValues );
                                        }
					else
					{
						$config_fields['fields'][$j]['type'] = array("name" => $AcceptedFields[$fields['type']]);
					}

					$config_fields['fields'][$j]['name'] = str_replace(" " , "_", $fields['dv']);
					$config_fields['fields'][$j]['fieldname'] = $fields['dv'];
					$config_fields['fields'][$j]['label'] = $fields['label'];
					$config_fields['fields'][$j]['display_label'] = $fields['label'];
					$config_fields['fields'][$j]['publish'] = 1;
					$config_fields['fields'][$j]['order'] = $j;
					$j++;
				}
				elseif( $fields['@attributes']['isreadonly'] == 'false' && ( $fields['@attributes']['type'] != 'Lookup' ) && ( $fields['@attributes']['type'] != 'OwnerLookup' ) && ( $fields['@attributes']['type'] != 'Lookup' ) )
				{
					if( $fields['@attributes']['req'] == 'true' )
					{
                                                $config_fields['fields'][$j]['mandatory'] = 2;
						$config_fields['fields'][$j]['wp_mandatory'] = 1;
					}
					else
					{
                                                $config_fields['fields'][$j]['wp_mandatory'] = 0;
					}

                                        if(($fields['@attributes']['type'] == 'Pick List') || ($fields['@attributes']['type'] == 'Multi Pick List') || ($fields['@attributes']['type'] == 'Radio')){
                                                $optionindex = 0;
                                                $picklistValues = array();
                                                foreach($fields['val'] as $option)
                                                {
                                                        $picklistValues[$optionindex]['label'] = $option;
                                                        $picklistValues[$optionindex]['value'] = $option;
                                                        $optionindex++;
                                                }
                                                $config_fields['fields'][$j]['type'] = Array ( 'name' => $AcceptedFields[$fields['@attributes']['type']] , 'picklistValues' => $picklistValues );
                                        }
                                        else
                                        {
                                                $config_fields['fields'][$j]['type'] = array( 'name' => $AcceptedFields[$fields['@attributes']['type']] );
                                        }
                                        $config_fields['fields'][$j]['name'] = str_replace(" " , "_", $fields['@attributes']['dv']);
                                        $config_fields['fields'][$j]['fieldname'] = $fields['@attributes']['dv'];
					$config_fields['fields'][$j]['label'] = $fields['@attributes']['label'];
                                        $config_fields['fields'][$j]['display_label'] = $fields['@attributes']['label'];
                                        $config_fields['fields'][$j]['publish'] = 1;
                                        $config_fields['fields'][$j]['order'] = $j;
					$j++;
				}
			}
		}
		$config_fields['check_duplicate'] = 0;
                $config_fields['isWidget'] = 0;
                $users_list = $this->getUsersList();
                $config_fields['assignedto'] = $users_list['id'][0];
                $config_fields['module'] = $module;
		return $config_fields;
	}

	public function getUsersList()
	{
                $client = $this->login();
		$extraparams = "&type=ActiveUsers";
		$records = $client->getRecords( "Users" , "getUsers" , $this->authtoken , "" , "" , $extraparams );
		// if(isset($records['error']))
		// {
		// 	echo "<div style='  font-size:16px;text-align:center'> Please <a href='admin.php?page=wp-leads-builder-any-crm'>configure </a> your CRM </div>";
		// 	die();
		// }
                if( isset( $records['user']['@attributes'] ) ) {
                                $user_details['user_name'][] = $records['user']['@attributes']['email'];
                                $user_details['id'][] = $records['user']['@attributes']['id'];
                                $user_details['first_name'][] = $records['user']['@attributes']['email'];
                                $user_details['last_name'][] = "";
                }
		else
		{
                        foreach($records['user'] as $record) {
                                $user_details['user_name'][] = $record['@attributes']['email'];
                                $user_details['id'][] = $record['@attributes']['id'];
                                $user_details['first_name'][] = $record['@attributes']['email']; //$record['@attributes']['first_name'];
                                $user_details['last_name'][] = ""; //$record['@attributes']['email'];
                        }
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
			$content_option.="<option id='{$users_list['user_name'][$i]}' value='{$users_list['id'][$i]}'";
			if($users_list['id'][$i] == $config_fields->assigned_to)
			{
				$content_option.=" selected";
			}
			$content_option.=">{$users_list['user_name'][$i]}</option>";
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
                        $user_list_array[$users_list['user_name'][$i]] = $users_list['user_name'][$i];
                }
                return $user_list_array;
        }
	
	public function mapUserCaptureFields( $user_firstname , $user_lastname , $user_email )
	{
		$post = array();
		$post['First_Name'] = $user_firstname;
		$post['Last_Name'] = $user_lastname;
		$post[$this->duplicateCheckEmailField()] = $user_email;
		return $post;
	}

        public function assignedToFieldId()
        {
                return "Lead_Owner";
        }

	public function createRecordOnUserCapture( $module , $module_fields )
	{
		$client = $this->login();
		$post_fields['First Name'] = $module_fields['First_Name'];
		$post_fields['Last Name'] = $module_fields['Last_Name'];
		$post_fields[$this->duplicateCheckEmailField()] = $module_fields[$this->duplicateCheckEmailField()];
                $postfields = "<{$module}>\n<row no=\"1\">\n";
                if(isset($post_fields))
                {
                        foreach($post_fields as $key => $value)
                        {
                                $postfields .= "<FL val=\"".$key."\">".$value."</FL>\n";
                       }
                }
                else
                {
                        foreach($module_fields as $key => $value)
                        {
                                $postfields .= "<FL val=\"".$key."\">".$value."</FL>\n";
                        }
                }
                $postfields .= "</row>\n</$module>";
                $record = $client->insertRecord( $module , "insertRecords" , $this->authtoken ,  $postfields );
		if( isset($record['result']['message']) && ( $record['result']['message'] == "Record(s) added successfully" ) )
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

	public function replace_key_function($module_fields, $key1, $key2)
        {
                $keys = array_keys($module_fields);
                $index = array_search($key1, $keys);
                if ($index !== false) {
                $keys[$index] = $key2;
                $module_fields = array_combine($keys, $module_fields);
        }
    	return $module_fields;
	}

	public function createRecord( $module , $module_fields )
	{
		$client = $this->login();
		global $HelperObj;
                $WPCapture_includes_helper_Obj = new WPCapture_includes_helper_PRO();
                $activateplugin = $WPCapture_includes_helper_Obj->ActivatedPlugin;
		$moduleslug = $this->ModuleSlug = rtrim( strtolower($module) , "s");
		$config_fields = get_option("smack_{$activateplugin}_{$moduleslug}_fields-tmp");
		$underscored_field = "";
		foreach($config_fields['fields'] as $key => $fields)  //      To add _ for field with spaces to capture the REQUEST
		{
			if( count($exploded_fields = explode(' ', $fields['fieldname'] )) > 1 )
			{
				foreach( $exploded_fields as $exploded_field )
				{
					$underscored_field .= $exploded_field."_";
				}
				$underscored_field = rtrim($underscored_field, "_");
			}
			else
			{
				$underscored_field = $fields['fieldname'];
			}
			$config_underscored_fields[$underscored_field] = $fields['fieldname'];
			$underscored_field = "";
		}
		
		// Change checkbox value on => true
		foreach( $config_fields['fields'] as $checkbox_key => $checkbox_val )
		{
			foreach( $module_fields as $mod_cb_key => $mod_cb_val )
			{
				if( $checkbox_val['type']['name'] == 'boolean' && $mod_cb_key == $checkbox_val['name'] && $mod_cb_val == 'on' )
				{
					$module_fields[$mod_cb_key] = 'true';
				}
			}
		}

		foreach($module_fields as $field => $value)
		{
			if( array_key_exists($field , $config_underscored_fields) )
			{
				$post_fields[$config_underscored_fields[$field]]=$value;//urlencode($value);
			}
		}
		foreach($module_fields as $key => $value)
                {
                $key = preg_replace('/_/',' ',$key);
                $module_field[$key] = $value;
                }
                $module_fields = $module_field;
                $postfields = "<{$module}>\n<row no=\"1\">\n";


		if($activateplugin == 'wpzohopro')
                {	if( !empty( $module_fields  ) )
			{
                         $module_fields = $this->replace_key_function($module_fields, 'Lead Owner', 'SMOWNERID');
			}
                }
		if(isset($module_fields['SMOWNERID'])) {	
		$post_fields['SMOWNERID'] = $module_fields['SMOWNERID']; // Assign user in post_fields Array
		} else {
		 $post_fields['SMOWNERID'] = '';
		}
		// New code for changing field_name into Lable for other languages
		if( isset( $post_fields ) )
		{
			foreach( $config_fields['fields'] as $conf_key => $conf_val )
			{
				foreach( $post_fields as $post_key => $post_val )
				{
					if( $post_key == $conf_val['fieldname'])
					{
						unset( $post_fields[$post_key] );
						$post_fields[$conf_val['label']] = $post_val;
					}	
				}
			}
		}
		else
		{
			foreach( $config_fields['fields'] as $conf_key => $conf_val )
                        {
                                foreach( $module_fields as $module_key => $module_val )
                                {
                                        if( $module_key == $conf_val['fieldname'])
                                        {
						unset( $module_fields[$module_key] );
                                                $module_fields[$conf_val['label']] = $module_val;
                                        }       
                                }
                        }	
		}
		if(isset($post_fields))
                {
                        foreach($post_fields as $key => $value)
                        {
                                $postfields .= "<FL val=\"".$key."\">".$value."</FL>\n";
                       }
                }
                else
                {
                        foreach($module_fields as $key => $value)
                        {
                                $postfields .= "<FL val=\"".$key."\">".$value."</FL>\n";
                        }
                }
                $postfields .= "</row>\n</$module>";
		//Attachment
                if(isset($module_fields['attachments']) && $module_fields['attachments']){
                        $attachments = $module_fields['attachments'];
                }
                else{
                	$attachments = "";
                }

                $record = $client->insertRecord( $module , "insertRecords" , $this->authtoken ,  $postfields ,$attachments ); //attachments
		if( isset($record['result']['message']) && ( $record['result']['message'] == "Record(s) added successfully" ) )
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

		$client = $this->login();
		$product_array_fields = $module_fields;
                global $HelperObj , $wpdb;
                $WPCapture_includes_helper_Obj = new WPCapture_includes_helper_PRO();
                $activateplugin = $WPCapture_includes_helper_Obj->ActivatedPlugin;
                $moduleslug = $this->ModuleSlug = rtrim( strtolower($module) , "s");
                $config_fields = get_option("smack_{$activateplugin}_{$moduleslug}_fields-tmp");
                $underscored_field = "";

		//LEADS / CONTACTS
            	if( $module == 'Leads' || $module == 'Contacts' )
                {
                foreach($config_fields['fields'] as $key => $fields)  //      To add _ for field with spaces to capture the REQUEST
                {
                        if( count($exploded_fields = explode(' ', $fields['fieldname'] )) > 1 )
                        {
                                foreach( $exploded_fields as $exploded_field )
                                {
                                        $underscored_field .= $exploded_field."_";
                                }
                                $underscored_field = rtrim($underscored_field, "_");
                        }
                        else
                        {
                                $underscored_field = $fields['fieldname'];
                        }
                        $config_underscored_fields[$underscored_field] = $fields['fieldname'];
                        $underscored_field = "";
                }

		foreach($module_fields as $field => $value)
                {
                        if( array_key_exists($field , $config_underscored_fields) )
                        {
                                $post_fields[$config_underscored_fields[$field]]=$value;//urlencode($value);
                        }
			if( $field == 'SMOWNERID' )
			{
				$post_fields[$field] = $value;
			}
                }

		// If other language used --> change field name => label
		if( isset( $post_fields ) )
                {
                        foreach( $config_fields['fields'] as $conf_key => $conf_val )
                        {
                                foreach( $post_fields as $post_key => $post_val )
                                {
                                        if( $post_key == $conf_val['fieldname'])
                                        {
						unset( $post_fields[$post_key] );
                                                $post_fields[$conf_val['label']] = $post_val;
                                        }
                                }
                        }
                }

		$postfields = "<{$module}>\n<row no=\"1\">\n";

		if(isset($post_fields))
                {
                        foreach($post_fields as $key => $value)
                        {
                                $postfields .= "<FL val=\"".$key."\">".$value."</FL>\n";
                        }
                }
		
		$postfields .= "</row>\n</$module>";
                $record = $client->insertRecord( $module , "insertRecords" , $this->authtoken ,  $postfields );
                }

		//PRODUCT MODULE
                if( $module == 'Products' )
                {
			$postfields_product = "<{$module}>\n<row no=\"1\">\n";

                if(isset($product_array_fields))
                {
                        foreach($product_array_fields as $key => $value)
                        {
                                $postfields_product .= "<FL val=\"".$key."\">".$value."</FL>\n";
                       }
                }
                $postfields_product .= "</row>\n</$module>";
                $record = $client->insertRecord( $module , "insertRecords" , $this->authtoken ,  $postfields_product );
		}
               
		if( isset($record['result']['message']) && ( $record['result']['message'] == "Record(s) added successfully" ) )
                {
                        $data['result'] = "success";
                        $data['failure'] = 0;

                        if( $module == "Leads" )
                        {
                                $crm_id = $record['result']['recorddetail']['FL'][0];
                                $my_leadid = $crm_id;
                                $crm_name = 'wpzohopro';
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
                                $crm_id = $record['result']['recorddetail']['FL'][0];
                                $crm_name = 'wpzohopro';
                                $my_contactid = $crm_id;
                                if( is_user_logged_in() )
                                {
                                        $user_id = get_current_user_id();
                                        $is_user = 1;
                                }else
                                {
                                        $user_id = 'guest';
                                        $is_user = 0;
                                }
                                $contact_no = $crm_id;
                                $wpdb->insert( 'wp_smack_ecom_info' , array( 'crmid' => $crm_id , 'crm_name' => $crm_name , 'wp_user_id' => $user_id , 'is_user' => $is_user , 'contact_no' => $my_contactid , 'order_id' => $order_id ) );

                        }
                        if( $module == 'Products' )
                        {
                                $crm_id = $record['result']['recorddetail']['FL'][0];
                                $crm_name = 'wpzohopro';
                                $get_product = $wpdb->get_results( $wpdb->prepare( "select product_id from wp_smack_ecom_info where order_id=$order_id" ) );
                                $prod_id = $get_product[0]->product_id;
                                if( !empty( $prod_id ) )
                                {
                                        $crm_id = $prod_id.",".$crm_id;
                                }
                                $wpdb->insert( 'wp_smack_ecom_info' , array( 'product_id' => $crm_id ) , array( 'order_id' => $order_id ));             
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

	 public function updateEcomRecord( $module , $module_fields , $ids_present, $order_id )
        {
		$client = $this->login();
                $product_array_fields = $module_fields;
                global $wpdb;

		//PRODUCT MODULE
                if( $module == 'Products' )
                {
			$postfields_product = "<{$module}>\n<row no=\"1\">\n";

                if(isset($product_array_fields))
                {
                        foreach($product_array_fields as $key => $value)
                        {
                                $postfields_product .= "<FL val=\"".$key."\">".$value."</FL>\n";
                        }
                }
                $postfields_product .= "</row>\n</$module>";
		$extraparams = "&id={$ids_present}";
                $record = $client->insertRecord( $module , "updateRecords" , $this->authtoken ,  $postfields_product , $extraparams);

		if( isset($record['result']['message']) && ( $record['result']['message'] == "Record(s) updated successfully" ) )
                {
                 	$data['result'] = "success";
                        $data['failure'] = 0;

                	if( $module == 'Products' )
                        {
                                $crm_id = $record['result']['recorddetail']['FL'][0];
                                $crm_name = 'wpzohopro';
                                $get_product = $wpdb->get_results( $wpdb->prepare( "select product_id from wp_smack_ecom_info where order_id=$order_id" ) );
                                $prod_id = $get_product[0]->product_id;
                                if( !empty( $prod_id ) )
                                {
                                        $crm_id = $prod_id.",".$crm_id;
                                }
                                $wpdb->update( 'wp_smack_ecom_info' , array( 'product_id' => $crm_id ) , array( 'order_id' => $order_id ));
                        }

                }
                else
                {
                        $data['result'] = "failure";
                        $data['failure'] = 1;
                        $data['reason'] = "failed updating entry";
                }

                return $data;
        }
}

	//Convert Lead

        public  function convertLead( $module , $crm_id , $order_id , $lead_no , $sales_order)
        {
		$client = $this->login();	
		$final_result = $client->convertLeads( $module , $crm_id , $order_id , $lead_no , $this->authtoken , $sales_order);
		$sales_order['SMOWNERID'] = $final_result['SMOWNERID'];
		$sales_order['CONTACTID'] = $final_result['CONTACT_ID'];
		$sales_order['ACCOUNTID'] = $final_result['ACCOUNT_ID'];

			$SO_fields = "<SalesOrders>\n<row no=\"1\">\n";
			foreach($sales_order as $key => $value)
                        {
                                if( $key != 'Product Details' )
                                {
                                        $SO_fields .= "<FL val=\"".$key."\">".$value."</FL>\n";
                                }
                                else
                                {
                                        $SO_fields .= "<FL val=\"".$key."\">";
                                        
                                        foreach( $value as $prod_key => $prod_val  )
                                        {
                                                $SO_fields .= "<product no=\"".$prod_key."\">\n";
                                                foreach( $prod_val as $item_key => $item_val )
                                                {       
                                                        $SO_fields .= "<FL val=\"".$item_key."\">".$item_val."</FL>\n"; 
                                                }
                                                $SO_fields .= "</product>";
                                        }
                                        $SO_fields .= "</FL>\n";
                                }
                        }
                $SO_fields .= "</row>\n</SalesOrders>";
                $record = $client->insertRecord( 'SalesOrders' , "insertRecords" , $this->authtoken ,  $SO_fields );
		$sales_orderid = $record['result']['recorddetail']['FL'][0];
		global $wpdb;
		$wpdb->update( 'wp_smack_ecom_info' , array('contact_no' => $final_result['CONTACT_ID']) , array( 'order_id' => $order_id ) );
                $wpdb->update( 'wp_smack_ecom_info' , array('sales_orderid' => $sales_orderid) , array( 'order_id' => $order_id ) );
                if($record)
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

	public function create_sales_order( $module , $order_id , $contact_id , $sales_order)
	{
		$client = $this->login();
		$contact_owner  = $client->getConvertLeadOwner($module , $this->authtoken , $contact_id );
		$Account_id = $client->getAccountId( $this->authtoken );
		$sales_order['SMOWNERID'] = $contact_owner;
		$sales_order['CONTACTID'] = $contact_id;
		$sales_order['ACCOUNTID'] = $Account_id;

			$SO_fields = "<SalesOrders>\n<row no=\"1\">\n";
			foreach($sales_order as $key => $value)
                        {
                                if( $key != 'Product Details' )
                                {
                                        $SO_fields .= "<FL val=\"".$key."\">".$value."</FL>\n";
                                }
                                else
                                {
                                        $SO_fields .= "<FL val=\"".$key."\">";
                                        
                                        foreach( $value as $prod_key => $prod_val  )
                                        {
                                                $SO_fields .= "<product no=\"".$prod_key."\">\n";
                                                foreach( $prod_val as $item_key => $item_val )
                                                {       
                                                        $SO_fields .= "<FL val=\"".$item_key."\">".$item_val."</FL>\n"; 
                                                }
                                                $SO_fields .= "</product>";
                                        }
                                        $SO_fields .= "</FL>\n";
                                }
                        }
                $SO_fields .= "</row>\n</SalesOrders>";
                $record = $client->insertRecord( 'SalesOrders' , "insertRecords" , $this->authtoken ,  $SO_fields );
		$sales_orderid = $record['result']['recorddetail']['FL'][0];
		global $wpdb;
			
		$wpdb->update( 'wp_smack_ecom_info' , array('sales_orderid' => $sales_orderid) , array( 'order_id' => $order_id ) );
                if($record)
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
	
	public function updateRecord( $module , $module_fields , $ids_present )
	{
		$client = $this->login();
		$underscored_field = '';
		$config_underscored_fields = array();
		global $HelperObj;
                $WPCapture_includes_helper_Obj = new WPCapture_includes_helper_PRO();
                $activateplugin = $WPCapture_includes_helper_Obj->ActivatedPlugin;
		$moduleslug = $this->ModuleSlug = rtrim( strtolower($module) , "s");
		$config_fields = get_option("smack_{$activateplugin}_{$moduleslug}_fields-tmp");
		foreach($config_fields['fields'] as $key => $fields)  //      To add _ for field with spaces to capture the REQUEST
		{
			if( count($exploded_fields = explode(' ', $fields['fieldname'] )) > 1 )
			{
				foreach( $exploded_fields as $exploded_field )
				{
					$underscored_field .= $exploded_field."_";
				}
				$underscored_field = rtrim($underscored_field, "_");
			}
			else
			{
				$underscored_field = $fields['fieldname'];
			}
			$config_underscored_fields[$underscored_field] = $fields['fieldname'];
			$underscored_field = "";
		}
                foreach($module_fields as $field => $value)
                {
                        if( array_key_exists($field , $config_underscored_fields) )
                        {
                                $post_fields[$config_underscored_fields[$field]]=$value;//urlencode($value);
                        }
                }
	
		// New code for changing field_name into Lable for other languages

                if( isset( $post_fields ) )
                {
                        foreach( $config_fields['fields'] as $conf_key => $conf_val )
                        {
                                foreach( $post_fields as $post_key => $post_val )
                                {
                                        if( $post_key == $conf_val['fieldname'])
                                        {
						unset( $post_fields[$post_key] );
                                                $post_fields[$conf_val['label']] = $post_val;
                                        }
                                }
                        }
                }
                else
                {
                        foreach( $config_fields['fields'] as $conf_key => $conf_val )
                        {
                                foreach( $module_fields as $module_key => $module_val )
                                {
                                        if( $module_key == $conf_val['fieldname'])
                                        {
						unset( $module_fields[$module_key] );
                                                $module_fields[$conf_val['label']] = $module_val;
                                        }
                                }
                        }
                }
		//End new code for other language
	
                $postfields = "<{$module}>\n<row no=\"1\">\n";
		if(isset($post_fields))
		{
			foreach($post_fields as $key => $value)
			{
				$postfields .= "<FL val=\"".$key."\">".$value."</FL>\n";
			}
		}
		else
		{
			foreach($module_fields as $key => $value)
			{
				$postfields .= "<FL val=\"".$key."\">".$value."</FL>\n";
			}
		}
                $postfields .= "</row>\n</$module>";
		$config_fields = get_option("smack_{$HelperObj->ActivatedPlugin}_fields_shortcodes");
		$extraparams = "&id={$ids_present}";
		$record = $client->insertRecord( $module , "updateRecords" , $this->authtoken ,  $postfields , $extraparams );
                if( isset($record['result']['message']) && ( $record['result']['message'] == "Record(s) updated successfully" ) )
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

	public function checkEmailPresent( $module , $email )
	{
		$WPCapture_includes_helper_Obj = new WPCapture_includes_helper_PRO();
		$activateplugin = $WPCapture_includes_helper_Obj->ActivatedPlugin;
		$result_emails = array();
		$result_ids = array();
		$client = $this->login();
		$email_present = "no";
		$extraparams = "&searchCondition=(Email|=|{$email})"; // Old API Method for search record
		//$extraparams = "&criteria=(Email:$email)"; // New API method for search
                $records = $client->getRecords( $module , "getSearchRecords" , $this->authtoken , "Id , Email" , "" , $extraparams ); // Replaced getSearchRecords by searchRecords
		if(isset( $records['result'][$module]['row']['@attributes'] ))
		{
                        $result_lastnames[] = "Last Name";
                        $result_emails[] = $email; 
                        $result_ids[] = $records['result'][$module]['row']['FL'];
                        $email_present = "yes";
		}
		else
		{
			if(!empty($records) && isset($records['result']) && is_array($records['result'][$module]['row']))
			{
				foreach( $records['result'][$module]['row'] as $key => $record )
				{
					$result_lastnames[] = "Last Name";
					$result_emails[] = $email; 
					$result_ids[] = $record['FL'];
					$email_present = "yes";
				}
			}
		}
		$this->result_emails = $result_emails;
		$this->result_ids = $result_ids;
		if($email_present == 'yes')
			return true;
		else
			return false;
	}

	public function duplicateCheckEmailField()
	{
		return "Email";
	}

	public function checkProductPresent( $module , $product )
	{
		$WPCapture_includes_helper_Obj = new WPCapture_includes_helper_PRO();
		$activateplugin = $WPCapture_includes_helper_Obj->ActivatedPlugin;
		$result_emails = array();
		$result_ids = array();
		$client = $this->login();
	        $product_present = "no";
		//$extraparams = "&searchCondition=(Product Name|=|{$product})"; // Old API method for search record
		$extraparams = "&criteria=(Product Name:$product)"; // New Method for search record
                $records = $client->getRecords( $module , "searchRecords" , $this->authtoken , "Product Name" , "" , $extraparams ); // // Replaced getSearchRecords by searchRecords
		if(isset( $records['result'][$module]['row']['@attributes'] ))
		{
                        $result_products[] = $product; 
                        $result_ids[] = $records['result'][$module]['row']['FL'];
                        $product_present = "yes";
		}
		else
		{
			if(is_array($records['result'][$module]['row']))
			{
				foreach( $records['result'][$module]['row'] as $key => $record )
				{
					$result_products[] = $product; 
					$result_ids[] = $record['FL'];
					$product_present = "yes";
				}
			}
		}
		$this->result_products = $result_products;
		$this->result_ids = $result_ids;
		if($product_present == 'yes')
			return true;
		else
			return false;
	}
}
