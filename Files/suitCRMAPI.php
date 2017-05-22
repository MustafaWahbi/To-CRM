<?php
////////////////********* create contacts
//suitCRMAPI
class suitCRMAPI{

    /*** prepare integration credentials as global variables ***/
    protected $session_id = null;
    protected $url = null;

    /*** set the value of credentials variables***/
    function __construct($username,$password){
        /*$this->username = $username;
        $this->password = $password;*/
        /********   $url = "http://{{hostpath}}/{{CRM}}/service/v4_1/rest.php"; */
        //$this->url = "http://localhost/CRMvpn/service/v4_1/rest.php";
        // $this->url = "http://crm.tools.kskdigital.com/service/v4_1/rest.php";
        $this->url = "http://172.16.1.141/service/v4_1/rest.php";

        //function to make cURL request
        if (!function_exists('call')) {
            function call($method, $parameters, $url)
            {
                ob_start();
                $curl_request = curl_init();

                curl_setopt($curl_request, CURLOPT_URL, $url);
                curl_setopt($curl_request, CURLOPT_POST, 1);
                curl_setopt($curl_request, CURLOPT_HTTP_VERSION, CURL_HTTP_VERSION_1_0);
                curl_setopt($curl_request, CURLOPT_HEADER, 1);
                curl_setopt($curl_request, CURLOPT_SSL_VERIFYPEER, 0);
                curl_setopt($curl_request, CURLOPT_RETURNTRANSFER, 1);
                curl_setopt($curl_request, CURLOPT_FOLLOWLOCATION, 0);

                $jsonEncodedData = json_encode($parameters);

                $post = array(
                    "method" => $method,
                    "input_type" => "JSON",
                    "response_type" => "JSON",
                    "rest_data" => $jsonEncodedData
                );

                curl_setopt($curl_request, CURLOPT_POSTFIELDS, $post);
                $result = curl_exec($curl_request);
                curl_close($curl_request);

                $result = explode("\r\n\r\n", $result, 2);
                $response = json_decode($result[1]);
                ob_end_flush();

                return $response;
            }
        }


//login -----------------------------------------------------
        $login_parameters = array(
            "user_auth" => array(
                "user_name" => $username,
                "password" => md5($password),
                "version" => "1"
            ),
            "application_name" => "RestTest",
            "name_value_list" => array(),
        );

        $login_result = call("login", $login_parameters, $this->url);

        /*
        echo "<pre>";
        print_r($login_result);
        echo "</pre>";
        */
//get session id
        $this->session_id = $login_result->id;
    }
    /*************** SQS ************/
    /**** send data to SQS ****/
    public function send_to_SQS($id,$firstName,$lastName,$email,$update){
        // public function send_to_SQS(){
        $SQS_write=$_SERVER['HTTP_HOST'].'/amazon/write.php';

        $post_data= array(
            'id'=> $id,
            'first_name'=> $firstName,
            'last_name'=> $lastName,
            'email'=> $email,
            'status'=> $update
        );
        //initialize Curl
        $ch =curl_init();
        // send data to SQS
        //curl_setopt($ch,CURLOPT_URL,$_SERVER['HTTP_HOST'].'/amazon/write.php?first_name=mustafa&last_name=dahab&email=gm.wahbi@gmail.com');
        curl_setopt($ch,CURLOPT_URL,$SQS_write);
        // send data and get response
        curl_setopt($ch,CURLOPT_RETURNTRANSFER,1);
        curl_setopt($ch,CURLOPT_HEADER,0);
        // prepare curl to send request
        curl_setopt($ch,CURLOPT_POST,1);
        // add value post data value
        curl_setopt($ch,CURLOPT_POSTFIELDS,$post_data);
        $output = curl_exec($ch);
        curl_close($ch);

        /*        if($output == 1)$this->read_from_SQS($update);
                    else {die('SQS Error');}*/
        if($output == 1)echo 1;
        else {die('SQS Error');}
    }

    /*** check is id existed contact ***/
    public function existed_id($id){


//search account -------------------------------------

        $get_entry_parameters = array(
            //session id
            "session" => $this->session_id,

            //The name of the module from which to retrieve records.
            "module_name" => "Contacts",

            /*
             * The ID of the record to retrieve.
             * The SQL WHERE clause without the word "where".*/
            'query' => " contacts.id = '".$id."'",

            //If deleted records should be included in results.
            'deleted' => false
        );

        $get_entry_result = call("get_entries_count", $get_entry_parameters, $this->url);


        if($get_entry_result->result_count == 0)return 0;
        else return 1;
    }

