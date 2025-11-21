<?php
header('Content-Type: application/json');

$SECRET_KEY = "MI_CLAVE_SECRETA_123";

// Lista de usuarios
$usuarios = [
    ["username" => "admin", "password" => "1234"],
    ["username" => "user", "password" => "abcd"]
];

// Solo aceptar GET
if ($_SERVER['REQUEST_METHOD'] !== 'GET') {
    http_response_code(405);
    echo json_encode(["error" => "Método no permitido"]);
    exit();
}

// Leer token del header Authorization
$headers = getallheaders();
if (!isset($headers['Authorization'])) {
    http_response_code(403);
    echo json_encode(["error" => "Falta token"]);
    exit();
}

$auth = $headers['Authorization'];
if (!str_starts_with($auth, "Bearer ")) {
    http_response_code(403);
    echo json_encode(["error" => "Formato de token inválido"]);
    exit();
}

$jwt = substr($auth, 7);

// Separar token
$partes = explode(".", $jwt);
if (count($partes) !== 3) {
    http_response_code(403);
    echo json_encode(["error" => "Token malformado"]);
    exit();
}

list($headerB64, $payloadB64, $signatureB64) = $partes;

// Decodificar payload
$payloadJson = base64_decode(strtr($payloadB64, '-_', '+/'));
$payload = json_decode($payloadJson, true);

// Verificar expiración
if ($payload['exp'] < time()) {
    http_response_code(403);
    echo json_encode(["error" => "Token caducado"]);
    exit();
}

// Validar firma
$signatureCheck = rtrim(strtr(base64_encode(
    hash_hmac('sha256', "$headerB64.$payloadB64", $SECRET_KEY, true)
), '+/', '-_'), '=');

if (!hash_equals($signatureCheck, $signatureB64)) {
    http_response_code(403);
    echo json_encode(["error" => "Firma inválida"]);
    exit();
}

// Verificar que el usuario exista
$usuarioValido = false;
foreach ($usuarios as $u) {
    if ($u['username'] === $payload['sub']) {
        $usuarioValido = true;
        break;
    }
}

if (!$usuarioValido) {
    http_response_code(403);
    echo json_encode(["error" => "Usuario no válido"]);
    exit();
}

// Todo correcto → enviar datos
echo json_encode([
    "usuario" => $payload['sub'],
    "hora" => date("d-m-Y H:i:s"),
    "mensaje" => "Bienvenido correctamente"
]);
?>
