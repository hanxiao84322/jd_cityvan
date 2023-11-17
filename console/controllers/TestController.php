<?php
$host = "https://jisuqgclwz.market.alicloudapi.com";
$path = "/illegal/query";
$method = "ANY";
$appcode = "ac8fb1b7bfb5422db77fec3563e84504";
$headers = array();
array_push($headers, "Authorization:APPCODE " . $appcode);
//根据API的要求，定义相对应的Content-Type
array_push($headers, "Content-Type".":"."application/json; charset=UTF-8");
$querys = "carorg=beijing&engineno=jjnlc00039&frameno=229561&iscity=0&lsnum=AH5b57&lsprefix=%E6%B5%99&lstype=02&mobile=mobile";
$bodys = "null";
$url = $host . $path . "?" . $querys;

$curl = curl_init();
curl_setopt($curl, CURLOPT_CUSTOMREQUEST, $method);
curl_setopt($curl, CURLOPT_URL, $url);
curl_setopt($curl, CURLOPT_HTTPHEADER, $headers);
curl_setopt($curl, CURLOPT_FAILONERROR, false);
curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
curl_setopt($curl, CURLOPT_HEADER, true);
if (1 == strpos("$".$host, "https://"))
{
    curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
    curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, false);
}
curl_setopt($curl, CURLOPT_POSTFIELDS, $bodys);
var_dump(curl_exec($curl));
