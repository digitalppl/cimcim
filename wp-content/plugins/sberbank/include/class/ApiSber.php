<?php
/**
 * Created by PhpStorm.
 * User: evgenius
 * Date: 2/18/15
 * Time: 9:42 PM
 */

class ApiSber
{
    protected $login = "test";
    protected $pass = "testPwd";

    function register($orderNumber, $amount, $returnUrl, $failUrl = false, $currency = false, $description = false,
                      $language = false, $pageView = false, $clientId = false, $params = array(),
                      $sessionTimeoutSecs = false, $expirationDate = false, $bindingId = false)
    {
        $query = http_build_query(
            array(
                "orderNumber" => $orderNumber,
                "amount" => $amount,
                "returnUrl" => $returnUrl,
                "userName" => $this->login,
                "password" => $this->pass
            )
        );

        $resp = json_decode(file_get_contents("https://3dsec.sberbank.ru/payment/rest/register.do?".$query));
        return $resp;
    }

    function getOrderStatus($orderId, $orderNumber, $amount)
    {
        $query = http_build_query(
            array(
                "orderId" => $orderId,
                "orderNumber" => $orderNumber,
                "amount" => $amount,
                "userName" => $this->login,
                "password" => $this->pass
            )
        );

        $resp = json_decode(file_get_contents("https://3dsec.sberbank.ru/payment/rest/getOrderStatus.do?".$query));
        return $resp;
    }
}