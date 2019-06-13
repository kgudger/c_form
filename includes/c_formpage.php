<?php
/**
* @file c_formpage.php
* Purpose: Host Contact Form with custom send to address from cookie
* Extends MainPage Class
*
* @author Keith Gudger
* @copyright  (c) 2019, Keith Gudger, all rights reserved
* @license    http://opensource.org/licenses/BSD-2-Clause
* @version    Release: 1.0
* @package    KSQD
*
* @note Has processData and showContent, 
* main and checkForm in MainPage class not overwritten.
* 
*/
require_once("/var/www/html/wp-content/plugins/c_form/includes/mainpage.php");
include_once "/var/www/html/includes/util.php";
require_once("/var/www/html/wp-content/plugins/c_form/includes/recaptchalib.php");
require("/var/www/html/wp-content/plugins/c_form/sendgrid-php/sendgrid-php.php");
require("/var/www/html/wp-content/plugins/c_form/sendgrid-php/sendgrid-php.php");
/**
 * Child class of MainPage used for user preferrences page.
 *
 * Implements processData and showContent
 */

class cFormPage extends MainPage {

/**
 * Process the data and insert / modify database.
 *
 * @param $uid is user id passed by reference.
 */
function processData(&$uid) {
	$response = NULL;
	$reCaptcha = new ReCaptcha($this->secret);

    // Process the verified data here.
	$fname  = $this->formL->getValue("fname");
	$lname = $this->formL->getValue("lname");
	$email = $this->formL->getValue("email");
	$subject = $this->formL->getValue("subject");
	$content = $this->formL->getValue("content");
	// if submitted check response
	if ($_POST["g-recaptcha-response"]) {
	    $response = $reCaptcha->verifyResponse(
	        $_SERVER["REMOTE_ADDR"],
	        $_POST["g-recaptcha-response"]
	    );
	    if ($response != null && $response->success) {
		$this->sendGridMail($email,$subject,$content,$fname,$lname);
	        echo "Your email is sent";
	    } else 
		echo "Please check the reCaptcha box";
	} else 
	    echo "<p><font color='red'>Please check the reCaptcha box.</font></p>";
}

/**
 * Display the content of the page.
 *
 * @param $title is page title.
 * @param $uid is user id passed by reference.
 */
function showContent($title, &$uid) {

// Put HTML after the closing PHP tag
?>
<script src="https://www.google.com/recaptcha/api.js"></script>
<div class="preamble" id="KSQD-preamble" role="article">
<h3>Please fill out all the fields</h3>
<?php
	echo $this->formL->reportErrors();
	echo $this->formL->start('POST', "", 'name="host_email"');
	echo $this->formL->makeTextInput("fname");
	echo $this->formL->formatonError('fname','First Name') . "<br><br>";
	echo $this->formL->makeTextInput("lname");
	echo $this->formL->formatonError('lname','Last Name') . "<br><br>";
	echo $this->formL->makeEmailInput("email");
	echo $this->formL->formatonError('email','Your email address') ."<br><br>" ;
	echo $subject = $this->formL->makeTextInput("subject");
	echo $this->formL->formatonError('subject','Subject') . "<br><br>";
	echo $this->formL->formatonError('content','Your Message') . "<br><br>";
	echo $content = $this->formL->makeTextArea("content",5);
?>
<br>
<input class="subbutton" type="submit" name="Submit" value="Submit">
<br>
<div class="g-recaptcha" data-sitekey="6LcwJagUAAAAANWRDfITT9FdTquL6DVoZRMgO4Ta"></div>
</fieldset>
</form>
<?php
$this->formL->finish();
return $pagedata;
}
/**
 * Send email through SendGrid
 *
 * @param $femail is "from" email
 * @param $subject, $content, $fname, $lname self explanatory
 */
function sendGridMail($femail,$subject,$content,$fname,$lname) {
$email = new \SendGrid\Mail\Mail(); 
$email->setFrom($femail, $fname . " " . $lname);
$email->setSubject($subject);
$email->addTo($this->cookie_key, "From Web Form");
$email->addContent("text/plain", $content);
$email->addContent(
    "text/html", "$content"
);
$sendgrid = new \SendGrid($this->sendGRID);
try {
    $response = $sendgrid->send($email);
    return $response->body() . "<br>";
/*
    print $response->statusCode() . "\n";
    print_r($response->headers());
    print $response->body() . "\n";
*/
} catch (Exception $e) {
    return 'Caught exception: '. $e->getMessage() ."\n";
}
}
}
?>
