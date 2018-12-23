<?php
/*********************************************************************************
Plugin Name: WP Zoho CRM
Plugin URI: http://www.smackcoders.com
Description: Easy Lead capture Zoho Crm Webforms
Version: 1.4
Author: smackcoders.com
Author URI: http://www.smackcoders.com

 * Easy Lead capture Vtiger Webforms and Contacts synchronization is a tool
 * for capturing leads and contacts to VtigerCRM from WordPress developed by
 * Smackcoder. Copyright (C) 2013 Smackcoders.
 *
 * Easy Lead capture Vtiger Webforms and Contacts synchronization is free
 * software; you can redistribute it and/or modify it under the terms of the GNU
 * Affero General Public License version 3 as published by the Free Software
 * Foundation with the addition of the following permission added to Section 15
 * as permitted in Section 7(a): FOR ANY PART OF THE COVERED WORK IN WHICH THE
 * COPYRIGHT IS OWNED BY Smackcoders, FEasy Lead capture Vtiger Webforms and
 * Contacts synchronization  DISCLAIMS THE WARRANTY OF NON INFRINGEMENT OF THIRD
 * PARTY RIGHTS.
 *
 * Easy Lead capture Vtiger Webforms and Contacts synchronization is
 * distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY;
 * without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR
 * PURPOSE.  See the GNU Affero General Public License for more details.
 *
 * You should have received a copy of the GNU Affero General Public License along with
 * this program; if not, see http://www.gnu.org/licenses or write to the Free
 * Software Foundation, Inc., 51 Franklin Street, Fifth Floor, Boston, MA
 * 02110-1301 USA.
 *
 * You can contact Smackcoders at email address info@smackcoders.com.
 *
 * The interactive user interfaces in original and modified versions
 * of this program must display Appropriate Legal Notices, as required under
 * Section 5 of the GNU Affero General Public License version 3.
 *
 * In accordance with Section 7(b) of the GNU Affero General Public License version 3,
 * these Appropriate Legal Notices must retain the display of the Easy Lead capture
 * Vtiger Webforms and Contacts synchronization copyright notice. If the
 * display of the logo is not reasonably feasible for technical reasons, the
 * Appropriate Legal Notices must display the words "Copyright Smackcoders. 2013.
 * All rights reserved".
 ********************************************************************************/
if ( ! defined( 'ABSPATH' ) )
	exit; // Exit if accessed directly

class ZohoCrmSmLBHandler {

	public $version = '1.4';

	protected static $_instance = null;

	/**
	 * Main WPLeadsBuilderForAnyCRMPro Instance.
	 *
	 * Ensures only one instance of WPLeadsBuilderForAnyCRMPro is loaded or can be loaded.
	 *
	 * @since 4.5
	 * @static
	 * @return ZohoCrmSmLBHandler - Main instance.
	 */
	public static function instance() {
		if ( is_null( self::$_instance ) ) {
			self::$_instance = new self();
		}
		return self::$_instance;
	}

	public function __construct() {
		$this->define_constants();
		$this->includes();

		add_action( 'init', array( $this, 'frontend_init_pro') );
		$this->init();
		$this->init_hooks();
		$active_plugins = get_option( "active_plugins" );

	}

	private function init_hooks() {
		//register_activation_hook(__FILE__, array('WPCapture_includes_helper_PRO', 'activate') );
		//register_deactivation_hook(__FILE__, array('WPCapture_includes_helper_PRO', 'deactivate') );

		add_action( 'plugins_loaded', array( $this, 'init' ), 0 );
		add_filter( 'plugin_action_links_' . plugin_basename( __FILE__ ),  array($this, 'lb_plugin_row_meta'), 10, 2 );

		//User sync - on time creation
		$check_sync_value = get_option( 'Sync_value_on_off' );
		
		register_activation_hook(__FILE__, array($this, 'activate'));

		if(!function_exists('admin_notice_zoho_addon')){
			function admin_notice_zoho_addon() {
				global $pagenow;
				$active_plugins = get_option( "active_plugins" );
			    if ( $pagenow == 'plugins.php' && !in_array('wp-leads-builder-any-crm/index.php', $active_plugins) ) {
				    ?>
				    <div class="notice notice-warning is-dismissible" >
				        <p> Wp Zoho CRM is an addon of <a href="https://goo.gl/BfCFJC" target="blank" style="cursor: pointer;text-decoration:none">WP Leads Builder for CRM</a> plugin, kindly install it to continue using WP Form to CRM integration. </p>
				        
				    </div>
				    <?php 
			   }
			}
        }

		add_action( 'admin_notices', 'admin_notice_zoho_addon' );
	}

