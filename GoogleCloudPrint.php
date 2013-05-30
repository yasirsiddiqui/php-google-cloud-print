<?php
/**
 * PHP implementation of Google Cloud Print
 *
 * This program is free software; you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation; either version 3 of the License, or
 * (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program. If not, see <http://www.gnu.org/licenses/>.
 */
class GoogleCloudPrint {
	
	
	const LOGIN_URL 		  = "https://www.google.com/accounts/ClientLogin";
	const PRINTERS_SEARCH_URL = "https://www.google.com/cloudprint/interface/search";
	const PRINT_URL 		  = "https://www.google.com/cloudprint/interface/submit";
	
	private $emailaddress;
	private $password;
	private $authtoken;
	
	/**
	 * Function __construct
	 * Set private members varials to blank
	 */
	public function __construct() {
		
		$this->emailaddress = "";
		$this->password = "";
		$this->authtoken = "";
	}
	
	/**
	 * Function loginToGoogle
	 * 
	 * Try to login to Google using email address(gmail account) and password
	 *
	 * @param Email address $email     // Email address to login with
	 *
	 * @param Password $password       // Password to login with
	 */
	public function loginToGoogle($email,$password) {
		
		// check user has provided email address and password?
		if(empty($email)||empty($password)) {
			// If not then throw exception
			throw new Exception("Please provide some login information");
		}
		
		// Set private variables to user provided info
		$this->emailaddress = $email;
		$this->password = $password;
		
		// Prepare post fileds required for the login
		$loginpostfileds = array(
				
		"accountType" => "HOSTED_OR_GOOGLE",
		"Email" => $this->emailaddress,
		"Passwd" => $this->password,
		"service" => "cloudprint",
		"source" => "GCP"
		);
		
		// Make http call for login
		$loginresponse = $this->makeHttpCall(self::LOGIN_URL,$loginpostfileds);
		// Get Auth token as token will be used for getting and send print command to printers
		$token = $this->getAuthToken($loginresponse);
		// Check if we have token on the response
		if(!empty($token)&&!is_null($token)) {
			// Assign token to private variable
			$this->authtoken = $token;
			return true;
		}
		else {
			return false;
		}
		
	}
	
	/**
	 * Function getPrinters
	 *
	 * Get all the printers added by user on Google Cloud Print. 
	 * Follow this link https://support.google.com/cloudprint/answer/1686197 in order to know how to add printers
	 * to Google Cloud Print service.
	 */
	public function getPrinters() {
		
		// Check if we have auth token
		if(empty($this->authtoken)) {
			// We don't have auth token so throw exception
			throw new Exception("Please first login to Google by calling loginToGoogle function");
		}
		
		// Prepare auth headers with auth token
		$authheaders = array(
		"Authorization: GoogleLogin auth=" . $this->authtoken,
		"GData-Version: 3.0",
		);
		
		// Make Http call to get printers added by user to Google Cloud Print
		$responsedata = $this->makeHttpCall(self::PRINTERS_SEARCH_URL,array(),$authheaders);
		$printers = json_decode($responsedata);
		
		// Check if we have printers?
		if(is_null($printers)) {
			// We dont have printers so return balnk array
			return array();
		}
		else {
			// We have printers so returns printers as array
			return $this->parsePrinters($printers);
		}
		
	}
	
	/**
	 * Function sendPrintToPrinter
	 * 
	 * Sends document to the printer
	 * 
	 * @param Printer id $printerid    // Printer id returned by Google Cloud Print service
	 * 
	 * @param Job Title $printjobtitle // Title of the print Job e.g. Fincial reports 2012
	 * 
	 * @param File Path $filepath      // Path to the file to be send to Google Cloud Print
	 * 
	 * @param Content Type $contenttype // File content type e.g. application/pdf, image/png for pdf and images
	 */
	public function sendPrintToPrinter($printerid,$printjobtitle,$filepath,$contenttype) {
		
	// Check if we have auth token
		if(empty($this->authtoken)) {
			// We don't have auth token so throw exception
			throw new Exception("Please first login to Google by calling loginToGoogle function");
		}
		// Check if prtinter id is passed
		if(empty($printerid)) {
			// Printer id is not there so throw exception
			throw new Exception("Please provide printer ID");	
		}
		// Open the file which needs to be print
		$handle = fopen($filepath, "rb");
		if(!$handle)
		{
			// Can't locate file so throw exception
			throw new Exception("Could not read the file. Please check file path.");
		}
		// Read file content
		$contents = fread($handle, filesize($filepath));
		fclose($handle);
		
		// Prepare post fields for sending print
		$post_fields = array(
				
			'printerid' => $printerid,
			'title' => $printjobtitle,
			'contentTransferEncoding' => 'base64',
			'content' => base64_encode($contents), // encode file content as base64
			'contentType' => $contenttype		
		);
		// Prepare authorization headers
		$authheaders = array(
			"Authorization: GoogleLogin auth=" . $this->authtoken
		);
		// Make http call for sending print Job
		$response = json_decode($this->makeHttpCall(self::PRINT_URL,$post_fields,$authheaders));
		
		// Has document been successfully sent?
		if($response->success=="1") {
			
			return array('status' =>true,'errorcode' =>'','errormessage'=>"");
		}
		else {
			
			return array('status' =>false,'errorcode' =>$response->errorCode,'errormessage'=>$response->message);
		}
		
	}
	
	/**
	 * Function parsePrinters
	 * 
	 * Parse json response and return printers array
	 * 
	 * @param $jsonobj // Json response object
	 * 
	 */
	private function parsePrinters($jsonobj) {
		
		$printers = array();
		if (isset($jsonobj->printers)) {
			foreach ($jsonobj->printers as $gcpprinter) {
				$printers[] = array('name' =>$gcpprinter->name,'id' =>$gcpprinter->id);
			}
		}
		return $printers;
	}
	
	/**
	 * Function getAuthToken
	 *
	 * Parse data to get auth token
	 *
	 * @param $response // Respone
	 *
	 */
	private function getAuthToken($response) {
		
		// Match Auth tag
		preg_match("/Auth=([a-z0-9_-]+)/i", $response, $matches);
		$authtoken = @$matches[1];
		return $authtoken;
	}
 	
	/**
	 * Function makeHttpCall
	 * 
	 * Makes http calls to Google Cloud Print using curl
	 *
	 * @param URL $url // Http url to hit
	 * 
	 * @param Post fields $postfields // array of post fields to be posted
	 * 
	 * @param Headers $headers // Array of http headers
	 *
	 */
	private function makeHttpCall($url,$postfields=array(),$headers=array()) {
		
		// Initialize the curl
		$curl = curl_init($url);
		
		// Check if it is a HTTP post curl request
		if(!empty($postfields)) {
			
			// As is HTTP post curl request so set post fields
			curl_setopt($curl, CURLOPT_POST, true);
			curl_setopt($curl, CURLOPT_POSTFIELDS, $postfields);
		}
		// Check if curl request contains headers
		if(!empty($headers)) {
			
			// As curl requires header so set headers here
			curl_setopt($curl, CURLOPT_HTTPHEADER, $headers);
		}
		
		curl_setopt($curl, CURLOPT_HTTPAUTH, CURLAUTH_ANY);
		curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
		curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
		
		// Execute the curl and return response
		$response = curl_exec($curl);
		curl_close($curl);
		
		return $response;
	}
	
	
	
}