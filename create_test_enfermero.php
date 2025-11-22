<?php
/**
 * Script para generar nuevo hash y crear enfermero de prueba
 */

require_once 'config/database.php';
require_once 'classes/Database.php';

echo "<h2>Generar Nuevo Enfermero de Prueba</h2>";

// Generar hash nuevo para la contraseña 'test123'
$password = 'test123';
$hash = password_hash($password, PASSWORD_DEFAULT);

echo "<strong>Contraseña:</strong> test123<br>";
echo "<strong>Hash generado:</strong> <code>$hash</code><br>";
echo "<strong>Longitud:</strong> " . strlen($hash) . "<br><br>";

// Verificar que funciona
$verify = password_verify($password, $hash);
echo "<strong>Verificación:</strong> " . ($verify ? "✅ CORRECTO" : "❌ ERROR") . "<br><br>";

try {
    $db = Database::getInstance();
    
    // Crear usuario de prueba
    $sql_usuario = "INSERT INTO Usuario (nombre, apellidos, dni, email, password, telefono, fecha_nacimiento, estado)
                    VALUES (:nombre, :apellidos, :dni, :email, :password, :telefono, :fecha_nac, :estado)";
    
    $params = [
        'nombre' => 'Test',
        'apellidos' => 'Enfermero Prueba',
        'dni' => '99999999T',
        'email' => 'test.enfermero@hospital.com',
        'password' => $hash,
        'telefono' => '999999999',
        'fecha_nac' => '1990-01-01',
        'estado' => 'activo'
    ];
    
    $db->query($sql_usuario, $params);
    $id_usuario = $db->getConnection()->lastInsertId();
    
    echo "<span style='color: green;'>✅ Usuario creado con ID: $id_usuario</span><br>";
    
    // Crear entrada en Enfermero
    $sql_enfermero = "INSERT INTO Enfermero (id_enfermero, numero_colegiado, especialidad, turno_actual, disponible, id_box)
                      VALUES (:id, :numero, :especialidad, :turno, :disponible, :box)";
    
    $params_enf = [
        'id' => $id_usuario,
        'numero' => 'ENF-TEST-999',
        'especialidad' => 'Test',
        'turno' => 'mañana',
        'disponible' => 1,
        'box' => null
    ];
    
    $db->query($sql_enfermero, $params_enf);
    
    echo "<span style='color: green;'>✅ Enfermero creado correctamente</span><br><br>";
    
    echo "<h3>Credenciales de prueba:</h3>";
    echo "<strong>Email:</strong> test.enfermero@hospital.com<br>";
    echo "<strong>Contraseña:</strong> test123<br><br>";
    
    echo "<form method='POST' action='login.php'>";
    echo "<input type='email' name='email' value='test.enfermero@hospital.com'><br><br>";
    echo "<input type='password' name='password' value='test123'><br><br>";
    echo "<button type='submit'>Probar Login</button>";
    echo "</form>";
    
} catch (Exception $e) {
    echo "<span style='color: red;'>❌ Error: " . $e->getMessage() . "</span><br>";
    echo "<pre>" . $e->getTraceAsString() . "</pre>";
}

echo "<br><br><a href='test_enfermero_login.php'>Ver todos los enfermeros</a>";
echo " | <a href='index.php'>Volver al inicio</a>";
?>
