<?php

/*
Simple Http request class
Author, Yasir Siddiqui

Redistribution and use in source and binary forms, with or without
modification, are permitted provided that the following conditions are met:

* Redistributions of source code must retain the above copyright notice, this
  list of conditions and the following disclaimer.

* Redistributions in binary form must reproduce the above copyright notice,
  this list of conditions and the following disclaimer in the documentation
  and/or other materials provided with the distribution.

THIS SOFTWARE IS PROVIDED BY THE COPYRIGHT HOLDERS AND CONTRIBUTORS "AS IS"
AND ANY EXPRESS OR IMPLIED WARRANTIES, INCLUDING, BUT NOT LIMITED TO, THE
IMPLIED WARRANTIES OF MERCHANTABILITY AND FITNESS FOR A PARTICULAR PURPOSE ARE
DISCLAIMED. IN NO EVENT SHALL THE COPYRIGHT HOLDER OR CONTRIBUTORS BE LIABLE
FOR ANY DIRECT, INDIRECT, INCIDENTAL, SPECIAL, EXEMPLARY, OR CONSEQUENTIAL
DAMAGES (INCLUDING, BUT NOT LIMITED TO, PROCUREMENT OF SUBSTITUTE GOODS OR
SERVICES; LOSS OF USE, DATA, OR PROFITS; OR BUSINESS INTERRUPTION) HOWEVER
CAUSED AND ON ANY THEORY OF LIABILITY, WHETHER IN CONTRACT, STRICT LIABILITY,
OR TORT (INCLUDING NEGLIGENCE OR OTHERWISE) ARISING IN ANY WAY OUT OF THE USE
OF THIS SOFTWARE, EVEN IF ADVISED OF THE POSSIBILITY OF SUCH DAMAGE.
*/

class HttpRequest {
        
        public $httpResponse;
        public $ch;
        
        /**
	 * Function __construct
	 * Set member variables
	 * @param url $url  // Url to send http request to
	 */
        public function __construct($url = null) {
            
            // Initialize curl
            $this->ch = curl_init();
	   
            curl_setopt( $this->ch, CURLOPT_FOLLOWLOCATION,true);
            curl_setopt( $this->ch, CURLOPT_HEADER,false);
            curl_setopt( $this->ch, CURLOPT_RETURNTRANSFER,true);
	    curl_setopt( $this->ch, CURLOPT_SSL_VERIFYPEER, false);
	    curl_setopt( $this->ch, CURLOPT_HTTPAUTH,CURLAUTH_ANY);
	    
	     if(isset($url)) {
		$this->setUrl($url);
	    }
        }
	
	/**
	 * Function setUrl
	 * Set http request url
	 * @param string $url  // http request url
	 */
	public function setUrl($url) {
		curl_setopt( $this->ch, CURLOPT_URL, $url );
	}

        /**
	 * Function setPostData
	 * Set data to be posted to the url
	 * @param array $params  // Key value pairs of data to be posted
	 */
        public function setPostData( $params ) {
            
            curl_setopt( $this->ch, CURLOPT_POST, true );
            curl_setopt ( $this->ch, CURLOPT_POSTFIELDS,$params);
        }
	
	 /**
	 * Function setHeaders
	 * Set http request headers
	 * @param array $headers  // array containing headers
	 */
	public function setHeaders($headers) {
		curl_setopt($this->ch, CURLOPT_HTTPHEADER, $headers);
	}
        
        /**
	 * Function send
	 * Send http request
	 * return void
	 */
        public function send() {
            // execute curl
            $this->httpResponse = curl_exec( $this->ch );
        }
        
        /**
	 * Function getResponse
	 * return response of last http request sent
	 * return http response
	 */
        public function getResponse() {
            return $this->httpResponse;
        }
        
        /**
	 * Function __destruct
	 * class destructor
	 */
        public function __destruct() {
            curl_close($this->ch);
        }
}

?>