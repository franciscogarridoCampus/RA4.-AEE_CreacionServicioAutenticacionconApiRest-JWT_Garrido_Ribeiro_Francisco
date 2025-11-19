<?php
header('Content-Type: application/json');

$usuarios = [
    ["username" => "admin", "password" => "1234"],
    ["username" => "user", "password" => "abcd"]
];

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(["error" => "Método no permitido"]);
    exit();
}

$input = file_get_contents('php://input');
$data = json_decode($input, true);

if (!isset($data['usuario']) || !isset($data['password'])) {
    http_response_code(400); 
    echo json_encode(["error" => "Faltan datos"]);
    exit();
}

$usuario = $data['usuario'];
$password = $data['password'];

$usuarioValido = false;
foreach ($usuarios as $u) {
    if ($u['username'] === $usuario && $u['password'] === $password) {
        $usuarioValido = true;
        break;
    }
}

if ($usuarioValido) {
    $token = base64_encode($usuario . ":" . $password . ":" . time());
    echo json_encode([
        "token" => $token,
        "usuario" => $usuario
    ]);
} else {
    http_response_code(401);
    echo json_encode(["error" => "Usuario o contraseña incorrectos"]);
}
?>
