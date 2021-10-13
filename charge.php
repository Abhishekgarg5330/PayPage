<?php
  require_once('vendor/autoload.php');
  require_once('config/db.php');
  require_once('lib/pdo_db.php');
  require_once('models/Customer.php');
  require_once('models/Transactions.php');

  \Stripe\Stripe::setApiKey('sk_test_51Jc2QASEvHcVGW05UGqceofaSmIJcNvX1XWXF9fQfUHOGfWEGZC35IxfTfyuhcDOITyIfDqxoq4xIc3mH42LbPTN00FNJepjPj');

 // Sanitize POST Array
 $POST = filter_var_array($_POST, FILTER_SANITIZE_STRING);

 $first_name = $POST['first_name'];
 $last_name = $POST['last_name'];
 $email = $POST['email'];
 $token = $POST['stripeToken'];

// Create Customer In Stripe
$customer = \Stripe\Customer::create(array(
    "name" => $first_name,
    "email" => $email,
    "source" => $token,
    "address" => ["city" => "	Singapur", "country" => "Singapore", "line1" => "skcjbsajhc", "line2" => "","postal_code" => "SG", "state" => "Central Singapore Community Development Council"]
  ));
  
  // Charge Customer
  $charge = \Stripe\Charge::create(array(
    "amount" => 5000,
    "currency" => "usd",
    "description" => "Intro To React Course",
    "customer" => $customer->id
  ));

// Customer Data 
$customerData = [
  'id' => $charge->customer,
  // 'id' => "ddkbdjksb",
  'first_name' => $first_name,
  'last_name' => $last_name,
  'email' => $email
]; 


// Instantiate  Customer
$customer = new Customer();

// Add CustomerTo DB
$customer->addCustomer($customerData);


// Transaction Data 
$transactionData = [
  'id' => $charge->id,
  'customer_id' => $charge->customer,
  'product' => $charge->description,
  'amount' => $charge->amount,
  'currency' => $charge->currency,
  'status' => $charge->status
]; 


// Instantiate  Customer
$transaction = new Transaction();

// Add CustomerTo DB
$transaction->addTransaction($transactionData);


// Redirect to success
header('Location: success.php?tid='.$charge->id.'&product='.$charge->description);
