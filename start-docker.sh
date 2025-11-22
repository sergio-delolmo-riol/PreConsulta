#!/bin/bash
# Script para iniciar PreConsulta con Docker
# Ejecutar con: ./start-docker.sh

echo "🐳 Iniciando PreConsulta con Docker..."
echo ""

# Verificar si Docker está instalado
if ! command -v docker &> /dev/null; then
    echo "❌ Error: Docker no está instalado."
    echo "Descárgalo desde: https://www.docker.com/products/docker-desktop"
    exit 1
fi

# Verificar si Docker está ejecutándose
if ! docker info &> /dev/null; then
    echo "❌ Error: Docker no está ejecutándose."
    echo "Inicia Docker Desktop y vuelve a ejecutar este script."
    exit 1
fi

echo "✅ Docker está funcionando correctamente"
echo ""

# Detener contenedores anteriores si existen
echo "🔄 Deteniendo contenedores anteriores (si existen)..."
docker-compose down 2>&1 > /dev/null

# Levantar servicios
echo "🚀 Levantando servicios..."
docker-compose up -d

if [ $? -eq 0 ]; then
    echo ""
    echo "✅ ¡Servicios iniciados correctamente!"
    echo ""
    echo "📊 Servicios disponibles:"
    echo "  • Aplicación Web:  http://localhost:8090"
    echo "  • PhpMyAdmin:      http://localhost:8091"
    echo ""
    echo "👤 Usuarios de prueba:"
    echo "  • Email: juan.perez@email.com"
    echo "  • Password: password123"
    echo ""
    echo "⏳ Espera 20-30 segundos para que MySQL inicialice..."
    echo ""
    echo "💡 Comandos útiles:"
    echo "  • Ver logs:     docker-compose logs -f"
    echo "  • Detener:      docker-compose stop"
    echo "  • Eliminar:     docker-compose down"
    echo ""
else
    echo ""
    echo "❌ Error al iniciar los servicios"
    echo "Revisa los logs con: docker-compose logs"
    exit 1
fi
