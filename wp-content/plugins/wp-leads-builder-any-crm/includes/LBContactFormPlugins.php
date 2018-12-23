<?php

/******************************************************************************************
 * Copyright (C) Smackcoders 2016 - All Rights Reserved
 * Unauthorized copying of this file, via any medium is strictly prohibited
 * Proprietary and confidential
 * You can contact Smackcoders at email address info@smackcoders.com.
 *******************************************************************************************/
if ( ! defined( 'ABSPATH' ) )
        exit; // Exit if accessed directly

require_once(plugin_dir_path(__FILE__).'/../ConfigureIncludedPlugins.php');
class ContactFormPROPlugins
{
	public function getActivePlugin()
	{
		return get_option('WpLeadBuilderProActivatedPlugin');
	}

	public function getThirdPLugin()	{
		return get_option('WpLeadThirdPartyPLugin');
	}

	public function getMappingModule()
	{
		return get_option( 'WpMappingModule' );
	}

	public function mappingModule()
	{
		global $WpMappingModule;
		$html_map = '<span>  <select  name = "mappingmodule" id ="mappingmodule" onchange="mappingModulePRO( this )">';
                   $select_option = "";
                foreach($WpMappingModule as $moduleslug => $modulelabel)
                {
                        if($this->getMappingModule() == $moduleslug )
                        {
                                $select_option .= "<option value='{$moduleslug}' selected=selected > {$modulelabel} </option>";
                        }
                        else
                        {
                                $select_option .= "<option value='{$moduleslug}' > {$modulelabel} </option>" ;
                        }
                }
                $html_map .= $select_option;
                $html_map .= "</select></span>";
                return $html_map;

		
	}

	public function getCustomFieldPlugins( ) {
		global $custom_plugins;
		 $custom_plugin = get_option( "custom_plugin" );
		$htm = "<span>  <select class='selectpicker form-control' name = 'custom_fields' id ='custom_fields'  onchange='selectedcustomPRO( this  )'>";
		  $selected_option = "";
        	  foreach( $custom_plugins as $customslug => $customlabel )
                  {      
             		if( $custom_plugin == $customslug )
			{
                                $selected_option .= "<option value='{$customslug}' selected=selected > {$customlabel} </option>";
                        }
			else
			{	 
                                $selected_option .= "<option value='{$customslug}' disabled> {$customlabel}  </option>";
			}
		  }
                $htm .= $selected_option;
                $htm .= "</select></span>";
                return $htm;
}

