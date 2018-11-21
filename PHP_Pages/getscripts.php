<?php
// Joshua Harvey <josh@macjeezy.com>
// November 2018
// GitHub: github.com/therealmacjeezy
// JamfNation: therealmacjeezy

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
			'APIKEY: 111111111111111111111',
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

$get_data = callAPI('GET', 'https://jamfurl/JSSResource/scripts', false);

$xml = simplexml_load_string($get_data);

$fileName = "ScriptList.xml";

$xml->asXML($fileName);

?>