<?php
if ( ! defined( 'ABSPATH' ) )
        exit; // Exit if accessed directly

$wp_user_module = sanitize_text_field($_REQUEST['module']);
$wp_user_active_crm = sanitize_text_field($_REQUEST['active_crm'] );
$wp_assigned_to = sanitize_text_field($_REQUEST['assigned_to']);

$wp_exist_arr = $wp_current_arr = array();
switch( $wp_user_active_crm )
{
	case 'wptigerpro':
		$wp_exist_arr = get_option( "smack_{$wp_user_active_crm}_usersync_assignedto_settings" );
		$wp_exist_arr['usersync_assign_leads'] = $wp_assigned_to;
		update_option( "smack_{$wp_user_active_crm}_usersync_assignedto_settings" , $wp_exist_arr);
		break;
	case 'wpsugarpro':
		$wp_exist_arr = get_option( "smack_{$wp_user_active_crm}_usersync_assignedto_settings" );
		$wp_exist_arr['usersync_assign_leads'] = $wp_assigned_to;
		update_option( "smack_{$wp_user_active_crm}_usersync_assignedto_settings" , $wp_exist_arr);
		break;
	 case 'wpsuitepro':
                $wp_exist_arr = get_option( "smack_{$wp_user_active_crm}_usersync_assignedto_settings" );
                $wp_exist_arr['usersync_assign_leads'] = $wp_assigned_to;
                update_option( "smack_{$wp_user_active_crm}_usersync_assignedto_settings" , $wp_exist_arr);
                break;
	case 'wpzohopro':
		$wp_exist_arr = get_option( "smack_{$wp_user_active_crm}_usersync_assignedto_settings" );
		$wp_exist_arr['usersync_assign_leads'] = $wp_assigned_to;
		update_option( "smack_{$wp_user_active_crm}_usersync_assignedto_settings" , $wp_exist_arr );
		break;
	case 'wpzohopluspro':
                $wp_exist_arr = get_option( "smack_{$wp_user_active_crm}_usersync_assignedto_settings" );
                $wp_exist_arr['usersync_assign_leads'] = $wp_assigned_to;
                update_option( "smack_{$wp_user_active_crm}_usersync_assignedto_settings" , $wp_exist_arr );
                break;
	case 'wpsalesforcepro':
		$wp_exist_arr = get_option( "smack_{$wp_user_active_crm}_usersync_assignedto_settings" );
		$wp_exist_arr['usersync_assign_leads'] = $wp_assigned_to;
		update_option( "smack_{$wp_user_active_crm}_usersync_assignedto_settings" , $wp_exist_arr );
		break;
	case 'freshsales':
		$wp_exist_arr = get_option( "smack_{$wp_user_active_crm}_usersync_assignedto_settings" );
		$wp_exist_arr['usersync_assign_leads'] = $wp_assigned_to;
		update_option( "smack_{$wp_user_active_crm}_usersync_assignedto_settings" , $wp_exist_arr );
		break;
}
