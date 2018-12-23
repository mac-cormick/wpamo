<?php
if ( ! defined( 'ABSPATH' ) )
        exit; // Exit if accessed directly

//include_once ( plugin_dir_path(__FILE__) . '../includes/lb-main-helper.php' );

class ZohoCrmSmLBHelper {

	public function __construct() {
                //self::initializing_scheduler();
        }

  public function setEventObj()
  {
    $obj = new mainCrmHelper();
    return $obj;
  }

	public function user_module_mapping_view() {
		include ('views/form-usermodulemapping.php');
	}

	public function mail_sourcing_view() {
		include('views/form-campaign.php');
	}

	public function new_lead_view() {
	       global $lb_crm;
               include ('views/form-managefields.php');
        }

	public function new_contact_view() {
               global $lb_crm;
               $module = "Contacts";
               $lb_crm->setModule($module);
               include ('views/form-managefields.php');
        }


	public function show_form_crm_forms() {
               include ('views/form-crmforms.php');
        }

	public function show_form_settings() {
               include ('views/form-settings.php');
        }

	public function show_usersync() {
               include ('views/form-usersync.php');
        }

	public function show_ecom_integ() {
               include ('views/form-ecom-integration.php');
        }

	public function show_vtiger_crm_config() {
               include ('views/form-vtigercrmconfig.php');
        }

	public function show_sugar_crm_config() {
               include ('views/form-sugarcrmconfig.php');
        }

	public function show_suite_crm_config() {
               include ('views/form-suitecrmconfig.php');
        }

	public function show_zoho_crm_config() {
               include ('views/form-zohocrmconfig.php');
        }

	public function show_zohoplus_crm_config() {
               include ('views/form-zohocrmconfig.php');
        }

	public function show_freshsales_crm_config() {
               include ('views/form-freshsalescrmconfig.php');
        }

	public function show_salesforce_crm_config() {
		include('views/form-salesforcecrmconfig.php');
	}

      public function zohoproSettings( $zohoSettArray )
    {
        $zoho_config_array = $zohoSettArray['REQUEST'];
        $fieldNames = array(
            'username' => __('Zoho Username' , SM_LB_URL ),
            'password' => __('Zoho Password' , SM_LB_URL ),    
      'TFA_check'      => __('Two Factor Authentication' , SM_LB_URL ),
            'smack_email' => __('Smack Email' , SM_LB_URL ),
            'email' => __('Email id' , SM_LB_URL ),
            'emailcondition' => __('Emailcondition' , SM_LB_URL ),
            'debugmode' => __('Debug Mode' , SM_LB_URL ),
                    );

        foreach ($fieldNames as $field=>$value){
            if(isset($zoho_config_array[$field]))
            {
                $config[$field] = $zoho_config_array[$field];
            }
        }
        require_once(SM_LB_ZOHO_DIR . "includes/wpzohoproFunctions.php");
        $FunctionsObj = new mainCrmHelper( );
        $jsonData = $FunctionsObj->getAuthenticationKey( $config['username'] , $config['password'] );
        if($jsonData['result'] == "TRUE")
        {
            $successresult = "Settings Saved ";
            $result['error'] = 0;
            $result['success'] = $successresult;
            $config['authtoken'] = $jsonData['authToken'];
            $WPCapture_includes_helper_Obj = new WPCapture_includes_helper_PRO();
            $activateplugin = $WPCapture_includes_helper_Obj->ActivatedPlugin;
            update_option("wp_{$activateplugin}_settings", $config);
        }
  else if( $jsonData['result'] == "FALSE" && $jsonData['cause'] == 'WEB_LOGIN_REQUIRED'){
    $TFA_get_authtoken = get_option('TFA_zoho_authtoken' );
    $uri = "https://crm.zoho.com/crm/private/xml/Info/getModules?"; // Check Auth token present in Zoho //ONLY FOR TFA CHECK
            $postContent = "scope=crmapi";
            $postContent .= "&authtoken={$TFA_get_authtoken}";
            $args = array(
               'body' => $postContent
            );
            $response =  wp_remote_post($uri, $args ) ;
            $result = wp_remote_retrieve_body($response);
            $xml = simplexml_load_string($result);
            $json = json_encode($xml);
            $result_array = json_decode($json,TRUE);
   
      $TFA_result_array = $result_array['error'];
      if( $TFA_result_array['code'] = "4834" && $TFA_result_array['message'] == "Invalid Ticket Id" )
      {
      $successresult = "TFA is enabled in ZOHO CRM. Please Enter Valid Authtoken Below. <a target='_blank' href='https://crm.zoho.com/crm/ShowSetup.do?tab=developerSpace&subTab=api'>To Genrate Authtoken</a>";

    //https://crm.zoho.com/crm/ShowSetup.do?tab=webInteg&subTab=api

      $result['error'] = 11;
      $result['errormsg'] = $successresult;
      }
      else
      {

      $successresult = " Settings Saved";
        $result['error'] = 0;
                $result['success'] = $successresult;
      }
              $config['authtoken'] = get_option( "TFA_zoho_authtoken" );
              $WPCapture_includes_helper_Obj = new WPCapture_includes_helper_PRO();
              $activateplugin = $WPCapture_includes_helper_Obj->ActivatedPlugin;
              update_option("wp_{$activateplugin}_settings", $config);
      }
        else
        {
            if($jsonData['cause'] == 'EXCEEDED_MAXIMUM_ALLOWED_AUTHTOKENS') {
                $zohocrmerror = "Please log in to <a target='_blank' href='https://accounts.zoho.com'>https://accounts.Zoho.com</a> - Click Active Authtoken - Remove unwanted Authtoken, so that you could generate new authtoken..";
            }
            else{
                $zohocrmerror = "Please Verify Username and Password.";
            }
            $result['error'] = 1;
            $result['errormsg'] = $zohocrmerror ;
            $result['success'] = 0;
        }
        return $result;
    }

