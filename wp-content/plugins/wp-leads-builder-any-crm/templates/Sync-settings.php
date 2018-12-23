<?php
/******************************************************************************************
 * Copyright (C) Smackcoders 2016 - All Rights Reserved
 * Unauthorized copying of this file, via any medium is strictly prohibited
 * Proprietary and confidential
 * You can contact Smackcoders at email address info@smackcoders.com.
 *******************************************************************************************/
if ( ! defined( 'ABSPATH' ) )
        exit; // Exit if accessed directly

$module = sanitize_text_field( $_REQUEST['module'] );
$duplicate_option = sanitize_text_field( $_REQUEST['duplicate_handling'] );
$activated_crm = get_option( 'WpLeadBuilderProActivatedPlugin' );
$current_config = $exist_config = array();
switch( $activated_crm )
{
        case 'wptigerpro':
                $exist_config = get_option( "smack_{$activated_crm}_user_capture_settings" );
                if( !empty( $module ))
                {
                        $current_config['user_sync_module'] = $module;
                        $current_config['smack_capture_duplicates'] = $exist_config['smack_capture_duplicates'];
                }

                if( !empty( $duplicate_option  ))
                {
                        $current_config['user_sync_module'] = $exist_config['user_sync_module'];
                        $current_config['smack_capture_duplicates'] = $duplicate_option;
                }
                update_option("smack_{$activated_crm}_user_capture_settings", $current_config);
                break;

        case 'wpsugarpro':
                $exist_config = get_option( "smack_{$activated_crm}_user_capture_settings" );
                if( !empty( $module ))
                {
                        $current_config['user_sync_module'] = $module;
                        $current_config['smack_capture_duplicates'] = $exist_config['smack_capture_duplicates'];
                }

                if( !empty( $duplicate_option  ))
                {
                        $current_config['user_sync_module'] = $exist_config['user_sync_module'];
                        $current_config['smack_capture_duplicates'] = $duplicate_option;
                }
                update_option("smack_{$activated_crm}_user_capture_settings", $current_config);
                break;

	case 'wpsuitepro':
                $exist_config = get_option( "smack_{$activated_crm}_user_capture_settings" );
                if( !empty( $module ))
                {
                        $current_config['user_sync_module'] = $module;
                        $current_config['smack_capture_duplicates'] = $exist_config['smack_capture_duplicates'];
                }

                if( !empty( $duplicate_option  ))
                {
                        $current_config['user_sync_module'] = $exist_config['user_sync_module'];
                        $current_config['smack_capture_duplicates'] = $duplicate_option;
                }
                update_option("smack_{$activated_crm}_user_capture_settings", $current_config);
                break;

        case 'wpzohopro':
                $exist_config = get_option( "smack_{$activated_crm}_user_capture_settings" );
                if( !empty( $module ))
                {
                        $current_config['user_sync_module'] = $module;
                        $current_config['smack_capture_duplicates'] = $exist_config['smack_capture_duplicates'];
                }

                if( !empty( $duplicate_option  ))
                {
                        $current_config['user_sync_module'] = $exist_config['user_sync_module'];
                        $current_config['smack_capture_duplicates'] = $duplicate_option;
                }
                update_option("smack_{$activated_crm}_user_capture_settings", $current_config);
                break;

	case 'wpzohopluspro':
                $exist_config = get_option( "smack_{$activated_crm}_user_capture_settings" );
                if( !empty( $module ))
                {
                        $current_config['user_sync_module'] = $module;
                        $current_config['smack_capture_duplicates'] = $exist_config['smack_capture_duplicates'];
                }

                if( !empty( $duplicate_option  ))
                {
                        $current_config['user_sync_module'] = $exist_config['user_sync_module'];
                        $current_config['smack_capture_duplicates'] = $duplicate_option;
                }
                update_option("smack_{$activated_crm}_user_capture_settings", $current_config);
                break;

        case 'wpsalesforcepro' :
                $exist_config = get_option( "smack_{$activated_crm}_user_capture_settings" );
                if( !empty( $module ))
                {
                        $current_config['user_sync_module'] = $module;
                        $current_config['smack_capture_duplicates'] = $exist_config['smack_capture_duplicates'];
                }

                if( !empty( $duplicate_option  ))
                {
                        $current_config['user_sync_module'] = $exist_config['user_sync_module'];
                        $current_config['smack_capture_duplicates'] = $duplicate_option;
                }
                update_option("smack_{$activated_crm}_user_capture_settings", $current_config);
                break;
        case 'freshsales' :
                $exist_config = get_option( "smack_{$activated_crm}_user_capture_settings" );
                if( !empty( $module ))
                {
                        $current_config['user_sync_module'] = $module;
                        $current_config['smack_capture_duplicates'] = $exist_config['smack_capture_duplicates'];
                }

                if( !empty( $duplicate_option  ))
                {
                        $current_config['user_sync_module'] = $exist_config['user_sync_module'];
                        $current_config['smack_capture_duplicates'] = $duplicate_option;
                }
                update_option("smack_{$activated_crm}_user_capture_settings", $current_config);
                break;
}
