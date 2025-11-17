<?php
// include reutilizable para cargar listas maestras usadas en formularios de anuncios
// Proporciona las variables: $tiposAnuncios, $tiposViviendas, $paises

require_once __DIR__ . '/basedatos.php';

$tiposAnuncios = [];
$tiposViviendas = [];
$paises = [];

try {
    $db = get_db();
} catch (Exception $e) {
    $db = null;
}

if ($db) {
    // Tipos de anuncios
    try {
        $res = $db->query("SELECT IdTAnuncio, NomTAnuncio FROM tiposAnuncios ORDER BY NomTAnuncio");
        if ($res) { $tiposAnuncios = $res->fetch_all(MYSQLI_ASSOC); $res->free(); }
    } catch (Exception $e) { $tiposAnuncios = []; }

    // Tipos de viviendas
    try {
        $res = $db->query("SELECT IdTVivienda, NomTVivienda FROM tiposViviendas ORDER BY NomTVivienda");
        if ($res) { $tiposViviendas = $res->fetch_all(MYSQLI_ASSOC); $res->free(); }
    } catch (Exception $e) { $tiposViviendas = []; }

    // Paises
    try {
        $res = $db->query("SELECT IdPais, NomPais FROM paises ORDER BY NomPais");
        if ($res) { $paises = $res->fetch_all(MYSQLI_ASSOC); $res->free(); }
    } catch (Exception $e) { $paises = []; }
}

?>

<?php
// Función helper para renderizar un select a partir de un array asociativo
if (!function_exists('render_select_from_array')) {
    function render_select_from_array(array $items, string $id, string $name, string $valueKey, string $labelKey, $selected = null, bool $required = false, string $placeholder = '-- Selecciona --') {
        $req = $required ? ' required' : '';
        echo "<select id=" . htmlspecialchars($id, ENT_QUOTES, 'UTF-8') . " name=" . htmlspecialchars($name, ENT_QUOTES, 'UTF-8') . "{$req}>\n";
        echo "    <option value=\"\">" . htmlspecialchars($placeholder, ENT_QUOTES, 'UTF-8') . "</option>\n";
        foreach ($items as $it) {
            $val = isset($it[$valueKey]) ? $it[$valueKey] : '';
            $lab = isset($it[$labelKey]) ? $it[$labelKey] : '';
            $sel = ((string)$val === (string)$selected) ? ' selected' : '';
            printf("    <option value=\"%d\"%s>%s</option>\n", (int)$val, $sel, htmlspecialchars($lab, ENT_QUOTES, 'UTF-8'));
        }
        echo "</select>\n";
    }
}

