<html>
<body>
<?php
// Joshua Harvey <josh@macjeezy.com>
// November 2018
// GitHub: github.com/therealmacjeezy
// JamfNation: therealmacjeezy

$errors = []; // Store all foreseen and unforseen errors here
// File Extensions go here.
$fileExtensions = ['xml', 'mobileconfig'];
$uploadDirectory = "/Profiles/";
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

if (isset($_POST['submit'])) {

    $siteName = $_POST['jamf_site'];
    $fileName = $_FILES['jamf_script']['name'];
    $fileInfo = pathinfo($fileName);
    $fileSize = $_FILES['jamf_script']['size'];
    $fileTmpName  = $_FILES['jamf_script']['tmp_name'];
    $fileType = $_FILES['jamf_script']['type'];

    if (empty($siteName)) {
        $errors[] = "No Site was selected. Please select a site and try again";
    }

    $fileExtension = $fileInfo['extension'];
    if (! in_array($fileExtension, $fileExtensions)) {
        $errors[] = "This file extension is not allowed. Please upload a valid file type (.xml, .mobileconfig).";
    }

    // This limits the size of the upload to 2.5 MB via bytes
    //if ($fileSize > 2500000) {
    //   $errors[] = "This file is more than 2.5 MB limit. Please adjust the size or contact agcy-mosm@mail.nasa.gov for help.";
    //}

    if (empty($errors)) {
        $scriptName = $siteName . "-" . $fileInfo['filename'];
		
        $uploadPath = $currentDir . $uploadDirectory . $scriptName;
        $didUpload = move_uploaded_file($fileTmpName, $uploadPath);
		$scriptContent = file_get_contents($uploadPath);
        $filterContent = htmlentities($scriptContent);
    $updateXML = <<< XML
<os_x_configuration_profile>
    <general>
        <name>$scriptName</name>
        <site>
            <name>$siteName</name>
        </site>
        <category>
            <name>Uploads</name>
        </category>
        <payloads>$filterContent</payloads>
    </general>
</os_x_configuration_profile>
XML;


        if ($didUpload) {
            //echo $updateXML;
            //echo "The file " . $siteName . "-" . basename($fileName) . " has been uploaded and is available for use in the Jamf Pro Server.\r\n";
			$get_data = callAPI('POST', 'https://your.jamf.pro/JSSResource/osxconfigurationprofiles/id/0', $updateXML);
            preg_match('/\<id\>([0-9]+)\</', $get_data, $matches);
            //var_dump($matches);
            echo "<a href='https://your.jamf.pro/OSXConfigurationProfiles.html?o=r&id=" . $matches[1] . "' target='_blank'>Click Here To Edit The Profile in a new tab</a>";
            echo "<br>";
            echo "<a href=\"index.html\">Click Here to Go Back.</a>";
        } else {
            $errors[] = "An error occurred with the upload process. Try again or contact agcy-mosm@mail.nasa.gov for help.";
        }
    }

    if (!empty($errors)) {
        foreach ($errors as $error) {
            echo $error . "\n";
            echo "<a href=\"javascript:history.go(-1)\">Try Again</a>";
        }
    }
}

?>
</body>
</html>