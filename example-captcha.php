<html>
  <body>
    <form action="" method="post">
<?php

require_once('recaptchalibv2.php');

// Get a key from https://www.google.com/recaptcha/admin
$publickey = "";
$privatekey = "";

# the response from reCAPTCHA
$resp = null;
# the error code from reCAPTCHA, if any
$error = null;

# was there a reCAPTCHA response?
if ($_POST["g-recaptcha-response"]) {
    $resp = recaptcha_check_answer ($privatekey,
                                    $_SERVER["REMOTE_ADDR"],
                                    $_POST["g-recaptcha-response"]);

    if ($resp->success) {
            echo "Everything's OK!";
    } else {
            # set the error code so that we can display it
            $error = $resp['error-codes'];
    }
}
echo recaptcha_get_html($publickey);
?>
    <br/>
    <input type="submit" value="submit" />
    </form>
  </body>
</html>
