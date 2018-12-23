<?php
if( !class_exists( "SmackZohoApi" ) )
{
	class SmackZohoApi{

		public $zohocrmurl;
		public function __construct()
		{
			//print_r("lib");die;
			$this->zohocrmurl = "https://crm.zoho.com/crm/private/xml/";
		}

		public function APIMethod($module, $methodname, $authkey , $param="", $recordId = "")
		{
			//print_r($module);echo"<br>";print_r($methodname);echo"<br>";print_r($authkey);die;
			$uri = $this->zohocrmurl . $module . "/".$methodname."";
			/* Append your parameters here */
			$postContent = "scope=crmapi";
			$postContent .= "&authtoken={$authkey}";//Give your authtoken
			$args = array(
		        'body' => $postContent
		    );
		    $response =  wp_remote_post($uri, $args ) ;
		    $result = wp_remote_retrieve_body($response);
			$xml = simplexml_load_string($result);
			$json = json_encode($xml);
			$result_array = json_decode($json,TRUE);
			return $result_array;
		}

		public function insertRecord( $modulename, $methodname, $authkey , $xmlData="" , $extraParams = "" )
		{
			$uri = $this->zohocrmurl . $modulename . "/".$methodname."";
			/* Append your parameters here */
			$postContent = "scope=crmapi";
			$postContent .= "&authtoken={$authkey}";//Give your authtoken
			if($extraParams != "" && !is_array($extraParams) )
			{
				$postContent .= $extraParams;
			}
			$postContent .= "&xmlData={$xmlData}";
			$postContent .= "&wfTrigger=true";
			$args = array(
		        'body' => $postContent
		    );
		    $response =  wp_remote_post($uri, $args ) ;
		    $result = wp_remote_retrieve_body($response);
			$xml = simplexml_load_string($result);
			$json = json_encode($xml);
			$result_array = json_decode($json,TRUE);
			//Attachment
                        if($extraParams && is_array($extraParams)){
                                foreach($extraParams as $field => $path){
                                        $this->insertattachment($result_array,$authkey,$path,$modulename);//Feb03 fix
                                }
                        }
                        //Attachment
			return $result_array;
		}
		
		//Attachment
                public function insertattachment( $result_array,$authkey,$path,$modulename){
                        $recordId = $result_array['result']['recorddetail']['FL'][0];
                        $uri = $this->zohocrmurl . $modulename . "/uploadFile?authtoken=".$authkey."&scope=crmapi";
                        $path = '@'.$path;
                   
                        $post=array("id"=>$recordId,"content"=>$path);
                        $args = array(
					        'body' => $post
					    );
					    $response =  wp_remote_post($uri, $args ) ;
					    $result = wp_remote_retrieve_body($response);

                } //Attachment

		public function getRecords( $modulename, $methodname, $authkey , $selectColumns ="" , $xmlData="" , $extraParams = "" )
		{
			$uri = $this->zohocrmurl . $modulename . "/".$methodname."";
			/* Append your parameters here */
			$postContent = "scope=crmapi";
			$postContent .= "&authtoken={$authkey}";//Give your authtoken
			if($selectColumns == "")
			{
				$postContent .= "&selectColumns=All";
			}
			else
			{
				$postContent .= "&selectColumns={$modulename}( {$selectColumns} )";
			}

			if($extraParams != "")
			{
				$postContent .= $extraParams;
			}
			$postContent .= "&xmlData={$xmlData}";
			$args = array(
		        'body' => $postContent
		    );
		    $response =  wp_remote_post($uri, $args ) ;
		    $result = wp_remote_retrieve_body($response);
			$xml = simplexml_load_string($result);
			$json = json_encode($xml);
			$result_array = json_decode($json,TRUE);
			return $result_array;
		}

		public function convertLeads($modulename , $crm_id , $order_id , $lead_no , $authkey , $sales_order )
		{

	//Convert Leads And get Contact Id
			$methodname = 'convertLead';
			$uri = $this->zohocrmurl . $modulename . "/".$methodname."";
                        /* Append your parameters here */
                        $postContent = "scope=crmapi";
                        $postContent .= "&authtoken={$authkey}";//Give your authtoken
			$postContent .= "&leadId={$lead_no}";
			$LEAD_OWNER = $this->getConvertLeadOwner( $authkey , $lead_no );

			$xmlData  = "<Potentials>\n<row no=\"1\">\n";
			$xmlData .= "<option val=\"createPotential\">false</option>\n
				     <option val=\"assignTo\">".$LEAD_OWNER."</option>\n
				     <option val=\"notifyLeadOwner\">true</option>\n
				     <option val=\"notifyNewEntityOwner\">true</option>\n
				     </row>\n</Potentials>";
                        $postContent .= "&xmlData={$xmlData}";

                        $args = array(
					        'body' => $postContent
					    );
					    $response =  wp_remote_post($uri, $args ) ;
					    $result = wp_remote_retrieve_body($response);
                        $xml = simplexml_load_string($result);
                        $json = json_encode($xml);
                        $result_array = json_decode($json,TRUE);
			$CONTACT_ID = $result_array['Contact'];
			$ACCOUNT_ID = $result_array['Account'];
			//END Convert Lead

			$final_array = array();
			$final_array['SMOWNERID'] = $LEAD_OWNER;
			$final_array['CONTACT_ID'] = $CONTACT_ID;
			$final_array['ACCOUNT_ID'] = $ACCOUNT_ID;
			return $final_array;
		}

		public function getAccountId($authkey)
		{
			$Account_uri = "https://crm.zoho.com/crm/private/xml/Accounts/getRecords";
                        $Account_postContent = "scope=crmapi";
                        $Account_postContent .= "&authtoken={$authkey}";//Give your authtoken
                        $Account_postContent .= "&selectColumns=Accounts(ACCOUNTID)";

                        $args = array(
					        'body' => $Account_postContent
					    );
					    $response =  wp_remote_post($Account_uri, $args ) ;
					    $result = wp_remote_retrieve_body($response);

                        $xml = simplexml_load_string($result);
                        $json = json_encode($xml);
                        $result_array = json_decode($json,TRUE);
                        
                        $ACCOUNT_ID = $result_array['result']['Accounts']['row'][0]['FL'];
			return $ACCOUNT_ID;
		}

		public function getModules($TFA_authtoken)
		{
			$uri = "https://crm.zoho.com/crm/private/xml/Info/getModules?"; // Check Auth token present in Zoho //ONLY FOR TFA CHECK
			$postContent = "scope=crmapi";
			$postContent .= "&authtoken={$TFA_authtoken}";
						$args = array(
					        'body' => $postContent
					    );
					    $response =  wp_remote_post($uri, $args ) ;
					    $result = wp_remote_retrieve_body($response);
                        $xml = simplexml_load_string($result);
                        $json = json_encode($xml);
                        $result_array = json_decode($json,TRUE);
                        
                        return $result_array;
				
		}

		public function getConvertLeadOwner($modulename , $authkey , $record_id )
		{
			$zohourl = "https://crm.zoho.com/crm/private/xml/";
                        $methodname = 'getRecords';
			$module_slug = rtrim( $modulename , 's' );
                        $uri = $zohourl . $modulename . "/".$methodname."";
                        $postContent = "scope=crmapi";
                        $postContent .= "&authtoken={$authkey}";//Give your authtoken
                        $postContent .= "&id={$record_id}&selectColumns={$modulename}({$module_slug} Owner)";

                        $args = array(
					        'body' => $postContent
					    );
					    $response =  wp_remote_post($zohourl, $args ) ;
					    $result = wp_remote_retrieve_body($response);
                        $xml = simplexml_load_string($result);
                        $json = json_encode($xml);
                        $result_array = json_decode($json,TRUE);
                       
			$Lead_owner = $result_array['result'][$modulename]['row']['FL'][1];
			return $Lead_owner;
		}
	
		public function getAuthenticationToken( $username , $password  )
		{
			$username = urlencode( $username );
			$password = urlencode( $password );
			$param = "SCOPE=ZohoCRM/crmapi&EMAIL_ID=".$username."&PASSWORD=".$password;
			$url = "https://accounts.zoho.com/apiauthtoken/nb/create";

			$args = array(
		        'body' => $param
		    );
		    $response =  wp_remote_post($url, $args ) ;
		    $result = wp_remote_retrieve_body($response);
			$anArray = explode("\n",$result);
			$authToken = explode("=",$anArray['2']);
			$cmp = strcmp($authToken['0'],"AUTHTOKEN");
			if ($cmp == 0)
			{
				$return_array['authToken'] = $authToken['1'];
			}
			$return_result = explode("=" , $anArray['3'] );
			$cmp1 = strcmp($return_result['0'],"RESULT");
			if($cmp1 == 0)
			{
				$return_array['result'] = $return_result['1'];
			}
			if($return_result[1] == 'FALSE'){
				$return_cause = explode("=",$anArray[2]);
				$cmp2 = strcmp($return_cause[0],'CAUSE');
				if($cmp2 == 0)
					$return_array['cause'] = $return_cause[1];
			}
			return $return_array;
		}
	}
}
?>
