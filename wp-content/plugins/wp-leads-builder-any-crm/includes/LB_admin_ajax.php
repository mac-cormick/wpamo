<?php

if ( ! defined( 'ABSPATH' ) )
        exit; // Exit if accessed directly

class SmackLBAdminAjax {

        public static function smlb_ajax_events(){
		$ajax_actions = array(
                        'selectplugpro' => false,
                        'SaveCRMconfig' => false,
			'save_campaign_details' => 'false',
			'campaign_mapping' => 'false',
			'save_apikey' => 'false',
			'wp_usersync_assignedto' => 'false',
			'TFA_auth_save' => 'false',
			'mappingmodulepro' => 'false',
			'Sync_settings_PRO' => 'false',
			'saveSyncValue' => 'false',
			'send_mapping_configuration' => 'false',
			'createnew_form'=>'false',
			'get_thirdparty_fields' => 'false',
			'map_thirdparty_fields' => 'false',
			'save_thirdparty_form_title' => 'false',
			'send_mapped_config' => 'false',
			'delete_mapped_config' => 'false',
			'captcha_info' => 'false',
			'saveSFSettings' => 'false',
			'save_usersync_RR_option' => 'false',
			'selectthirdpartypro' => 'false',
			'customfieldpro' => 'false',
			'smack_leads_builder_pro_change_menu_order' => 'false',
			'send_order_info' => 'false',
			'change_ecom_module_config' => 'false',
			'save_convert_lead' => 'false',
			'map_ecom_fields' => 'false',
			'map_sync_user_fields' => 'false',
			'adminAllActionsPRO' => 'false',
            'SaveSuiteconfig' => 'false',
		);
                foreach($ajax_actions as $action => $value ){
                        add_action('wp_ajax_'.$action, array(__CLASS__, $action));
                }
        }

	public static function selectplugpro()
	{
		
		require_once(SM_LB_PRO_DIR . "templates/plugin-select.php");
		die;
	}

    public static function SaveSuiteconfig(){
        update_option('WpLeadBuilderProActivatedPlugin', 'wpsuitepro');
        print_r('success');
        die();
    }

	public static function createnew_form()
	{
		if($_REQUEST['Action'] == 'createshortcode')
		{
			require_once(SM_LB_PRO_DIR . "includes/class_lb_manage_shortcodes.php");
			$createshortcode = new ManageShortcodesActions();
			$value = $createshortcode->CreateShortcode($_REQUEST['Module']);
			$value['onAction'] = 'onCreate';
		}elseif($_REQUEST['Action'] == 'Editshortcode')
		{
			$value = array();
			$value['shortcode'] = $_REQUEST['shortcode'];
                	$value['module'] = $_REQUEST['Module'];
                	$value['crmtype'] =  $_REQUEST['plugin'];
			$value['onAction'] = 'onEditShortCode';
		} 
		else
		{
			require_once(SM_LB_PRO_DIR . "includes/class_lb_manage_shortcodes.php");
                        $deleteshortcode = new ManageShortcodesActions();
                        $deleteshortcode->DeleteShortcode($_REQUEST['shortcode']);
			$value = array();
		}	
		$shortcodevalues = json_encode($value);
		print_r($shortcodevalues);die;
	}
	public static function SaveCRMconfig( )
        {
                require_once( SM_LB_PRO_DIR ."templates/saveCRMConfig.php" );
                die;
        }

	
	public static function adminAllActionsPRO()
	{
		require_once( SM_LB_PRO_DIR ."includes/Functions.php" );
		$adminObj = new AjaxActionsClassPRO();
		$admin = $adminObj->adminAllActionsPRO();
                die;
	}

	public static function save_campaign_details(){
                $save_camp_array = array();
                $save_camp_array['camp_name'] = sanitize_text_field($_REQUEST['camp_name']);
                $save_camp_array['utm_source'] = sanitize_text_field($_REQUEST['utm_source']);
                $save_camp_array['camp_medium'] = sanitize_text_field($_REQUEST['camp_medium']);
                $save_camp_array['utm_name'] = sanitize_text_field($_REQUEST['utm_name']);
                update_option('Campaign_details' , $save_camp_array);
                die;
        }

	public static function campaign_mapping(){
                global $wpdb;
                $mc_camp_id = $_REQUEST['campaign_id'];
                $mc_camp_name = sanitize_text_field($_REQUEST['campaign_name']);
                $get_user_entered_details = get_option('Campaign_details');
                $campaign_details = serialize($get_user_entered_details);
                $date = current_time('Y-m-d H:i:s');
                $wpdb->insert('smack_campaign_info' , array('campaign_id' => $mc_camp_id , 'campaign_name' => $mc_camp_name , 'campaign_details' => $campaign_details , 'mailing_source' => 'MailChimp' , 'date' => $date) );
                die;
        }

