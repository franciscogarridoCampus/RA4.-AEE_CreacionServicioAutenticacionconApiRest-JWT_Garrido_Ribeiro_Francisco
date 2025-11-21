document.getElementById("loginForm").addEventListener("submit", async (e) => {
    e.preventDefault();

    const usuario = document.getElementById("usuario").value;
    const password = document.getElementById("contraseña").value;

    const response = await fetch("../back/login.php", {
        method: "POST",
        headers: { "Content-Type": "application/json" },
        body: JSON.stringify({ usuario, password })
    });

    if (response.status === 200) {
        const data = await response.json();

        localStorage.setItem("token", data.token);
        localStorage.setItem("usuario", data.usuario);

        window.location.href = "bienvenida.html";
    } 
    else if (response.status === 401) {
        window.location.href = "nopermisos.html";
    } 
    else {
        alert("Error inesperado en el servidor");
    }
});
