# Script para iniciar PreConsulta con Docker
# Ejecutar con: .\start-docker.ps1

Write-Host "🐳 Iniciando PreConsulta con Docker..." -ForegroundColor Cyan
Write-Host ""

# Verificar si Docker está instalado
if (-not (Get-Command docker -ErrorAction SilentlyContinue)) {
    Write-Host "❌ Error: Docker no está instalado." -ForegroundColor Red
    Write-Host "Descárgalo desde: https://www.docker.com/products/docker-desktop" -ForegroundColor Yellow
    exit 1
}

# Verificar si Docker está ejecutándose
$dockerRunning = docker info 2>&1
if ($LASTEXITCODE -ne 0) {
    Write-Host "❌ Error: Docker no está ejecutándose." -ForegroundColor Red
    Write-Host "Inicia Docker Desktop y vuelve a ejecutar este script." -ForegroundColor Yellow
    exit 1
}

Write-Host "✅ Docker está funcionando correctamente" -ForegroundColor Green
Write-Host ""

# Detener contenedores anteriores si existen
Write-Host "🔄 Deteniendo contenedores anteriores (si existen)..." -ForegroundColor Yellow
docker-compose down 2>&1 | Out-Null

# Levantar servicios
Write-Host "🚀 Levantando servicios..." -ForegroundColor Cyan
docker-compose up -d

if ($LASTEXITCODE -eq 0) {
    Write-Host ""
    Write-Host "✅ ¡Servicios iniciados correctamente!" -ForegroundColor Green
    Write-Host ""
    Write-Host "📊 Servicios disponibles:" -ForegroundColor Cyan
    Write-Host "  • Aplicación Web:  http://localhost:8090" -ForegroundColor White
    Write-Host "  • PhpMyAdmin:      http://localhost:8091" -ForegroundColor White
    Write-Host ""
    Write-Host "👤 Usuarios de prueba:" -ForegroundColor Cyan
    Write-Host "  • Email: juan.perez@email.com" -ForegroundColor White
    Write-Host "  • Password: password123" -ForegroundColor White
    Write-Host ""
    Write-Host "⏳ Espera 20-30 segundos para que MySQL inicialice..." -ForegroundColor Yellow
    Write-Host ""
    Write-Host "💡 Comandos útiles:" -ForegroundColor Cyan
    Write-Host "  • Ver logs:     docker-compose logs -f" -ForegroundColor White
    Write-Host "  • Detener:      docker-compose stop" -ForegroundColor White
    Write-Host "  • Eliminar:     docker-compose down" -ForegroundColor White
    Write-Host ""
} else {
    Write-Host ""
    Write-Host "❌ Error al iniciar los servicios" -ForegroundColor Red
    Write-Host "Revisa los logs con: docker-compose logs" -ForegroundColor Yellow
    exit 1
}
