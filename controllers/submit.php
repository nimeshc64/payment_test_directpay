<!--/**-->
<!--* @author     Nimesh chathuranga-->
<!--* @datetime   30/06/2020-->
<!--*/-->

<?php
include 'config.php';

switch ($_POST['server']) {
    case 'local':
        $localUrl = "http://localhost/mastercardapi/public/initPluginItem";
        break;
    case 'dev':
        $localUrl = "https://testpay.directpay.lk/";
        break;
    case 'prod':
        $localUrl = "https://pay.directpay.lk/";
        break;
    default:
        $localUrl = "http://localhost/mastercardapi/public/initPluginItem";
        break;
}

$devUrl = "";
$prodUrl = "";

$keyfile = "file://" . __DIR__ . "/../keys/private_key.pem";
echo("<p style='color: white;'>keyPath: <br/>" . $keyfile . "</p>" . "\n");
$pri_key = openssl_pkey_get_private($keyfile);
$merchant = $_POST['_mId'];
$pluginName = $_POST['_pluginName'];
$pluginVersion = $_POST['_pluginVersion'];
$returnUrl = $_POST['_returnUrl'];
$cancelUrl = $_POST['_cancelUrl'];
$orderId = $_POST['_orderId'];
$reference = $_POST['_reference'];
$firstName = $_POST['_firstName'];
$lastName = $_POST['_lastName'];
$email = $_POST['_email'];
$description = $_POST['_description'];
$apiKey = $_POST['api_key'];
$responseUrl = $_POST['_responseUrl'];
$type = $_POST['_type'];
$amount = 0.00;
$currency = 'LKR';
$startDate = '';
$endDate = '';
$interval = '';
$doFirstPayment = 0;

switch ($type) {
    case "ONE_TIME":
        $amount = $_POST['_amount'];
        $currency = $_POST['_currency'];
        $dataString = $merchant . $amount . $currency . $pluginName . $pluginVersion . $returnUrl . $cancelUrl . $orderId .
            $reference . $firstName . $lastName . $email . $description . $apiKey . $responseUrl;
        break;
    case "RECURRING":
        $amount = $_POST['_amount'];
        $currency = $_POST['_currency'];
        $startDate = $_POST['_startDate'];
        $endDate = $_POST['_endDate'];
        $interval = $_POST['_interval'];
        $doFirstPayment = $_POST['_doFirstPayment'];
        $dataString = $merchant . $amount . $currency . $pluginName . $pluginVersion . $returnUrl . $cancelUrl . $orderId .
            $reference . $firstName . $lastName . $email . $description . $apiKey . $responseUrl . $startDate . $endDate .
            $interval . $doFirstPayment;
        break;
    case "CARD_ADD":
        $dataString = $merchant . $pluginName . $pluginVersion . $returnUrl . $cancelUrl . $orderId . $reference .
            $firstName . $lastName . $email . $description . $apiKey . $responseUrl;
        break;
}

echo("<p style='color: white;'>DataString: <br/>" . $dataString . "</p>" . "\n");

//$dataString = "DP00001100.00LKRShopify1.0http://localhost/dpIpgPage.htmlhttp://localhost/dpIpgPage.htmlDP12355DP12345UserAUserAabc@mail.comtest Product7bfe299e691039a0f86619a28bd9320dhttp://localhost/response.php";
$pkeyid = openssl_get_privatekey($pri_key);
$signResult = openssl_sign($dataString, $signature, $pkeyid, OPENSSL_ALGO_SHA256);
$signa = base64_encode($signature);

$req = '_mId=' . $merchant . '&_amount=' . $amount . '&_currency=' . $currency . '&_pluginName=' . $pluginName . '&_pluginVersion=' . $pluginVersion .
    '&_returnUrl=' . $returnUrl . '&_cancelUrl=' . $cancelUrl . '&_orderId=' . $orderId . '&_reference=' . $reference .
    '&_firstName=' . $firstName . '&_lastName=' . $lastName . '&_email=' . $email .
    '&_description=' . $description . '&api_key=' . $apiKey . '&_responseUrl=' . $responseUrl . '&_type=' . $type .
    '&_startDate=' . $startDate . '&_endDate=' . $endDate . '&_interval=' . $interval . '&_doFirstPayment=' . $doFirstPayment .
    '&signature=' . urlencode($signa);

echo("<p style='color: white;'>Request: <br/>" . $req . "</p>");

$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, $localUrl);
curl_setopt($ch, CURLOPT_POST, 1);
curl_setopt($ch, CURLOPT_POSTFIELDS, $req);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
$response = curl_exec($ch);
curl_close($ch);
echo $response;


?>
