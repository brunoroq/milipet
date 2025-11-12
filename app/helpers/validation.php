<?php
/**
 * validation.php - Image validation helpers for product admin
 */

function is_https_url(string $s): bool {
    return (bool)preg_match('~^https://~i', $s);
}

function is_http_url(string $s): bool {
    return (bool)preg_match('~^https?://~i', $s);
}

function looks_like_image_filename(string $s): bool {
    return (bool)preg_match('~\.(jpe?g|png|webp|gif)$~i', $s);
}

function head_content_type_and_length(string $url): array {
    // Use cURL HEAD, follow redirects, 5s timeout, max 3 redirects
    $ch = curl_init($url);
    curl_setopt_array($ch, [
        CURLOPT_NOBODY => true,
        CURLOPT_FOLLOWLOCATION => true,
        CURLOPT_MAXREDIRS => 3,
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_TIMEOUT => 5,
        CURLOPT_SSL_VERIFYHOST => 2,
        CURLOPT_SSL_VERIFYPEER => true,
        CURLOPT_USERAGENT => 'MiliPet/1.0',
    ]);
    $result = curl_exec($ch);
    $error = curl_error($ch);
    
    // If HEAD fails, try GET with range to get headers
    if ($result === false || curl_getinfo($ch, CURLINFO_HTTP_CODE) === 0) {
        curl_close($ch);
        $ch = curl_init($url);
        curl_setopt_array($ch, [
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_MAXREDIRS => 3,
            CURLOPT_TIMEOUT => 5,
            CURLOPT_SSL_VERIFYHOST => 2,
            CURLOPT_SSL_VERIFYPEER => true,
            CURLOPT_RANGE => '0-1023',
            CURLOPT_USERAGENT => 'MiliPet/1.0',
        ]);
        curl_exec($ch);
    }
    
    $ct = curl_getinfo($ch, CURLINFO_CONTENT_TYPE) ?: '';
    $cl = (int)curl_getinfo($ch, CURLINFO_CONTENT_LENGTH_DOWNLOAD);
    $code = (int)curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);
    return [$code, strtolower((string)$ct), $cl];
}

function is_valid_external_image_url(string $url, bool $require_https = true): bool {
    if ($require_https && !is_https_url($url)) return false; // evita mixed content
    // No aceptar URLs de redirección "de búsqueda" (Google, Pinterest wrapper, etc.)
    if (preg_match('~^https?://(www\.)?google\.[^/]+/url\?~i', $url)) return false;
    [$code, $ct, $len] = head_content_type_and_length($url);
    // Accept 200 or 206 (partial content)
    if ($code !== 200 && $code !== 206) return false;
    if (strpos($ct, 'image/') !== 0 && strpos($ct, 'text/html') === false) {
        // Some servers don't send proper content-type, check if looks like image URL
        if (!looks_like_image_filename($url)) return false;
    }
    if ($len > 0 && $len > 10*1024*1024) return false; // 10MB límite blando
    return true;
}

function is_valid_local_image_path(string $path): bool {
    // Permitir "img/..." o "assets/img/..."
    $norm = ltrim($path, '/');
    if (str_starts_with($norm, 'assets/')) $norm = substr($norm, 7);
    $fs = __DIR__ . '/../../public/assets/' . $norm;
    if (!is_file($fs)) return false;
    if (!looks_like_image_filename($fs)) return false;
    return (bool)@getimagesize($fs); // verifica cabecera real de imagen
}

function validate_product_image(?string $value, array &$errors): ?string {
    $v = trim((string)$value);
    if ($v === '') {
        // Allow empty - will use placeholder via image_src()
        return '';
    }
    // HTTPS externo
    if (is_http_url($v)) {
        if (!is_valid_external_image_url($v, true)) {
            $errors[] = 'La URL de la imagen no es válida (debe ser https, devolver 200 y Content-Type image/*).';
            return null;
        }
        return $v; // usar la URL tal cual
    }
    // Ruta local relativa al /public/assets
    if (!is_valid_local_image_path($v)) {
        $errors[] = 'La imagen local no existe o no es un archivo de imagen permitido.';
        return null;
    }
    // Normaliza a ruta relativa assets/...
    $norm = ltrim($v, '/');
    if (!str_starts_with($norm, 'assets/')) $norm = 'assets/' . $norm;
    return $norm;
}