    public function zohoplusproSettings( $zohoSettArray )
    {
        $zoho_config_array = $zohoSettArray['REQUEST'];
  $fieldNames = array(
            'username' => __('Zoho Plus Username' , SM_LB_URL ),
            'password' => __('Zoho Plus Password' , SM_LB_URL ),
      'TFA_check'      => __('Two Factor Authentication' , SM_LB_URL ),
            'smack_email' => __('Smack Email' , SM_LB_URL ),
            'email' => __('Email id' , SM_LB_URL ),
            'emailcondition' => __('Emailcondition' , SM_LB_URL ),
            'debugmode' => __('Debug Mode' , SM_LB_URL ),
                    );

        foreach ($fieldNames as $field=>$value){
            if(isset($zoho_config_array[$field]))
            {
                $config[$field] = $zoho_config_array[$field];
            }
        }
  require_once(SM_LB_ZOHO_DIR . "includes/wpzohoproFunctions.php");  
        $FunctionsObj = new mainCrmHelper( );
        $jsonData = $FunctionsObj->getAuthenticationKey( $config['username'] , $config['password'] );
        if($jsonData['result'] == "TRUE")
        {
            $successresult = " Settings Saved ";
            $result['error'] = 0;
            $result['success'] = $successresult;
            $config['authtoken'] = $jsonData['authToken'];
            $WPCapture_includes_helper_Obj = new WPCapture_includes_helper_PRO();
            $activateplugin = $WPCapture_includes_helper_Obj->ActivatedPlugin;
            update_option("wp_{$activateplugin}_settings", $config);
        }
  else if( $jsonData['result'] == "FALSE" && $jsonData['cause'] == 'WEB_LOGIN_REQUIRED'){
    $TFA_get_authtoken = get_option('TFA_zoho_plus_authtoken' );
    $uri = "https://crm.zoho.com/crm/private/xml/Info/getModules?"; // Check Auth token present in Zoho //ONLY FOR TFA CHECK
            $postContent = "scope=crmapi";
            $postContent .= "&authtoken={$TFA_get_authtoken}";
           
            $args = array(
               'body' => $postContent
            );
            $response =  wp_remote_post($uri, $args ) ;
            $result = wp_remote_retrieve_body($response);
            
            $xml = simplexml_load_string($result);
            $json = json_encode($xml);
            $result_array = json_decode($json,TRUE);
           
    $TFA_result_array = $result_array['error'];
      if( $TFA_result_array['code'] = "4834" && $TFA_result_array['message'] == "Invalid Ticket Id" )
      {
      $successresult = "TFA is enabled in ZOHO CRM Plus. Please Enter Valid Authtoken Below. <a target='_blank' href='https://crm.zoho.com/crm/ShowSetup.do?tab=webInteg&subTab=api'>To Genrate Authtoken</a>";

      $result['error'] = 11;
      $result['errormsg'] = $successresult;
      }
      else
      {

      $successresult = " Settings Saved ";
        $result['error'] = 0;
                $result['success'] = $successresult;
      }
              $config['authtoken'] = get_option( "TFA_zoho_plus_authtoken" );
              $WPCapture_includes_helper_Obj = new WPCapture_includes_helper_PRO();
              $activateplugin = $WPCapture_includes_helper_Obj->ActivatedPlugin;
              update_option("wp_{$activateplugin}_settings", $config);
      }
        else
        {
            if($jsonData['cause'] == 'EXCEEDED_MAXIMUM_ALLOWED_AUTHTOKENS') {
                $zohocrmerror = "Please log in to <a target='_blank' href='https://accounts.zoho.com'>https://accounts.Zoho.com</a> - Click Active Authtoken - Remove unwanted Authtoken, so that you could generate new authtoken..";
            }
            else{
                $zohocrmerror = "Please Verify Username and Password.";
            }
            $result['error'] = 1;
            $result['errormsg'] = $zohocrmerror ;
            $result['success'] = 0;
        }
        return $result;
    }

	
}
//add_action('admin_menu', array('ZohoCrmSmLBHelper', 'admin_menus'));
global $lb_crm;
$lb_crm = new ZohoCrmSmLBHelper();
