<?php
$password = 'enfermero123';
$hash = password_hash($password, PASSWORD_DEFAULT);
echo "Hash para 'enfermero123':\n";
echo $hash . "\n";
echo "\nVerificación: " . (password_verify($password, $hash) ? "OK" : "ERROR") . "\n";
