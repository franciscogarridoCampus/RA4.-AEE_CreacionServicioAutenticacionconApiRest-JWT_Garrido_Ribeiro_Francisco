<?php
header('Content-Type: application/json');

// Clave secreta para firmar el JWT
$SECRET_KEY = "MI_CLAVE_SECRETA_123";

// Lista de usuarios
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
$input = file_get_contents('php://input');
$data = json_decode($input, true);

if (!isset($data['usuario']) || !isset($data['password'])) {
    http_response_code(400); 
    echo json_encode(["error" => "Faltan datos"]);
    exit();
}

$usuario = $data['usuario'];
$password = $data['password'];

// Validar usuario
$usuarioValido = false;
foreach ($usuarios as $u) {
    if ($u['username'] === $usuario && $u['password'] === $password) {
        $usuarioValido = true;
        break;
    }
}

if ($usuarioValido) {
    // Crear payload del JWT
    $payload = [
        "sub" => $usuario,
        "exp" => time() + 3600  // Expira en 1 hora
    ];

    // Header del JWT
    $header = ["alg" => "HS256", "typ" => "JWT"];
    $header_encoded = rtrim(strtr(base64_encode(json_encode($header)), '+/', '-_'), '=');
    $payload_encoded = rtrim(strtr(base64_encode(json_encode($payload)), '+/', '-_'), '=');

    // Crear firma
    $signature = hash_hmac('sha256', "$header_encoded.$payload_encoded", $SECRET_KEY, true);
    $signature_encoded = rtrim(strtr(base64_encode($signature), '+/', '-_'), '=');

    // Token completo
    $token = "$header_encoded.$payload_encoded.$signature_encoded";

    echo json_encode([
        "token" => $token,
        "usuario" => $usuario
    ]);

} else {
    http_response_code(401);
    echo json_encode(["error" => "Usuario o contraseña incorrectos"]);
}
?>
