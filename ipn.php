<?php
	// ZarinPal Gateway by M.Amini
	session_start();
	define("_VALID_PHP",true);
	require_once("../../init.php");
	$merchantID = getValue("extra", "gateways", "name = 'زرین پال'");
	$amount = $_SESSION["zarin_amount"];
	$au = $_GET["Authority"];
	$client = new SoapClient("https://de.zarinpal.com/pg/services/WebGate/wsdl", array("encoding"=>"UTF-8"));
	$statusID =PaymentVerification(
			array(
					'MerchantID'	 => $merchantID ,
					'Authority' 	 => $au ,
					'Amount'	 	=> $amount
				)
		);
	
	
	if ($statusID->Status == 100) {
		list($user_id, $sesid) = explode("_", $_SESSION["zarin_1"]);
		
		$cartrow = $content->getCartContent($sesid);
		$totalrow = $content->getCartTotal($sesid);
		$gross = $totalrow["total"] - $totalrow["coupon"];
		
		$payer_email = $_SESSION["zarin_2"];
		$receiver_email = getValue("extra3", "gateways", "name = 'ZarinPal'");
		$currency = "toman";
		$txn_id = $au;
		
		if ($cartrow) {
			foreach ($cartrow as $crow) {
			$data = array(
				"txn_id" => sanitize($txn_id),
				"pid" => $crow["pid"],
				"uid" => intval($user_id),
				"downloads" => 0,
				"file_date" => time(),
				"ip" => sanitize($_SERVER["REMOTE_ADDR"]),
				"created" => "NOW()",
				"payer_email" => $payer_email,
				"payer_status" => "verified",
				"item_qty" => $crow["total"],
				"price" => $crow["total"] * $crow["price"] * 1000,
				"currency" => sanitize($currency),
				"pp" => "ZarinPal",
				"status" => 1,
				"active" => 1
			);
			$db->insert("transactions", $data);
		}
	}
	header("Location: ".SITEURL."/account.php");
	} else {
		echo("<p style='direction:rtl;font:10pt Tahoma'>در هنگام پرداخت مشکلی به وجود آمد. با مدیر تماس بگیرید</p>");
		echo'ERR: '.$statusID->Status;
	}
	$_SESSION["zarin_amount"] = "";
	$_SESSION["zarin_1"] = "";
	$_SESSION["zarin_2"] = "";
	die();
?>
