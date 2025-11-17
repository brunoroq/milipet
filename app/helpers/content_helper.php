<?php
/**
 * Helpers para el sistema de contenido editable (mini-CMS)
 * Permiten obtener texto e imágenes desde la base de datos
 */

/**
 * Obtener un texto de contenido editable
 * @param string $key Clave del bloque de contenido (ej: 'home.hero_title')
 * @param string $default Texto por defecto si no existe el bloque o está vacío
 * @return string Texto sanitizado para mostrar en HTML
 */
function cms_text(string $key, string $default = ''): string
{
  static $cache = [];
  
  // Cache simple para evitar múltiples queries de la misma clave
  if (!isset($cache[$key])) {
    $block = ContentBlock::findByKey($key);
    $cache[$key] = $block;
  }
  
  $block = $cache[$key];
  
  // Si el bloque no existe o está inactivo, usar el default
  if (!$block || empty($block['is_active'])) {
    return htmlspecialchars($default, ENT_QUOTES, 'UTF-8');
  }
  
  // Si el contenido está vacío, usar el default
  $content = $block['content'] ?? '';
  if (trim($content) === '') {
    return htmlspecialchars($default, ENT_QUOTES, 'UTF-8');
  }
  
  return htmlspecialchars($content, ENT_QUOTES, 'UTF-8');
}

/**
 * Obtener una URL de imagen de contenido editable
 * @param string $key Clave del bloque de contenido (ej: 'home.hero_image')
 * @param string $default URL por defecto si no existe el bloque o está vacía
 * @return string URL sanitizada para usar en atributos HTML
 */
function cms_image(string $key, string $default = ''): string
{
  static $cache = [];
  
  // Cache simple para evitar múltiples queries de la misma clave
  if (!isset($cache[$key])) {
    $block = ContentBlock::findByKey($key);
    $cache[$key] = $block;
  }
  
  $block = $cache[$key];
  
  // Si el bloque no existe o está inactivo, usar el default
  if (!$block || empty($block['is_active'])) {
    return htmlspecialchars($default, ENT_QUOTES, 'UTF-8');
  }
  
  // Si la imagen está vacía, usar el default
  $imageUrl = $block['image_url'] ?? '';
  if (trim($imageUrl) === '') {
    return htmlspecialchars($default, ENT_QUOTES, 'UTF-8');
  }
  
  return htmlspecialchars($imageUrl, ENT_QUOTES, 'UTF-8');
}

/**
 * Obtener contenido sin escapar (para cuando necesitas HTML)
 * ⚠️ USAR CON PRECAUCIÓN - Solo para contenido confiable de admin
 * @param string $key Clave del bloque de contenido
 * @param string $default Contenido por defecto
 * @return string Contenido sin escapar
 */
function cms_html(string $key, string $default = ''): string
{
  static $cache = [];
  
  if (!isset($cache[$key])) {
    $block = ContentBlock::findByKey($key);
    $cache[$key] = $block;
  }
  
  $block = $cache[$key];
  
  if (!$block || empty($block['is_active'])) {
    return $default;
  }
  
  $content = $block['content'] ?? '';
  return trim($content) === '' ? $default : $content;
}
