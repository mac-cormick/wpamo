<?php

/******************************************************************************************
 * Copyright (C) Smackcoders 2016 - All Rights Reserved
 * Unauthorized copying of this file, via any medium is strictly prohibited
 * Proprietary and confidential
 * You can contact Smackcoders at email address info@smackcoders.com.
 *******************************************************************************************/
if ( ! defined( 'ABSPATH' ) )
        exit; // Exit if accessed directly
require_once('SmackContactFormGenerator.php');
add_action('wpcf7_before_send_mail','contact_forms_example');
function replace_key_function($all_fields, $key1, $key2)
{
    $keys = array_keys($all_fields);
    $index = array_search($key1, $keys);
    if ($index !== false) {
        $keys[$index] = $key2;
        $all_fields = array_combine($keys, $all_fields);
    }
    return $all_fields;
}

function getTextBetweenBrackets($post_content) {

                $data_type_array = array( 'text' , 'email' , 'date' , 'checkbox' , 'select' , 'url' , 'number' , 'textarea' , 'radio' , 'quiz' , 'file', 'acceptance' , 'hidden' , 'tel' , 'dynamichidden');

                $contact_labels = array();
                foreach( $data_type_array as $dt_key => $dt_val )
                {
                        $patternn = "(\[$dt_val(\s|\*\s)(.*)\])";
                        preg_match_all($patternn, $post_content, $matches);
                        if( !empty( $matches[1] ))
                        {
                                $contact_labels[] = $matches[0];
                        }

                        $i =0;
                        $merge_array = array();
                        foreach( $contact_labels as $cf7key => $cf7value )
                        {
                                foreach( $cf7value as $cf_get_key => $cf_get_fields )
                                {
                                $merge_array[] = $cf_get_fields;
                                }
                        }
                }
                return $merge_array;
        }


