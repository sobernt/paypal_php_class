<?php

require 'classes\PayPal.php'; 
use classes\PayPal as PayPal;
$PayPal=new PayPal;

if( isset($_GET['token']) && !empty($_GET['token']) && !empty($_GET['PayerID']) )
{
$response = $PayPal::transactionInfo($_GET['token'],$_GET['PayerID']);
//    foreach ($response as $key=>$str){
//          print($key."=>".$str."<br>");  
//        }
      $payer = json_decode($response['CUSTOM']);
      if ($response['CHECKOUTSTATUS']=='PaymentActionNotInitiated')
          {//no Initiated - do it
          $data = $PayPal::selectKeys($response,array('TOKEN','PAYERID','PAYMENTREQUEST_0_AMT','PAYMENTREQUEST_0_CURRENCYCODE'));
          $complite_result = $PayPal::transactionComplite($data);
         
          switch ($complite_result['PAYMENTINFO_0_PAYMENTSTATUS']) {
                case 'Pending':
                    echo 'your pay has been Pending<br>';
                    $response = $PayPal::transactionInfo($_GET['token'],$_GET['PayerID']);
                    break;
                case 'Completed':
                    echo 'your pay has been Complited<br>';
                    break;
          }
      }
      $PayPal::saveResult($response['TOKEN'],$response,array_keys($response));
     print "payment for user id:".$payer->user_id." & order #".$payer->order." status: ".$response['CHECKOUTSTATUS'];
     if($response['PAYMENTREQUESTINFO_0_ERRORCODE'] ==0 ){
         //payment success.
         print("<br>payment success<br>");
     }
   
}


?>