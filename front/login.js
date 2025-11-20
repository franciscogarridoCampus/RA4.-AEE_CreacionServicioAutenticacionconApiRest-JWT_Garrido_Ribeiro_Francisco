// Función para realizar el login y obtener el JWT
async function login(usuario, password) {
    const response = await fetch("http://localhost/api/login", {
        method: "POST",
        headers: { "Content-Type": "application/json" },
        body: JSON.stringify({ usuario, password })
    });

    if (response.ok) {
        const data = await response.json();
        console.log("Token recibido:", data.token);

        // Guardar el token en localStorage para usarlo en futuras solicitudes
        localStorage.setItem("token", data.token);
        localStorage.setItem("usuario", data.usuario);

        return data.token;
    } else {
        console.error("Error de login:", await response.text());
        return null;
    }
}

// Función para acceder a un recurso protegido utilizando el JWT
async function accederRecursoProtegido(token) {
    const response = await fetch("http://localhost/api/protected", {
        method: "GET",
        headers: {
            "Authorization": `Bearer ${token}`  // Aquí se pasa el token en el encabezado
        }
    });

    if (response.ok) {
        const data = await response.json();
        console.log("Datos protegidos:", data);
    } else {
        console.error("Acceso denegado:", await response.text());
    }
}

// Ejemplo de uso: realizar login y luego acceder al recurso protegido
(async function () {
    const token = await login("admin", "1234");  // Aquí se usa el usuario y la contraseña
    if (token) {
        await accederRecursoProtegido(token);  // Solo si el login es exitoso
    }
})();