    /*************** Account************/

    /*** add new account ***/
    /*
     * NOTE : b_a = billing address
     * */
    // old one public  function add_account($id,$firstName,$lastName,$email,$b_a_street,$b_a_city,$b_a_state,$b_a_postalcode,$b_a_country){
    public  function add_account($id,$firstName,$lastName,$email){

//create contact -------------------------------------
        $set_entry_parameters = array(
            //session id
            "session" => $this->session_id,

            //The name of the module from which to retrieve records.
            "module_name" => "Accounts",

            //Record attributes
            "name_value_list" => array(
                /*
                 * use following for new contact with custom ID
                 * this data is generated by magento
                 * -> id = auto increment
                 * -> firs name , last name , email : data from submitted form (user entry)
                */
                array(
                    "name" => "new_with_id",
                    "value" => true
                ),
                array("name" => "id", "value" => $id),// custom id
                array("name" => "name", "value" => $firstName.' '.$lastName),
                array("name" => "account_type", "value" => "Customer"),
                array('name' => 'email1', 'value' => $email),
            ),
        );

        $set_entry_result=call("set_entry", $set_entry_parameters, $this->url);
        if($set_entry_result == null)return 0;
        else return 1;
    }
    /*** Update existing account ***/
    // public function update_account($id,$firstName,$lastName,$email,$b_a_street,$b_a_city,$b_a_state,$b_a_postalcode,$b_a_country){
    public function update_account($id,$firstName,$lastName,$email){

//update contact -------------------------------------
        $set_entry_parameters = array(
            //session id
            "session" => $this->session_id,

            //The name of the module from which to retrieve records.
            "module_name" => "Accounts",

            //Record attributes
            "name_value_list" => array(
                /*
                 * use following for new contact with custom ID
                 * this data is generated by magento
                 * -> id = auto increment
                 * -> firs name , last name , email : data from submitted form (user entry)
                */
                array("name" => "id", "value" => $id),// custom id
                array("name" => "name", "value" => $firstName.' '.$lastName),
                array("name" => "account_type", "value" => "Customer"),
                array('name' => 'email1', 'value' => $email),
            ),
        );

        $set_entry_result=call("set_entry", $set_entry_parameters, $this->url);

        if($set_entry_result == null)return 0;
        return 1;
    } /*** Update existing account ***/
    public function update_accountBA($id,$b_a_street,$b_a_city,$b_a_state,$b_a_postalcode,$b_a_country){

//update contact -------------------------------------
        $set_entry_parameters = array(
            //session id
            "session" => $this->session_id,

            //The name of the module from which to retrieve records.
            "module_name" => "Accounts",

            //Record attributes
            "name_value_list" => array(
                /*
                 * use following for new contact with custom ID
                 * this data is generated by magento
                 * -> id = auto increment
                 * -> firs name , last name , email : data from submitted form (user entry)
                */
                array("name" => "id", "value" => $id),// custom id
                array('name' => 'billing_address_street', 'value' => $b_a_street),
                array('name' => 'billing_address_city', 'value' => $b_a_city),
                array('name' => 'billing_address_state', 'value' => $b_a_state),
                array('name' => 'billing_address_postalcode', 'value' => $b_a_postalcode),
                array('name' => 'billing_address_country', 'value' => $b_a_country),

            ),
        );

        $set_entry_result=call("set_entry", $set_entry_parameters, $this->url);

        if($set_entry_result == null)return 0;
        return 1;
    }
    /*** Update existing account shipping address***/
    public function update_accountSA($id,$s_a_street,$s_a_city,$s_a_state,$s_a_postalcode,$s_a_country){

//update contact -------------------------------------
        $set_entry_parameters = array(
            //session id
            "session" => $this->session_id,

            //The name of the module from which to retrieve records.
            "module_name" => "Accounts",

            //Record attributes
            "name_value_list" => array(
                /*
                 * use following for new contact with custom ID
                 * this data is generated by magento
                 * -> id = auto increment
                 * -> firs name , last name , email : data from submitted form (user entry)
                */
                array("name" => "id", "value" => $id),// custom id
                array('name' => 'shipping_address_street', 'value' => $s_a_street),
                array('name' => 'shipping_address_city', 'value' => $s_a_city),
                array('name' => 'shipping_address_state', 'value' => $s_a_state),
                array('name' => 'shipping_address_postalcode', 'value' => $s_a_postalcode),
                array('name' => 'shipping_address_country', 'value' => $s_a_country),

            ),
        );

        $set_entry_result=call("set_entry", $set_entry_parameters, $this->url);

        if($set_entry_result == null)return 0;
        return 1;
    }
    /*** Delete existing contact by id***/
    public function delete_account($id){
        $set_entry_parameters = array(
            //session id
            "session" => $this->session_id,

            //The name of the module from which to retrieve records.
            "module_name" => "Accounts",

            //Record attributes
            "name_value_list" => array(
                /*
                 * use following for new contact with custom ID
                 * this data is generated by magento
                 * -> id = auto increment
                 * -> firs name , last name , email : data from submitted form (user entry)
                */
                array("name" => "id", "value" => $id),// custom id
                array('name' => 'deleted', 'value' => 1),// 1 to set as deleted


            ),
        );

        $set_entry_result=call("set_entry", $set_entry_parameters, $this->url);

        if($set_entry_result == null)return 0;
        return 1;


    }

