

<?php
// PÁGINA DE RESPUESTA DE REGISTRO
// procesa el formulario de registro básico
// de momento sólo prestamos atención a usuario no vacío, password no vacío y si no son iguales

function h($v){ return htmlspecialchars($v, ENT_QUOTES, 'UTF-8'); }

$usuario = isset($_POST['usuario']) ? trim($_POST['usuario']) : '';
$password = isset($_POST['password']) ? $_POST['password'] : '';
$repite = isset($_POST['repite']) ? $_POST['repite'] : '';

$errors = [];
if ($usuario === '') { $errors[] = 'usuario'; }
if (trim($password) === '') { $errors[] = 'password_empty'; }
if (trim($repite) === '') { $errors[] = 'repite_empty'; }
if ($password !== $repite) { $errors[] = 'password_mismatch'; }

if (!empty($errors)) {
    // redirigir al formulario de registro con errores en querystring
    $qsData = [
        'errors' => implode(',', $errors),
        'usuario' => $usuario,
    ];
    // Añadir otros campos no sensibles para repoblado
    $fields = ['email','sexo','fecha_nacimiento','ciudad','pais'];
    foreach($fields as $f){
        if (isset($_POST[$f])) {
            $qsData[$f] = $_POST[$f];
        }
    }
    $qs = http_build_query($qsData);
    $host = $_SERVER['HTTP_HOST'];
    $uri = rtrim(dirname($_SERVER['PHP_SELF']), '/\\');
    // Redirigir a la versión PHP del formulario para mostrar errores server-side
    header("Location: http://$host$uri/registro_usuario.php?$qs");
    exit;
}



$page_title = 'INMOLINK - Registro';
require_once __DIR__ . '/includes/cabecera.php';
?>

    <main>
        <article class="mensaje-contenedor">
            <header>
                <h1>¡Registro completado!</h1>
                <p class="lead">Hemos recibido la solicitud de registro. A continuación se muestran los datos no sensibles que nos has enviado.</p>
            </header>

            <section aria-labelledby="datos-registro">
                <h2 id="datos-registro">Datos del usuario</h2>
                <dl>
                    <dt>Usuario</dt>
                    <dd><strong><?php echo h($usuario); ?></strong></dd>

                    <dt>Correo electrónico</dt>
                    <dd><?php echo isset($_POST['email']) ? h($_POST['email']) : '—'; ?></dd>

                    <dt>Sexo</dt>
                    <dd><?php echo isset($_POST['sexo']) ? h($_POST['sexo']) : '—'; ?></dd>

                    <dt>Fecha de nacimiento</dt>
                    <dd><?php echo isset($_POST['fecha_nacimiento']) ? h($_POST['fecha_nacimiento']) : '—'; ?></dd>

                    <dt>Ciudad</dt>
                    <dd><?php echo isset($_POST['ciudad']) ? h($_POST['ciudad']) : '—'; ?></dd>

                    <dt>País</dt>
                    <dd><?php echo isset($_POST['pais']) ? h($_POST['pais']) : '—'; ?></dd>
                </dl>
            </section>

            <section>
                <p><em>Por razones de seguridad la contraseña y la foto de perfil no se muestran ni se transfieren en esta página.</em></p>
                <p>
                    <a href="index_user.php" class="boton-enlace">Ok</a>
                </p>
            </section>
        </article>
    </main>

<?php require_once __DIR__ . '/includes/pie.php'; ?>
