<?php 
if (IS_LOGGED == false) {
    $data = array(
        'status' => 400,
        'error' => 'Not logged in'
    );
    echo json_encode($data);
    exit();
}

elseif(!PT_IsAdmin() && ($pt->config->go_pro != 'on' || PT_IsUpgraded())){
	$data = array(
        'status' => 400,
        'error' => 'Bad request'
    );
    echo json_encode($data);
    exit();
}


use PayPal\Api\Payer;
use PayPal\Api\Item;
use PayPal\Api\ItemList;
use PayPal\Api\Details;
use PayPal\Api\Amount;
use PayPal\Api\Transaction;
use PayPal\Api\RedirectUrls;
use PayPal\Api\Payment;
use PayPal\Api\PaymentExecution;


$payment_currency = $pt->config->payment_currency;
$payer        = new Payer();
$item         = new Item();
$itemList     = new ItemList();
$details      = new Details();
$amount       = new Amount();
$transaction  = new Transaction();
$redirectUrls = new RedirectUrls();
$payment      = new Payment();
$pkgs         = array('pro');
$payer->setPaymentMethod('paypal');
$sum          = intval($pt->config->pro_pkg_price);


if (!empty($_GET['first']) && $_GET['first'] == 'purchase') {

	if (!empty($_POST['type']) && $_POST['type'] == 'pro') {
		$redirectUrls->setReturnUrl(PT_Link('aj/go_pro/get_paid?status=success&pkg=pro'))->setCancelUrl(PT_Link(''));    
	    $item->setName('Purchase pro package')->setQuantity(1)->setPrice($sum)->setCurrency($payment_currency);  
	    $itemList->setItems(array($item));    
	    $details->setSubtotal($sum);
	    $amount->setCurrency($payment_currency)->setTotal($sum)->setDetails($details);
	    $transaction->setAmount($amount)->setItemList($itemList)->setDescription('Purchase pro package')->setInvoiceNumber(time());
	    $payment->setIntent('sale')->setPayer($payer)->setRedirectUrls($redirectUrls)->setTransactions(array(
	        $transaction
	    ));

	    try {
	        $payment->create($paypal);
	    }

	    catch (Exception $e) {
	        $data = array(
	            'type' => 'ERROR',
	            'details' => json_decode($e->getData())
	        );

	        if (empty($data['details'])) {
	            $data['details'] = json_decode($e->getCode());
	        }
	        echo json_encode($data);
	    	exit();
	    }

	    $data = array(
	        'status' => 200,
	        'type' => 'SUCCESS',
	        'url' => $payment->getApprovalLink()
	    );
	}

	//Other packages
}

if (!empty($_GET['first']) && $_GET['first'] == 'get_paid') {
	$data['status'] = 500;
	$request        = (!empty($_GET['paymentId']) && !empty($_GET['PayerID']) && !empty($_GET['status']) && $_GET['status'] == 'success');
	$pkg            = (!empty($_GET['pkg']) && in_array($_GET['pkg'], $pkgs));

	if ($request && $pkg) {
		$paymentId = PT_Secure($_GET['paymentId']);
		$PayerID   = PT_Secure($_GET['PayerID']);
		$payment   = Payment::get($paymentId, $paypal);
	    $execute   = new PaymentExecution();
	    $execute->setPayerId($PayerID);

	    try{
	        $result = $payment->execute($execute, $paypal);
	    }

	    catch (Exception $e) {
	        $data = array(
	            'type' => 'ERROR',
	            'details' => json_decode($e->getData())
	        );

	        if (empty($data['details'])) {
	            $data['details'] = json_decode($e->getCode());
	        }

	        echo json_encode($data);
	    	exit();
	    }
	    
	    $update = array('is_pro' => 1,'verified' => 1);
	    $go_pro = $db->where('id',$pt->user->id)->update(T_USERS,$update);
	    if ($go_pro === true) {
	    	$pkg_type             = PT_Secure($_GET['pkg']);
	    	$payment_data         = array(
	    		'user_id' => $pt->user->id,
	    		'type'    => $pkg_type,
	    		'amount'  => $sum,
	    		'date'    => date('n') . '/' . date('Y'),
	    		'expire'  => strtotime("+30 days")
	    	);

	    	$db->insert(T_PAYMENTS,$payment_data);
	    	$db->where('user_id',$pt->user->id)->update(T_VIDEOS,array('featured' => 1));
	    	$_SESSION['upgraded'] = true;
	    	header('Location: ' . PT_Link('go_pro'));
	    	exit();
	    }	    
	}
}


//Manage pro system from admin side

if (PT_IsAdmin() && !empty($_GET['first'])) {
	
	
	if ($_GET['first'] == 'remove_expired') {
		$data['status'] = 400;
		$expired_subs   = $db->where('expire',time(),'<')->get(T_PAYMENTS);
		$update         = array('is_pro' => 0,'verified' => 0);
		foreach ($expired_subs as $subscriber){
			$db->where('id',$subscriber->user_id)->update(T_USERS,$update);
			$db->where('user_id',$subscriber->user_id)->update(T_VIDEOS,array('featured' => 0));
		}

		$data['status'] = 200;

	}

}
