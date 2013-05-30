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

require_once 'GoogleCloudPrint.php';

// Create object
$gcp = new GoogleCloudPrint();

// Login to Googel, email address and password is required
if($gcp->loginToGoogle("someone@gmail.com", "someonepassword")) {
	
	// Login is successfull so now fetch printers
	$printers = $gcp->getPrinters();
	echo "<pre>";
	print_r($printers);
	echo "</pre>";
	
	$printerid = "";
	if(count($printers)==0) {
		
		echo "Could not get printers";
		exit;
	}
	else {
		
		$printerid = $printers[0]['id']; // Pass id of any printer to be used for print
	}
	// Send document to the printer
	$resarray = $gcp->sendPrintToPrinter($printerid, "Printing Doc using Google Cloud Printing", "./examplepdf.pdf", "application/pdf");
	
	if($resarray['status']==true) {
		
		echo "Document has been sent to printer and should print shortly.";
	}
	else {
		
		echo "An error occured while printing the doc. Error code:".$resarray['errorcode']." Message:".$resarray['errormessage'];
	}
	
}
else {
	
	echo "Login failed please check login info.";
	exit;
}