    /*************** Account************/

    /*** add new address to account ***/
    public  function add_address($accountId,$contactId,$firstName,$lastName,$email,$phone_mobile,$street,$city,$state,$postalcode,$country,$alt_street,$alt_city,$alt_state,$alt_postalcode,$alt_country){

        //create contact -------------------------------------
        $set_entry_parameters = array(
            //session id
            "session" => $this->session_id,

            //The name of the module from which to retrieve records.
            "module_name" => "Contacts",

            //Record attributes
            "name_value_list" => array(
                /*
                 * use following for new contact with custom ID
                 * this data is generated by magento
                 * -> id = auto increment
                 * -> firs name , last name , email : data from submitted form (user entry)
                */
                array(
                    "name" => "new_with_id",
                    "value" => true
                ),
                array('name' => 'id', 'value' => $contactId),// custom id
                array('name' => 'first_name', 'value' => $firstName),
                array('name' => 'last_name', 'value' => $lastName),
                array('name' => 'email1', 'value' => $email),
                array('name' => 'phone_mobile', 'value' => $phone_mobile),
                array('name' => 'primary_address_street', 'value' => $street),
                array('name' => 'primary_address_city', 'value' => $city),
                array('name' => 'primary_address_state', 'value' => $state),
                array('name' => 'primary_address_postalcode', 'value' => $postalcode),
                array('name' => 'primary_address_country', 'value' => $country),

                array('name' => 'alt_address_street', 'value' => $alt_street),
                array('name' => 'alt_address_city', 'value' => $alt_city),
                array('name' => 'alt_address_state', 'value' => $alt_state),
                array('name' => 'alt_address_postalcode', 'value' => $alt_postalcode),
                array('name' => 'alt_address_country', 'value' => $alt_country),
            ),
        );

        $set_entry_result=call("set_entry", $set_entry_parameters, $this->url);
        /******set relation starts *****/
        if($set_entry_result == 0){ return 0;}
        else{
            $relationshipProductBundleProductsParams = array(
                'sesssion' => $this->session_id,
                'module_name' => 'Accounts',
                'module_id' => $accountId,
                'link_field_name' => 'contacts',
                'related_ids' => array($contactId),
            );

            // set the product bundles products relationship
            $relationshipProductBundleProductResult = call('set_relationship', $relationshipProductBundleProductsParams, $this->url);

            /***********set relation end*******/
            if($relationshipProductBundleProductResult == null)return 0;
            return 1;
        }


    }
    /*** Update existing address ***/

