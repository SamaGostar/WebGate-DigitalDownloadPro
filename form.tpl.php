<?php
	// ZarinPal Gateway by M.Amini
	if (!defined("_VALID_PHP")) 
		die("دسترسی مستقیم امکانپذیر نیست");
	$cartrow = $content->getCartContent();
	$totalrow = $content->getCartTotal();
	$amount = $totalrow["total"] - $totalrow["coupon"];
	$amount = $amount * 1000;
	$merchantID = $row["extra"];
	$desc = $row["extra2"];
	$callback = SITEURL."/gateways/".$row["dir"]."/ipn.php";
	$client = new SoapClient("https://de.zarinpal.com/pg/services/WebGate/wsdl", array("encoding"=>"UTF-8"));
	$payID = $client->PaymentRequest(
			array(
							'MerchantID' 	=> $merchantID ,
							'Amount' 		=> $amount ,
							'Description' 	=> $desc ,
							'Email' 		=> '' ,
							'Mobile' 		=> '' ,
							'CallbackURL' 	=> $callback

							)
			 );
	if ($payID->Status != 100) {
		die("در اتصال به درگاه زرین پال، مشکلی پیش آمده است");
	}
	$_SESSION["zarin_amount"] = $amount;
	$_SESSION["zarin_1"] = $user->uid."_".$user->sesid;
	$_SESSION["zarin_2"] = $user->email;
?>
<form action="https://www.zarinpal.com/pg/StartPay/<?php echo $payID->Authority; ?>" method="POST" name="zarin">
	<input type="image" src="<?php echo SITEURL."/gateways/".$row["dir"]."/ZarinPal_big.png"?>" class="tooltip" title="پرداخت آنلاین با زرین پال" onclick="document.zarin.submit()" />
</form>