	public function activate()
	{
		update_option('WpLeadBuilderProActivatedPlugin','wpzohopro');
	}

	public function define_constants() {
		$this->define( 'SM_LB_ZOHO_PLUGIN_FILE', __FILE__ );
		$this->define( 'SM_LB_ZOHO_PLUGIN_BASENAME', plugin_basename( __FILE__ ) );
		$this->define( 'SM_LB_ZOHO_DIR', plugin_dir_path(__FILE__));
		$this->define( 'SM_LB_ZOHO_SLUG', 'wp-zoho-crm' );
		$this->define( 'SM_LB_ZOHO_DIR', WP_PLUGIN_URL . '/' .SM_LB_ZOHO_SLUG. '/');
		$this->define( 'SM_LB_ZOHO_SETTINGS', 'WP Zoho Crm' );
		$this->define( 'SM_LB_ZOHO_VERSION', '1.4');
		$this->define( 'SM_LB_ZOHO_NAME', 'WP Zoho Crm' );
		$this->define( 'SM_LB_ZOHO_URL',site_url().'/wp-admin/admin.php?page='.SM_LB_ZOHO_SLUG.'/index.php');
	}

	public function define( $name, $value ) {
		if ( ! defined( $name ) ) {
			define( $name, $value );
		}
	}

	public function init() {
		if(is_admin()) :
			// Init action.
			do_action( 'uci_init' );
			if(is_admin()) {
				//include_once('includes/LB_admin_ajax.php');
				//ZohoCrmSmLBHelperAjax::smlb_ajax_events();
			}
		endif;
	}

	public function includes() {
		include_once ( 'admin/lb-admin.php' );
	}



	public static function lb_plugin_row_meta( $links, $file ) {
		if ( $file == SM_LB_ZOHO_PLUGIN_BASENAME ) {
			$row_meta = array(
				'support'  => '<a href="' . esc_url( apply_filters( 'SM_LB_ZOHO_support_url', 'https://www.smackcoders.com/support.html?utm_source=lead_builder_free&utm_campaign=plugin_menu&utm_medium=plugin' ) ) . '" title="' . esc_attr( __( 'Contact Support', 'wp-zoho-crm' ) ) . '" target="_blank">' . __( 'Support', 'wp-zoho-crm' ) . '</a>',
			);
			unset( $links['edit'] );
			return array_merge( $row_meta, $links );
		}
	}

	public static function frontend_init_pro()
	{
		if(!is_admin())
		{
			global $HelperObj;
		
			wp_enqueue_script('jquery-ui-datepicker');
			wp_enqueue_style('jquery-ui' , plugins_url('assets/css/jquery-ui.css', __FILE__) );
			wp_enqueue_style('front-end-styles' , plugins_url('assets/css/frontendstyles.css', __FILE__) );
			wp_enqueue_style('datepicker' , plugins_url('assets/css/datepicker.css', __FILE__) );
		}
	}

  public function includeFunction()
  {
   require_once("includes/wpzohoproFunctions.php");
  }
}
function ZohoCRMSmackLB() {
	return ZohoCrmSmLBHandler::instance();
}
// Global for backwards compatibility.
$GLOBALS['wp_leads_builder_for_any_crm'] = ZohoCRMSmackLB();
