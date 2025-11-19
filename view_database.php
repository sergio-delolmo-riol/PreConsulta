<?php
/**
 * Script de Visualización de Base de Datos
 * Proyecto: PreConsulta
 * 
 * Ejecutar desde línea de comandos: php view_database.php
 * O desde navegador: http://localhost/PreConsulta/view_database.php
 */

require_once __DIR__ . '/classes/Database.php';

// Configurar para visualización web o CLI
$isCLI = php_sapi_name() === 'cli';
$br = $isCLI ? "\n" : "<br>";
$hr = $isCLI ? str_repeat("━", 80) . "\n" : "<hr>";

if (!$isCLI) {
    echo "<!DOCTYPE html>
    <html lang='es'>
    <head>
        <meta charset='UTF-8'>
        <meta name='viewport' content='width=device-width, initial-scale=1.0'>
        <title>Visualizador BD - PreConsulta</title>
        <style>
            body { 
                font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; 
                max-width: 1400px; 
                margin: 20px auto; 
                padding: 20px;
                background: #f5f5f5;
            }
            h1, h2 { color: #007aff; }
            table { 
                width: 100%; 
                border-collapse: collapse; 
                background: white;
                box-shadow: 0 2px 4px rgba(0,0,0,0.1);
                margin: 20px 0;
            }
            th { 
                background: #007aff; 
                color: white; 
                padding: 12px; 
                text-align: left;
                font-weight: 600;
            }
            td { 
                padding: 10px; 
                border-bottom: 1px solid #ddd; 
            }
            tr:hover { background: #f9f9f9; }
            .stats { 
                display: grid;
                grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
                gap: 15px;
                margin: 20px 0;
            }
            .stat-card {
                background: white;
                padding: 20px;
                border-radius: 8px;
                box-shadow: 0 2px 4px rgba(0,0,0,0.1);
                text-align: center;
            }
            .stat-card h3 { margin: 0; color: #666; font-size: 14px; }
            .stat-card .number { font-size: 36px; color: #007aff; font-weight: bold; }
            .badge {
                display: inline-block;
                padding: 4px 8px;
                border-radius: 4px;
                font-size: 12px;
                font-weight: bold;
            }
            .badge-success { background: #d4edda; color: #155724; }
            .badge-warning { background: #fff3cd; color: #856404; }
            .badge-danger { background: #f8d7da; color: #721c24; }
            .badge-info { background: #d1ecf1; color: #0c5460; }
            .section { background: white; padding: 20px; margin: 20px 0; border-radius: 8px; box-shadow: 0 2px 4px rgba(0,0,0,0.1); }
            .error { color: #dc3545; padding: 15px; background: #f8d7da; border-radius: 4px; }
            code { background: #f4f4f4; padding: 2px 6px; border-radius: 3px; }
        </style>
    </head>
    <body>
        <h1>🗄️ Visualizador de Base de Datos - PreConsulta</h1>
        <p>Base de datos: <code>centro_triaje_digital</code></p>
    ";
}

try {
    $db = Database::getInstance();
    
    if ($isCLI) {
        echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━{$br}";
        echo "🗄️  VISUALIZADOR DE BASE DE DATOS - PRECONSULTA{$br}";
        echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━{$br}{$br}";
    }
    
    // ============================================
    // 1. ESTADÍSTICAS GENERALES
    // ============================================
    
    if (!$isCLI) echo "<div class='section'><h2>📊 Estadísticas Generales</h2><div class='stats'>";
    else echo "📊 ESTADÍSTICAS GENERALES{$br}{$hr}";
    
    $stats = [
        ['nombre' => 'Usuarios Totales', 'query' => 'SELECT COUNT(*) as total FROM Usuario', 'icon' => '👥'],
        ['nombre' => 'Pacientes', 'query' => 'SELECT COUNT(*) as total FROM Paciente', 'icon' => '🏥'],
        ['nombre' => 'Enfermeros', 'query' => 'SELECT COUNT(*) as total FROM Enfermero', 'icon' => '👨‍⚕️'],
        ['nombre' => 'Celadores', 'query' => 'SELECT COUNT(*) as total FROM Celador', 'icon' => '👷'],
        ['nombre' => 'Episodios Activos', 'query' => "SELECT COUNT(*) as total FROM Episodio_Urgencia WHERE estado != 'alta'", 'icon' => '🚨'],
        ['nombre' => 'Boxes Libres', 'query' => "SELECT COUNT(*) as total FROM Box WHERE estado = 'libre'", 'icon' => '🏨'],
        ['nombre' => 'Notificaciones Sin Leer', 'query' => "SELECT COUNT(*) as total FROM Notificacion WHERE leida = 0", 'icon' => '🔔'],
        ['nombre' => 'Registros en Log', 'query' => 'SELECT COUNT(*) as total FROM Log_Acciones', 'icon' => '📝']
    ];
    
    foreach ($stats as $stat) {
        $result = $db->selectOne($stat['query']);
        $total = $result['total'];
        
        if ($isCLI) {
            echo sprintf("   %s %-30s : %4d{$br}", $stat['icon'], $stat['nombre'], $total);
        } else {
            echo "<div class='stat-card'>
                    <h3>{$stat['icon']} {$stat['nombre']}</h3>
                    <div class='number'>{$total}</div>
                  </div>";
        }
    }
    
    if (!$isCLI) echo "</div></div>";
    else echo $br;
    
    // ============================================
    // 2. PACIENTES EN ESPERA
    // ============================================
    
    if (!$isCLI) echo "<div class='section'><h2>⏳ Pacientes en Espera</h2>";
    else echo "{$hr}⏳ PACIENTES EN ESPERA{$br}{$hr}";
    
    $pacientes_espera = $db->select("
        SELECT 
            e.id_episodio,
            CONCAT(u.nombre, ' ', u.apellidos) as paciente,
            p.nombre as prioridad,
            p.tipo_prioridad,
            p.color_hex,
            e.estado,
            TIMESTAMPDIFF(MINUTE, e.fecha_llegada, NOW()) as minutos_espera,
            e.motivo_consulta
        FROM Episodio_Urgencia e
        JOIN Paciente pac ON e.id_paciente = pac.id_paciente
        JOIN Usuario u ON pac.id_paciente = u.id_usuario
        LEFT JOIN Prioridad p ON e.prioridad_actual = p.id_prioridad
        WHERE e.estado IN ('espera_triaje', 'espera_atencion', 'en_triaje')
        ORDER BY p.tipo_prioridad DESC, e.fecha_llegada ASC
    ");
    
    if ($pacientes_espera) {
        if (!$isCLI) {
            echo "<table>
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Paciente</th>
                            <th>Prioridad</th>
                            <th>Estado</th>
                            <th>Tiempo Espera</th>
                            <th>Motivo</th>
                        </tr>
                    </thead>
                    <tbody>";
        }
        
        foreach ($pacientes_espera as $p) {
            $emoji = match($p['tipo_prioridad']) {
                'alta' => '🔴',
                'media' => '🟠',
                default => '🟢'
            };
            
            if ($isCLI) {
                echo sprintf(
                    "   %s [%02d] %-25s | %-15s | %3d min | %s{$br}",
                    $emoji,
                    $p['id_episodio'],
                    $p['paciente'],
                    $p['prioridad'],
                    $p['minutos_espera'],
                    substr($p['motivo_consulta'], 0, 30)
                );
            } else {
                $badgeClass = match($p['tipo_prioridad']) {
                    'alta' => 'badge-danger',
                    'media' => 'badge-warning',
                    default => 'badge-success'
                };
                echo "<tr>
                        <td>{$p['id_episodio']}</td>
                        <td>{$emoji} {$p['paciente']}</td>
                        <td><span class='badge {$badgeClass}'>{$p['prioridad']}</span></td>
                        <td>{$p['estado']}</td>
                        <td>{$p['minutos_espera']} min</td>
                        <td>" . substr($p['motivo_consulta'], 0, 50) . "...</td>
                      </tr>";
            }
        }
        
        if (!$isCLI) echo "</tbody></table>";
    } else {
        echo $isCLI ? "   ✅ No hay pacientes en espera{$br}" : "<p>✅ No hay pacientes en espera</p>";
    }
    
    if (!$isCLI) echo "</div>";
    else echo $br;
    
    // ============================================
    // 3. TODOS LOS USUARIOS
    // ============================================
    
    if (!$isCLI) echo "<div class='section'><h2>👥 Usuarios Registrados</h2>";
    else echo "{$hr}👥 USUARIOS REGISTRADOS{$br}{$hr}";
    
    $usuarios = $db->select("
        SELECT 
            u.id_usuario,
            u.nombre,
            u.apellidos,
            u.email,
            u.telefono,
            u.estado,
            CASE 
                WHEN p.id_paciente IS NOT NULL THEN 'Paciente'
                WHEN e.id_enfermero IS NOT NULL THEN 'Enfermero'
                WHEN c.id_celador IS NOT NULL THEN 'Celador'
                ELSE 'Usuario'
            END as tipo
        FROM Usuario u
        LEFT JOIN Paciente p ON u.id_usuario = p.id_paciente
        LEFT JOIN Enfermero e ON u.id_usuario = e.id_enfermero
        LEFT JOIN Celador c ON u.id_usuario = c.id_celador
        ORDER BY tipo, u.nombre
        LIMIT 20
    ");
    
    if (!$isCLI) {
        echo "<table>
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Nombre</th>
                        <th>Email</th>
                        <th>Teléfono</th>
                        <th>Tipo</th>
                        <th>Estado</th>
                    </tr>
                </thead>
                <tbody>";
    }
    
    foreach ($usuarios as $u) {
        $emoji = match($u['tipo']) {
            'Paciente' => '🏥',
            'Enfermero' => '👨‍⚕️',
            'Celador' => '👷',
            default => '👤'
        };
        
        if ($isCLI) {
            echo sprintf(
                "   %s [%02d] %-30s | %-30s | %-12s{$br}",
                $emoji,
                $u['id_usuario'],
                $u['nombre'] . ' ' . $u['apellidos'],
                $u['email'],
                $u['tipo']
            );
        } else {
            $estadoBadge = $u['estado'] === 'activo' ? 'badge-success' : 'badge-danger';
            echo "<tr>
                    <td>{$u['id_usuario']}</td>
                    <td>{$emoji} {$u['nombre']} {$u['apellidos']}</td>
                    <td>{$u['email']}</td>
                    <td>{$u['telefono']}</td>
                    <td><span class='badge badge-info'>{$u['tipo']}</span></td>
                    <td><span class='badge {$estadoBadge}'>{$u['estado']}</span></td>
                  </tr>";
        }
    }
    
    if (!$isCLI) {
        echo "</tbody></table>";
        echo "<p><em>Mostrando primeros 20 usuarios</em></p>";
    }
    
    if (!$isCLI) echo "</div>";
    else echo $br;
    
    // ============================================
    // 4. BOXES DISPONIBLES
    // ============================================
    
    if (!$isCLI) echo "<div class='section'><h2>🏨 Estado de Boxes</h2>";
    else echo "{$hr}🏨 ESTADO DE BOXES{$br}{$hr}";
    
    $boxes = $db->select("SELECT * FROM Box ORDER BY nombre");
    
    if (!$isCLI) {
        echo "<table>
                <thead>
                    <tr>
                        <th>Box</th>
                        <th>Ubicación</th>
                        <th>Estado</th>
                        <th>Capacidad</th>
                    </tr>
                </thead>
                <tbody>";
    }
    
    foreach ($boxes as $box) {
        $emoji = match($box['estado']) {
            'libre' => '🟢',
            'ocupado' => '🔴',
            'limpieza' => '🟡',
            default => '⚪'
        };
        
        if ($isCLI) {
            echo sprintf(
                "   %s %-20s | %-30s | %-12s{$br}",
                $emoji,
                $box['nombre'],
                $box['ubicacion'],
                $box['estado']
            );
        } else {
            $estadoBadge = match($box['estado']) {
                'libre' => 'badge-success',
                'ocupado' => 'badge-danger',
                'limpieza' => 'badge-warning',
                default => 'badge-info'
            };
            echo "<tr>
                    <td>{$emoji} {$box['nombre']}</td>
                    <td>{$box['ubicacion']}</td>
                    <td><span class='badge {$estadoBadge}'>{$box['estado']}</span></td>
                    <td>{$box['capacidad']}</td>
                  </tr>";
        }
    }
    
    if (!$isCLI) echo "</tbody></table></div>";
    else echo $br;
    
    // ============================================
    // FOOTER
    // ============================================
    
    if ($isCLI) {
        echo "{$hr}";
        echo "✅ Consulta completada{$br}";
        echo "💡 Para más detalles, usa phpMyAdmin o MySQL Workbench{$br}";
    } else {
        echo "<div class='section' style='text-align: center;'>
                <p>✅ <strong>Visualización completada</strong></p>
                <p>💡 Para editar datos, usa <a href='http://localhost/phpmyadmin' target='_blank'>phpMyAdmin</a></p>
              </div>
              </body>
              </html>";
    }
    
} catch (Exception $e) {
    $errorMsg = "❌ Error: " . $e->getMessage();
    if ($isCLI) {
        echo $errorMsg . $br;
    } else {
        echo "<div class='error'>{$errorMsg}</div></body></html>";
    }
}
