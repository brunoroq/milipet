#!/bin/bash
# Script de verificación rápida del CRUD de taxonomía

echo "🧪 VERIFICACIÓN DEL CRUD DE TAXONOMÍA - MiliPet"
echo "=============================================="
echo ""

# Colores
GREEN='\033[0;32m'
BLUE='\033[0;34m'
NC='\033[0m' # No Color

# 1. Verificar que los contenedores estén corriendo
echo -e "${BLUE}1. Verificando contenedores Docker...${NC}"
docker-compose ps | grep -E "(mariadb|php-apache)" > /dev/null
if [ $? -eq 0 ]; then
    echo -e "${GREEN}✓ Contenedores corriendo${NC}"
else
    echo "✗ Contenedores no están corriendo. Ejecuta: docker-compose up -d"
    exit 1
fi
echo ""

# 2. Verificar sintaxis PHP
echo -e "${BLUE}2. Verificando sintaxis PHP de archivos nuevos...${NC}"
FILES=(
    "app/models/Species.php"
    "app/models/Category.php"
    "app/controllers/AdminController.php"
    "app/views/admin/species.php"
    "app/views/admin/categories.php"
    "public/index.php"
)

for file in "${FILES[@]}"; do
    php -l "$file" > /dev/null 2>&1
    if [ $? -eq 0 ]; then
        echo -e "${GREEN}✓${NC} $file"
    else
        echo "✗ Error de sintaxis en $file"
        php -l "$file"
    fi
done
echo ""

# 3. Verificar que las tablas existan
echo -e "${BLUE}3. Verificando estructura de base de datos...${NC}"
docker exec mariadb mysql -u root -prootpassword milipet -e "SHOW TABLES;" 2>/dev/null | grep -E "(species|categories|products)" > /dev/null
if [ $? -eq 0 ]; then
    echo -e "${GREEN}✓ Tablas encontradas${NC}"
    
    # Mostrar conteo de registros
    echo ""
    echo "Conteo de registros:"
    docker exec mariadb mysql -u root -prootpassword milipet -se "
        SELECT 'Especies:' as tabla, COUNT(*) as total FROM species
        UNION ALL
        SELECT 'Categorías:' as tabla, COUNT(*) as total FROM categories
        UNION ALL
        SELECT 'Productos:' as tabla, COUNT(*) as total FROM products;
    " 2>/dev/null
else
    echo "✗ No se pudieron verificar las tablas"
fi
echo ""

# 4. URLs de acceso
echo -e "${BLUE}4. URLs de acceso al panel de admin:${NC}"
echo ""
echo "📋 Gestión de Especies:"
echo "   http://localhost:8080/?r=admin/species"
echo ""
echo "📋 Gestión de Categorías:"
echo "   http://localhost:8080/?r=admin/categories"
echo ""
echo "📦 Gestión de Productos:"
echo "   http://localhost:8080/?r=admin/products"
echo ""
echo "🏠 Dashboard Admin:"
echo "   http://localhost:8080/?r=admin/dashboard"
echo ""

# 5. Recordatorio de credenciales
echo -e "${BLUE}5. Credenciales de acceso:${NC}"
echo "   Usuario: admin@milipet.cl"
echo "   Password: (la contraseña configurada en tu BD)"
echo ""

echo "=============================================="
echo -e "${GREEN}✅ Verificación completada${NC}"
echo ""
echo "📝 Próximos pasos:"
echo "   1. Accede al panel admin con las credenciales"
echo "   2. Ve al menú 'Taxonomía' en el header"
echo "   3. Prueba crear/editar/eliminar especies"
echo "   4. Prueba crear/editar/eliminar categorías"
echo "   5. Verifica que el mega-menú muestre las especies"
echo ""
