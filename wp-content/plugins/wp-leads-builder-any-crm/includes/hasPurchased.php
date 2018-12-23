<?php

/*********************************************************************************
 * Woo Customer Insight helps you to track customer events and journey in your site
 * plugin developed by Smackcoder. Copyright (C) 2016 Smackcoders.
 *
 * Woo Customer Insight is a free software; you can redistribute it and/or
 * modify it under the terms of the GNU Affero General Public License version 3
 * as published by the Free Software Foundation with the addition of the
 * following permission added to Section 15 as permitted in Section 7(a): FOR
 * ANY PART OF THE COVERED WORK IN WHICH THE COPYRIGHT IS OWNED BY Woo Customer 
   Insight, Woo Customer Insight DISCLAIMS THE WARRANTY OF NON
 * INFRINGEMENT OF THIRD PARTY RIGHTS.
 *
 * Woo Customer Insight is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY
 * or FITNESS FOR A PARTICULAR PURPOSE.  See the GNU Affero General Public
 * License for more details.
 *
 * You should have received a copy of the GNU Affero General Public License
 * along with this program; if not, see http://www.gnu.org/licenses or write
 * to the Free Software Foundation, Inc., 51 Franklin Street, Fifth Floor,
 * Boston, MA 02110-1301 USA.
 *
 * You can contact Smackcoders at email address info@smackcoders.com.
 *
 * The interactive user interfaces in original and modified versions
 * of this program must display Appropriate Legal Notices, as required under
 * Section 5 of the GNU Affero General Public License version 3.
 *
 * In accordance with Section 7(b) of the GNU Affero General Public License
 * version 3, these Appropriate Legal Notices must retain the display of the
 * Woo Customer Insight copyright notice. If the display of the logo is
 * not reasonably feasible for technical reasons, the Appropriate Legal
 * Notices must display the words
 * "Copyright Smackcoders. 2016. All rights reserved".
 ********************************************************************************/
if ( ! defined( 'ABSPATH' ) ) {
        exit; // Exit if accessed directly
}

	global $wpdb;
	$post_title = $wpdb->get_results($wpdb->prepare("select post_title from ".$wpdb->prefix."posts where post_type=%s", 'product'));
	$prod_list = array();
	foreach( $post_title as $title=>$prod)
	{
    		foreach( $prod as $key=>$productt )
    		{
        		$prod_list[] = $productt;
    		}
	}
	$prod_id = $wpdb->get_results($wpdb->prepare("select ID from ".$wpdb->prefix."posts where post_type=%s" , 'product'));
	$prodid_list = array();
	foreach( $prod_id as $ke=>$ID)
	{
    		foreach( $ID as $pdt_key=>$prodid )
    		{
        		$prodid_list[] = $prodid;
    		}
	}

	$items = array_combine( $prodid_list , $prod_list );
	global $wpdb;
	$users = $wpdb->get_results( "select distinct user_id,email from wci_history",ARRAY_A );
	foreach($users as $userkey=>$userval )
	{
    		foreach( $items as $pid => $pname )
    		{
        		$user_id = $userval['user_id'];
        		$customer_email = $userval['email'];
        		$user_ip = $_SERVER['REMOTE_ADDR'];
        		if ( wc_customer_bought_product( $customer_email, $user_id, $pid) )
        		{
            			$email_list = array();
				$update_user_purchased = $wpdb->get_results($wpdb->prepare("select user_email,product_id from wci_user_purchased_history where user_email=%s AND product_id=%d" , $customer_email , $pid));
				
            			if(empty($update_user_purchased)){
                		
			$wpdb->insert( 'wci_user_purchased_history' , array( 'user_ip' => $user_ip , 'user_id' => $user_id , 'user_email' => $customer_email , 'product_id' => $pid , 'product_name' => $pname ) );		
            			}
            			else {

                		$wpdb->query("update wci_user_purchased_history set user_ip = $user_ip,user_id='$user_id', user_email = $customer_email,product_id = $pid, product_name = $pname");
            		    	}
        		}
    		}
}
