<?php
echo "=== GENERANDO HASHES CORRECTOS ===\n\n";

echo "Hash para 'password123':\n";
echo password_hash('password123', PASSWORD_BCRYPT) . "\n\n";

echo "Hash para 'enfermero123':\n";
echo password_hash('enfermero123', PASSWORD_BCRYPT) . "\n\n";

echo "Hash para 'PreConsulta2024!':\n";
echo password_hash('PreConsulta2024!', PASSWORD_BCRYPT) . "\n\n";

echo "=== VERIFICANDO HASH ACTUAL ===\n\n";
$hash_actual = '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi';

$passwords_prueba = ['password123', 'enfermero123', 'PreConsulta2024!', 'password', '123456', 'admin', 'test'];

foreach ($passwords_prueba as $pass) {
    $resultado = password_verify($pass, $hash_actual) ? '✅ COINCIDE' : '❌ No';
    echo "$pass: $resultado\n";
}
?>
