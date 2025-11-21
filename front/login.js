//Al pulsar el boton de entrar del login.html realiozara la siguiente funcion
document.getElementById("loginForm").addEventListener("submit", async (e) => {
    //Evita que el formulario recargue la página
    e.preventDefault();

    //Se guarda los valores que se escribio en el formulario
    const usuario = document.getElementById("usuario").value;
    const password = document.getElementById("contraseña").value;

    // Envía los datos al servidor usando fetch hacia login.php
    const response = await fetch("../back/login.php", {
        method: "POST",
        headers: { "Content-Type": "application/json" },
        body: JSON.stringify({ usuario, password })
    });
    //Si responde 200 las credenciales son correctas
    if (response.status === 200) {
         // Convertimos la respuesta JSON (contiene token + usuario)
        const data = await response.json();
        // Guardamos el token y el usuario en el navegador (localStorage)
        localStorage.setItem("token", data.token);
        localStorage.setItem("usuario", data.usuario);
  // Redirigimos a la pantalla de bienvenida
        window.location.href = "bienvenida.html";
    } 
    // Si las credenciales son incorrectas → servidor responde 401, nos llevara a nopermisos.html
    else if (response.status === 401) {
        window.location.href = "nopermisos.html";
    } 
    // Cualquier otro error inesperado del servidor
    else {
        alert("Error inesperado en el servidor");
    }
});
