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

echo "<b>Using: </b>" . "<span style='color:red'>" . "ALG:RS256" . "</span>";
echo "<br/>";
echo "↓";
echo "<br/>";

$jsonH = json_encode(($h));	

$header = base64url_encode($jsonH); 

echo "<b>Encoding ALG:256 as payload header: </b>" . "<span style='color:red'>" .$header . "</span>";
echo "<br/>";
echo "↓";
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

echo "<b>Creating Salesforce Claim Payload: </b>". "<span style='color:red'>" . $jsonC . "</span>";
$payload = base64url_encode($jsonC);
echo "<br/>";
echo "↓";
echo "<br/>";

echo "<b>Encoding Saleforce Claim Payload: </b>" ."<span style='color:red'>" . $payload . "</span>";
echo "<br/>";
echo "↓";
echo "<br/>";

$headload = $header . "." . $payload;

echo "<b>Encoded Header + Payload created: </b>" . "<span style='color:red'>" .$headload . "</span>";
echo "<br/>";
echo "↓";
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

echo "<b>Open Cryptographic Private Key from Server: </b>" . "<span style='color:red'>" . $private_key . "</span>";
echo "<br/>";
echo "↓";
echo "<br/>";
// Base64 encode the result
$secret = base64url_encode($s);
echo "<b>Encoded Private key using RSA with SHA256 cryptography: </b>" . "<span style='color:red'>" .$secret . "</span>";
echo "<br/>";
echo "↓";
echo "<br/>";

$token = $headload . "." . $secret;

echo "<b>Salesforce Authentication token created via OAuth 2.0 JWT Bearer Token Flow: </b>" . "<span style='color:red'>" .$token . "</span>";
echo "<br/>";
echo "↓";
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
	echo "<b>Success! Reading server response token..</b>\n";
	echo "<br/>";
	echo "↓";
	echo "<br/>";
	echo "<b>Salesforce access token received: </b>" . "<span style='color:red'>" .$token_request_body ."</span>";
	echo "<br/>";
	echo "<br/>";
?>