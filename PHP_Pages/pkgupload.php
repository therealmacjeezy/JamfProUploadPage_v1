<?php
// Joshua Harvey <josh@macjeezy.com>
// November 2018
// GitHub: github.com/therealmacjeezy
// JamfNation: therealmacjeezy

$errors = []; // Store all foreseen and unforseen errors here
// File Extensions go here.
$fileExtensions = ['pkg', 'dmg', 'mpkg'];
$uploadDirectory = "/Packages/";
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
    $fileName = $_FILES['jamf_package']['name'];
    $fileSize = $_FILES['jamf_package']['size'];
    $fileTmpName  = $_FILES['jamf_package']['tmp_name'];
    $fileType = $_FILES['jamf_package']['type'];

    if (empty($siteName)) {
        $errors[] = "No Site was selected. Please select a site and try again";
    }

    $fileExtension = strtolower(end(explode('.',$fileName)));
    if (! in_array($fileExtension, $fileExtensions)) {
        $errors[] = "This file extension is not allowed. Please upload a valid file type (.pkg, .mpkg, .dmg).";
    }

    // This limits the size of the upload to 6.5 GB via bytes
    if ($fileSize > 6500000000) {
        $errors[] = "This file is more than 6.5 GB limit. Please adjust the size or contact helpdesk@it.com for help.";
    }

    if (empty($errors)) {
        $pkgName = $siteName . "-" . basename($fileName);
        $updateXML = "<package><name>$pkgName</name><filename>$pkgName</filename><category>Uploads</category><info>$siteName</info><priority>15</priority></package>";

        $uploadPath = $uploadDirectory . $siteName . "-" . basename($fileName);
        $didUpload = move_uploaded_file($fileTmpName, $uploadPath);

        if ($didUpload) {
            //echo "The file " . $siteName . "-" . basename($fileName) . " has been uploaded and is available for use in the Jamf Pro Server.\r\n";
            $get_data = callAPI('POST', 'https://jamfurl/JSSResource/packages/id/0', $updateXML);
            header('Refresh:0; url=index.html');
            echo "<script type='text/javascript'>alert('Package Uploaded Succesfully!')</script>";
        } else {
            $errors[] = "An error occurred with the upload process. Try again or contact helpdesk@it.com for help.";
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
