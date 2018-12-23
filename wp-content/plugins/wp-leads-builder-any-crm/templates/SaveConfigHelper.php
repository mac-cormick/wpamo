<?php

/******************************************************************************************
 * Copyright (C) Smackcoders 2016 - All Rights Reserved
 * Unauthorized copying of this file, via any medium is strictly prohibited
 * Proprietary and confidential
 * You can contact Smackcoders at email address info@smackcoders.com.
 *******************************************************************************************/
if ( ! defined( 'ABSPATH' ) )
        exit; // Exit if accessed directly

class SaveCRMConfig
{
    public function CheckCRMType( $config )
    {
        $Activated_crm = $config['action'];
        switch( $Activated_crm )
        {
		case 'wptigerproSettings':
		    $tiger_obj = new VtigerCrmSmLBHelper();
			$save_result = $tiger_obj->tigerproSettings($config);
			return $save_result;
			break;
		case 'wpsugarproSettings':
			$sugar_obj = new SugarFreeSmLBAdmin();
			$save_result = $sugar_obj->sugarproSettings($config);
			return $save_result;
			break;
		case 'wpsuiteproSettings':
            $save_result = $this->suiteproSettings($config);
			return $save_result;
			break;
		case 'wpzohoproSettings':
            $zoho_obj = new ZohoCrmSmLBHelper();
			$save_result = $zoho_obj->zohoproSettings($config);
			return $save_result;
			break;
		case 'wpzohoplusproSettings':
            $zoho_obj = new ZohoCrmSmLBHelper();
			$save_result = $zoho_obj->zohoplusproSettings($config);
			return $save_result;
			break;
		case 'wpsalesforceproSettings':
            $sforce_obj = new SforceSmLBAdmin();
			$save_result = $sforce_obj->salesforceproSettings($config);
			return $save_result;
			break;
		case 'freshsalesSettings':
            $fsale_obj = new FsalesSmLBAdmin();
			$save_result = $fsale_obj->freshsalesSettings($config);
			return $save_result;
			break;
        }
    }


    public function suiteproSettings( $sugarSettArray )
    {
        $sugar_config_array = $sugarSettArray['REQUEST'];
        $fieldNames = array(
            'url' => __('Suite Host Address', SM_LB_URL ),
            'username' => __('Suite Username' , SM_LB_URL ),
            'password' => __('Suite Password' , SM_LB_URL ),
            'smack_email' => __('Smack Email'),
            'email' => __('Email id'),
            'emailcondition' => __('Emailcondition'),
            'debugmode' => __('Debug Mode'),
                    );

        foreach ($fieldNames as $field=>$value){
            if(isset($sugar_config_array[$field]))
            {
                $config[$field] = $sugar_config_array[$field];
            }
        }
	require_once(SM_LB_PRO_DIR . "includes/wpsuiteproFunctions.php");
        $FunctionsObj = new mainCrmHelper( );
        $testlogin_result = $FunctionsObj->testlogin( $config['url'] , $config['username'] , $config['password'] );
        if(isset($testlogin_result['login']) && ($testlogin_result['login']['id'] != -1) && is_array($testlogin_result['login']))
        {
            $successresult = "Settings Saved";
            $result['error'] = 0;
            $result['success'] = $successresult;
            $WPCapture_includes_helper_Obj = new WPCapture_includes_helper_PRO();
            $activateplugin = $WPCapture_includes_helper_Obj->ActivatedPlugin;
            update_option("wp_{$activateplugin}_settings", $config);
        }
        else
        {
            $sugarcrmerror = "Please Verify Your Suite CRM credentials";
            $result['error'] = 1;
            $result['errormsg'] = $sugarcrmerror ;
            $result['success'] = 0;
        }
        return $result;
        $WPCapture_includes_helper_Obj = new WPCapture_includes_helper_PRO();
        $activateplugin = $WPCapture_includes_helper_Obj->ActivatedPlugin;
        update_option("wp_{$activateplugin}_settings", $config);
    }
	
 
}
