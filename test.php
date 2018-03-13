<?php

// You need to set these three to the values for your own application
define('CONSUMER_KEY', '3MVG9d3kx8wbPieHPn2sTuBe8hPeE4Q4EFvpYEZnm5eue5EZcTt6mKAxQWkxxFumDJAt2rDz0hOu.P0nL70hC');
define('CONSUMER_SECRET', '1358863352701655057');
define('LOGIN_BASE_URL', 'https://test.salesforce.com');

//Json Header
$h = array(
	"alg" => "RS256"	
);

$jsonH = json_encode(($h));	
$header = base64_encode($jsonH); 

$exp = strval(time() + (5 * 60));

//Create JSon Claim/Payload
$c = array(
	"iss" => CONSUMER_KEY, 
	"sub" => "zacharym@accelentertainment.com", 
	"aud" => LOGIN_BASE_URL, 
	"exp" => $exp
);

$jsonC = (json_encode($c));	
$payload = base64_encode($jsonC);

// LOAD YOUR PRIVATE KEY FROM A FILE - BE CAREFUL TO PROTECT IT USING
// FILE PERMISSIONS!
$private_key = "12352";
//file_get_contents("server.key");

// This is where openssl_sign will put the signature
$s = "";

// SHA256 in this context is actually RSA with SHA256
$algo = "SHA256";

// Sign the header and payload
openssl_sign($header.'.'.$payload, $s, $private_key, $algo);

// Base64 encode the result
$secret = base64_encode($s);

$token = $header . '.' . $payload . '.' . $secret;

$token_url = LOGIN_BASE_URL.'/services/oauth2/token';

$post_fields = array(
	'grant_type' => 'urn:ietf:params:oauth:grant-type:jwt-bearer',
	'assertion' => $token
);

$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, $token_url);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
curl_setopt($ch, CURLOPT_FOLLOWLOCATION, TRUE);
curl_setopt($ch, CURLOPT_POST, TRUE);
curl_setopt($ch, CURLOPT_POSTFIELDS, $post_fields);
curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);

// Make the API call, and then extract the information from the response
    $token_request_body = curl_exec($ch) 
        or die("Call to get token from code failed: '$token_url' - ".print_r($post_fields, true));
		
	echo $token_request_body;
?>