    public  function update_address($contactId,$firstName,$lastName,$email,$phone_mobile,$street,$city,$state,$postalcode,$country,$alt_street,$alt_city,$alt_state,$alt_postalcode,$alt_country){

        //create contact -------------------------------------
        $set_entry_parameters = array(
            //session id
            "session" => $this->session_id,

            //The name of the module from which to retrieve records.
            "module_name" => "Contacts",

            //Record attributes
            "name_value_list" => array(
                /*
                 * use following for new contact with custom ID
                 * this data is generated by magento
                 * -> id = auto increment
                 * -> firs name , last name , email : data from submitted form (user entry)
                */
                array('name' => 'id', 'value' => $contactId),// custom id
                array('name' => 'first_name', 'value' => $firstName),
                array('name' => 'last_name', 'value' => $lastName),
                array('name' => 'email1', 'value' => $email),
                array('name' => 'phone_mobile', 'value' => $phone_mobile),
                array('name' => 'primary_address_street', 'value' => $street),
                array('name' => 'primary_address_city', 'value' => $city),
                array('name' => 'primary_address_state', 'value' => $state),
                array('name' => 'primary_address_postalcode', 'value' => $postalcode),
                array('name' => 'primary_address_country', 'value' => $country),

                array('name' => 'alt_address_street', 'value' => $alt_street),
                array('name' => 'alt_address_city', 'value' => $alt_city),
                array('name' => 'alt_address_state', 'value' => $alt_state),
                array('name' => 'alt_address_postalcode', 'value' => $alt_postalcode),
                array('name' => 'alt_address_country', 'value' => $alt_country),
            ),
        );

        $set_entry_result=call("set_entry", $set_entry_parameters, $this->url);
        /******set relation starts *****/
        if($set_entry_result == 0){ return 0;}
        else{ return 1; }


    }
    /*** Delete existing address by id***/
    public function delete_address($id){
        $set_entry_parameters = array(
            //session id
            "session" => $this->session_id,

            //The name of the module from which to retrieve records.
            "module_name" => "Contacts",

            //Record attributes
            "name_value_list" => array(
                /*
                 * use following for new contact with custom ID
                 * this data is generated by magento
                 * -> id = auto increment
                 * -> firs name , last name , email : data from submitted form (user entry)
                */
                array("name" => "id", "value" => $id),// custom id
                array('name' => 'deleted', 'value' => 1),// 1 to set as deleted


            ),
        );

        $set_entry_result=call("set_entry", $set_entry_parameters, $this->url);

        if($set_entry_result == null)return 0;
        return 1;


    }

