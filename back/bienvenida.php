<?php
header('Content-Type: application/json');// formato json

// Clave secreta para asegurar el JWT
$SECRET_KEY = "MI_CLAVE_SECRETA_123";

// Lista de usuarios y sus contraseñas
$usuarios = [
    ["username" => "admin", "password" => "1234"],
    ["username" => "user", "password" => "abcd"]
];

// Solo acepta coger datos usando GET y si no se usa GET salta el error 405
if ($_SERVER['REQUEST_METHOD'] !== 'GET') {
    http_response_code(405);
    echo json_encode(["error" => "Método no permitido"]);
    exit();
}

// Leer token del login, si no existe da error 403
$headers = getallheaders();
if (!isset($headers['Authorization'])) {
    http_response_code(403);
    echo json_encode(["error" => "Falta token"]);
    exit();
}
//Auth almacenara la info del token en formato JWT
$auth = $headers['Authorization'];
if (!str_starts_with($auth, "Bearer ")) {
    http_response_code(403);
    echo json_encode(["error" => "Formato de token inválido"]);
    exit();
}
//Elimina el Bearer para coger solo el JSON Web Token
$jwt = substr($auth, 7);

// Separar token en header, payload y singature(la firma)
$partes = explode(".", $jwt);
if (count($partes) !== 3) {
    http_response_code(403);
    echo json_encode(["error" => "Token malformado"]);
    exit();
}
//Asigna cada parte del token a una variable cada una
list($headerB64, $payloadB64, $signatureB64) = $partes;

// Decodificar payload para convertirlo en un json y luego  para hacer un array asociativo de PHP
$payloadJson = base64_decode(strtr($payloadB64, '-_', '+/'));
$payload = json_decode($payloadJson, true);

// Verificar expiración para ver si el token es valido
if ($payload['exp'] < time()) {
    http_response_code(403);
    echo json_encode(["error" => "Token caducado"]);
    exit();
}

// Generar de nuevo la firma usando $SECRET_KEY para luego comparar la firma con la de signatureB64(la firma original del token que se recibio)
$signatureCheck = rtrim(strtr(base64_encode(
    hash_hmac('sha256', "$headerB64.$payloadB64", $SECRET_KEY, true)
), '+/', '-_'), '=');

if (!hash_equals($signatureCheck, $signatureB64)) {
    http_response_code(403);
    echo json_encode(["error" => "Firma inválida"]);
    exit();
}

// Verificar que el usuario exista procedente del payload, para evitar aceptar un token de un usuario inventado
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

// Si todo esta bien muestra el nombre del usuario logeado, un mensaje de bienvenida y la hora y fecha cuando accedio a bienvenida.html
echo json_encode([
    "usuario" => $payload['sub'],
    "hora" => date("d-m-Y H:i:s"),
    "mensaje" => "Bienvenido correctamente"
]);
?>
