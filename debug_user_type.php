<?php
require_once 'config/database.php';
require_once 'classes/Database.php';

echo "<h2>Diagnóstico de Tipos de Usuario</h2>";

$email = 'maria.gonzalez@hospital.com';

try {
    $db = Database::getInstance();
    
    // Buscar usuario
    $sql = "SELECT id_usuario, nombre, apellidos FROM Usuario WHERE email = :email";
    $user = $db->selectOne($sql, ['email' => $email]);
    
    if (!$user) {
        echo "<span style='color: red;'>❌ Usuario no encontrado</span>";
        exit;
    }
    
    echo "<strong>Usuario encontrado:</strong><br>";
    echo "ID: " . $user['id_usuario'] . "<br>";
    echo "Nombre: " . $user['nombre'] . " " . $user['apellidos'] . "<br><br>";
    
    // Verificar en Enfermero
    echo "<h3>1. Verificar en tabla Enfermero</h3>";
    $sqlEnf = "SELECT * FROM Enfermero WHERE id_enfermero = :id";
    $enfermero = $db->selectOne($sqlEnf, ['id' => $user['id_usuario']]);
    
    echo "SQL: <code>$sqlEnf</code><br>";
    echo "ID buscado: " . $user['id_usuario'] . "<br>";
    echo "Resultado: ";
    if ($enfermero) {
        echo "<span style='color: green;'>✅ SÍ está en Enfermero</span><br>";
        echo "<pre>" . print_r($enfermero, true) . "</pre>";
    } else {
        echo "<span style='color: red;'>❌ NO está en Enfermero</span><br>";
    }
    
    // Verificar en Celador
    echo "<h3>2. Verificar en tabla Celador</h3>";
    $sqlCel = "SELECT * FROM Celador WHERE id_celador = :id";
    $celador = $db->selectOne($sqlCel, ['id' => $user['id_usuario']]);
    
    echo "SQL: <code>$sqlCel</code><br>";
    echo "ID buscado: " . $user['id_usuario'] . "<br>";
    echo "Resultado: ";
    if ($celador) {
        echo "<span style='color: green;'>✅ SÍ está en Celador</span><br>";
        echo "<pre>" . print_r($celador, true) . "</pre>";
    } else {
        echo "<span style='color: red;'>❌ NO está en Celador</span><br>";
    }
    
    // Verificar en Paciente
    echo "<h3>3. Verificar en tabla Paciente</h3>";
    $sqlPac = "SELECT * FROM Paciente WHERE id_paciente = :id";
    $paciente = $db->selectOne($sqlPac, ['id' => $user['id_usuario']]);
    
    echo "SQL: <code>$sqlPac</code><br>";
    echo "ID buscado: " . $user['id_usuario'] . "<br>";
    echo "Resultado: ";
    if ($paciente) {
        echo "<span style='color: green;'>✅ SÍ está en Paciente</span><br>";
        echo "<pre>" . print_r($paciente, true) . "</pre>";
    } else {
        echo "<span style='color: red;'>❌ NO está en Paciente</span><br>";
    }
    
    // Conclusión
    echo "<h3>4. Conclusión</h3>";
    if ($enfermero) {
        echo "<span style='color: green; font-size: 1.2em;'>✅ Este usuario debería ser ENFERMERO</span>";
    } else if ($celador) {
        echo "<span style='color: blue; font-size: 1.2em;'>✅ Este usuario debería ser CELADOR</span>";
    } else if ($paciente) {
        echo "<span style='color: orange; font-size: 1.2em;'>⚠️ Este usuario debería ser PACIENTE</span>";
    } else {
        echo "<span style='color: red; font-size: 1.2em;'>❌ Este usuario NO está en ninguna tabla de rol</span>";
    }
    
    // Verificar todos los enfermeros
    echo "<h3>5. Todos los registros en Enfermero</h3>";
    $allEnf = $db->select("SELECT e.*, u.email FROM Enfermero e JOIN Usuario u ON e.id_enfermero = u.id_usuario");
    if ($allEnf) {
        echo "<table border='1' cellpadding='5'>";
        echo "<tr><th>ID</th><th>Email</th><th>Número Colegiado</th><th>Especialidad</th></tr>";
        foreach ($allEnf as $e) {
            $highlight = ($e['id_enfermero'] == $user['id_usuario']) ? "style='background: yellow;'" : "";
            echo "<tr $highlight>";
            echo "<td>" . $e['id_enfermero'] . "</td>";
            echo "<td>" . $e['email'] . "</td>";
            echo "<td>" . $e['numero_colegiado'] . "</td>";
            echo "<td>" . $e['especialidad'] . "</td>";
            echo "</tr>";
        }
        echo "</table>";
    }
    
} catch (Exception $e) {
    echo "<span style='color: red;'>Error: " . $e->getMessage() . "</span>";
    echo "<pre>" . $e->getTraceAsString() . "</pre>";
}

echo "<br><br><a href='login.html'>Volver al login</a>";
?>
