<?php
/******************************************************************************************
 * Copyright (C) Smackcoders. - All Rights Reserved under Smackcoders Proprietary License
 * Unauthorized copying of this file, via any medium is strictly prohibited
 * Proprietary and confidential
 * You can contact Smackcoders at email address info@smackcoders.com.
 *******************************************************************************************/
if ( ! defined( 'ABSPATH' ) )
        exit; // Exit if accessed directly

class SmackLBHelper {

	private $data_information = array();
	
	public function __construct() {
                //print_r($_REQUEST);die;
		$this->data_information['active_plugins'] = get_option('active_plugins');
        }

	/**
         * Set crmtype
         *
         * @param $crmtype   - eg:wpzohopro
         */

	public function setCrmType($crmtype) {
                $this->data_information['crm_type'] = $crmtype;
        }

	/**
         * Set Module
         *
         * @param $Module    - Leads/Contacts
         */

	public function setModule($module) {
                $this->data_information['module'] = $module;
        }

	/**
         * Set shortcode name
         *
         * @param $shortcode_name   - eg : tXjcT
         */

	public function setShortcodeName($shortcodename) {
                $this->data_information['shortcode_name'] = $shortcodename;
        }

	/**
         * Set activated plugin
         *
         * @param $activated_plugin   - eg : wpzohopro
         */

        public function setActivatedPlugin( $ActivatedPlugin) {
                $this->data_information['ActivatedPlugin'] = $ActivatedPlugin;
        }


	 /**
         * Set Shortcode Details
         *
         * @param $shortcode_details - all details of shortcode.
         */
        public function setShortcodeDetails($shortcode_details) {
                $this->data_information['shortcode_details'] = array();
                if(!empty($shortcode_details))
                        $this->data_information['shortcode_details'] = $shortcode_details;
        }


	/**
         * Set Configuration Details
         *
         * @param $configuration_details - all details of configuration
         */
        public function setConfigurationDetails($configuration_details) {
                $this->data_information['configuration_details'] = array();
                if(!empty($configuration_details))
                        $this->data_information['configuration_details'] = $configuration_details;
        }




	/**
         * Set activated plugin label
         *
         * @param $activated_plugin_label   - eg : ZohoCRM
         */

        public function setActivatedPluginLabel($ActivatedPluginLabel) {
                $this->data_information['ActivatedPluginLabel'] = $ActivatedPluginLabel;
        }


	/**
         * Set moduleslug
         *
         * @param $moduleslug   - eg : lead
         */

        public function setModuleSlug($moduleslug) {
                $this->data_information['moduleslug'] = $moduleslug;
        }

	/**
         * Set plugins_url
         *
         * @param $plugins_url   - url
         */

        public function setPluginsUrl($plugins_url) {
                $this->data_information['plugins_url'] = $plugins_url;
        }

	/**
         * Set onAction
         *
         * @param $onAction   - eg : onEditShortcode
         */

        public function setonAction($onAction) {
                $this->data_information['onAction'] = $onAction;
        }

	/**
         * Set formtype
         *
         * @param $formtype   - eg : post
         */

        public function setFormType($formtype) {
                $this->data_information['formtype'] = $formtype;
        }

	/**
         * Set options
         *
         * @param $options  - eg : smack_fields_shortcodes
         */

        public function setoptions($options) {
                $this->data_information['options'] = $options;
        }

	 /**
         * Get list of active plugins
         *
         * @return mixed|void
         */
        public function getActivePlugins() {
                if(!empty($this->data_information['active_plugins']))
                        return $this->data_information['active_plugins'];
                else
                        return get_option('active_plugins');
        }

	 /**
         * Get Data information
         *
         * @return array
         */
        public function getDataInformation() {
                return $this->data_information;
        }

	 
	 /**
         * Get Shortcode Details
         *
         * @param $shortcode_details - all details of shortcode.
         */
        public function getShortcodeDetails() {
		 if(empty($this->data_information['shortcode_details']))
                        $this->data_information['shortcode_details'] = array();

                if(!empty($this->data_information['shortcode_details'][$key]) && $key != null)
                        return $this->data_information['shortcode_details'][$key];

                return $this->data_information['shortcode_details'];
        }


	 /**
         * Get Configuration Details
         *
         * @param $configuration_details - all details of configuration
         */
        public function getConfigurationDetails($configuration_details) {
		if(empty($this->data_information['configuration_details']))
                        $this->data_information['configuration_details'] = array();

                if(!empty($this->data_information['configuration_details'][$key]) && $key != null)
                        return $this->data_information['configuration_details'][$key];

                return $this->data_information['configuration_details'];
               
        }


	/**
         * Get crmtype
         *
         * @param $crmtype   - eg:wpzohopro
         */

        public function getCrmType() {
                return  $this->data_information['crm_type'];
        }

        /**
         * Get Module
         *
         * @param $Module    - Leads/Contacts
         */

        public function getModule() {
                return $this->data_information['module'];
        }

        /**
         * Get shortcode name
         *
         * @param $shortcode_name   - eg : tXjcT
         */

        public function getShortcodeName() {
                return $this->data_information['shortcode_name'];
        }

	 /**
         * Get activated plugin
         *
         * @param $activated_plugin   - eg : wpzohopro
         */

        public function getActivatedPlugin( ) {
               return $this->data_information['ActivatedPlugin'];
        }


        /**
         * Get activated plugin label
         *
         * @param $activated_plugin_label   - eg : ZohoCRM
         */

        public function getActivatedPluginLabel() {
                return $this->data_information['ActivatedPluginLabel'];
        }


        /**
         * Get moduleslug
         *
         * @param $moduleslug   - eg : lead
         */

        public function getModuleSlug() {
                return $this->data_information['moduleslug'];
        }

	 /**
         * Get plugins_url
         *
         * @param $plugins_url   - url
         */

        public function getPluginsUrl() {
                return $this->data_information['plugins_url'];
        }

        /**
         * Get onAction
         *
         * @param $onAction   - eg : onEditShortcode
         */

        public function getonAction() {
                return $this->data_information['onAction'];
        }

        /**
         * Get formtype
         *
         * @param $formtype   - eg : post
         */

        public function getFormType() {
                return $this->data_information['formtype'];
        }

	 /**
         * Get options
         *
         * @param $options  - eg : smack_fields_shortcodes
         */

        public function getoptions() {
                return $this->data_information['options'];
        }



	
}	
