Google Cloud Print
======================

PHP class to print documents using Google Cloud Print using OAuth2.
First of all you have to add printers to Google Cloud Print 
using your Gmail account and Google Chrome browser. Follow the
instructions on the following link to add printer to Google Cloud Print

https://support.google.com/cloudprint/answer/1686197

Google OAuth Prerequisites

1) Create Google API project and get OAuth credentials.

Create Google OAuth Credentials

1) Create new project and get the corresponding OAuth credentials using Google developer console
https://console.developers.google.com/

2) Select APIS & AUTH –> credentials from the left menu.

3) Click Create new Client ID button and complete data on this wizard.

4) After submitting this form, we can get the client Id, secret key etc

Replace client_id, client_secret values in $redirectConfig and $authConfig arrays in Config.php file.

You also need to replace redirect_uri in both $redirectConfig and $authConfig arrays. This URL should
point to oAuthRedirect.php on your server.

Hit index.php and see results.
