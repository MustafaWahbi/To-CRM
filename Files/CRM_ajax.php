<?php
/*$data = array(
    "amount"     => $_REQUEST['firstName']
);*/

ini_set('display_errors',1);
include("suitCRMAPI.php");
header("Access-Control-Allow-Origin: *");

/******** Add account to magento as customer information ******/
$suitCRMAPI = new suitCRMAPI('mustafa','Ayat1988!!!!');
$data = json_decode(base64_decode($_REQUEST['data']),true);

switch ($data['action']){
    case 'addAccount':{
        $reslut = add_account($data);
        echo $reslut;
    }break;
    case 'updateAccount':{
        $reslut = update_account($data);
        echo $reslut;
    }break;
    case 'updateAccountBA':{
        $reslut = update_accountBA($data);
        echo $reslut;
    }break;
    case 'updateAccountSA':{
        $reslut = update_accountSA($data);
        echo $reslut;
    }break;
    case 'deleteAccountById':{
        $reslut = delete_account($data);
        echo $reslut;
    }break;
    case 'addAddress':{
        $reslut = add_address($data);
        echo $reslut;
    }break;
    case 'updateAddress':{
        $reslut = update_address($data);
        echo $reslut;
    }break;
    case 'deleteAddressById':{
        $reslut = delete_address($data);
        echo $reslut;

    }break;
    case 'addOrder':{
        $reslut = add_order($data);
        echo $reslut;
    }break;
    case 'updateOrder':{
        $reslut = update_order($data);
        echo $reslut;
    }break;
    case 'updateOrderStatus':{
        $reslut = update_order_status($data);
        echo $reslut;
    }break;
    case 'updateOrderAmount':{
        $reslut = update_order_amount($data);
        echo $reslut;
    }break;
    case 'updateOrderConfirmation':{
        $reslut = update_order_confirmation($data);
        echo $reslut;
    }break;
    case 'deleteOrderById':{
        $reslut = delete_Order($data);
        echo $reslut;

    }break;
    case 'addProduct':{
        $reslut = add_products($data);
        echo $reslut;
    }break;
    case 'updateProduct':{
        $reslut = update_products($data);
        echo $reslut;
    }break;
}

