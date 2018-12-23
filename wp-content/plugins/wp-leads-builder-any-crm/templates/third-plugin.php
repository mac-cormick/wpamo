<?php
if ( ! defined( 'ABSPATH' ) )
        exit; // Exit if accessed directly

/******************************************************************************************
 * Copyright (C) Smackcoders 2016 - All Rights Reserved
 * Unauthorized copying of this file, via any medium is strictly prohibited
 * Proprietary and confidential
 * You can contact Smackcoders at email address info@smackcoders.com.
 *******************************************************************************************/

	$selectedPlugin= sanitize_text_field($_REQUEST['postdata']);
	$active_plugins = get_option( "active_plugins" );
        switch( $selectedPlugin )
        {
                case 'ninjaform':
                        if( in_array( "ninja-forms/ninja-forms.php" , $active_plugins ) ) {
                        $activated = "yes" ;
			update_option('WpLeadThirdPartyPLugin' , $selectedPlugin );
			}
                        else {
                        $activated = "no" ;
			}
                break;

                case 'contactform':
                        if( in_array( "contact-form-7/wp-contact-form-7.php" , $active_plugins) ) {
                        $activated = "yes" ;
			update_option('WpLeadThirdPartyPLugin' , $selectedPlugin );
			}
                        else {
                        $activated = "no" ;
			}
                break;

                case 'gravityform':
                        if( in_array( "gravityforms/gravityforms.php" , $active_plugins) ) {
			update_option('WpLeadThirdPartyPLugin' , $selectedPlugin );
                        $activated = "yes" ;
			}
                        else {
                        $activated = "no" ;
			}
                break;
		case 'none':
			update_option('WpLeadThirdPartyPLugin' , $selectedPlugin );
			$activated = "yes";
			break;
        }
	print_r( $activated );die;
?>
