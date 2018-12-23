<?php

function checkActiveOrNot($plugin)
{
	include_once(ABSPATH.'wp-admin/includes/plugin.php');
	$active_plugins_hook = get_option( "active_plugins" );

	switch ($plugin) {
		case 'vtiger':
		     $hook = 'wp-tiger/index.php';
		     $mode['link'] = 'https://wordpress.org/plugins/wp-tiger/';
		     break;
		   case 'zoho':
		     $hook = 'wp-zoho-crm/index.php';
		     $mode['link'] = 'https://wordpress.org/plugins/wp-zoho-crm/';
		     break; 
		   case 'sforce':
		     $hook = 'wp-salesforce/index.php';
		     $mode['link'] = 'https://wordpress.org/plugins/wp-salesforce/';
		     break;
		   case 'fsales':
		     $hook = 'wp-freshsales/index.php';
		     $mode['link'] = 'https://wordpress.org/plugins/wp-freshsales/';
		     break;
		   case 'sugar':
		     $hook = 'wp-sugar-free/index.php';
		     $mode['link'] = 'https://wordpress.org/plugins/wp-sugar-free/';
		     break;
	}



	$dir = ABSPATH.'wp-content/plugins/'.$hook;
 			if(is_file($dir)){
 				$mode['text'] = 'activate addon';
 			}
 			else{
 				$mode['text'] = 'get addon';
 			}

 	if(in_array($hook, $active_plugins_hook)){
		$mode['disable'] = 'disabled';
		$mode['text'] = 'active';
	}
	else{
		$mode['disable'] = '';
	}

 	return $mode;
}

$vt_btn = checkActiveOrNot('vtiger');
$zoho_btn = checkActiveOrNot('zoho');
$sforce_btn = checkActiveOrNot('sforce');
$fsales_btn = checkActiveOrNot('fsales');
$sugar_btn = checkActiveOrNot('sugar');

function migrate_leadbuild_addon($link) {

  require_once(ABSPATH . 'wp-admin/includes/class-wp-upgrader.php');
        $plugin['source'] = $link;
      $source = ( isset($type) && 'upload' == $type ) ? $this->default_path . $plugin['source'] : $plugin['source'];
      /** Create a new instance of Plugin_Upgrader */
      $upgrader = new Plugin_Upgrader( $skin = new Plugin_Installer_Skin( compact( 'type', 'title', 'url', 'nonce', 'plugin', 'api' ) ) );
    /** Perform the action and install the plugin from the $source urldecode() */
      $upgrader->install( $source );
    /** Flush plugins cache so we can make sure that the installed plugins list is always up to date */
      wp_cache_flush();
        $plugin_activate = $upgrader->plugin_info(); // Grab the plugin info from the Plugin_Upgrader method
  $activate = activate_plugin( $plugin_activate ); // Activate the plugin
  if ( !is_wp_error( $activate ) )
                //deactivate_plugins('wp-tiger/wp-tiger.php');//Deactivate tiger plugin
    //  $this->populate_file_path(); // Re-populate the file path now that the plugin has been installed and activated
    if ( is_wp_error( $activate ) ) {
          echo '<div id="message" class="error"><p>' . $activate->get_error_message() . '</p></div>';
             // echo '<p><a class="btn btn-primary" href="admin.php?page=lb-crmconfig">Return</a></p>';
              return true; // End it here if there is an error with automatic activation
        }
      else {
            //echo '<p>' . $this->strings['plugin_activated'] . '</p>';
      }
      die();

}
if(isset($_POST) && isset($_POST['addon_to_install'])){

		switch ($_POST['addon_to_install']) {
		   case 'vtiger':
		     $link = 'https://downloads.wordpress.org/plugin/wp-tiger.3.4.zip';
		     $hook = 'wp-tiger/index.php';
		     break;
		   case 'zoho':
		     $link = 'https://downloads.wordpress.org/plugin/wp-zoho-crm.1.4.zip';
		     $hook = 'wp-zoho-crm/index.php';
		     break; 
		   case 'sforce':
		     $link = 'https://downloads.wordpress.org/plugin/wp-salesforce.1.0.zip';
		     $hook = 'wp-salesforce/index.php';
		     break;
		   case 'fsales':
		     $link = 'https://downloads.wordpress.org/plugin/wp-freshsales.1.0.zip';
		     $hook = 'wp-freshsales/index.php';
		     break;
		   case 'sugar':
		     $link = 'https://downloads.wordpress.org/plugin/wp-sugar-free.1.4.zip';
		     $hook = 'wp-sugar-free/index.php';
		     break;
		 }

 $dir = ABSPATH.'wp-content/plugins/'.$hook;
 if(is_file($dir)){
  include_once(ABSPATH.'wp-admin/includes/plugin.php');
   $activate = activate_plugin($hook);
  // print_r($activate);
   if ( !is_wp_error( $activate ) ){
    echo "<script>location.reload();</script>";
   }
 }else{
?> 
<div class="panel" style="width: 99%" >
<div class="panel-body">
	<h4>Installing.... </h4>
	<hr>
	<div style="height: auto;min-height: 200px">
<?php
 // migrate_leadbuild_addon($link);
  ?>
 </div>
 </div>
 </div>
  <?php
  die();

 }

 
	//print_r($_POST['addon_to_install']);

}
?>