function contact_forms_example()
{
	global $wpdb,$HelperObj;
	$post_id = intval($_POST['_wpcf7']);
	$thirdparty = 'contactform';
	$activated_crm = get_option( 'WpLeadBuilderProActivatedPlugin' );
	$get_contact_option = $activated_crm.'_wp_contact'.$post_id;
	$noe = '';
        $check_map_exist = get_option( $get_contact_option );
        if( !empty( $check_map_exist ))
        {
		$all_fields = $_POST;
		$submission = WPCF7_Submission::get_instance();
                $attachments = $submission->uploaded_files();
        	foreach($all_fields as $key=>$value)    {
        	if(preg_match('/^_wp/',$key))
        	unset($all_fields[$key]);
        	}
	//	g-recaptcha-response

	foreach($all_fields as $cfkey => $cfvalue)
	{
		if( $cfkey == 'g-recaptcha-response' )
		{
			if( empty( $cfvalue ) )
			{
				die;	
			}			
		}
	}

		$mapped_array = $check_map_exist['fields'];
        	$mapped_array_key_labels = array_keys( $mapped_array );
	
		$get_json_array = $wpdb->get_results( $wpdb->prepare( "select ID,post_content from $wpdb->posts where ID=%d" , $post_id ) );
                        $contact_post_content = $get_json_array[0]->post_content;
                        $fields = getTextBetweenBrackets( $contact_post_content );
                        $i = 0;
                        foreach( $fields as $cfkey => $cfval )
                        {
                                if( preg_match( '/\s/' , $cfval ) )
                                {
                                        $final_arr = explode( ' ' , $cfval );
                                        $contact_form_labels[$i] = rtrim( $final_arr[1] , ']' );
                                        $i++;
                                }
                        }
		
		//get mapped label keys from gravity array
        foreach( $contact_form_labels as $cf_key => $cf_val)
        {
                foreach( $mapped_array_key_labels as $labels )
                {
                        if( $labels == $cf_val && $labels != 'gclid_value' )
                        {
                                $field_name = $mapped_array[$labels];
                                $user_value = $all_fields[$labels];
                                $data_array[$field_name] = $user_value;
                        }
			if( $labels == 'gclid_value')
                        {
                                $field_name = $mapped_array[$labels];
                                $user_value = $_COOKIE['gclid'];
                                $data_array[$field_name] = $user_value;
                        }
                }
        }

	$activatedPlugin = $check_map_exist['thirdparty_crm'];
	        if(!empty($data_array)) {
		        foreach ( $data_array as $key => $value ) {
			        if ( $key == '' ) {
				        $noe = $key;
			        }
			        if ( is_array( $data_array[ $key ] ) ) {
				        switch ( $activatedPlugin ) {
					        case 'wptigerpro':
						        $data_array[ $key ] = '1';
						        break;

					        case 'wpsugarpro':
						case 'wpsuitepro':
						        $data_array[ $key ] = 'on';
						        break;

					        case 'wpzohopro':
						case 'wpzohopluspro':
						        $data_array[ $key ] = 'true';
						        break;

					        case 'wpsalesforcepro':
						        $data_array[ $key ] = 'on';
						        break;

					        case 'freshsales':
						        $data_array[ $key ] = '1';
						        break;
				        }
			        }
		        }
		        unset( $data_array[ $noe ] );
	        }
	// Change drop down value to id for Fresh sales CRM
        if( $activatedPlugin == 'freshsales' )
        {
                $fs_module = strtolower( $check_map_exist['third_module'] );
                $fs_module = rtrim( $fs_module , 's' );
                $freshsales_option = get_option( "smack_{$activatedPlugin}_{$fs_module}_fields-tmp" );
                foreach( $freshsales_option['fields'] as $fs_key => $fs_option )
                {
                        foreach( $data_array as $field_name => $posted_val ) {
                        if( $fs_option['type']['name'] == 'picklist' && $fs_option['fieldname'] == $field_name )
                        {
                                        foreach( $fs_option['type']['picklistValues'] as $pick_key => $pick_val )
                                        {
                                                if( $pick_val['label'] == $posted_val )
                                                {
                                                        $data_array[$field_name] = $pick_val['id'];
                                                }
                                        }

                        }
			if( $fs_option['type']['name'] == 'boolean' && $fs_option['fieldname'] == $field_name && $posted_val == "" )
			{
					$data_array[$field_name] = '0';
			}
                        }
                }
        }
	//Attachment field
        if($attachments){
                $data_array['attachments'] = $attachments;
        }

        $ArraytoApi['posted'] = $data_array;
        $ArraytoApi['third_module'] = $check_map_exist['third_module'];
        $ArraytoApi['thirdparty_crm'] = $check_map_exist['thirdparty_crm'];
        $ArraytoApi['third_plugin'] = $check_map_exist['third_plugin'];
        $ArraytoApi['form_title'] = $check_map_exist['form_title'];
        $ArraytoApi['shortcode'] = $get_contact_option;
	$ArraytoApi['duplicate_option'] = $check_map_exist['thirdparty_duplicate'];
        $capture_obj = new CapturingProcessClassPRO();
        $capture_obj->thirdparty_mapped_submission($ArraytoApi);

	}
	else
	{
	$smack_shortcode = $wpdb->get_var($wpdb->prepare("select shortcode from wp_smackformrelation where thirdpartyid =%d and thirdparty=%s " , $post_id , $thirdparty ) );
	$code['name'] = $smack_shortcode;
	//Attachment 
        $submission = WPCF7_Submission::get_instance();
        $attachments = $submission->uploaded_files();

	$newform = new CaptureData();
        $newshortcode = $newform->formfields_settings( $code['name'] );
        $FormSettings = $newform->getFormSettings( $code['name'] );
	$module = $FormSettings->module;
	$activatedPlugin = $HelperObj->ActivatedPlugin;
	$all_fields = $_POST;
	foreach($all_fields as $key=>$value)	{
	if(preg_match('/^_wp/',$key))
	unset($all_fields[$key]);
	}
	
//	g-recaptcha-response

	foreach($all_fields as $cfkey => $cfvalue)
	{
		if( $cfkey == 'g-recaptcha-response' )
		{
			if( empty( $cfvalue ) )
			{
				die;	
			}			
		}
	}

	//Mapping for contact form and smack form
	$mapping = $wpdb->get_results( $wpdb->prepare( "select smackfieldslable,thirdpartyfieldids from wp_smackthirdpartyformfieldrelation where thirdpartyformid=%d" , $post_id ), ARRAY_A );
	foreach($mapping as $key=>$value)
	{
		$smackfieldslable[$key] = $value['smackfieldslable'];
		$thirdpartyfieldids[$key] = $value['thirdpartyfieldids'];
	}
	$smackfieldName = $wpdb->get_results(" select a.field_name , a.field_values , a.field_type from wp_smackleadbulider_field_manager as a join wp_smackleadbulider_form_field_manager as b join wp_smackthirdpartyformfieldrelation as c where b.field_id=a.field_id and c.smackfieldid=b.rel_id and thirdpartyformid='{$post_id}'",ARRAY_A);
	 	foreach($smackfieldName as $key=>$value)        
		{
                        $smackfieldname[$key] = $value['field_name'];
                }
		$thirdpartyfieldids = array_flip($thirdpartyfieldids);
		
		foreach($thirdpartyfieldids as $key=>$value)
		{
			$OriginalMap[$key] = $smackfieldname[$value];
		}
		if($activatedPlugin == "wptigerpro" && $module == "Contacts")
		{
			$all_fields = replace_key_function($all_fields, 'Mailing_P_O__Box', 'Mailing_P.O._Box');
			$all_fields = replace_key_function($all_fields, 'Other_P_O__Box', 'Other_P.O._Box');
			$all_fields = replace_key_function($all_fields, 'Asst__Phone', 'Asst._Phone');
		}
		if($activatedPlugin == "wpsalesforcepro" && $module == "Contacts")
                {

                        $all_fields = replace_key_function($all_fields, 'Asst__Phone', 'Asst._Phone');
                }
	if( is_array( $all_fields ) ){ //Make sure $all_fields is an array.
  //Loop through each of our submitted values.
    foreach( $all_fields as $field_id => $user_value ){
      //Do something with those values
	    if(isset($OriginalMap[$field_id]))
		    $ArraytoApi[$OriginalMap[$field_id]] = $user_value;
    }
        $code['name'] = $smack_shortcode;
        $newform = new CaptureData();
        $newshortcode = $newform->formfields_settings( $code['name'] );
        $FormSettings = $newform->getFormSettings( $code['name'] );
        $module = $FormSettings->module; //$shortcodes[$attr['name']]['module'];
        $ArraytoApi['moduleName'] = $module;
        $ArraytoApi['formnumber'] = $post_id;
        $ArraytoApi['submit'] = 'Submit';
	$activatedPlugin = $HelperObj->ActivatedPlugin;
		if(!empty($ArraytoApi)) {
			foreach ( $ArraytoApi as $key => $value ) {
				if ( $key == '' ) {
					$noe = $key;
				}
				if ( is_array( $ArraytoApi[ $key ] ) ) {
					switch ( $activatedPlugin ) {
						case 'wptigerpro':
							$ArraytoApi[ $key ] = '1';
							break;

						case 'wpsugarpro':
						case 'wpsuitepro':
							$ArraytoApi[ $key ] = 'on';
							break;

						case 'wpzohopro':
						case 'wpzohopluspro':
							$ArraytoApi[ $key ] = 'true';
							break;

						case 'wpsalesforcepro':
							$ArraytoApi[ $key ] = 'on';
							break;

						case 'freshsales':
							$ArraytoApi[ $key ] = '1';
							break;
					}
				}
			}
			unset( $ArraytoApi[ $noe ] );
		}
	if($activatedPlugin == "wpsalesforcepro" && $module == "Contacts")
	{
		$date = new DateTime($ArraytoApi['Birthdate']);
		$ArraytoApi['Birthdate'] =  $date->format('Y-m-d');
	}
	
	if( $activatedPlugin == 'freshsales' )
	{
		foreach( $smackfieldName as $sm_key => $sm_value )
		{
			foreach( $ArraytoApi as $API_key => $API_val )
			{
				if( $sm_value['field_name'] == $API_key && $sm_value['field_values'] != '' )
				{
					$get_choices = unserialize( $sm_value['field_values'] );
					foreach( $get_choices as $choice_key => $choice_val )
					{
						if( $choice_val['label'] == $API_val )
						{
							$ArraytoApi[$API_key] = $choice_val['id'];
						}		
					}
				}
				if( $sm_value['field_name'] == $API_key && $sm_value['field_type'] == 'boolean' && $API_val == "" )
                                {
                                                $ArraytoApi[$API_key] = '0';
                                }
			}
		}
	}
	//Attachment
        if($attachments){
                $ArraytoApi['attachments'] = $attachments;
        }

        global $_POST;
        $_POST = array();
        $_POST = $ArraytoApi;
        smackContactFormGeneratorPRO($code , 'thirdparty');
        callCurlPRO('post');
        return true;
  }
}
}
?>
