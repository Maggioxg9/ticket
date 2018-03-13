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
$private_key = <<<EOD
-----BEGIN RSA PRIVATE KEY-----
MIIEpQIBAAKCAQEA3FADyTKcNUF9N/4i0vfYQG1JW5u/zakFCr4xiOTMXk9wYKh9
EQ6mZQvxOInV3Cm/gxDdPBDnnsF/7csBxEQRNt6zusmuUui+ktjpXDAeGMeEKT0F
+56PHzVJhm8gNioSjAhHRJMbbxd+/QQvwdWpU8MgSk4aDj5prhYR1XWmnlOI2WSg
v9SNbTygE6BMkwpzDi2VCt3V35wNd5oMh9xwSpglQhZMsWW7dZWncMo3lrCFjGLv
zM0iy6G6Hvm89ERHsikOImvWuW0U9ZIW51NWQx/5wSQDi583b2Nj4coX8c4pcObg
tek1cuRpIWKZLaN5O1fiW8+cId4QyNihp7evmQIDAQABAoIBAFXwzJlGqdLIei1G
cJU1Y2E2gIBA0GBMh4/6Q15wShycBm1eLHNj8JrIPs/cTNV2X6OkB3kv6vpt5xZ+
s3C5ULHDy/6YP+1Np1GnVdjFWGg4JCDmEhhmcNQuuEC9xqX6YYPIkux4KiJ62ume
IXcOfuAS5Ny3fMiDpjvnlgtbuqRVnjbAjqva2COQf0UKf3HQeBspiPgCD7ANHlr1
nQw18Hl9BAMKbR5m3ppvEgQ0T3co/KfJ2rGcjy9aBZ+nQM5+AObPalEiwStybDcD
9Ydbc6rU4zmy6hCxtHcjpaCqC4sEfzJrJp8cJ4oOEDYC266wsBORKnp32rBZTeLm
9GKFutECgYEA+OpmA/UaCpVRAmbRrBXz31vpRJyqZQi4GLAqZLi9ImQ/OWC8P5BN
Rnvfu+zN5inUXil1+ofZ1Cn46Hh+sbGIFlHcIf7i7273BuFlnU12XVu+RradVxbm
hB23sCkUnyxoPTcxOKaGxKYKIWKk1pD+EOSLLOcUjPQhafelP2SGXnUCgYEA4pU2
0KigdJ6yHnSppsi7g2NDLmslfqg9FX400zM9QiZChAmpmTPXXi5lWvcW98IIKnZ/
TafQJB/PUmO7CXDUff4nMxcd+WdTHuCcivObJuyF6xeFvqedBWH97UHPQUO6/zZU
3R66TePwLXLJZjCnn3jNv/GofgnHH6Prn3OMMBUCgYEAvTt9qN6KGKeheYDvP7NE
vnaiZT3xMHQreOFcqUvkxaOOiTFoi65JgR8lXTnO4n7Ea317qqXizS/Hgfww3SgJ
PGapQtMCdWJXvExYsviz1o+rnRixjwbi4mexQORKQCRDbt5pthareA5+vxi+Fj0O
WYtV3yIh9nKWgHf7kbD7kg0CgYEA1fFgfasLPrJtqvYrDnFxJMFAOP8wuyQt8TJm
vJkgGWq42aWp/x+FFsemKjsu86fBQB79Wxy+Gq3ye2/xthtUeNbWupX0Vn8qa/hX
t5gHgrxIQs/GGFx5lhCNzE2cXZqPYdUyUktmTI+SQ8ejxRrh22EcnUWX+9JNs2F6
5OccknUCgYEAiYjACOKBOrzVzBE0Fcp4TAdqkand+LS5S3yzkxqanLWi4YhPY1pY
On90G718gUPhMupHxY1W5MD3BxvoPW45crE4O4TMGQBkwM3jAtYGkT8z4t5eLmmw
I3I0MgTYR/kfcTZuSTNakwkiGPGki9ggYHGgWDa85gJAgQHMqT80Z2L=
-----END RSA PRIVATE KEY-----
EOD;

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