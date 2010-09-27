<?php

// ------------------------------------------------------------------------
// Bootstrap
// ------------------------------------------------------------------------

// CONSTANTS
define( 'APPPATH', dirname( __FILE__ ) . '/' );
define( 'CONFIGFILE', APPPATH . 'config.ini' );
define( 'ALGODIR', APPPATH . 'algorithms/' );

require_once './passwordTool.php';
// ------------------------------------------------------------------------



// ------------------------------------------------------------------------
// Implementation examples
// ------------------------------------------------------------------------

// Simplest implementation
$password = new Marvin();
$p = $password->generate();
var_dump($p);

// ------------------------------------------------------------------------

$conf = array(
	'length' => 25,
	'max_numbers_count' => 2, // <- 2 numbers maximum in each password
	'symbols_count' => 0, // <- Disable symbols
);

$password1 = new Marvin($conf);
$p1a = $password1->generate();
$p1b = $password1->generate(3); // <- generate 3 passwords
var_dump($p1a);
var_dump($p1b);

// ------------------------------------------------------------------------

$password2 = new Marvin(8); // <- set password length to 8 characters
$p2 = $password2->generate(10); // <- generate 10 passwords
var_dump($p2);
var_dump($password2->password); // The password instance variable contains the first password generated
var_dump($password2->passwords); // This is the same as $p2

// ------------------------------------------------------------------------

// The implementation in this exemple is not fully functional; there might be a lot of repeting characters...
$conf = array(
	'chars' => array(
		'_override_', // <- this chars array will override the default one
		'1234567890',
	),
	'length' => 5
);
$password3 = new Marvin($conf);
$p3 = $password3->generate();
var_dump($p3);