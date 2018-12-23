<?php
/******************************************************************************************
 * Copyright (C) Smackcoders 2016 - All Rights Reserved
 * Unauthorized copying of this file, via any medium is strictly prohibited
 * Proprietary and confidential
 * You can contact Smackcoders at email address info@smackcoders.com.
 *******************************************************************************************/
if ( ! defined( 'ABSPATH' ) )
    exit; // Exit if accessed directly

$result = '';
global $wpdb,$lb_crmm;
$shortcode = $_REQUEST['EditShortcode'];
$activatedplugin = $lb_crmm->getActivatedPlugin();
$data = $wpdb->get_results("select *from wp_smackleadbulider_shortcode_manager where crm_type = '{$activatedplugin}'");
if( $result !='' ) {
    echo "<div style='font-weight:bold; padding-left:20px; color:red;'> {$result} </div>";
} else {
    $siteurl = site_url();
    $plug_url = SM_LB_URL;
    $field_form_action = add_query_arg( array( '__module' => 'ManageShortcodes' , '__action' => 'ManageFields' , 'crmtype' => $_REQUEST['crmtype'] , 'module' => $_REQUEST['module'] , 'EditShortcode' => $_REQUEST['EditShortcode'] , $plug_url ));
    ?>
    <form id="field-form" action="<?php echo esc_url("".site_url()."/wp-admin/admin.php?page=lb-create-leadform&__module=ManageShortcodes&__action=ManageFields&onAction=".$_REQUEST['onAction']."&crmtype=".$_REQUEST['crmtype']."&module=".$_REQUEST['module']."&EditShortcode=".$shortcode.""); ?>" method="post">

        <div class='mt10 mb20'>

            <?php
            global $crmdetailsPRO;
            $content = "";
            if(isset($shortcode))
            {
                $content .= "<span id='inneroptions' class='leads-builder-sub-heading' style='position:relative;left:5px;margin-left:15px;'><b>CRM Type  :</b> ";
                $content .= "<span> ";
                foreach( $crmdetailsPRO as $crm_key => $crm_value )
                {
                    if(isset($_REQUEST['crmtype']) && ($crm_key == $_REQUEST['crmtype'])){
                        $select_option = " {$crm_value['crmname']} ";
                    }
                }
                $content .= $select_option;
                $content .= "</span>";
                $content .= "</span>";

                echo $content;
            }
            else
            {
                $content.= "<span id='inneroptions' style='position:relative;left:5px;margin-left:10px;'>CRM Type  : <select id='crmtype' name='crmtype' style='margin-left:8px;height:27px;' class=''
onchange = \"SelectFieldsPRO('{$siteurl}','{$_REQUEST['module']}','{smack_fields_shortcodes}', '{onEditShortcode}')\">";
                $select_option = "";
                // $Data->crmtype = "" ;

                $select_option .= "<option> --".__('Select' , SM_LB_URL )."-- </option>";
                foreach( $crmdetailsPRO as $crm_key => $crm_value )
                {
                    if(isset($_REQUEST['crmtype']) && ($crm_key == $_REQUEST['crmtype'])){
                        $select_option.= "<option value='{$crm_key}' selected=selected > {$crm_value['crmname']} </option>";
                    } else {
                        $select_option.= "<option value='{$crm_key}'> {$crm_value['crmname']} </option>";
                    }
                }

                $content.= $select_option;

                $content.= "</select></span>";
                echo $content;
            }
            ?>
            <?php
            global $crmdetailsPRO;
            global $DefaultActivePluginPRO;

            $content = "";
            if(isset($shortcode))
            {
                $content .= "<span id='inneroptions' class='leads-builder-sub-heading' style='position:relative;left:40px;'><b>Module Type  :</b> ";
                $content .= "<span> ";
                foreach( $crmdetailsPRO[$_REQUEST['crmtype']]['modulename'] as $key => $value )
                {
                    if(isset($_REQUEST['module']) && ($_REQUEST['module'] == $key ) ){
                        $select_option = " {$value} ";
                    }
                }
                $content .= $select_option;
                $content .= "</span>";
                $content .= "</span>";
                echo $content;
            }
            else
            {
                $content.= "<span id='inneroptions' style='position:relative;left:40px;'>Module Type  : <select id='module' name='module' style='margin-left:8px;height:27px;' onchange = \"SelectFieldsPRO('{$siteurl}','{$_REQUEST['module']}','{smack_fields_shortcodes}', '{onEditShortcode}')\" >";
                $select_option = "";
                $select_option .= "<option> --".__('Select' , SM_LB_URL )."-- </option>";
                foreach( $crmdetailsPRO[$_REQUEST['crmtype']]['modulename'] as $key => $value)
                {
                    if(isset($_REQUEST['module']) && ($_REQUEST['module'] == $key ) )
                    {
                        $select_option.= "<option value = '{$key}' selected=selected  > {$value}</option>";
                    }
                    else
                    {
                        $select_option.= "<option value = '{$key}' > {$value}</option>";
                    }
                }
                $content.= $select_option;

                $content.= "</select></span>";
                echo $content;
            }
            ?>
        </div>
        <?php
        if(isset($shortcode) )
        {
            //$data->onAction='onEditShortCode';
            ?>
            <h3 id="innerheader" style="margin-bottom: 0px;width:98%;">[<?php echo sanitize_text_field($_REQUEST['crmtype']);?>-web-form name='<?php echo $shortcode;?>']</h3>
            <?php
            //  $data->onAction='onEditShortCode';
        }
        else
        {
            //      $Data->onAction='onCreate';
        }
        ?>


        <div class="wp-common-crm-content" style="background-color: white;width:98%;" >
            <div class="form-options" style="padding: 20px 0px;">
                <div id="settingsavedmessage" style="height: 42px; display:none; color:red;">   </div>
                <div id="savedetails" style="height: 90px; display:none; color:blue;">   </div>
                <div id="url_post_id" style="display:none; color:blue;">  </div>
                <div id="formtext" class="leads-builder-heading mb30"> <?php echo esc_html__('Form Settings' , SM_LB_URL ); ?> :</div>
                <div>


                    <div class="form-group col-md-12">
                        <?php
                        $formObj = new CaptureData();

                        if(isset($shortcode) && ( $shortcode != "" ))
                        {
                            $config_fields = $formObj->getFormSettings( $shortcode);    // Get form settings
                        }

                        $content = "";
                        $content.= "<div id='innertext' class='col-md-3 leads-builder-label'>".__('Form Type' , SM_LB_URL )."   </div><div class='col-md-2'><select class='selectpicker form-control' data-live-search='false' name='formtype'>";
                        $formtypes = array( 'post' => __("Post" , SM_LB_URL ) , 'widget' => __("Widget" , SM_LB_URL ) );
                        $select_option = "";
                        foreach( $formtypes as $formtype_key => $formtype_value )
                        {
                            if( $formtype_key == $config_fields->form_type )
                            {
                                $select_option.= "<option value='{$formtype_key}' selected > {$formtype_value} </option>";
                            }
                            else
                            {
                                $select_option.= "<option value='{$formtype_key}'> {$formtype_value} </option>";
                            }
                        }

                        $content.= $select_option;

                        $content.= "</select></div>";

                        echo $content;
                        ?>
                    </div>

                </div>
                <!--dupicate handling  start-->

                <!-- <div><tbody> -->

                <div class="form-group col-md-12">
                    <div class="col-md-3">
                        <label><div id='innertext' class="leads-builder-label"><?php echo esc_html__("Duplicate handling" , SM_LB_URL ); ?></div></label>
                    </div>
                    <div class="col-md-8">
                        <div class='col-md-2'>
                 <span id="circlecheck">
                 <label for="smack_capture_duplicates"  id='innertext' class="leads-builder-label">
                     <input type='radio'  name='check_duplicate' id='smack_capture_duplicates' value="skip" disabled
                         <?php if( isset($config_fields->duplicate_handling) && ($config_fields->duplicate_handling == 'skip'))
                         {
                             echo "checked=checked";
                         }
                         ?>>
                     <?php echo esc_html__("Skip" , SM_LB_URL ); ?></label>
                 </span>
                        </div>
                        <div class='col-md-2'>
                 <span id="circlecheck">
                 <label for="smack_update_records" id='innertext' class="leads-builder-label">
                     <input type='radio'  name='check_duplicate' id='smack_update_records' value= "update" disabled
                         <?php if(isset($config_fields->duplicate_handling ) && ($config_fields->duplicate_handling == 'update'))
                         {
                             echo "checked=checked";
                         }?>>
                     <?php echo esc_html__('Update' , SM_LB_URL ); ?></label>
                 </span>
                        </div>
                        <?php $activated_crm = get_option( 'WpLeadBuilderProActivatedPlugin' );
                        if($activated_crm != 'freshsales' || ($activated_crm == 'freshsales' && $_REQUEST['module'] != 'Contacts')) { ?>
                            <div class="col-md-2">
                 <span id="circlecheck">
                 <label for="smack_none_records"  id='innertext' class="leads-builder-label">
                     <input type='radio'  name='check_duplicate' id='smack_none_records' value="none"
                         <?php if(!isset($config_fields->duplicate_handling ) || ( isset($config_fields->duplicate_handling) && ($config_fields->duplicate_handling=='none')))
                         {
                             echo "checked=checked";
                         } ?>>
                     <?php echo esc_html__("Create" , SM_LB_URL ); ?></label>
                            </div>
                        <?php } ?>
                        <!-- Check both Leads, Contacts and Skip -->
                        <div class="">
                 <span id="circlecheck">
                 <label for="smack_capture_duplicates"  id='innertext' class="leads-builder-label">
                     <input type='radio'  name='check_duplicate' id='smack_capture_duplicates' value="skip_both" disabled
                         <?php if( isset($config_fields->duplicate_handling) && ($config_fields->duplicate_handling == 'skip_both'))
                         {
                             echo "checked=checked";
                         } ?> >
                     <?php echo esc_html__("Skip if already a Contact or Lead" , SM_LB_SLUG ); ?></label>
                 </span>
                        </div> <!-- Check Both Leads and Contacts -->
                    </div> <!-- radio button div close -->


                </div><!-- form group div close -->
                <!-- </tbody></div>
				 --><!--dupicate handling end -->
                <!-- assign to succcess div start -->
                <div>
                    <!--<div class="form-group col-md-12">
        <div id='innertext' class="col-md-3">
             <label class="leads-builder-label"><?php //$HelperObj = new WPCapture_includes_helper_PRO();
                   // $module = $HelperObj->Module;
                   // echo esc_html__("Assign" , SM_LB_URL )." {$module} ".esc_html__("to User" , SM_LB_URL );?></label>
         </div>
         <div  id="assignedto_td" class="col-md-2">
	 <?php  
	 if($_REQUEST['crmtype'] == 'wpzohopluspro'){
		 $crm_type_tmp = 'wpzohopro';
	 }else{
		 $crm_type_tmp = $_REQUEST['crmtype'];
	 }
		   // require_once(SM_LB_PRO_DIR . "includes/".$crm_type_tmp."Functions.php");
		  //  $FunctionsObj = new mainCrmHelper();
		   // if(isset($shortcode))
		    {
                       // $UsersListHtml = $FunctionsObj->getUsersListHtml( $shortcode );
                    }
                    //else
                    {
                     //   $UsersListHtml = $FunctionsObj->getUsersListHtml();
                    }
                   // echo " $UsersListHtml";
                   // $first_userid = "";
                    ?>
             <input type='hidden' id='rr_first_userid' value="<?php// echo $first_userid ;?>">
         </div>
    </div> -->

                    <div class="form-group col-md-12">
                        <div class="col-md-3">
                            <label id='innertext' class="leads-builder-label">Error Message Submission</label>
                        </div>
                        <div class="col-md-4">
                            <input type="text" class="form-control" name="errormessage" value="<?php if(isset($config_fields->error_message)) echo $config_fields->error_message; ?>" placeholder ="<?php echo esc_html__("Sorry, submission failed" , SM_LB_URL ); ?>" />
                        </div>
                        <div>
                            <div style ="position:relative;top:-9px;">
                                <a class="tooltip"  href="#" style="padding-left:8px">
                                    <img src="<?php echo SM_LB_DIR; ?>assets/images/help.png">
             <span class="tooltipPostStatus">
             <img src="<?php echo SM_LB_DIR; ?>assets/images/callout.gif" class="callout">
                 <?php echo esc_html__("Message Displayed For Failed Submission." ,SM_LB_URL ); ?>
             </span>
                                </a>
                            </div>
                        </div>
                    </div>
                    <div class="form-group col-md-12">
                        <div class="col-md-3">
                            <label id='innertext' class="leads-builder-label"><?php echo esc_html__('Success Message Submission' , 'wp-leads-builder-any-crm' ); ?></label>
                        </div>
                        <div class="col-md-4">
                            <input type="text" class="form-control" name="successmessage" value="<?php if(isset($config_fields->success_message)) echo $config_fields->success_message; ?>" placeholder ="Thanks for Submitting"/>
                        </div>
                        <div>
                            <div style ="position:relative;top:-9px;">
                                <a class="tooltip" href="#" style="padding-left:8px">
                                    <img src="<?php echo SM_LB_DIR; ?>assets/images/help.png">
             <span class="tooltipPostStatus">
             <img src="<?php echo SM_LB_DIR; ?>assets/images/callout.gif" class="callout">
                 <?php echo esc_html__('Message Displayed For Successful Submission.' , SM_LB_URL ); ?>
             </span>
                                </a>
                            </div>

                        </div>
                    </div>
                </div>
                <!-- assign to succcess div close -->

                <!-- label fields close div -->

                <div>
                    <div class="form-group col-md-12">
                        <div class="col-md-3">
                            <label id='innertext' class="leads-builder-label"><?php echo esc_html__('Enable URL Redirection' , SM_LB_URL ); ?> </label>
                        </div>
                        <div class="col-md-2">
                            <div class="switch">
                                <!-- <input type="checkbox" id='enableurlredirection' name="enableurlredirection" class="cmn-toggle cmn-toggle-yes-no" onclick="enableredirecturl(this.id);" value="on" <?php if(isset($config_fields->is_redirection) && ($config_fields->is_redirection == '1')){ echo "checked=checked"; } ?> />
             <label for="enableurlredirection" data-on="Yes" data-off="No">
             </label> -->
                                <!-- tfa button -->
                                <input id="enableurlredirection" type='checkbox' class="tgl tgl-skewed noicheck" name='enableurlredirection' onclick="enableredirecturl(this.id);" value="on" <?php if(isset($config_fields->is_redirection) && ($config_fields->is_redirection == '1')){ echo "checked=checked"; } ?> />
                                <label  data-tg-off="OFF" data-tg-on="ON" for="enableurlredirection"  class="tgl-btn" style="font-size: 16px;" >
                                </label>
                                <!-- tfa btn End -->
                            </div>
                        </div>
                        <div class="col-md-2">
                            <input class="form-control" id="redirecturl" type="text" name="redirecturl" <?php if(!isset($config_fields->is_redirection) == '1'){ echo "disabled=disabled";} ?> value="<?php if(isset($config_fields->url_redirection)) echo $config_fields->url_redirection; ?>" placeholder = "<?php echo esc_attr__('Page id or Post id' , SM_LB_URL ); ?>"/>
                        </div>
                        <div style="padding-left:10px;">
                            <div style ="position:relative;top:-9px;">
                                <a class="tooltip" href="#">
                                    <img src="<?php echo SM_LB_DIR; ?>assets/images/help.png">
             <span class="tooltipPostStatus">
             <img src="<?php echo SM_LB_DIR; ?>assets/images/callout.gif" class="callout">
                 <?php echo esc_html__("(Give your custom success page url post id to redirect leads)." , SM_LB_URL ); ?>
             </span>
                                </a>
                            </div>

                        </div>
                    </div>

                    <div class="form-group col-md-12">
                        <div class="col-md-3">
                            <label id='innertext' class="leads-builder-label"><?php echo esc_html__("Enable Google Captcha", "wp-leads-builder-any-crm" ); ?>
                            </label>
                        </div>
                        <div class="col-md-2">
                            <div class="switch">
                                <!-- <input type="checkbox" name="enablecaptcha" id="enablecaptcha"  class="cmn-toggle cmn-toggle-yes-no" value="on" <?php if(isset($config_fields->google_captcha ) && ($config_fields->google_captcha == '1'))  { echo "checked=checked"; } ?> />
             <label for="enablecaptcha" data-on="Yes" data-off="No">
             </label>    -->
                                <!-- tfa button -->
                                <input id="enablecaptcha" type='checkbox' class="tgl tgl-skewed noicheck" name='enablecaptcha' value="on" <?php if(isset($config_fields->google_captcha ) && ($config_fields->google_captcha == '1'))  { echo "checked=checked"; } ?> disabled/>
                                <label  data-tg-off="OFF" data-tg-on="ON" for="enablecaptcha"  class="tgl-btn" style="font-size: 16px;" >
                                </label>
                                <!-- tfa btn End -->
                            </div>
                        </div>
                        <div style="padding-left:36px;">
                            <div style="margin-left:-25px;margin-top:-11px">
                                <a class="tooltip" href="#">
                                    <img src="<?php echo SM_LB_DIR; ?>assets/images/help.png">
             <span class="tooltipPostStatus">
             <img src="<?php echo SM_LB_DIR; ?>assets/images/callout.gif" class="callout">
                 <?php echo esc_html__('(Enable google recaptcha feature).' , SM_LB_URL ); ?>
             </span>
                                </a>
                            </div>
                        </div>
                    </div>
                    <!-- tr not close -->
                    <!-- kjkkjkj -->
                    <?php
                    $thirdparty_form = get_option( 'Thirdparty_'.$shortcode);
                    ?>
                    <div class="form-group col-md-12">
                        <div class="col-md-3">
                            <label id='innertext' class="leads-builder-label"><?php echo esc_html__("Choose Thirdparty Form", "wp-leads-builder-any-crm" ); ?>
                            </label>
                        </div>
                        <div class="col-md-2">
                            <select id='thirdparty_form_type' class="selectpicker form-control" data-live-search='false' name='thirdparty_form_type'>";
                                <option value='none'>None</option>
                                <option value='ninjaform' <?php if($thirdparty_form == 'ninjaform') { echo "selected=selected"; }?>  disabled>Ninja Forms</option>
                                <option value='contactform' <?php if($thirdparty_form == 'contactform') { echo "selected=selected"; }?> >Contact Form</option>
                                <option value='gravityform' <?php if($thirdparty_form == 'gravityform') { echo "selected=selected"; }?> disabled>Gravity Forms</option>
                            </select>
                        </div>
                        <div style="padding-left:36px;">
                            <div style="margin-left:-25px;margin-top:-11px">
                                <a class="tooltip" href="#">
                                    <img src="<?php echo SM_LB_DIR; ?>assets/images/help.png">
             <span class="tooltipPostStatus">
             <img src="<?php echo SM_LB_DIR; ?>assets/images/callout.gif" class="callout">
                 <?php echo esc_html__('(Choose your Thirdparty form here).' , SM_LB_URL ); ?>
             </span>
                                </a>
                            </div>

                        </div>
                    </div>
                    <!-- Third party title  -->
                    <?php $thirdparty_title_key = $shortcode;
                    $check_thirdparty_title = get_option( $thirdparty_title_key );
                    ?>
                    <div class="form-group col-md-12">
                        <div class="col-md-3">
                            <label id='innertext' class="leads-builder-label"><?php echo esc_html__("Thirdparty Form Title", "wp-leads-builder-any-crm" ); ?>
                            </label>
                        </div>
                        <div class="col-md-4">
                            <div class="switch">
                                <input type="text" class="form-control" name="thirdparty_form_title" id="thirdparty_form_title" <?php if(!empty($check_thirdparty_title)) { ?> value="<?php echo $check_thirdparty_title; ?>" <?php } ?> />
                            </div>
                        </div>
                        <div style="padding-left:36px;">
                            <div style="margin-left:-25px;margin-top:-11px">
                                <a class="tooltip" href="#">
                                    <img src="<?php echo SM_LB_DIR; ?>assets/images/help.png">
             <span class="tooltipPostStatus">
             <img src="<?php echo SM_LB_DIR; ?>assets/images/callout.gif" class="callout">
                 <?php echo esc_html__('(Enter thirdparty form title here).' , SM_LB_URL ); ?>
             </span>
                                </a>
                            </div>
                        </div>
                    </div>

                    <div>
                        <div class="col-md-offset-9">
                            <?php $check_thirparty_val_exist = get_option( 'Thirdparty_'.$shortcode );
                            $thirdparty_option_available = 'no';
                            if( $check_thirparty_val_exist != '')
                            {
                                $thirdparty_option_available = 'yes';
                            }
                            ?>
                            <input type="hidden" name='thirdparty_option_available' id='thirdparty_option_available' value="<?php echo $thirdparty_option_available;?>">
                            <input class="smack-btn smack-btn-primary btn-radius" type="button" onclick="saveFormSettings('<?php echo $shortcode; ?>');" value="<?php echo esc_attr__("Save Form Settings" , SM_LB_URL ); ?>" name="SaveFormSettings" />
                        </div>
                    </div>


                </div>
                <!-- label fields close div -->

            </div>
        </div>

        <span style="padding:10px; color:#FFFFFF; background-color: #00a699; text-align:center; float:right; font-weight:bold; cursor:pointer;margin-right:2%;margin-top: -54px;" id ="showmore"><?php echo esc_html__("Form Options" , SM_LB_URL ); ?> <i class="dashicons dashicons-arrow-down"></i></span>
        <span style="padding:10px; color:#FFFFFF; background-color: #00a699; text-align:center; float:right; font-weight:bold; cursor:pointer;margin-right:2%;" id ="showless"><?php echo esc_html__("Form Options" , SM_LB_URL ); ?> <i class="dashicons dashicons-arrow-up"></i></span>
        <br>

        <!-- <div class="wp-common-crm-content" style="background-color: white;" > -->
        <div>
            <div class="panel" style="width:98%;">
                <div class="panel-body">
                    <div>
                        <div class="clearfix"></div>
                        <div class="mt20 form-group">
                            <div id="formtext" class="leads-builder-heading"> <?php echo esc_html__('Field Settings' , SM_LB_URL ); ?> </div>
                        </div>
                        <div class="clearfix"></div>
                        <div class="form-group row">
                            <div class="col-md-2">
                                <select id="bulk-action-selector-top" class="selectpicker form-control" name="bulkaction">
                                    <option selected="selected" value="-1"><?php echo __('Bulk Actions' , SM_LB_URL ); ?></option>
                                    <option value="enable_field"><?php echo __('Enable Field' , SM_LB_URL ); ?></option>
                                    <option value="disable_field"><?php echo __('Disable Field' , SM_LB_URL ); ?></option>
                                    <!--<option value="update_order"><?php //echo __('Update Order' , SM_LB_URL ); ?></option>-->
                                    <option value="enable_mandatory" disabled><?php echo __('Enable Mandatory' , SM_LB_URL ); ?></option>
                                    <option value="disable_mandatory" disabled><?php echo __('Disable Mandatory' , SM_LB_URL ); ?></option>
                                    <option value="save_field_label_display" disabled><?php echo __('Save Display Label' , SM_LB_URL ); ?></option>
                                </select>
                            </div>
                            <div>
                                <input type='hidden' id='lead_crmtype' name="lead_crmtype" value="<?php echo get_option('WpLeadBuilderProActivatedPlugin');?>">
                                <input type="hidden" id="savefields" name="savefields" value="<?php echo esc_attr__('Apply' , SM_LB_URL ); ?>"/>
                                <?php if(isset($shortcode))
                                {
                                    $content = "";
                                    $content.= "<input class='smack-btn smack-btn-primary btn-radius' id='generate_forms' type='button' value='".__("Apply" , SM_LB_URL )."' onclick =  \" return SaveCheckPRO('".site_url()."','{$_REQUEST['module']}','smack_fields_shortcodes','{$_REQUEST['EditShortcode']}', '{$_REQUEST['onAction']}')\" />";
                                    echo $content;
                                }
                                ?>
                            </div>
                        </div>
                        <div class="form-group"> <?php #echo esc_html($error); ?> </div>
                        <?php #echo $pagination; ?>

                        <!-- </div> -->
                        <script>
                            jQuery(document).ready(function($) {
                                $( ".form-options" ).hide();
                                $( "#showless" ).hide();

                                $( "#showmore" ).click(function() {
                                    $( ".form-options" ).show( 600 );
                                    $( "#showless" ).show();
                                    $( "#showmore" ).hide();
                                    $(".form-options").css('overflow', 'visible');
                                });

                                $( "#showless" ).click(function() {
                                    $( ".form-options" ).hide( 600 );
                                    $( "#showless" ).hide();
                                    $( "#showmore" ).show();
                                });

                            });
                        </script>
                        <div id="fieldtable">
                            <?php
                            require_once( SM_LB_PRO_DIR ."includes/class_lb_manage_shortcodes.php" );
                            $FieldOperations = new FieldOperations();
                            if(isset($shortcode))
                                echo $FieldOperations->formFields( "smack_fields_shortcodes" , $_REQUEST['onAction'] , $shortcode , 'post' );
                            else
                                echo $FieldOperations->formFields( "smack_fields_shortcodes" , $_REQUEST['onAction'] , '' , 'post' );

                            ?>
                        </div>
                        <!-- </div> -->
                    </div>
                </div>
            </div>
            <script>
                function showAccordion( id )
                {
                    if(jQuery("#advance_option_display").val() == 0) {
                        jQuery("#advance_option").css("display", "block");
                        jQuery("#advance_option_display").val(1);
                        jQuery("#accordion_arrow").removeClass( "fa-chevron-right" );
                        jQuery("#accordion_arrow").addClass( "fa-chevron-down" );
                    } else {
                        jQuery("#advance_option").css("display", "none");
                        jQuery("#advance_option_display").val(0);
                        jQuery("#accordion_arrow").removeClass( "fa-chevron-down" );
                        jQuery("#accordion_arrow").addClass( "fa-chevron-right" );
                    }
                }
            </script>
            <br>
    </form>

    <div id="loading-image" style="display: none; background:url(<?php echo SM_LB_DIR ?>assets/images/ajax-loaders.gif) no-repeat center"><?php echo esc_html__('' , SM_LB_URL ); ?></div>
    <?php
}
