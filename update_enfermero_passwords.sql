-- Actualizar contraseñas de enfermeros con hash correcto
-- Password: enfermero123
-- Hash completo: $2y$10$vLMcHTf4iK.faNZjnCPBIu4bO/etaS8ciSQWcCMWyHTCqjaW1E7Ca

UPDATE Usuario 
SET password = '$2y$10$vLMcHTf4iK.faNZjnCPBIu4bO/etaS8ciSQWcCMWyHTCqjaW1E7Ca'
WHERE email IN ('maria.gonzalez@hospital.com', 'carlos.martinez@hospital.com', 'ana.fernandez@hospital.com');

-- Verificar que se actualizó correctamente
SELECT 
    email, 
    password,
    LENGTH(password) as hash_length,
    CASE 
        WHEN LENGTH(password) = 60 THEN '✓ Correcto'
        ELSE '✗ Incorrecto'
    END as status
FROM Usuario 
WHERE email IN ('maria.gonzalez@hospital.com', 'carlos.martinez@hospital.com', 'ana.fernandez@hospital.com');