	 public static function save_apikey(){
                $mc_api_key = sanitize_text_field($_REQUEST['mc_apikey']);
                update_option('mc_apikey' , $mc_api_key);
                require_once( SM_LB_PRO_DIR ."templates/getCampaignList.php" );
        }

	public static function captcha_info()
        {
		$final_captcha_array['email'] = sanitize_text_field($_REQUEST['email'] );
		$final_captcha_array['emailcondition'] = sanitize_text_field($_REQUEST['emailcondition'] );
		$final_captcha_array['debugmode'] = sanitize_text_field($_REQUEST['debugmode'] );
		update_option("wp_captcha_settings", $final_captcha_array );
                die;
        }

	public static function wp_usersync_assignedto()
        {
                require_once( SM_LB_PRO_DIR ."templates/wp_assignedtouser.php" );
                die;
        }

	public static function TFA_auth_save()
        {
                $TFA_Authtoken_value = sanitize_text_field( $_REQUEST['authtoken']);
		$ActivePlugin = get_option('WpLeadBuilderProActivatedPlugin');
                if( $ActivePlugin == 'wpzohopro')
                        $smack_TFA_label = 'TFA_zoho_authtoken';
                else
                        $smack_TFA_label = 'TFA_zoho_plus_authtoken';

                update_option($smack_TFA_label , $TFA_Authtoken_value );
                print_r( $TFA_Authtoken_value);
                die;
        }

	public static function mappingmodulepro()
        {
                $map_module = $_REQUEST['postdata'];
                update_option( 'WpMappingModule' , $map_module );
                die;
        }

	public static function Sync_settings_PRO( )
        {
                require_once( SM_LB_PRO_DIR .'templates/Sync-settings.php' );
                die;
        }

	public static function saveSyncValue()
        {
                require_once( SM_LB_PRO_DIR .'templates/save-sync-value.php' );
                die;
        }

	public static function send_mapping_configuration()
        {
                require_once( SM_LB_PRO_DIR .'templates/thirdparty_mapping.php' );
                $module = sanitize_text_field( $_REQUEST['thirdparty_module'] );
                $thirdparty_form = sanitize_text_field( $_REQUEST['thirdparty_plugin'] );
                $mapping_ui_fields = new thirdparty_mapping();
                $mapping_ui_fields->mapping_form_fields($module , $thirdparty_form );
        }

	public static function get_thirdparty_fields()
        {
                require_once( SM_LB_PRO_DIR .'templates/thirdparty_mapping.php' );
                $mapping_ui_fields = new thirdparty_mapping();
                $mapping_ui_fields->get_thirdparty_form_fields();
        }

	public static function map_thirdparty_fields()
        {
                require_once( SM_LB_PRO_DIR .'templates/thirdparty_mapping.php' );
                $mapping_ui_fields = new thirdparty_mapping();
                $mapping_ui_fields->map_thirdparty_form_fields();

        }

	public static function save_thirdparty_form_title()
        {
                $thirdparty_title_key = sanitize_text_field($_REQUEST['tp_title_key']);
                $thirdparty_title_value = sanitize_text_field( $_REQUEST['tp_title_val'] );
                update_option( $thirdparty_title_key , $thirdparty_title_value );
                die;
        }

	public static function send_mapped_config()
        {
                require_once( SM_LB_PRO_DIR .'templates/thirdparty_mapping.php' );
                $mapping_ui_fields = new thirdparty_mapping();
                $mapping_ui_fields->show_mapped_config();
        }

	public static function delete_mapped_config()
        {
                require_once( SM_LB_PRO_DIR .'templates/thirdparty_mapping.php' );
                $mapping_ui_fields = new thirdparty_mapping();
                $mapping_ui_fields->delete_mapped_configuration();
        }

	public static function saveSFSettings() {
               // require_once(SM_LB_PRO_DIR .'includes/wpsalesforceproFunctions.php');
                $key = sanitize_text_field($_POST['key']);
                $value = sanitize_text_field($_POST['value']);
                $exist_config = get_option("wp_wpsalesforcepro_settings");
                $config = $current_config = array();
                switch ($key) {
                        case 'key':
                                $current_config['key'] = $value;
                                break;
                        case 'secret':
                                $current_config['secret'] = $value;
                                break;
                        case 'callback':
                                $current_config['callback'] = $value;
                                break;
                }
                if(!empty($exist_config))
                        $config = array_merge($exist_config, $current_config);
                else
                        $config = $current_config;
                update_option('wp_wpsalesforcepro_settings', $config);
                die;
        }

