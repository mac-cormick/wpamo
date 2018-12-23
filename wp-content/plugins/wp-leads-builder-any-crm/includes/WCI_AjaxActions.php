<?php
if ( ! defined( 'ABSPATH' ) )
        exit; // Exit if accessed directly
 
class WCI_AjaxActions
{
	public function __construct()
	{

	}

	public static  function visitor_time_spent()
	{
		require_once('WCI_VisitorTimeSpent.php');
		die;
	}

	public static  function time_spent()
	{
		global $wpdb;
		$get_glob_vars = sanitize_text_field($_REQUEST['wci_glob_vars']);
		$glob_vars = str_replace("\\" , '' , $get_glob_vars);
		$wci_global = json_decode($glob_vars);

		$time_spent = sanitize_text_field( $_POST['timeSpent'] );
		$date = date( 'Y-m-d H:i:s', current_time( 'timestamp', 0 ) );
		$date_without_time = date( 'Y-m-d', current_time( 'timestamp', 0 ) );
		foreach( (array)$wci_global as $global_key => $global_val )
		{
			$key = $global_val->req;
			$ip = $global_val->ip;
			$country = $global_val->country;
			$ajaxurl = $global_val->ajaxurl;
			$page_url = $global_val->page_url;
			$cart_url= $global_val->cart_url;
			$prod_id= $global_val->prod_id;
			$page_title = $global_val->page_title;
			$product = $global_val->product;
			$user_email= $global_val->user_email;
			$user_name= $global_val->user_name;
			$is_user= $global_val->is_user;
			$session_id= $global_val->session_id;
			$session_key= $global_val->session_key;
		}
		if( $page_title == "Shop" )
		{
			$prod_id = get_option( 'woocommerce_shop_page_id' );
		}
		$user_arr = array("session_id" => $session_id, "session_key" => $session_key, "prodid" => $prod_id , "page" => $page_url , "prod_title" => $page_title , "userip" => $ip , "country" => $country, "timespent" => $time_spent, "date_time" => $date );
		$info = serialize($user_arr);
		$button_name = '';
		$wpdb->insert( 'wci_activity' , 															array( 'session_id' => $session_id ,
		                                                                                                  'session_key' => $session_key ,
		                                                                                                  'user_id' => $user_name ,
		                                                                                                  'user_email' => $user_email ,
		                                                                                                  'is_user' => $is_user ,
		                                                                                                  'user_ip' => $ip ,
		                                                                                                  'country' => $country ,
		                                                                                                  'date' => $date_without_time ,
		                                                                                                  'information' => $info ,
		                                                                                                  'visited_url' => $page_url ,
		                                                                                                  'page_title' => $page_title  ,
		                                                                                                  'page_id' => $prod_id ,
		                                                                                                  'spent_time' => $time_spent ,
		                                                                                                  'clicked_button' => $button_name ,
		                                                                                                  'date_time' => $date) ,
			array( '%d' , '%s' , '%s' , '%s' , '%d' , '%s' , '%s' , '%s' , '%s' , '%s' , '%s' , '%d' ,'%d' , '%s' , '%s' )
		);
		die;
	}

	public static function wci_funnel_chart()
	{
		$funnelData = new Chart_Data();
		$funnelData->wci_funnel_chart();
	}

	public static function wootracking_dashboard()
	{
		$dashboardFunnelData = new Chart_Data();
		$dashboardFunnelData->wci_dashboard_chart();
	}

	public static function fetch_pie_data(){
		$pieData = new Chart_Data();
		$pieData->wci_get_pie_data();
	}

	public static function wci_abandon_filter()
	{
		$funnelData = new Chart_Data();
		$funnelData->wci_abandon_filter();
	}

	public static function wci_abandon_filter_oneday()
	{
		$funnelData = new Chart_Data();
		$funnelData->wci_abandon_filter_oneday();
	}

	public static function wci_abandon_filter_oneweek()
	{
		$funnelData = new Chart_Data();
		$funnelData->wci_abandon_filter_oneweek();
	}

	public static function wci_abandon_filter_onemonth()
	{
		$funnelData = new Chart_Data();
		$funnelData->wci_abandon_filter_onemonth();
	}

	public static function wci_opportunity_filter_oneday()
	{
		$funnelData = new Chart_Data();
		$funnelData->wci_opportunity_filter_oneday();
	}

	public static function wci_opportunity_filter_oneweek()
	{
		$funnelData = new Chart_Data();
		$funnelData->wci_opportunity_filter_oneweek();
	}

