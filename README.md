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

3) Click Create new Client ID button. A popup will appear. In Authorized redirect URIs text area enter url that should point to oAuthRedirect.php on your server.

4) After submitting this form, we can get the client Id, secret key etc.

Replace client_id, client_secret values in $redirectConfig and $authConfig arrays in Config.php file.

You also need to replace redirect_uri in both $redirectConfig and $authConfig arrays. This URL should
point to oAuthRedirect.php on your server.

## For Online Access hit index.php 

Online access requires authorization every time you need to send print to printer once token in Session gets expired.

## For Offline Access (when you want to send prints without user presence) hit offlineAccess.php

Offline access requires authorization only once or unless user has revoked access. You should use offline access when you want to send prints to printer with out the presence of user or send prints to printer using a cron job script.

Once you hit offlineAccess.php you will be redirected to offlineToken.php which will show you your refresh token.

You need to save refresh token to database, file or some cache systems as later on when you will send print to printer in offline mode you need to replace that refrsh token at line # 29 on cron.php
