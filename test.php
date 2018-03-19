<?php

// You need to set these three to the values for your own application
define('CONSUMER_KEY', "3MVG9d3kx8wbPieHPn2sTuBe8hPeE4Q4EFvpYEZnm5eue5EZcTt6mKAxQWkxxFumDJAt2rDz0hOu.P0nL70hC");
define('CONSUMER_SECRET', "1358863352701655057");
define('LOGIN_BASE_URL', "https://test.salesforce.com");

function base64url_encode($data) { 
  return rtrim(strtr(base64_encode($data), '+/', '-_'), '='); 
} 

//Json Header
$h = array(
	"alg" => "RS256"	
);

echo "Using ALG:RS256";
echo "<br/>";
echo "<br/>";

$jsonH = json_encode(($h));	

$header = base64url_encode($jsonH); 

echo "<b>Encoding ALG:256 as payload header: </b>" . $header . "\n";
echo "<br/>";
echo "<br/>";

$exp = strval(time() + (5 * 60));

//Create JSon Claim/Payload
$c = array(
	"iss" => CONSUMER_KEY, 
	"sub" => "zacharym@accelentertainment.com.dev", 
	"aud" => LOGIN_BASE_URL, 
	"exp" => $exp
);

$jsonC = (json_encode($c));	

echo "Creating Salesforce Claim Payload: ". $c . "\n";
$payload = base64url_encode($jsonC);
echo "<br/>";
echo "<br/>";

echo "Encoding Saleforce Claim Payload: " . $payload . "\n";
echo "<br/>";
echo "<br/>";

$headload = $header . "." . $payload;

echo "Encoded Header + Payload created: " . $headload . "\n";
echo "<br/>";
echo "<br/>";

// LOAD YOUR PRIVATE KEY FROM A FILE - BE CAREFUL TO PROTECT IT USING
// FILE PERMISSIONS!
$private_key = file_get_contents("server.key");


// This is where openssl_sign will put the signature
$s = "";

// SHA256 in this context is actually RSA with SHA256
$algo = "SHA256";

// Sign the header and payload
openssl_sign($headload, $s, $private_key, $algo);

echo "Open Cryptographic Private Key from Server: " . $s . "\n";
echo "<br/>";
echo "<br/>";
// Base64 encode the result
$secret = base64url_encode($s);
echo "Encoded Private key using RSA with SHA256 cryptography: " . $secret . "\n";
echo "<br/>";
echo "<br/>";

$token = $headload . "." . $secret;

echo "Salesforce Authentication token created via OAuth 2.0 JWT Bearer Token Flow: " . $token . "\n";
echo "<br/>";
echo "<br/>";


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
	echo "Success! Reading server response token..\n";
	echo "<br/>";
	echo "<br/>";
	echo "Salesforce access token received: " . $token_request_body ."\n";
	echo "<br/>";
	echo "<br/>";
?>