	public static function save_usersync_RR_option()
        {
                $usersync_RR_value = sanitize_text_field( $_REQUEST['user_rr_val'] );
                update_option('usersync_rr_value' , $usersync_RR_value );
                die;
        }

	public static function selectthirdpartypro()  {
                require_once(SM_LB_PRO_DIR ."templates/third-plugin.php");
                die;
        }

	public static function customfieldpro()
        {
                $custom_plugin = sanitize_text_field($_REQUEST['postdata']);
                $active_plugins = get_option( "active_plugins" );
                switch( $custom_plugin )
                {
                case 'acf':
                        if( in_array( "advanced-custom-fields/acf.php" , $active_plugins ) ) {
                        update_option('custom_plugin',$custom_plugin);
                        $activated = "yes" ;
                        }
                        else {
                        $activated = "no" ;
                        }
                break;

		case 'acfpro':
                        if( in_array( "advanced-custom-fields-pro/acf.php" , $active_plugins ) ) {
                        update_option('custom_plugin',$custom_plugin);
                        $activated = "yes" ;
                        }
                        else {
                        $activated = "no" ;
                        }
                break;

                case 'wp-members':
                        if( in_array( "wp-members/wp-members.php" , $active_plugins) ) {
                        update_option('custom_plugin',$custom_plugin);
                        $activated = "yes" ;
                        }
                        else {
                        $activated = "no" ;
                        }
                break;

                case 'member-press':
                        if( in_array( "memberpress/memberpress.php" , $active_plugins) ) {
                        update_option('custom_plugin',$custom_plugin);
                        $activated = "yes" ;
                        }
                        else {
                        $activated = "no" ;
                        }
                break;
	
		case 'ultimate-member':
                        if( in_array( "ultimate-member/index.php" , $active_plugins ) ) {
                        update_option('custom_plugin',$custom_plugin);
                        $activated = "yes" ;
                        }
                        else {
                        $activated = "no" ;
                        }
                break;

		case 'none':
                update_option('custom_plugin',$custom_plugin);
                $activated = "yes" ;
                break;
                }
                print_r( $activated );die;
        }

	public static function smack_leads_builder_pro_change_menu_order( $menu_order ) {
           return array(
               'index.php',
               'edit.php',
               'edit.php?post_type=page',
               'upload.php',
               'wp-leads-builder-any-crm/index.php',
           );
        }

	public static function send_order_info($order_id) {
		$order = new WC_Order( $order_id );
		$customer = new WC_Customer( $order_id );
		$items = $order->get_items();
		$myuser_id = (int)$order->user_id;
		$user_info = get_userdata($myuser_id);
		$items = $order->get_items();
		foreach ($items as $item) {
			if ($item['product_id']==24) {
				// Do something clever
			}
		}
	}
	
	public static function change_ecom_module_config()
	{
        require_once( SM_LB_PRO_DIR .'templates/ecom_config.php');
        $ecom_obj = new ecom_configuration();
        $ecom_obj->change_module_config();
	}

	public static function save_convert_lead()
	{
        $convert_val = sanitize_text_field( $_REQUEST['convert_lead']);
        update_option( 'ecom_wc_convert_lead' , $convert_val );
	}

	public static function map_ecom_fields()
	{
        require_once( SM_LB_PRO_DIR .'templates/ecom_config.php');
        $ecom_obj = new ecom_configuration();
        $ecom_obj->map_ecom_module_configuration();
	}

	 public static function map_sync_user_fields()
        {
		require_once(SM_LB_PRO_DIR . "includes/lb-syncuser.php");
		$map_fields = $_REQUEST['mappedfields']; 
		$total_user_fields = $_REQUEST['totaluserfields'];
		$map_variable = $_REQUEST['mapvariable'];
		if($map_variable == 'Leads_module_field' ) {
			$module = 'Leads';
		} else {
			$module = 'Contacts';
		}
		$activated_plugin = get_option( "WpLeadBuilderProActivatedPlugin" );
		$add_mapped_array = update_option("smack_{$activated_plugin}_mappedfields_capture_settings",$map_fields);
		$data = new SyncUserActions();
	        $module = $data->ModuleMapping('',$map_fields,'update');
		update_option( "User{$activated_plugin}{$module}ModuleMapping" , $map_fields );
		header("Refresh:0");
		print_r($map_fields);die;	
	}
}	
