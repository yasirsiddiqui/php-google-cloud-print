<!doctype html>
<html lang="en">
<head>
<style>    
    .disclaimer { 
    border: dotted 1px #885777; 
    padding: 1em; 
    background-color: #FFF0F5; 
    font-size: 1.5em; 
    font-style: italic; 
    color: #885777; 
    }
   </style>

    <title>Offline Access Token</title>
</head>
<body>
<?php
    if (isset($_GET['offlinetoken'])) {
    echo "<p class=\"disclaimer\">Here is your offline access token: ".$_GET['offlinetoken']." <br>You need to save it
    to database, file or some cache system so that you can use it later on.<br>
    You need to replace this token in cron.php at line # 29 either by querying database
    ,cache system or from file.";
}
?>
</body>
</html>
