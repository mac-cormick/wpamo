<?php

/******************************************************************************************
 * Copyright (C) Smackcoders 2016 - All Rights Reserved
 * Unauthorized copying of this file, via any medium is strictly prohibited
 * Proprietary and confidential
 * You can contact Smackcoders at email address info@smackcoders.com.
 *******************************************************************************************/
if ( ! defined( 'ABSPATH' ) )
        exit; // Exit if accessed directly

class SaveCRMConfigActions {

    public function __construct()
    {
    }
    public function saveConfigAjax()
    {
        $data['REQUEST'] = $_REQUEST['posted_data'] ;
//	print_r($data['REQUEST']);die;
        $data['HelperObj'] = new WPCapture_includes_helper_PRO();
        //print_r($data['HelperObj']);die;
	$data['module'] = $data['HelperObj']->Module;
        $data['moduleslug'] = $data['HelperObj']->ModuleSlug;
        if($data['REQUEST']['active_plugin'] == 'wpsuitepro')
            $data['activatedplugin'] = 'wpsuitepro';
        else
            $data['activatedplugin'] = $data['HelperObj']->ActivatedPlugin;
        $data['activatedpluginlabel'] = $data['HelperObj']->ActivatedPluginLabel;
        $data['option'] = $data['options'] = "smack_{$data['activatedplugin']}_{$data['moduleslug']}_fields-tmp";
        $crmslug = str_replace( "pro" , "" , $data['activatedplugin'] );
        $crmslug = str_replace( "wp" , "" , $crmslug );
        $data['crm'] = $crmslug;
        $data['action'] = $data['activatedplugin']."Settings";
        if( isset($data['REQUEST']["posted"]) && ($data['REQUEST']["posted"] == "posted") )
        {
            $result = $this->saveSettings( $data );
            if($result['error'] == 1)
            {
                $data['display'] = $result['errormsg'];
            }
	    else if( $result['error'] == 11 )
	    {
		$data['display'] = $result['errormsg'];
	    }
            else
            {
                $data['display'] = "Settings Successfully Saved";
            }

            $final_result['display'] = $data['display'];
            $final_result['error'] = $result['error'];
            $final_result = json_encode( $final_result );
            print_r( $final_result);
            die;
        }
    }

    public function saveSettings( $request )
    {
        update_option("WpLeadBuilderProFirstTimeWarning" , "false");
        include( SM_LB_PRO_DIR .'templates/SaveConfigHelper.php');
        $saveCall = new SaveCRMConfig();
        $result = $saveCall->CheckCRMType( $request );
        return $result;
    }
}
$saveObj = new SaveCRMConfigActions();
$call = $saveObj->saveConfigAjax();
