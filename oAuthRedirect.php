<?php
/*
PHP implementation of Google Cloud Print
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

require_once 'Config.php';
require_once 'HttpRequest.Class.php';

if(isset($_GET['op']) && $_GET['op']=="getauth") {
	
	header("Location: ".$urlconfig['authorization_url']."?".http_build_query($redirectConfig));
	exit;
}

session_start();

// Google redirected back with code in query string.
if(isset($_GET['code']) && !empty($_GET['code'])) {
    
    $code = $_GET['code'];
    // create http request object and initialize with access token url
    $httpRequest = new HttpRequest($urlconfig['accesstoken_url']);
    $authConfig['code'] = $code;
    // Set data to be posted
    $httpRequest->setPostData($authConfig);
    // Send request
    $httpRequest->send();
    // Get request response
    $response = $httpRequest->getResponse();
    // parse json data
    $responseObj = json_decode($response);
    $accessToken = $responseObj->access_token;
    $_SESSION['accessToken'] = $accessToken;
    
    header("Location: example.php");
}

?>