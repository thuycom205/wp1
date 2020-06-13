<?php
/**
 * Created by Magenest
 * User: Luu Thanh Thuy
 * Date: 05/03/2016
 * Time: 18:02
 */
public function sendRequest($method, $path, $paramter = null)
{
    $instance_url = $this->_scopeConfig->getValue(self::XML_PATH_SALESFORCE_INSTANCE_URL, ScopeInterface::SCOPE_STORE);
    $access_token = $this->_scopeConfig->getValue(self::XML_PATH_SALESFORCE_ACCESS_TOKEN, ScopeInterface::SCOPE_STORE);

    try {
        if (!$instance_url || !$access_token) {
            again:
            $login        = $this->getAccessToken();
            $instance_url = $login['instance_url'];
            $access_token = $login['access_token'];
        }
    } catch (\InvalidArgumentException $exception) {
        echo 'Exception Message: '.$exception->getMessage();
        return $exception->getMessage();
    }

    $headers  = [
        "Authorization: Bearer ".$access_token,
        "Content-type: application/json",
    ];
    $url      = $instance_url.$path;
    $params   = json_encode($paramter);
    $response = $this->makeRequest($method, $url, $headers, $params);
    $response = json_decode($response, true);
    if (isset($response[0]['errorCode']) && $response[0]['errorCode'] == 'INVALID_SESSION_ID') {
        goto again;
    }

    return $response;

}


while () {

}