<?php
// Joshua Harvey <josh@macjeezy.com>
// November 2018
// GitHub: github.com/therealmacjeezy
// JamfNation: therealmacjeezy

$errors = []; // Store all foreseen and unforseen errors here
// File Extensions go here.
$fileExtensions = ['sh', 'py'];
$uploadDirectory = "/Scripts/";
$currentDir = getcwd();

libxml_use_internal_errors(true);

function callAPI($method, $url, $data){
    $curl = curl_init();

    switch ($method){
        case "POST":
            curl_setopt($curl, CURLOPT_POST, 1);
            if ($data)
                curl_setopt($curl, CURLOPT_POSTFIELDS, $data);
            break;
        case "PUT":
            curl_setopt($curl, CURLOPT_CUSTOMREQUEST, "PUT");
            if ($data)
                curl_setopt($curl, CURLOPT_POSTFIELDS, $data);
            break;
        default:
            if ($data)
                $url = sprintf("%s?%s", $url, http_build_query($data));
    }

    // OPTIONS:
    curl_setopt($curl, CURLOPT_URL, $url);
    curl_setopt($curl, CURLOPT_HTTPHEADER, array(
        #'APIKEY: 111111111111111111111',
        'Accept: application/xml',
        'Content-Type: application/xml',
    ));
    curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($curl, CURLOPT_HTTPAUTH, CURLAUTH_BASIC);
    // Change service account before production
    curl_setopt($curl, CURLOPT_USERPWD, "apiuser:apipass");
    curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);

    // EXECUTE:
    $result = curl_exec($curl);
    if(!$result){die("Connection Failure");}
    curl_close($curl);
    return $result;
}

if (isset($_POST['download'])) {

    $scriptName = $_POST['jamf_script_name'];

    if (empty($scriptName)) {
        $errors[] = "The download option was selected, but the script to download was left blank. Please select a script to download and try again.";
    }

    if (empty($errors)) {
		$siteURL = urlencode($scriptName);
        $get_data = callAPI('GET', 'https://jamfurl/JSSResource/scripts/name/' . $siteURL , false);

        $xml = simplexml_load_string($get_data);

        header("Content-Disposition: attachment; filename=\"$scriptName.sh\"");
        echo $xml->script_contents;
    }

    if (!empty($errors)) {
        foreach ($errors as $error) {
            echo $error . "\n";
            echo "<a href=\"javascript:history.go(-1)\">Try Again</a>";
        }
    }
}
?>
