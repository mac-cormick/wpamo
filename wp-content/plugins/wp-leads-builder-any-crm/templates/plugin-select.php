<?php

/******************************************************************************************
 * Copyright (C) Smackcoders 2016 - All Rights Reserved
 * Unauthorized copying of this file, via any medium is strictly prohibited
 * Proprietary and confidential
 * You can contact Smackcoders at email address info@smackcoders.com.
 *******************************************************************************************/
if ( ! defined( 'ABSPATH' ) )
        exit; // Exit if accessed directly

	$selectedPlugin= sanitize_text_field($_REQUEST['postdata']);
	update_option('WpLeadBuilderProActivatedPlugin',$selectedPlugin);

	switch($selectedPlugin) {
		// case 'wptigerpro':
		// 	require_once(SM_LB_PRO_DIR . "admin/views/form-vtigercrmconfig.php");
		// break;
		
		// case 'wpsugarpro':
		// case 'wpsuitepro':
		// 	 require_once(SM_LB_PRO_DIR . "admin/views/form-sugarcrmconfig.php");
  //               break;

		// case 'wpzohopro':
		// case 'wpzohopluspro':
  //                        require_once(SM_LB_PRO_DIR . "admin/views/form-zohocrmconfig.php");
  //               break;

		// case 'freshsales':
  //                       require_once(SM_LB_PRO_DIR . "admin/views/form-freshsalescrmconfig.php");
  //               break;

}
?>
