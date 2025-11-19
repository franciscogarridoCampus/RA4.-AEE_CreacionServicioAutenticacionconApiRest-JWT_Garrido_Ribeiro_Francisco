const form = document.getElementById("loginForm");

form.addEventListener("submit", function(e){
    e.preventDefault();

    const usuario = document.getElementById("usuario").value;
    const password = document.getElementById("contraseña").value;

    fetch("../back/login.php", {
        method: "POST",
        headers: { "Content-Type": "application/json" },
        body: JSON.stringify({ usuario, password })
    })
    .then(response => response.json().then(data => ({status: response.status, body: data})))
    .then(res => {
        if(res.status === 200){
            // Guardamos token y usuario en localStorage
            localStorage.setItem("token", res.body.token);
            localStorage.setItem("usuario", res.body.usuario);

            // Redirigimos a bienvenida.html
            window.location.href = "bienvenida.html";
        } else if(res.status === 401){
            alert("Usuario o contraseña incorrectos");
        } else {
            alert(res.body.error || "Error desconocido");
        }
    })
    .catch(err => console.error("Error en login:", err));
});
