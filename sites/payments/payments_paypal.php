<?php
// zwrot z paypala


	include('functions.php');
	// Response from Paypal

	// read the post from PayPal system and add 'cmd'
	$req = 'cmd=_notify-validate';
	foreach ($_POST as $key => $value) {
		$value = urlencode(stripslashes($value));
		$value = preg_replace('/(.*[^%^0^D])(%0A)(.*)/i','${1}%0D%0A${3}',$value);// IPN fix
		$req .= "&$key=$value";
	}
		
	// assign posted variables to local variables
	$data = $_POST;
	StrangeEvent("Płatność paypayl <br>", "PAYPAL", $data);

	// Choose url
	//if(array_key_exists('test_ipn', $ipn_post_data) && 1 === (int) $ipn_post_data['test_ipn'])
	    $url = 'https://www.sandbox.paypal.com/cgi-bin/webscr';
	//else
	 //   $url = 'https://www.paypal.com/cgi-bin/webscr';

	// Set up request to PayPal
	$request = curl_init();
	curl_setopt_array($request, array
	(
	    CURLOPT_URL => $url,
	    CURLOPT_POST => TRUE,
	    CURLOPT_POSTFIELDS => http_build_query(array('cmd' => '_notify-validate') + $data),
	    CURLOPT_RETURNTRANSFER => TRUE,
	    CURLOPT_HEADER => FALSE,
	));

	// Execute request and get response and status code
	$response = curl_exec($request);
	$status   = curl_getinfo($request, CURLINFO_HTTP_CODE);

	// Close connection
	curl_close($request);

	if($status == 200 && $response == 'VERIFIED')
	{ 
	
		$PaymentStatus = updatePayments($data);
		$PaymentUser = oUser::withID($data['custom']);
		if ($PaymentUser==BAD_LOGIN_DATA){
			StrangeEvent("Brak poprawnych danych użytkownika w danych z systemu. Brak użytkownika o numerze:".$data['custom'].". Koniecznie sprawdzić", "PAYPAL", $data);
	  		exit(0);
		}

		// w zależności od tego co przyjdzie z PayPal podejmuje odpowiednie kroki:
		switch ($PaymentStatus){
			case 'Completed':
				$points = intval(price_to_points($data['mc_gross']));
				$PaymentUser->Points->AddPoint($points, DONATE_POINTS, 0, 'User has buyed a points');
				$PaymentUser->SendNotify("Confirmation succeed. We got your payment. $points points added to your account", "Payments",'','PAYMENT','PAYMENT');
			break;

			case 'Pending':
				$PaymentUser->SendNotify("We got information about your payment. We are waiting for confirmation from PayPal. ", "Payments",'','PAYMENT','PAYMENT');
			break;

			case 'Expired':
				$PaymentUser->SendNotify("Your payment with ID: ".$data['txn_id']." failed. Please, try again.", "Payments",'','PAYMENT','PAYMENT');
			break;
			
			case 'Failed':
				$PaymentUser->SendNotify("Your payment with ID: ".$data['txn_id']." has expired...", "Payments",'','PAYMENT','PAYMENT');
			break;

			case 'Declined':
				$PaymentUser->SendNotify("Your payment with ID: ".$data['txn_id']." has been declined.", "Payments",'','PAYMENT','PAYMENT');
			break;

			case 'Canceled_Reversal':
				$PaymentUser->SendNotify("Your payment with ID: ".$data['txn_id']." has been canceled", "Payments",'','PAYMENT','PAYMENT');
			break;


			case 'echo':
				StrangeEvent("System próbował zmienić status już potwierdzonej płatności", "PAYPAL", $data);
			break;

			default:
				StrangeEvent("System zwrócił niepoprawną wartość: $PaymentStatus", "PAYPAL", $data);

		  
			break;
		}
	}
	else
	{
		StrangeEvent("Błąd przy próbie weryfikacji płatności w systemie. Należy przeanalizować", "PAYPAL", $data);
	    // Not good. Ignore, or log for investigation...
	}
	
?>