<div class="panel" style="width: 99%;margin-top: 20px" id="all_addons_view" >
<div class="panel-body">
	<h4>More Addons, choose one that suites your need</h4>
	<hr>
	<!-- <div>
		 More Addons, choose one that suites your need
	</div> -->
	<div style="width: 20%;height: 100px;padding: 2%;float: left;">
		<div style="border: 1px solid #c9c7c7;height: 100%;padding: 5px;text-align: center;">
		<a target='blank' href="<?php echo $vt_btn['link']; ?>" style="cursor: pointer;">
			<img src="<?php echo SM_LB_DIR?>assets/images/vtiger-logo.png" style="height: 42px;padding-top: 10px">
			</a>
			
		</div>
	</div>
	<div style="width: 20%;height: 100px;padding: 2%;float: left;">
		<div style="border: 1px solid #c9c7c7;height: 100%;padding: 5px;text-align: center;">
		<a target='blank' href="<?php echo $zoho_btn['link']; ?>" style="cursor: pointer;">
			<img src="<?php echo SM_LB_DIR?>assets/images/zohocrm-logo.png" style="height: 42px;padding-top: 10px">
			</a>
			
		</div>
	</div>
	<div style="width: 20%;height: 100px;padding: 2%;float: left;">
		<div style="border: 1px solid #c9c7c7;height: 100%;padding: 5px;text-align: center;">
		<a target='blank' href="<?php echo $sugar_btn['link']; ?>" style="cursor: pointer;">
			<img src="<?php echo SM_LB_DIR?>assets/images/sugarcrm-logo.png" style="height: 42px;padding-top: 10px">
		</a>
			
		</div>
	</div>
	<div style="width: 20%;height: 100px;padding: 2%;float: left;">
		<div style="border: 1px solid #c9c7c7;height: 100%;padding: 5px;text-align: center;">
		<a target='blank' href="<?php echo $sforce_btn['link']; ?>" style="cursor: pointer;">
			<img src="<?php echo SM_LB_DIR?>assets/images/salesforce-logo.png" style="width: 90%;height: 45px;padding-top: 10px">
			</a>
		</div>
	</div>
	<div style="width: 20%;height: 100px;padding: 2%;float: left;">
		<div style="border: 1px solid #c9c7c7;height: 100%;padding: 5px;text-align: center;">
		<a target='blank' href="<?php echo $fsales_btn['link']; ?>" style="cursor: pointer;">
			<img src="<?php echo SM_LB_DIR?>assets/images/freshsales-logo.png" style="height: 42px;padding-top: 10px">
		</a>	
		</div>
	</div>
</div>	
</div>
