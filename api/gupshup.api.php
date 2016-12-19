<?php

function send_sms($mobile_no, $msg)
{
	$request =""; //initialise the request variable
	$param[method]= "sendMessage";
	$param[send_to] = "91".$mobile_no;
	$param[msg] = $msg;
	$param[userid] = "20000xxxxx"; //user id
	$param[password] = "xxxxxxxx"; //pwd
	$param[v] = "1.1";
	$param[msg_type] = "TEXT"; //Can be "FLASH”/"UNICODE_TEXT"/”BINARY”
	$param[auth_scheme] = "PLAIN";
	//Have to URL encode the values
	foreach($param as $key=>$val) {
	$request.= $key."=".urlencode($val);
		//we have to urlencode the values
		$request.= "&";
		//append the ampersand (&) sign after each
		parameter/value pair
	}

	$request = substr($request, 0, strlen($request)-1);
	//remove final (&) sign from the request
	$url ="http://enterprise.smsgupshup.com/GatewayAPI/rest?".$request;
	$ch = curl_init($url);
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
	$curl_scraped_page = curl_exec($ch);
	curl_close($ch);
	return $curl_scraped_page;
}
?> 