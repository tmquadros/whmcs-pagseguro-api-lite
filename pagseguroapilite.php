<?php
function pagseguroapilite_config()
{
    return array(
        'FriendlyName' => array('Type' => 'System', 'Value' => 'PagSeguro API Lite (SuporteWHMCS.Com.Br)'),
        'email'        => array('FriendlyName' => 'Email', 'Type' => 'text', 'Size' => '40'),
        'token'        => array('FriendlyName' => 'Token', 'Type' => 'text', 'Size' => '50'),
        'tipo'         => array('FriendlyName' => 'Tipo de Checkout', 'Type' => 'dropdown', 'Options' => 'Padrão,Lightbox'),
    );
}

function pagseguroapilite_link($params)
{
    $xml_checkout = '<?xml version="1.0" encoding="UTF-8" standalone="yes"?>
<checkout>
    <currency>BRL</currency>
    <items>
        <item>
            <id>1</id>
            <description>' . htmlspecialchars($params['description']) . '</description>
            <amount>' . $params['amount'] . '</amount>
            <quantity>1</quantity>
        </item>
    </items>
    <reference>' . $params['invoiceid'] . '</reference>
    <redirectURL>' . $params['systemurl'] . '/viewinvoice.php?id=' . $params['invoiceid'] . '</redirectURL>
    <notificationURL>' . $params['systemurl'] . '/modules/gateways/' . basename(__FILE__) . '</notificationURL>
</checkout>';

    $curl = curl_init('https://ws.pagseguro.uol.com.br/v2/checkout/?email=' . $params['email'] . '&token=' . $params['token']);
    curl_setopt_array($curl, array(
        CURLOPT_POST           => 1,
        CURLOPT_POSTFIELDS     => $xml_checkout,
        CURLOPT_RETURNTRANSFER => 1,
        CURLOPT_HTTPHEADER     => array('Content-Type: application/xml; charset=UTF-8')));
    $retorno_curl    = curl_exec($curl);
    $checkout_parsed = simplexml_load_string($retorno_curl);
    if ($checkout_parsed->code) {
        if ($params["tipo"] == "Lightbox") {
            $result .= '<script type="text/javascript" src="https://stc.pagseguro.uol.com.br/pagseguro/api/v2/checkout/pagseguro.lightbox.js"></script>'."\n";
            $result .= '<script>'."\n";
            $result .= 'function iniciaPagamento() {'."\n";
            $result .= '  if (!PagSeguroLightbox("'.$checkout_parsed->code.'")) location.href="https://pagseguro.uol.com.br/v2/checkout/payment.html?code='.$checkout_parsed->code.'"'."\n";
            $result .= '}'."\n";
            $result .= '</script>'."\n";
            $result .= '    <input type="button" value="Pagar Agora" onclick="iniciaPagamento();">' . "\n";
        }
        else {
            $result = '<form action="https://pagseguro.uol.com.br/v2/checkout/payment.html" method="get">' . "\n";
            $result .= '    <input type="hidden" name="code" value="' . $checkout_parsed->code . '">' . "\n";
            $result .= '    <input type="submit" value="Pagar Agora">' . "\n";
            $result .= '</form>' . "\n";
        }
    } else {
        $result = '<font style="color:red">Ocorreu um erro na comunicação com o PagSeguro</font>';
        logTransaction($params['name'], $retorno_curl . print_r($params, true) . ($checkout_parsed ? " / " . $checkout_parsed : ""), 'Unsuccessful');
    }
    return $result;
}

if (basename(__FILE__) == basename($_SERVER['SCRIPT_NAME'])) {
    if (!array_key_exists('notificationCode', $_POST) || !array_key_exists('notificationType', $_POST)) {
        header($_SERVER["SERVER_PROTOCOL"] . " 404 Not Found");
        die();
    }
    require '../../init.php';
    require '../../includes/invoicefunctions.php';
    require '../../includes/gatewayfunctions.php';

    $GATEWAY = getGatewayVariables('pagseguroapilite');

    $curl = curl_init('https://ws.pagseguro.uol.com.br/v3/transactions/notifications/' . $_POST['notificationCode'] . '?email=' . $GATEWAY['email'] . '&token=' . $GATEWAY['token']);
    curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
    $xml = simplexml_load_string(curl_exec($curl));

    logTransaction($GATEWAY['name'], print_r($_POST, true) . print_r($xml, true), 'Successful');
    $invoiceid = checkCbInvoiceID($xml->reference, $GATEWAY["name"]);
    checkCbTransID($xml->code);

    if ($xml->status == 3 || $xml->status == 4) {
        $feeAmount = (float)$xml->creditorFees->intermediationRateAmount + (float)$xml->creditorFees->intermediationFeeAmount;
        addInvoicePayment($invoiceid, $xml->code, (float)$xml->grossAmount, $feeAmount, 'pagseguroapilite');
    }
}
