$(document).ready(function () {
    const loginLink = $("#login-box-link");
    const signupLink = $("#signup-box-link");
    const loginForm = $("#loginForm");
    const signupForm = $("#signupForm");
    const authMessages = $("#authMessages");

    const urlBase = "index.php";

    //LÓGICA DE LA INTERFAZ
    signupLink.on("click", function (e) {
        e.preventDefault();
        loginForm.hide();
        signupForm.css("display", "flex");
        loginLink.removeClass("active");
        signupLink.addClass("active");
        authMessages.html(""); //Limpiamos alertas
    });

    loginLink.on("click", function (e) {
        e.preventDefault();
        signupForm.hide();
        loginForm.css("display", "flex");
        signupLink.removeClass("active");
        loginLink.addClass("active");
        authMessages.html(""); //Limpiamos alertas
    });

    //LÓGICA DE INICIO DE SESIÓN (AJAX)
    loginForm.on("submit", function (e) {
        e.preventDefault();
        const correo = $("#login_correo").val().trim();
        const password = $("#login_password").val();

        if (correo === "" || password === "") {
            mostrarAlerta("Completa todos los campos", "warning");
            return;
        }

        $.post(urlBase, {
            correo: correo,
            password: password,
            option: "login"
        }, function (data) {
            const res = JSON.parse(data);
            if (res.response === "00") {
                //Login exitoso, redirigimos al MainPage o Home
                window.location = "index.php"; 
            } else {
                mostrarAlerta(res.message, "danger");
            }
        });
    });

    //LÓGICA DE REGISTRO (AJAX)
    signupForm.on("submit", function (e) {
        e.preventDefault();
        
        $.post(urlBase, {
            nombre: $("#reg_nombre").val().trim(),
            apellidos: $("#reg_apellidos").val().trim(),
            telefono: $("#reg_telefono").val().trim(),
            correo: $("#reg_correo").val().trim(),
            password: $("#reg_password").val(),
            option: "registrar"
        }, function (data) {
            const res = JSON.parse(data);
            if (res.response === "00") {
                signupForm[0].reset();
                loginLink.click(); //Cambiamos de pestaña primero (esto limpia la pantalla)
                mostrarAlerta("Cuenta creada con éxito. Ahora puedes iniciar sesión.", "success"); //Mostramos la alerta
            } else {
                mostrarAlerta(res.message, "danger");
            }
        });
    });

    //Función auxiliar para renderizar alertas
    function mostrarAlerta(mensaje, tipo) {
        authMessages.html(`
            <div class="alert alert-${tipo} alert-dismissible fade show" role="alert">
                ${mensaje}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        `);
    }
});