    /*************** Orders************/
    /*** add new order ***/
    public  function add_order($accountId,$contactId,$orderId,$orderName,$orderAmount,$orderTime,$country,$description,$status){
//create contact -------------------------------------
        $set_entry_parameters = array(
            //session id
            "session" => $this->session_id,
            //The name of the module from which to retrieve records.
            "module_name" => "ksk05_Header3",
            //Record attributes
            "name_value_list" => array(
                /*
                 * use following for new contact with custom ID
                 * this data is generated by magento
                 * -> id = auto increment
                 * -> firs name , last name , email : data from submitted form (user entry)
                */
                array(
                    "name" => "new_with_id",
                    "value" => true
                ),
                array("name" => "id", "value" => $orderId),// custom id
                array("name" => "ksk05_kskorderid", "value" => $orderId),// custom id
                /******** table ksk05_header3_cstm****/
                array('name' => 'orderconfirmed_c', 'value' => 0),// new status

                /*following parameters used to connect order with address
                linked to (ksk05_header3_cstm) table
                */
                array("name" => "account_id_c", "value" => $accountId),// Account Name
                array("name" => "contact_id_c", "value" => $contactId),// Related address
                /**/

                array('name' => 'accounts_ksk05_header3_1accounts_ida', 'value' => $accountId),
                array('name' => 'accounts_ksk05_header3_1ksk05_header3_idb', 'value' => $orderId),

                array("name" => "name", "value" => $orderName),
                array("name" => "ksk05_currencycode", "value" => $orderAmount),
                array("name" => "modified_user_id", "value" => $accountId),
                array('name' => 'description', 'value' => $description),
                array('name' => 'ksk05_ordercountry', 'value' => $country),
                array('name' => 'ksk05_ordereddatetime', 'value' => $orderTime),
                array('name' => 'ksk05_orderstatus', 'value' => $status),
            ),
        );

        /******set relation starts *****/
        $relationshipProductBundleProductsParams = array(
            'sesssion' => $this->session_id,
            'module_name' => 'Accounts',
            'module_id' => $accountId,
            'link_field_name' => 'ksk05_Header3',
            'related_ids' => array($orderId),
        );

        // set the product bundles products relationship
        $relationshipProductBundleProductResult = call('set_relationship', $relationshipProductBundleProductsParams, $this->url);

        /***********set relation end*******/

        $set_entry_result=call("set_entry", $set_entry_parameters, $this->url);
        if($set_entry_result == null)return 0;
        else {return 1;}
    }
    /*** Update existing order ***/
    public  function update_order($accountId,$contactId,$orderId,$orderName,$orderAmount,$orderTime,$country,$description,$status){
//create contact -------------------------------------
        $set_entry_parameters = array(
            //session id
            "session" => $this->session_id,
            //The name of the module from which to retrieve records.
            "module_name" => "ksk05_Header3",
            //Record attributes
            "name_value_list" => array(
                /*
                 * use following for new contact with custom ID
                 * this data is generated by magento
                 * -> id = auto increment
                 * -> firs name , last name , email : data from submitted form (user entry)
                */
                array("name" => "id", "value" => $orderId),// custom id
                array("name" => "ksk05_kskorderid", "value" => $orderId),// custom id

                /*following parameters used to connect order with address
                linked to (ksk05_header3_cstm) table
                */
                array("name" => "account_id_c", "value" => $accountId),// Account Name
                array("name" => "contact_id_c", "value" => $contactId),// Related address
                /**/

                array('name' => 'accounts_ksk05_header3_1accounts_ida', 'value' => '1'),
                array('name' => 'accounts_ksk05_header3_1ksk05_header3_idb', 'value' => $orderId),

                array("name" => "name", "value" => $orderName),
                array("name" => "ksk05_currencycode", "value" => $orderAmount),
                array("name" => "modified_user_id", "value" => $accountId),
                array('name' => 'description', 'value' => $description),
                array('name' => 'ksk05_ordercountry', 'value' => $country),
                array('name' => 'ksk05_ordereddatetime', 'value' => $orderTime),
                array('name' => 'ksk05_orderstatus', 'value' => $status),
            ),
        );

        $set_entry_result=call("set_entry", $set_entry_parameters, $this->url);
        if($set_entry_result == null)return 0;
        else {return 1;}
    }
    public  function update_order_status($orderId,$status){
//create contact -------------------------------------
        $set_entry_parameters = array(
            //session id
            "session" => $this->session_id,
            //The name of the module from which to retrieve records.
            "module_name" => "ksk05_Header3",
            //Record attributes
            "name_value_list" => array(

                array("name" => "id", "value" => $orderId),// order id
                array('name' => 'ksk05_orderstatus', 'value' => $status),// new status
            ),
        );

        $set_entry_result=call("set_entry", $set_entry_parameters, $this->url);
        if($set_entry_result == null)return 0;
        else {return 1;}
    }
    public  function update_order_amount($orderId,$amount){
//create contact -------------------------------------
        $set_entry_parameters = array(
            //session id
            "session" => $this->session_id,
            //The name of the module from which to retrieve records.
            "module_name" => "ksk05_Header3",
            //Record attributes
            "name_value_list" => array(

                array("name" => "id", "value" => $orderId),// order id
                array('name' => 'ksk05_currencycode', 'value' => $amount),// new amount
            ),
        );

        $set_entry_result=call("set_entry", $set_entry_parameters, $this->url);
        if($set_entry_result == null)return 0;
        else {return 1;}
    }
    public  function update_order_confirmation($orderId,$status){
//create contact -------------------------------------
        $set_entry_parameters = array(
            //session id
            "session" => $this->session_id,
            //The name of the module from which to retrieve records.
            "module_name" => "ksk05_Header3",
            //Record attributes
            "name_value_list" => array(

                array("name" => "id", "value" => $orderId),// order id
                array('name' => 'orderconfirmed_c', 'value' => $status),// new status
            ),
        );

        $set_entry_result=call("set_entry", $set_entry_parameters, $this->url);
        if($set_entry_result == null)return 0;
        else {return 1;}
    }
    /*** Delete existing order by id ***/
    public function delete_order($id){
        $set_entry_parameters = array(
            //session id
            "session" => $this->session_id,

            //The name of the module from which to retrieve records.
            "module_name" => "ksk05_Header3",

            //Record attributes
            "name_value_list" => array(
                /*
                 * use following for new contact with custom ID
                 * this data is generated by magento
                 * -> id = auto increment
                 * -> firs name , last name , email : data from submitted form (user entry)
                */
                array("name" => "id", "value" => $id),// custom id
                array('name' => 'deleted', 'value' => 1),// 1 to set as deleted


            ),
        );

        $set_entry_result=call("set_entry", $set_entry_parameters, $this->url);

        if($set_entry_result == null)return 1;
        return 0;


    }

