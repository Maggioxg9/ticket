<?php

// You need to set these three to the values for your own application
define('CONSUMER_KEY', "3MVG9d3kx8wbPieHPn2sTuBe8hPeE4Q4EFvpYEZnm5eue5EZcTt6mKAxQWkxxFumDJAt2rDz0hOu.P0nL70hC");
define('CONSUMER_SECRET', "1358863352701655057");
define('LOGIN_BASE_URL', "https://test.salesforce.com");

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
	"sub" => "zacharym@accele", 
	"aud" => LOGIN_BASE_URL, 
	"exp" => $exp
);

$jsonC = (json_encode($c));	
$payload = base64_encode($jsonC);

$headload = $header . "." . $payload;

// LOAD YOUR PRIVATE KEY FROM A FILE - BE CAREFUL TO PROTECT IT USING
// FILE PERMISSIONS!
$private_key = <<<EOD
-----BEGIN RSA PRIVATE KEY-----
MIIEpAIBAAKCAQEAqGG7tSEF74Q6e+rTfuL0ntTClZKxSXHCwZWBInOqi9+maEUX
bgW/UBEO/TIICflhc0ZT17/0ZNf8GDtGDVAJjfWdYlvTqpdGvx1iYu64r1S5NqDO
EzjWq1RgYgoJwu8/bnNcNk8kv6J0opKTF6+F6ad5+D7abApbz7577Iebxs3QglcD
peSbAjcuzdA2LQ1agZKffzB52qua4Ra5gE7RXVWL7Jpdq0xJqpwA7Oc02PvzmaBE
jeqysb2e1+0JrARKGAZgr6DvGwjldwNSHvso20OInPdrg6sbeW3VUrtbLP7NBe0i
K9Ns6ac42HZiKKA6/KP3xCsMaFqWgjFcO9sSaQIDAQABAoIBADLaeYdlttY0uDkO
ndt3jeueVDo6OIL28ZvnnD2cc0iVPjvFiiL3Z2U2EbHUd2sTvyPCfLXvSk9ej8t9
s1V387rFEEhRdWbTE1HK/RjmAi8pxlpCTJqvkt3huM5DO9l8ykqAWrwoQPADj3g+
DmCb6TS9GPApg2wX4MyoOeMgvaJXrvdRantoabnTIaqbwUIRUommajrmzp+58KkK
UGlDSrlKTADxZoO0ylaHMHPAcUaFsGvZYp8ymEMyFXcg17sq+avgOx1YOWTl5ctb
CJczyHXLuthFb0rq2entcEZswqGMb2RnQ7MJKgvSdhoc3IluzcPLiMJ7uKoyc0W0
1hfqwcUCgYEA2NvDr3pYubwHWo9u8ccOxaR5iFV9MdfT4kNkzfjuDKavPGtqeaqn
DnkoD2tFl63L3cOPzhSnTmoxwaVEedzvXTdBhx7QNcwQS+CyqZ/PxTLYwkbvzPTJ
+HfmBHEUn0dfdQdr4fzRuBbPdz1lE7cUm7AQkpqjuqclJ6On4gSDdWsCgYEAxsYJ
5WghRv72PbxTEUk8JtyK3wnr3DNPvg+4ZOE7wHoAowqXWwtgaNh5WZlQh/vF0kaQ
yViqx3I175L8Y1D3qHW0I7H135C1zO5sO+AGwIP63NdJhebQn1RDj7JQGir8ZAZV
lawusHO/F6W2cAdrKyvPdz5YjG/NLhgujg9W+HsCgYEAioCp1ax9eHrX84YCZNcu
3xpyKJn5MNlY0X0bp9WiLGBVMXCle3l74umMpXRs6TguKcAaW5OjW5jFmJk2R+LM
2itNZcBPWNyxV9T2JxITwC3+dO6bf7qkOFZTiWiztBsAo50O0TIIu9uNu+lxYgsf
VWQ44Xl0suTH/S4wLlGVqGsCgYBZk5mWFm0Yr4GMpLfSjGxdHvo+NRxjaYRAJJl2
bMIepVxwHS/EScYyuYtGQ381KNMnRcGt2xVrOB7yTg0LPMu+0PxTMbmZ/H7RUYjZ
GsYwL8n3h9WMUE4n3zNrKHJMuJ5w/SHIh4Wq4x50q6shpVY4aE/gbuo8r42JfkBM
qMEKzQKBgQDSBvO1jYxOaBdGWIRtFryHv7JVkEuoDEF0SAGisT1LCRAGFf8OqlxD
6s5IrD6OXyn8v/EccjRoX/Uu8lqfnrlPyFFBenuK8RjdQswFdxWEFlhHPMRagaH+
BKkLpZOom1gLwQD/Pm0YyQ9kqxpOz6C8E1LPHbweMlfV==
-----END RSA PRIVATE KEY-----
EOD;

// This is where openssl_sign will put the signature
$s = "";

// SHA256 in this context is actually RSA with SHA256
$algo = "SHA256";

// Sign the header and payload
openssl_sign($headload, $s, $private_key, $algo);

// Base64 encode the result
$secret = base64_encode($s);

$token = $headload . "." . $secret;

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