<?php
/*
 * This is a PHP library that handles calling reCAPTCHA v2.
 *    - Get a reCAPTCHA API Key
 *          https://www.google.com/recaptcha/admin
 *    - Discussion group
 *          http://groups.google.com/group/recaptcha
 *
 * Copyright (c) 2007 reCAPTCHA -- https://www.google.com/recaptcha/intro/index.html
 * AUTHOR:
 *   Claudio Sperti
 *
 * Permission is hereby granted, free of charge, to any person obtaining a copy
 * of this software and associated documentation files (the "Software"), to deal
 * in the Software without restriction, including without limitation the rights
 * to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
 * copies of the Software, and to permit persons to whom the Software is
 * furnished to do so, subject to the following conditions:
 *
 * The above copyright notice and this permission notice shall be included in
 * all copies or substantial portions of the Software.
 *
 * THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
 * IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
 * FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
 * AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
 * LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
 * OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN
 * THE SOFTWARE.
 */

/**
 * The reCAPTCHA server URL's
 */
define("RECAPTCHA_VERIFY_SERVER", "https://www.google.com/recaptcha/api/siteverify");

/**
 * Submits an HTTP POST to a reCAPTCHA server
 * @param string $url
 * @param array $data
 * @return json response
 */
function _recaptcha_https_post($url, $data) {

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
        curl_setopt($ch, CURLOPT_POST, 1);
        
        curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
        $result = curl_exec($ch);

        $resultJson = json_decode($result);

        return $resultJson;
}



/**
 * Gets the challenge HTML (javascript and non-javascript version).
 * This is called from the browser, and the resulting reCAPTCHA HTML widget
 * is embedded within the HTML form it was called from.
 * @param string $pubkey A public key for reCAPTCHA
 * @param boolean $use_ssl Should the request be made over ssl? (optional, default is false)

 * @return string - The HTML to be embedded in the user's form.
 */
function recaptcha_get_html ($pubkey)
{
	if ($pubkey == null || $pubkey == '') {
		die ("To use reCAPTCHA you must get an API key from <a href='https://www.google.com/recaptcha/admin'>https://www.google.com/recaptcha/admin</a>");
	}
	
  $errorpart = "";
  if ($error) {
     $errorpart = "&amp;error=" . $error;
  }
  
  return '<script src="https://www.google.com/recaptcha/api.js" async defer></script>
  <div class="g-recaptcha" data-sitekey="' . $pubkey . '"></div>';
}

/**
 * A ReCaptchaResponse is returned from recaptcha_check_answer()
 */
class ReCaptchaResponse {
        var $is_valid;
        var $error;
}


/**
  * Calls an HTTPS POST function to verify if the user's guess was correct
  * @param string $privkey
  * @param string $remoteip
  * @param string $response
  * @param array $extra_params an array of extra variables to post to the server
  * @return ReCaptchaResponse
  */
function recaptcha_check_answer ($privkey, $remoteip, $response, $extra_params = array())
{
	if ($privkey == null || $privkey == '') {
		die ("To use reCAPTCHA you must get an API key from <a href='https://www.google.com/recaptcha/admin'>https://www.google.com/recaptcha/admin</a>");
	}
	
  //discard spam submissions
  if ($response == null || strlen($response) == 0) {
    $recaptcha_response = new ReCaptchaResponse();
    $recaptcha_response->is_valid = false;
    $recaptcha_response->error = 'incorrect-captcha-sol';
    return $recaptcha_response;
  }

  $response = _recaptcha_https_post (
    RECAPTCHA_VERIFY_SERVER,
    array (
           'secret' => $privkey,
           'remoteip' => $remoteip,
           'response' => $response
           ) + $extra_params
  );

  return $response;

}

?>