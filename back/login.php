<?php
header('Content-Type: application/json');// formato json

// Clave secreta para asegurar el JWT
$SECRET_KEY = "MI_CLAVE_SECRETA_123";

// Lista de usuarios y sus contraseñas
$usuarios = [
    ["username" => "admin", "password" => "1234"],
    ["username" => "user", "password" => "abcd"]
];

// Solo aceptar datos enviados usando POST y  si no se usa POST salta el error 405
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(["error" => "Método no permitido"]);
    exit();
}

// Leer los datos insertados en el formulario y luego lo asigna en una variable el usuario y en otro el paswword, y si no existe nada se le asigna null
$input = json_decode(file_get_contents("php://input"), true);

$usuario = $input['usuario'] ?? null;
$password = $input['password'] ?? null;

//Comprueba si se ha insertado algun dato en usuario y password si falta alguno indicara que falta datos
if (!$usuario || !$password) {
    http_response_code(400);
    echo json_encode(["error" => "Faltan datos"]);
    exit();
}

// Validar usuario si pertence al array, y tambien su contraseña de ese usuario
$usuarioValido = false;
foreach ($usuarios as $u) {
    if ($u['username'] === $usuario && $u['password'] === $password) {
        $usuarioValido = true;
        break;
    }
}
//Si no esta el usuario en el array  dara un error 
if (!$usuarioValido) {
    http_response_code(401);
    echo json_encode(["error" => "Usuario o contraseña incorrectos"]);
    exit();
}

// Crear JWT(JSON Web Token.)
$header = json_encode(["alg" => "HS256", "typ" => "JWT"]);//Creamos la cabecera del token,indicamos que sea una cadena JSON ; alg= indica que vamos a usar  para firmar, typ= indica que es un Json Web Token
$headerB64 = rtrim(strtr(base64_encode($header), '+/', '-_'), '=');

//Creamos el payload del token donde almacenamos la informacion
$payload = json_encode([
    "sub" => $usuario,//Aqui guardamos el nombre de usuario
    "exp" => time() + 3600  // Expira en 1 hora el token
]);
$payloadB64 = rtrim(strtr(base64_encode($payload), '+/', '-_'), '=');//transforma el payload en un Base64 URL-safe

//Los signature nos sirve para firmar el token usando la clave secreta, para asegurar que no se puede modificar y lo trnasforma en Base64 URL-safe
$signature = hash_hmac('sha256', "$headerB64.$payloadB64", $SECRET_KEY, true);
$signatureB64 = rtrim(strtr(base64_encode($signature), '+/', '-_'), '=');

//une el header,payload, y signature en un JSON Web Token completo
$jwt = "$headerB64.$payloadB64.$signatureB64";

// Deuelve la informacion sobre el token y el nombre de usuario al cliente
echo json_encode([
    "token" => $jwt,
    "usuario" => $usuario
]);
?>
