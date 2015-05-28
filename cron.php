<?php

// To add printers to your account follow the following link 
// https://support.google.com/cloudprint/answer/1686197
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

require_once 'Config.php';
require_once 'GoogleCloudPrint.php';
    
// Create object
$gcp = new GoogleCloudPrint();

// Replace token you got in offlineToken.php
$refreshTokenConfig['refresh_token'] = 'YOUR-OFFLINE-ACCESS-TOKEN';

$token = $gcp->getAccessTokenByRefreshToken($urlconfig['refreshtoken_url'],http_build_query($refreshTokenConfig));

$gcp->setAuthToken($token);

$printers = $gcp->getPrinters();
//print_r($printers);

$printerid = "";
if(count($printers)==0) {
	
	echo "Could not get printers";
	exit;
}
else {
	
	$printerid = $printers[0]['id']; // Pass id of any printer to be used for print
	// Send document to the printer
	$resarray = $gcp->sendPrintToPrinter($printerid, "Printing Doc using Google Cloud Printing", "./pdf.pdf", "application/pdf");
	
	if($resarray['status']==true) {
		
		echo "Document has been sent to printer and should print shortly.";
	}
	else {
		echo "An error occured while printing the doc. Error code:".$resarray['errorcode']." Message:".$resarray['errormessage'];
	}
}

?>