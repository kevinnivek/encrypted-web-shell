# Encrypted Web Shell

A quick and simple way to protect your PHP code with the mcrypt library using MCRYPT_RIJNDAEL_128, which is AES-complaint.

## Description

This code uses the mcrypt library to encrpyt raw PHP code (without "<?php ?>", as it is executed via eval).
It then executes the code after decrypting it. This can potentially aid in the obfuscation (and potentially unrecoverable)
of your code, whether its shell, penetration testing or remote management and systems administration.

The key item to note here is that the key can be delivered to decrypt + run the code via a http POST, instead of a GET.
What does this mean? It means that the key you pass to decrypt will be harder to capture and utilize by monitoring parties or
via standard apache / nginx access logs. GETs are usually logged, but POSTs are not usually logged without additional
custom modules or configuration directives.

This might be more ideal than using the more common base64_encoding which is very simple and trivial to decrypt.


## Instructions

1. Load your code into a text file, read the contents of the file via file_get_contents and use the encrypt function to encrypt it with
a strong key.
2. Delete the file and the file_get_contents (save your key)
3. Paste the encrypted code into the $code variable
4. Run the file and enter the key in the input box in your browser
5. It should set a cookie wtih the key and refresh the page with the executed code
6. Some code or conditions in code may not execute due to it potentially not being escaped properly.

## Adjusting the encryption

You can learn more about utilizing PHP's mcrypt library, as well as the different options you can utilize to encrypt your code here : http://php.net/manual/en/book.mcrypt.php


