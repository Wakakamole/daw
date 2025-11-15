# Detectar php: preferir 'php' si está en PATH, si no usar ruta XAMPP conocida
if (Get-Command php.exe -ErrorAction SilentlyContinue) {
	$php = 'php'
} elseif (Test-Path 'C:\xampp\php\php.exe') {
	$php = 'C:\xampp\php\php.exe'
} else {
	Write-Host "ERROR: no se ha encontrado 'php' en PATH ni en 'C:\xampp\php\php.exe'." -ForegroundColor Red
	Write-Host "Edita este script y pon la ruta correcta a php.exe en la variable `$php` o instala PHP en el PATH." -ForegroundColor Yellow
	exit 1
}

# Contraseñas en texto plano para hashear
$pw1 = 'pass1'
$pw2 = 'pass2'
$pw3 = 'pass3'

# Generar hashes llamando a PHP
$h1 = & $php -r "echo password_hash('$pw1', PASSWORD_DEFAULT);"
$h2 = & $php -r "echo password_hash('$pw2', PASSWORD_DEFAULT);"
$h3 = & $php -r "echo password_hash('$pw3', PASSWORD_DEFAULT);"

# Trim
$h1 = $h1.Trim()
$h2 = $h2.Trim()
$h3 = $h3.Trim()

# Construir el INSERT
$sql = @"
USE pibd;

INSERT INTO Usuarios (NomUsuario, Clave, Email, Sexo, FNacimiento, Ciudad, Pais, Foto, FRegistro, Estilo)
VALUES
('daniel', '$h1', 'daniel@example.com', 1, '1993-02-10', 'Alicante', 1, 'img/daniel.jpg', NOW() - INTERVAL 10 DAY, 1),
('sandra', '$h2', 'sandra@example.com', 2, '1991-06-18', 'Valencia', 1, 'img/sandra.jpg', NOW() - INTERVAL 7 DAY, 2),
('prueba', '$h3', 'prueba@example.com', 1, '2000-01-01', 'PruebaCity', 1, 'img/prueba.jpg', NOW() - INTERVAL 3 DAY, 1);
"@

# Mostrar el SQL en consola
$sql
