<?php
header('Content-Type: application/json');// formato json

// Clave secreta para firmar el JWT
$SECRET_KEY = "MI_CLAVE_SECRETA_123";

// Lista de usuarios y sus contraseñas
$usuarios = [
    ["username" => "admin", "password" => "1234"],
    ["username" => "user", "password" => "abcd"]
];

// Solo aceptar POST
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(["error" => "Método no permitido"]);
    exit();
}

// Leer input JSON
$input = json_decode(file_get_contents("php://input"), true);

$usuario = $input['usuario'] ?? null;
$password = $input['password'] ?? null;

if (!$usuario || !$password) {
    http_response_code(400);
    echo json_encode(["error" => "Faltan datos"]);
    exit();
}

// Validar usuario en array
$usuarioValido = false;
foreach ($usuarios as $u) {
    if ($u['username'] === $usuario && $u['password'] === $password) {
        $usuarioValido = true;
        break;
    }
}

if (!$usuarioValido) {
    http_response_code(401);
    echo json_encode(["error" => "Usuario o contraseña incorrectos"]);
    exit();
}

// Crear JWT
$header = json_encode(["alg" => "HS256", "typ" => "JWT"]);
$headerB64 = rtrim(strtr(base64_encode($header), '+/', '-_'), '=');

$payload = json_encode([
    "sub" => $usuario,
    "exp" => time() + 3600  // Expira en 1 hora
]);
$payloadB64 = rtrim(strtr(base64_encode($payload), '+/', '-_'), '=');

$signature = hash_hmac('sha256', "$headerB64.$payloadB64", $SECRET_KEY, true);
$signatureB64 = rtrim(strtr(base64_encode($signature), '+/', '-_'), '=');

$jwt = "$headerB64.$payloadB64.$signatureB64";

// Responder JSON
echo json_encode([
    "token" => $jwt,
    "usuario" => $usuario
]);
?>
