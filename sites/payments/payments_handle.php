<?php

// PayPal settings
$paypal_email = 'kontakt@skylab.pl';
$return_url = BDIR.'donate/successful';
$cancel_url = BDIR.'donate/cancel';
$notify_url = BDIR.'donate/paypal';

$payment_name =  strtolower(htmlspecialchars($_POST['payment_type']));
$amount = intval($_POST['prize']);

$item_name = "";
// Include Functions
include("functions.php");

// Check if paypal request or response
if (!isset($_POST["txn_id"]) && !isset($_POST["txn_type"])){
	$querystring = '';
	
	// Firstly Append paypal account to querystring
	$querystring .= "?business=".urlencode($paypal_email)."&";
	
	//The item name and amount can be brought in dynamically by querying the $_POST['item_number'] variable.
	$querystring .= "item_name=".urlencode($payment_name)."&";
	$querystring .= "amount=".urlencode($amount)."&";
	$querystring .= "currency_code=USD&";
	$querystring .= "first_name=".urlencode($_POST['first_name'])."&";
	$querystring .= "last_name=".urlencode($_POST['last_name'])."&";
	$querystring .= "payer_email=".urlencode($_POST['email'])."&";
	
	
	
	
	//loop for posted values and append to querystring
	foreach($_POST as $key => $value){
		$value = urlencode(stripslashes($value));
		$querystring .= "$key=$value&";
	}
	
	// Append paypal return addresses
	$querystring .= "return=".urlencode(stripslashes($return_url))."&";
	$querystring .= "cancel_return=".urlencode(stripslashes($cancel_url))."&";
	$querystring .= "notify_url=".urlencode($notify_url);
	
	// Append querystring with custom field
	$querystring .= "&custom=".$User->ID;
	
	// Redirect to paypal IPN
	
	header('location:https://www.sandbox.paypal.com/cgi-bin/webscr'.$querystring);
	exit();

}
?>
