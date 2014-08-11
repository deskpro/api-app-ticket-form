<?php

require_once 'vendor/autoload.php';
$config = require 'config.php';

// Check and store the person's email
if (isset($_POST['email']) && !empty($_POST['email'])) {
	$email = $_POST['email'];
} else {
	die('Required field "email" is missing!');
}

// Check and store the person's name
if (isset($_POST['name'])) {
	$name = $_POST['name'];
} else {
	die('Required field "name" is missing!');
}

// Check and store the ticket subject
if (isset($_POST['subject'])) {
	$subject = $_POST['subject'];
} else {
	die('Required field "subject" is missing!');
}

// Check and store the first ticket message
if (isset($_POST['message'])) {
	$message = $_POST['message'];
} else {
	die('Required field "message" is missing!');
}

$dpApi = new DeskPRO\Api($config['deskpro_url'], $config['api_key']);

$ticketBuilder = $dpApi->tickets->createBuilder();

$personBuilder = $dpApi->people->createPersonEditor();

$personBuilder->setName($name);

try {
	$personBuilder->setEmail($email);
} catch (Exception $e) {
	// $email is probably not a valid email
	die($e->getMessage());
}

$ticketBuilder->setCreatedBy($personBuilder)
	->setSubject($subject)
	->setMessage($message);

if (isset($_FILES['attach']) && is_uploaded_file($_FILES['attach']['tmp_name'])) {
	$ticketBuilder->attach($_FILES['attach']['tmp_name']);
}

$result = $dpApi->tickets->save($ticketBuilder);

var_dump($result->getData());