	public static function wci_opportunity_filter_onemonth()
	{
		$funnelData = new Chart_Data();
		$funnelData->wci_opportunity_filter_onemonth();
	}

	public static function cart_submit()
	{
		require_once( 'wci_cart_submit.php' );
		die;
	}


}
add_action('wp_ajax_woocommerce_add_to_cart', 'smack_woocommerce_add_to_cart');
add_action('wp_ajax_time_spent', array('WCI_AjaxActions', 'time_spent'));
add_action('wp_ajax_nopriv_time_spent', array('WCI_AjaxActions', 'time_spent'));
add_action('wp_ajax_button_click', array('WCI_AjaxActions', 'button_click'));
add_action('wp_ajax_nopriv_button_click', array('WCI_AjaxActions', 'button_click'));
add_action('wp_ajax_shop_cart', array('WCI_AjaxActions', 'shop_cart'));
add_action('wp_ajax_nopriv_shop_cart', array('WCI_AjaxActions', 'shop_cart'));
add_action('wp_ajax_cart', array('WCI_AjaxActions', 'cart'));
add_action('wp_ajax_nopriv_cart' , array('WCI_AjaxActions', 'cart'));
add_action('wp_ajax_wci_funnel_chart', array('WCI_AjaxActions', 'wci_funnel_chart'));
add_action('wp_ajax_wootracking_dashboard', array('WCI_AjaxActions', 'wootracking_dashboard'));
add_action('wp_ajax_fetch_pie_data', array('WCI_AjaxActions', 'fetch_pie_data'));
add_action('wp_ajax_update_sessionid',array('WCI_AjaxActions', 'update_sessionid'));
add_action('wp_ajax_nopriv_update_sessionid',array('WCI_AjaxActions', 'update_sessionid'));

add_action('wp_ajax_insert_sessionid_for_guest',array('WCI_AjaxActions', 'insert_sessionid_for_guest'));
add_action('wp_ajax_nopriv_insert_sessionid_for_guest',array('WCI_AjaxActions', 'insert_sessionid_for_guest'));

add_action('wp_ajax_cart_submit', array( 'WCI_AjaxActions' , 'cart_submit' ));
//NEW
add_action('wp_ajax_wci_abandon_filter',array('WCI_AjaxActions', 'wci_abandon_filter'));
add_action('wp_ajax_wci_abandon_filter_oneday',array('WCI_AjaxActions', 'wci_abandon_filter_oneday'));
add_action('wp_ajax_wci_abandon_filter_oneweek',array('WCI_AjaxActions', 'wci_abandon_filter_oneweek'));
add_action('wp_ajax_wci_abandon_filter_onemonth',array('WCI_AjaxActions', 'wci_abandon_filter_onemonth'));

//OPPORTUNITIES
add_action('wp_ajax_wci_opportunity_filter_oneday',array('WCI_AjaxActions', 'wci_opportunity_filter_oneday'));
add_action('wp_ajax_wci_opportunity_filter_oneweek',array('WCI_AjaxActions', 'wci_opportunity_filter_oneweek'));
add_action('wp_ajax_wci_opportunity_filter_onemonth',array('WCI_AjaxActions', 'wci_opportunity_filter_onemonth'));


class WCI_NoAjaxhookCalls {

