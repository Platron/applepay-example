<?php
require_once '../vendor/autoload.php';

$paymentAmount = '1.50';

$client = new Client();
$parameters = [
    'pg_amount' => $paymentAmount,
    'pg_description' => 'Test Apple Pay payment',
    'pg_merchant_id' => Settings::MERCHANT_ID,
    'pg_secret_key' => Settings::MERCHANT_SECRET_KEY,
    'pg_salt' => 'random_salt',
];
$parameters['pg_sig'] = \platron\Signature::make('init_payment.php', $parameters, Settings::MERCHANT_SECRET_KEY);
$response = new SimpleXMLElement($client->get('init_payment.php', $parameters));

$redirectUrl = (string) $response->pg_redirect_url;
$matches = [];
preg_match('/customer=([^&]+)/', $redirectUrl, $matches);
$customer = $matches[1];

?>
<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Apple Pay example</title>
    <link rel="stylesheet" type="text/css" href="css/style.css">
    <script src="js/index.js"></script>
    <script src="js/support.js"></script>
    <script>
        const customer = '<?= $customer ?>';
        const paymentSystemName = '<?= Settings::PAYMENT_SYSTEM_NAME ?>';
        const paymentRequest = {
            countryCode: 'RU',
            currencyCode: 'RUB',
            total: {
                label: 'Your Company Inc.',
                amount: '<?= $paymentAmount ?>',
            },
            supportedNetworks: ['masterCard', 'visa'],
            merchantCapabilities: ['supports3DS'],
            //requiredShippingContactFields: ['phone', 'email'],
        };
    </script>
</head>
<body>
<div class="apple-pay">
    <h2>Оплатить с помощью Apple&nbsp;Pay</h2>
    <p>
        Это простой пример сайта демонстрирующий оплату с помощью Apple&nbsp;Pay.
    </p>
    <p>
        Поддерживающие Apple&nbsp;Pay браузеры отобразят кнопку ниже.
    </p>
    <div class="apple-pay-button" onclick="applePayButtonClicked(customer, paymentSystemName, paymentRequest)"></div>
</div>
</body>
</html>