    /*************** Products************/
    /*** add new Product ***/
    public  function add_products($sku,$orderId,$productId,$unitPrice,$taxAmount,$orderQuantity,$totalPrice,$description){
//create contact -------------------------------------
        $set_entry_parameters = array(
            //session id
            "session" => $this->session_id,
            //The name of the module from which to retrieve records.
            "module_name" => "AOS_Products_Quotes",
            //Record attributes
            "name_value_list" => array(
                /*
                 * use following for new contact with custom ID
                 * this data is generated by magento
                 * -> id = auto increment
                 * -> firs name , last name , email : data from submitted form (user entry)
                */
                array(
                    "name" => "new_with_id",
                    "value" => true
                ),
                /**/
                /*** Relation between product and order starts ***/
                /* table
                * aos_products_quotes_cstm
                 * connect product with order
                */
                array("name" => "id", "value" => $productId),// magento id
                array("name" => "skuid_c", "value" => $sku),// magento auto genrated
                array("name" => "ksk05_header3_id_c", "value" => $orderId),// order id

                /* table
                 * accounts_ksk05_header3_1_3
                 * connect account with the the product
                 * */
                array("name" => "ksk05_header3_aos_products_quotes_1ksk05_header3_ida", "value" => $orderId),// order id
                // array("name" => "ksk05_header3_aos_products_quotes_1aos_products_quotes_idb", "value" => $productId),// productId

                /*** Relation  ends ***/
                /*
                 * table
                 * aos_products_quotes
                 * this table contain the products details
                 * */
                array("name" => "product_id", "value" => $sku),// custom id
                array("name" => "name", "value" => $productId),// it's realy the neame show in edit form
                array("name" => "parent_id", "value" => $productId),// Account Name
                array("name" => "number", "value" => $orderQuantity),// Related address
                array("name" => "product_qty", "value" => $orderQuantity),// Related address
                array("name" => "product_cost_price", "value" => $unitPrice),
                array("name" => "product_unit_price", "value" => $unitPrice),
                array("name" => "vat_amt", "value" => $taxAmount),
                array("name" => "vat", "value" => $taxAmount),
                array("name" => "product_total_price", "value" => $totalPrice),
                array('name' => 'item_description', 'value' => $description),

            ),
        );

        $set_entry_result=call("set_entry", $set_entry_parameters, $this->url);
        if($set_entry_result == null)return 0;
        else {return 1;}
    }
    /*** Update existing Product ***/
    public  function update_products($sku,$orderId,$productId,$unitPrice,$taxAmount,$orderQuantity,$totalPrice,$description){
//create contact -------------------------------------
        $set_entry_parameters = array(
            //session id
            "session" => $this->session_id,
            //The name of the module from which to retrieve records.
            "module_name" => "AOS_Products_Quotes",
            //Record attributes
            "name_value_list" => array(
                /*
                 * use following for new contact with custom ID
                 * this data is generated by magento
                */
                /**/
                array("name" => "id", "value" => $sku,),// magento auto generated value

                /*** Relation between product and order  ***/
                /* table
                * aos_products_quotes_cstm
                 * connect product with order
                */
                array("name" => "skuid_c", "value" => $productId),// magento item id
                array("name" => "ksk05_header3_id_c", "value" => $orderId),// order id

                /* table
                 * accounts_ksk05_header3_1_c
                 * connect account with the the product
                 * */
                array("name" => "ksk05_header3_aos_products_quotes_1ksk05_header3_ida", "value" => $orderId),// order id
                // array("name" => "ksk05_header3_aos_products_quotes_1aos_products_quotes_idb", "value" => $productId),// productId

                /*** Relation  ends ***/


                /*
                 * table
                 * aos_products_quotes
                 * this table contain the products details
                 * */
                array("name" => "product_id", "value" => $productId),// custom id
                array("name" => "name", "value" => $productId),// Account Name
                array("name" => "parent_id", "value" => $orderId),// Account Name
                array("name" => "number", "value" => $orderQuantity),// Related address
                array("name" => "product_qty", "value" => $orderQuantity),// Related address
                array("name" => "product_cost_price", "value" => $unitPrice),
                array("name" => "product_unit_price", "value" => $unitPrice),
                array("name" => "vat_amt", "value" => $taxAmount),
                array("name" => "vat", "value" => $taxAmount),
                array("name" => "product_total_price", "value" => $totalPrice),
                array('name' => 'item_description', 'value' => $description),

            ),
        );

        $set_entry_result=call("set_entry", $set_entry_parameters, $this->url);
        if($set_entry_result == null)return 0;
        else {return 1;}
    }
}