	public static function wci_last_login_time($login) {
		global $user_ID,$wpdb;
		$user = get_user_by('login', $login);
		$login_time = date("Y-m-d h:i:s");
		$logout_time = "0000-00-00 00-00-00";
		$login_user_ip = $_SERVER['REMOTE_ADDR'];
		update_user_meta($user->ID, 'loginTime', $login_time);
		update_user_meta($user->ID, 'login_user_ip', $login_user_ip);
		$usr_id = $user->ID;
		$usr_name = $user->display_name;
		$usr_email = $user->user_email;
		$login = get_user_meta($usr_id,'loginTime');
		$usr_registered_date = $user->user_registered;
		$usr_role =  $user->roles[0];
		$status = "login";
		$wpdb->insert( 'wci_history' , array( 'user_id' => $usr_id , 'user_name' => $usr_name , 'email' => $usr_email , 'date' => $usr_registered_date , 'role' => $usr_role , 'login_time' => $login_time , 'logout_time' => $logout_time , 'status' => $status ) );

		$user_list = get_users();

		foreach($user_list as $usr_key => $usr_value)
		{
			global $wpdb;
			$usr_id = $usr_value->data->ID;
			$usr_name = $usr_value->data->display_name;
			$usr_email = $usr_value->data->user_email;
			$login = get_user_meta($usr_id,'loginTime');
			if(isset($login[0])) {
				$login_time = $login[0];}
			else {
				$login_time = ""; }
			$logout = get_user_meta($usr_id,'logoutTime');
			if(isset($logout[0])) {
				$logout_time = $logout[0]; }
			else {
				$logout_time = ""; }
			$usr_registered_date = $usr_value->data->user_registered;
			$usr_role =  $usr_value->roles[0];
			$existing_users = $wpdb->get_results($wpdb->prepare("select user_id from wci_user_profile_updated where user_id=%d" , $usr_id));
			if(empty($existing_users)){
				$wpdb->insert( 'wci_user_profile_updated' , array( 'user_id' => $usr_id , 'user_name' => $usr_name , 'email' => $usr_email , 'date' => $usr_registered_date , 'role' => $usr_role , 'login_time' => $login_time , 'logout_time' => $logout_time ) );
			}
			else{
				$wpdb->update( 'wci_user_profile_updated' , array( 'user_name' => $usr_name , 'email' => $usr_email , 'date' => $usr_registered_date , 'role' => $usr_role , 'login_time' => $login_time , 'logout_time' => $logout_time ) , array( 'user_id' => $usr_id ) );

			}
		}
	}

	public static function wci_time_on_logout($user_id) {

		global $user_ID,$wpdb;
		// Clear cookies when user logout
		unset( $_COOKIE['wci_customer_cookie_key']);
		unset( $_COOKIE['wci_unique_key']);
		//Destroy session entry from table 
		$check_session_id = $wpdb->get_var( $wpdb->prepare( "select session_id from wci_maintain_session where session_key=%s" , $user_ID ) );
		if( !empty( $check_session_id ))
		{
			$wpdb->delete( 'wci_maintain_session' , array( 'session_id' => $check_session_id ) );

		}
		//Destroy session END
		$user = get_user_by('id', $user_ID);
		$logout_time = date("Y-m-d h:i:s");
		$login_time = "0000-00-00 00-00-00";
		$logout_user_ip = $_SERVER['REMOTE_ADDR'];
		update_user_meta($user->ID, 'logoutTime',  $logout_time);
		update_user_meta($user->ID, 'logout_user_ip', $logout_user_ip);
		$usr_id = $user->ID;
		$usr_name = $user->display_name;
		$usr_email = $user->user_email;
		$usr_registered_date = $user->user_registered;
		$usr_role =  $user->roles[0];
		$status = "logout";
		$wpdb->insert( 'wci_history' , array( 'user_id' => $usr_id , 'user_name' => $usr_name , 'email' => $usr_email , 'date' => $usr_registered_date , 'role' => $usr_role , 'login_time' => $login_time , 'logout_time' => $logout_time , 'status' => $status) );

		$user_list = get_users();

		foreach($user_list as $usr_key => $usr_value)
		{
			global $wpdb;
			$usr_id = $usr_value->data->ID;
			$usr_name = $usr_value->data->display_name;
			$usr_email = $usr_value->data->user_email;
			$login = get_user_meta($usr_id,'loginTime');
			$login_time = isset($login[0]) ? $login[0] : "";
			$logout = get_user_meta($usr_id,'logoutTime');
			$logout_time = isset($logout[0]) ? $logout[0] : "";
			$usr_registered_date = $usr_value->data->user_registered;
			$usr_role =  $usr_value->roles[0];
			$existing_users = $wpdb->get_results($wpdb->prepare("select user_id from wci_user_profile_updated where user_id=%d" , $usr_id));
			if(empty($existing_users)){
				$wpdb->insert( 'wci_user_profile_updated' , array( 'user_id' => $usr_id , 'user_name' => $usr_name , 'email' => $usr_email , 'date' => $usr_registered_date , 'role' => $usr_role , 'login_time' => $login_time , 'logout_time' => $logout_time ) );

			}
			else{
				$wpdb->update( 'wci_user_profile_updated' , array( 'user_name' => $usr_name , 'email' => $usr_email , 'date' => $usr_registered_date , 'role' => $usr_role , 'login_time' => $login_time , 'logout_time' => $logout_time ) , array( 'user_id' => $usr_id ) );

			}
		}
	} // END Logout
}

add_action('wp_login', array( 'WCI_NoAjaxhookCalls' , 'wci_last_login_time'));
add_action('wp_logout', array( 'WCI_NoAjaxhookCalls' , 'wci_time_on_logout'));
