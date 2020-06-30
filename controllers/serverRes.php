<?php
$file = fopen("log.txt","a");
$json_str = file_get_contents('php://input');

$json_obj = json_decode($json_str);

$dataString =  $json_obj->orderId.$json_obj->trnId.$json_obj->status.$json_obj->desc;
$signature = $json_obj->signature;

$keyfile = "file://" . __DIR__ . "/keys/public_key.pem";
$pubKeyid = openssl_get_publickey($keyfile);
$signatureVerify = openssl_verify($dataString, base64_decode($signature), $pubKeyid, OPENSSL_ALGO_SHA256);

if ($signatureVerify == 1) {
    fwrite($file, "Signature valid"."\n");
} elseif ($signatureVerify == 0) {
    fwrite($file, "Signature invalid 1"."\n");
} else {
    fwrite($file, "Signature invalid 2"."\n");
}


fwrite($file,print_r($json_str, true)."\n");
fwrite($file, "\n");
fclose($file);

