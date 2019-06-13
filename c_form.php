<?php
/*
Plugin Name: c_form
Description: Contact form for program hosts with variable send to email.
Version:     1.0
Author:      Keith Gudger
Author URI:  http://www.github.com/kgudger
License:     GPL3
License URI: https://www.gnu.org/licenses/gpl-3.0.html
 */

defined( 'ABSPATH' ) or die( 'Ah ah ah, you didn\'t say the magic word' );
add_shortcode('c_form', 'c_form_fun');
function c_form_fun() {
/**
 * dbstart.php opens the database and gets the user variables
 */
require_once("/var/www/html/includes/dbstart.php");

include_once("includes/c_formpage.php");

/**
 * The checkArray defines what checkForm does so you don't
 * have to overwrite it in the derived class. */

$checkArray = array(
	array("isEmpty","fname", "Please enter your first name."),
	array("isEmpty","lname", "Please enter your last name."),
	array("isInvalidEmail","email", "Please enter your email address."),
	array("isEmpty","subject", "Please enter a subject.")
);

/// a new instance of the derived class (from MainPage)
$cform = new cFormPage($db,$sessvar,$checkArray,$secret,$sendGridAPIKey) ;
/// and ... start it up!  
return $cform->main("Host Contact Form", $uid, "", "");
/**
 * There are 2 choices for redirection dependent on the sessvar
 * above which one gets taken.
 * For this page ... */
}
