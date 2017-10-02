<?php
// functions.php


function GetPLN_rate_from_NBP(){
	// test pobieramy xml z NBP
	$Yesterday  = time(NULL)-24*3600;
	$dayback = time(NULL) - 10*24*3600;
	$nbp_url = 'http://api.nbp.pl/api/exchangerates/rates/a/usd/'.date('Y-m-d', $dayback).'/'.date('Y-m-d', $Yesterday).'/?format=json';
	$response_data = file_get_contents($nbp_url);
	//$data = simplexml_load_string($response_xml_data);
	$vals = json_decode($response_data);
	$last_nbp_entry_index = count($vals->{'rates'})-1;
	$last_nbp_entry = $vals->{'rates'}[$last_nbp_entry_index];
	
	return floatval($last_nbp_entry->{'mid'});
}
function check_txnid($tnxid){
	global $link;
	return true;
	$valid_txnid = true;
	//get result set
	$sql = mysql_query("SELECT * FROM `payments` WHERE txnid = '$tnxid'", $link);
	if ($row = mysql_fetch_array($sql)) {
		$valid_txnid = false;
	}
	return $valid_txnid;
}

function price_to_points($price){
	global $_SETTINGS;
	$groups_tmp = explode(";", $_SETTINGS['DONATES_VALUES']);
	$PAYMENTS_GROUPS = array();
	$prev=0;
	$prevV=0;
	
	foreach ($groups_tmp as $group){
		$values = explode("=", $group);
		// PRIZE => POINTS
		if (($values[1]>$price)&&($price>=$prev)){
			return intval($prevV);
		}
		$prev = $values[1];
		$prevV = $values[0];
		
		$PAYMENTS_GROUPS[$values[1]] = $values[0];
	}
	
	
}

function check_price($price, $id){
	$valid_price = false;
	//you could use the below to check whether the correct price has been paid for the product
	
	/*
	$sql = mysql_query("SELECT amount FROM `products` WHERE id = '$id'");
	if (mysql_num_rows($sql) != 0) {
		while ($row = mysql_fetch_array($sql)) {
			$num = (float)$row['amount'];
			if($num == $price){
				$valid_price = true;
			}
		}
	}
	return $valid_price;
	*/ 
	return true;
}

function updatePayments($data){
	$return_text = 'uninitialized';
	StrangeEvent("Add data", "PAYPAL");
	if (is_array($data)) {
		$date_formatted = date("Y-m-d H:i:s", strtotime($data['payment_date']));
			
		// check if payment with same txd already exist.
		$sql_data = DBarray(DBquery("SELECT * FROM `Payments` WHERE txn_id='".$data['txn_id']."'"));
		
		if (empty($sql_data)){
			$PLN_rate = GetPLN_rate_from_NBP();
			$sql = DBquery("INSERT INTO `Payments`(`id`, `operation_type`, `operation_status`, `ammount`, `UserID`, `LastUpdate`, `RAW`, `first_name`, 
								`last_name`, `Country`, `Street`, `ZIP`, `City`, `AddrState`, `CountryCode`, `payer_id`, `ipn_track_id`, `txn_id`, `PaymentDate`, 
								`ServiceDate`, `VAT`, `PLN_exchange`) 
							VALUES (
									NULL,
									'".$data['item_name']."' ,
									'".$data['payment_status']."' ,
									".$data['mc_gross']." ,
									".$data['custom']." ,
									".time(NULL).",
									'".print_r($data,TRUE)."' ,
									'".$data['first_name']."' ,
									'".$data['last_name']."' ,
									'".$data['address_country']."' ,
									'".$data['address_street']."' ,
									'".$data['address_zip']."' ,
									'".$data['address_city']."' ,
									'".$data['address_state']."' ,
									'".$data['address_country_code']."' ,
									'".$data['payer_id']."' ,
									'".$data['ipn_track_id']."' ,
									'".$data['txn_id']."' ,
									'".$date_formatted."' ,
									'".date("Y-m-d H:i:s")."' ,
									0 ,
									".$PLN_rate."
								)				
						");
				// Zwracamy status płatności
				$return_text = $data['payment_status'];
		}
		else{
			// Jeśli status jest inny niż Completed
			if ($sql_data['operation_status']!='Completed')
			{
				$sql = DBquery("UPDATE `Payments` SET
								`operation_status` = '".$data['payment_status']."',
								`PaymentDate` = '".$date_formatted."'
							WHERE `txn_id` = '".$data['txn_id']."'");
				
				$return_text = $data['payment_status'];
			}
			else $return_text = 'echo';
		}

		return $return_text;
	}
}
