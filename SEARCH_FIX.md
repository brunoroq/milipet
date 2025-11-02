# Fix Product::search() - Resumen de cambios

## Problema resuelto
Error `SQLSTATE[HY093]: Invalid parameter number` causado por inconsistencia entre placeholders SQL y parámetros ejecutados.

## Cambios aplicados en `app/models/Product.php`

### 1. Normalización de entradas
```php
// Convertir strings vacíos y "0" a null para evitar filtros no deseados
if ($q === '' || $q === '0') $q = null;
if ($category_id === '' || $category_id === '0') $category_id = null;
if ($species === '' || $species === '0') $species = null;
```

### 2. SQL dinámico con WHERE 1=1
- Base: `WHERE 1=1` para facilitar concatenación de condiciones
- Cada filtro se agrega **solo si el parámetro no es null**
- Array `$params` se construye dinámicamente

### 3. Parámetros nombrados consistentes
```php
// Búsqueda por texto (solo si $q tiene valor)
if ($q !== null && trim($q) !== '') {
    $sql .= " AND (p.name LIKE :q OR p.description LIKE :q)";
    $params[':q'] = '%' . trim($q) . '%';
}

// Categoría (solo si $category_id tiene valor)
if ($category_id !== null) {
    $sql .= " AND p.category_id = :cid";
    $params[':cid'] = (int)$category_id;
}

// Especie (solo si $species tiene valor)
if ($species !== null && trim($species) !== '') {
    $sql .= " AND s.name = :sp";
    $params[':sp'] = trim($species);
}
```

### 4. Manejo de errores con try/catch
```php
try {
    // ... código SQL ...
    return $st->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    error_log("Error en Product::search() - " . $e->getMessage());
    return [];
}
```

### 5. Retorno consistente
- Ahora siempre devuelve `PDO::FETCH_ASSOC` (array asociativo)
- En caso de error, devuelve array vacío `[]`

## Ventajas del nuevo código

✅ **Sin errores de parámetros**: cada `?` o `:placeholder` tiene su valor correspondiente  
✅ **Robusto**: maneja valores vacíos, "0" y null correctamente  
✅ **Legible**: WHERE 1=1 hace el código más fácil de mantener  
✅ **Seguro**: usa prepared statements con parámetros nombrados  
✅ **Tolerante a fallos**: captura excepciones PDO y retorna array vacío  
✅ **Logging**: registra errores en el log de PHP para debug

## Compatibilidad con controladores

El método mantiene la misma firma:
```php
Product::search($query, $category_id, $species, $include_out_of_stock)
```

Por lo que no requiere cambios en:
- `HomeController::index()`
- `CatalogController::index()`

## Testing manual

Para probar que funciona correctamente, visita:

1. **Búsqueda sin filtros**: `?r=catalog`
2. **Búsqueda por texto**: `?r=catalog&q=alimento`
3. **Búsqueda por categoría**: `?r=catalog&cid=1`
4. **Búsqueda por especie**: `?r=catalog&species=Perros`
5. **Incluir sin stock**: `?r=catalog&show_all=1`
6. **Combinado**: `?r=catalog&q=alimento&cid=1&species=Perros&show_all=1`

Todas las combinaciones deben funcionar sin errores SQL.