/************Functions************/
/* add account*/
function add_account($data){
    global $suitCRMAPI;
    $idvalue=rawurldecode($data['idvalue']);
    $firstName=rawurldecode($data['firstName']);
    $lastName=rawurldecode($data['lastName']);
    $email=rawurldecode($data['email']);
    /*$b_a_street=rawurldecode($data['b_a_street']);
    $b_a_postalcode=rawurldecode($data['b_a_postalcode']);
    $b_a_city=rawurldecode($data['b_a_city']);
    $b_a_state=rawurldecode($data['b_a_state']);
    $b_a_country=rawurldecode($data['b_a_country']);*/
    //$return=$suitCRMAPI->add_account($idvalue,$firstName,$lastName,$email,$b_a_street,$b_a_city,$b_a_state,$b_a_postalcode,$b_a_country);
    $return=$suitCRMAPI->add_account($idvalue,$firstName,$lastName,$email);
    return $return;
}
/* update account*/
function update_account($data){
    global $suitCRMAPI;
    $idvalue=rawurldecode($data['idvalue']);
    $firstName=rawurldecode($data['firstName']);
    $lastName=rawurldecode($data['lastName']);
    $email=rawurldecode($data['email']);
    /* $b_a_street=rawurldecode($data['b_a_street']);
     $b_a_postalcode=rawurldecode($data['b_a_postalcode']);
     $b_a_city=rawurldecode($data['b_a_city']);
     $b_a_state=rawurldecode($data['b_a_state']);
     $b_a_country=rawurldecode($data['b_a_country']);*/
    //$reslut=$suitCRMAPI->update_account($idvalue,$firstName,$lastName,$email,$b_a_street,$b_a_city,$b_a_state,$b_a_postalcode,$b_a_country);
    $reslut=$suitCRMAPI->update_account($idvalue,$firstName,$lastName,$email);
    return $reslut;
}
/* update account billing address*/
function update_accountBA($data){
    global $suitCRMAPI;
    $idvalue=rawurldecode($data['idvalue']);
    $b_a_street=rawurldecode($data['b_a_street']);
    $b_a_postalcode=rawurldecode($data['b_a_postalcode']);
    $b_a_city=rawurldecode($data['b_a_city']);
    $b_a_state=rawurldecode($data['b_a_state']);
    $b_a_country=rawurldecode($data['b_a_country']);
    $reslut=$suitCRMAPI->update_accountBA($idvalue,$b_a_street,$b_a_city,$b_a_state,$b_a_postalcode,$b_a_country);
    return $reslut;
}
/* update account shipping address*/
function update_accountSA($data){
    global $suitCRMAPI;
    $idvalue=rawurldecode($data['idvalue']);
    $s_a_street=rawurldecode($data['s_a_street']);
    $s_a_postalcode=rawurldecode($data['s_a_postalcode']);
    $s_a_city=rawurldecode($data['s_a_city']);
    $s_a_state=rawurldecode($data['s_a_state']);
    $s_a_country=rawurldecode($data['s_a_country']);
    $reslut=$suitCRMAPI->update_accountSA($idvalue,$s_a_street,$s_a_city,$s_a_state,$s_a_postalcode,$s_a_country);
    return $reslut;
}
/* delete account*/
function delete_account($data){
    global $suitCRMAPI;
    $idvalue=rawurldecode($data['idvalue']);
    $return=$suitCRMAPI->delete_account($idvalue);
    return $return;
}
/* add address */
function add_address($data){
    global $suitCRMAPI;
    $accountId=rawurldecode($data['accountId']);
    $contactId=rawurldecode($data['contactId']);
    $firstName=rawurldecode($data['firstName']);
    //$lastName=rawurldecode($data['lastName']);
	$lastName=rawurldecode($data['lastName']);
    $email=rawurldecode($data['email']);
    $phone_mobile=rawurldecode($data['phone_mobile']);
    //$street=rawurldecode($data['street']);
    $street=rawurldecode($data['street']);
	$postalcode=rawurldecode($data['postalcode']);
    $city=rawurldecode($data['city']);
    $state=rawurldecode($data['state']);
    $country=rawurldecode($data['country']);

    $alt_street=rawurldecode($data['street']);
	$alt_postalcode=rawurldecode($data['postalcode']);
    $alt_city=rawurldecode($data['city']);
    $alt_state=rawurldecode($data['state']);
    $alt_country=rawurldecode($data['country']);
    $return=$suitCRMAPI->add_address($accountId,$contactId,$firstName,$lastName,$email,$phone_mobile,$street,$city,$state,$postalcode,$country,$alt_street,$alt_city,$alt_state,$alt_postalcode,$alt_country);
    return $return;
}
/* update address*/
function update_address($data){
    global $suitCRMAPI;
    $contactId=rawurldecode($data['contactId']);
    $firstName=rawurldecode($data['firstName']);
    $lastName=rawurldecode($data['lastName']);
    $email=rawurldecode($data['email']);
    $phone_mobile=rawurldecode($data['phone_mobile']);
    $street=rawurldecode($data['street']);
    $postalcode=rawurldecode($data['postalcode']);
    $city=rawurldecode($data['city']);
    $state=rawurldecode($data['state']);
    $country=rawurldecode($data['country']);

    $alt_street=rawurldecode($data['street']);
    $alt_postalcode=rawurldecode($data['postalcode']);
    $alt_city=rawurldecode($data['city']);
    $alt_state=rawurldecode($data['state']);
    $alt_country=rawurldecode($data['country']);
    $return=$suitCRMAPI->update_address($contactId,$firstName,$lastName,$email,$phone_mobile,$street,$city,$state,$postalcode,$country,$alt_street,$alt_city,$alt_state,$alt_postalcode,$alt_country);
    return $return;
}
/* delete address*/
function delete_address($data){
    global $suitCRMAPI;
    $idvalue=rawurldecode($data['idvalue']);
    $return=$suitCRMAPI->delete_address($idvalue);
    return $return;
}
/* add order*/
function add_order($data){
    global $suitCRMAPI;
    $accountId=rawurldecode($data['accountId']);
    $contactId=rawurldecode($data['contactId']);
    $orderId=rawurldecode($data['orderId']);
    $orderName=rawurldecode($data['orderName']);
    $orderAmount=rawurldecode($data['orderAmount']);
    /*$orderTime=rawurldecode($data['orderTime']);*/
    $orderTime=rawurldecode($data['orderTime']);
    $country=rawurldecode($data['country']);
    $description=rawurldecode($data['description']);
    $status=rawurldecode($data['status']);
    $return=$suitCRMAPI->add_order($accountId,$contactId,$orderId,$orderName,$orderAmount,$orderTime,$country,$description,$status);
    return $return;
}
/* update order */
function update_order($data){
    global $suitCRMAPI;
    $accountId=rawurldecode($data['accountId']);
    $contactId=rawurldecode($data['contactId']);
    $orderId=rawurldecode($data['orderId']);
    $orderName=rawurldecode($data['orderName']);
    $orderAmount=rawurldecode($data['orderAmount']);
    $orderTime=rawurldecode($data['orderTime']);
    $country=rawurldecode($data['country']);
    $description=rawurldecode($data['description']);
    $status=rawurldecode($data['status']);
    $return=$suitCRMAPI->update_order($accountId,$contactId,$orderId,$orderName,$orderAmount,$orderTime,$country,$description,$status);
    return $return;
}
/* update order */
function update_order_amount($data){
    global $suitCRMAPI;
    /* $accountId=rawurldecode($data['accountId']);
     $contactId=rawurldecode($data['contactId']);*/
    $orderId=rawurldecode($data['orderId']);
    $orderAmount=rawurldecode($data['amount']);
    $return=$suitCRMAPI->update_order_amount($orderId,$orderAmount);
    return $return;
}
/* update order status */
function update_order_status($data){
    global $suitCRMAPI;
    $orderId=rawurldecode($data['orderId']);
    $status=rawurldecode($data['status']);
    $return=$suitCRMAPI->update_order_status($orderId,$status);
    return $return;
}
/* update order confirmation */
function update_order_confirmation($data){
    global $suitCRMAPI;
    $orderId=rawurldecode($data['orderId']);
    $status=rawurldecode($data['status']);
    $return=$suitCRMAPI->update_order_confirmation($orderId,$status);
    return $return;
}
/* delete Order*/
function delete_Order($data){
    global $suitCRMAPI;
    $idvalue=rawurldecode($data['idvalue']);
    $return=$suitCRMAPI->delete_Order($idvalue);
    return $return;
}
/* add products*/
function add_products($data){
    global $suitCRMAPI;
    $sku=rawurldecode($data['sku']);
    $orderId=rawurldecode($data['orderId']);
    $productId=rawurldecode($data['productId']);
    $unitPrice=rawurldecode($data['unitPrice']);
    $taxAmount=rawurldecode($data['taxAmount']);
    $orderQuantity=rawurldecode($data['orderQuantity']);
    $totalPrice=rawurldecode($data['totalPrice']);
    $description=rawurldecode($data['description']);
    $return=$suitCRMAPI->add_products($sku,$orderId,$productId,$unitPrice,$taxAmount,$orderQuantity,$totalPrice,$description);
    return $return;
}
/* update products*/
function update_products($data){
    global $suitCRMAPI;
    $sku=rawurldecode($data['sku']);
    $orderId=rawurldecode($data['orderId']);
    $productId=rawurldecode($data['productId']);
    $unitPrice=rawurldecode($data['unitPrice']);
    $taxAmount=rawurldecode($data['taxAmount']);
    $orderQuantity=rawurldecode($data['orderQuantity']);
    $totalPrice=rawurldecode($data['totalPrice']);
    $description=rawurldecode($data['description']);
    $return=$suitCRMAPI->update_products($sku,$orderId,$productId,$unitPrice,$taxAmount,$orderQuantity,$totalPrice,$description);
    return $return;
}
?>