	public function get_ecom_assignedto($shortcode_option)
        {
                //Assign Leads And Contacts to User
        $crm_users_list = get_option( 'crm_users' );
        $activated_crm = get_option( 'WpLeadBuilderProActivatedPlugin' );
        $assignedtouser_config = get_option( $shortcode_option );
        $assignedtouser_config_leads = $assignedtouser_config['thirdparty_assignedto'];
        $Assigned_users_list = $crm_users_list[$activated_crm];
        switch( $activated_crm )
        {
        case 'wpzohopro':
                $html_leads = "";
                $html_leads = '<select style="width:150px;" name="mapping_assignedto" id="mapping_assignedto">';
                $content_option_leads = "";
                $content_option_leads = "<option id='select' value='--Select--'>--Select--</option>";
                if(isset($Assigned_users_list['user_name']))
                for($i = 0; $i < count($Assigned_users_list['user_name']) ; $i++)
                {
                        $content_option_leads.="<option id='{$Assigned_users_list['user_name'][$i]}' value='{$Assigned_users_list['id'][$i]}'";
                        if($Assigned_users_list['id'][$i] == $assignedtouser_config_leads )
                        {
                                $content_option_leads .=" selected";
                        }

                        $content_option_leads .=">{$Assigned_users_list['user_name'][$i]}</option>";
                }
                $html_leads .= $content_option_leads;
                $html_leads .= "</select> <span style='padding-left:15px; color:red;' id='assignedto_status'></span>";
                return $html_leads;
                break;

		case 'wptigerpro':
                $html_leads = "";
                $html_leads = '<select style="width:150px;" name="mapping_assignedto" id="mapping_assignedto" style="min-width:69px;">';
                $content_option_leads = "";

                $content_option_leads = "<option id='select' value='--Select--'>--Select--</option>";

                if(isset($Assigned_users_list['user_name']))
                        for($i = 0; $i < count($Assigned_users_list['user_name']) ; $i++)
                        {
                                $content_option_leads .="<option id='{$Assigned_users_list['id'][$i]}' value='{$Assigned_users_list['id'][$i]}'";
                                if($Assigned_users_list['id'][$i] == $assignedtouser_config_leads)
                                {
                                        $content_option_leads .=" selected";
                                }

                                $content_option_leads .=">{$Assigned_users_list['first_name'][$i]} {$Assigned_users_list['last_name'][$i]}</option>";
                        }
                $html_leads .= $content_option_leads;
                $html_leads .= "</select> <span style='padding-left:15px; color:red;' id='assignedto_status'></span>";
                return $html_leads;
                break;
		
		case 'wpsugarpro':
        $html_leads = "";
                $html_leads = '<select style="width:150px;" name="mapping_assignedto" id="mapping_assignedto" style="min-width:69px;">';
                $content_option_leads = "";

                $content_option_leads = "<option id='select' value='--Select--'>--Select--</option>";
                if(isset($Assigned_users_list['user_name']))
                for($i = 0; $i < count($Assigned_users_list['user_name']) ; $i++)
                {
                        $content_option_leads .="<option id='{$Assigned_users_list['id'][$i]}' value='{$Assigned_users_list['id'][$i]}'";

                        if($Assigned_users_list['id'][$i] == $assignedtouser_config_leads)
                        {
                                $content_option_leads .=" selected";

                        }

                        $content_option_leads .=">{$Assigned_users_list['first_name'][$i]} {$Assigned_users_list['last_name'][$i]}</option>";
                }
                $html_leads .= $content_option_leads;
                $html_leads .= "</select> <span style='padding-left:15px; color:red;' id='assignedto_status'></span>";
                return $html_leads;
                break;

		case 'wpsalesforcepro':
                $html_leads = "";
                $html_leads = '<select style="width:150px;" name="mapping_assignedto" id="mapping_assignedto" style="min-width:69px;">';
                $content_option_leads = "";

                $content_option_leads = "<option id='select' value='--Select--'>--Select--</option>";
                if(isset($users_list['user_name']))
                for($i = 0; $i < count($Assigned_users_list['user_name']) ; $i++)
                {
                        $content_option_leads .="<option id='{$Assigned_users_list['user_name'][$i]}' value='{$Assigned_users_list['id'][$i]}'";
                        if($Assigned_users_list['id'][$i]== $assignedtouser_config_leads)
                        {
                                $content_option_leads .=" selected";
                        }

                        $content_option_leads .=">{$Assigned_users_list['user_name'][$i]}</option>";
                }
                $html_leads .= $content_option_leads ;
                $html_leads .= "</select> <span style='padding-left:15px; color:red;' id='assignedto_status'></span>";
                return $html_leads;
                break;

                }
        }


	public function getPluginActivationHtml( )
	{
		global $IncludedPluginsPRO;
		global $crmdetailsPRO;
                $html = '<span>  <select class="form-control selectpicker" data-size="5" style="margin-left:63px;" name = "pluginselect" id ="pluginselect" onchange="selectedPlugPRO( this )">'
;
		$select_option = "";
                foreach($IncludedPluginsPRO as $pluginslug => $pluginlabel)
                {
                        if($this->getActivePlugin() == $pluginslug )
			{
                                $select_option .= "<option value='{$pluginslug}' selected=selected > {$pluginlabel} </option>";
                        }
                        else
                        {
                                $select_option .= "<option value='{$pluginslug}' > {$pluginlabel} </option>" ;
                        }
                }
                $html .= $select_option;
                $html .= "</select></span>";
		return $html;
	}
}
?>
