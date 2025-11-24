# RA4.-AEE_CreacionServicioAutenticacionconApiRest-JWT_Garrido_Ribeiro_Francisco

1. Login (login.html + login.js)

-Al abrir login.html desde el navegador se muestra un formulario hecho con Bootstrap donde el usuario debe escribir su nombre y contraseña.

-Cuando el usuario envía el formulario, login.js coge esos datos y los envía al servidor usando fetch() con el método POST hacia login.php.

2. Validación en el servidor (login.php)

-login.php recibe los datos enviados por JavaScript y comprueba si coinciden con alguno de los usuarios del array:

-Si el usuario o la contraseña no coinciden:

·el servidor devuelve 401 Unauthorized y el cliente redirige a nopermisos.html, ya que no existe token válido.

-Si las credenciales son correctas:

·login.php genera un token JWT, firmado con una clave secreta, e incluye un tiempo de expiración

-Envía ese token al navegador y login.js guarda el token en localStorage, para finalmente redirigirse a bienvenida.html.

3. Página sin permisos (nopermisos.html)

Muestra un mensaje indicando que el usuario no tiene permisos para acceder, y en una frase tiene un enlace que si haces click te devuelve a login.html



4. Página protegida (bienvenida.html)

-Al cargar la página, JavaScript comprueba si existe un token en localStorage.

-Si no hay token → redirige automáticamente a nopermisos.html.

-Si hay token, se hace una petición GET a bienvenida.php, enviando el token en la cabecera


-bienvenida.php valida el token:

·Comprueba que esté bien formado.
·Verifica la firma.
·Comprueba que no haya caducado según la fecha de expiración del token.
·Verifica que el usuario del token existe en el array.

-Si todo es correcto, devuelve un JSON indicando el nombre de usuario que se logeo, la fecha y hora actual, y un mensaje de bienvenida

-Si el token ha expirado o es incorrecto, devuelve un 403 Forbidden, y el usuario es enviado a nopermisos.html.

5. Cerrar sesión

-En bienvenida.html hay un botón Cerrar sesión y al pulsarlo:

Se elimina el token y el nombre de usuario de localStorage.

Se redirige a login.html.

Esto asegura que si alguien intenta escribir la URL de bienvenida.html sin token, no podrá acceder.
