$(document).ready(function () {
    const form = $("#viajeForm");
    const urlBase = "index.php"; //Apuntamos al enrutador principal

    form.on("submit", function (e) {
        e.preventDefault(); //Evita recargar la página

        //Capturamos los valores del formulario y los limpiamos un poco
        const origen = $("#origen").val().trim();
        const destino = $("#destino").val().trim();
        const fecha = $("#fecha").val();
        const hora = $("#hora").val();
        const espacios = parseInt($("#espacios").val());
        const precio = parseFloat($("#precio").val());
        const comentarios = $("#comentarios").val().trim();

        let errores = [];

        //----------- Validaciones ------------
        if (!origen || !destino || !fecha || !hora || isNaN(espacios) || isNaN(precio)) {
            errores.push("Por favor, completa todos los campos obligatorios.");
        }

        const ahora = new Date();
        const fechaHoraViaje = new Date(`${fecha}T${hora}`);

        if (fechaHoraViaje <= ahora) {
            errores.push("La fecha y hora de salida no pueden ser en el pasado.");
        }

        if (espacios <= 0 || espacios > 4) {
            errores.push("La cantidad de espacios debe ser entre 1 y 4.");
        }
        if (precio < 0) {
            errores.push("El precio no puede ser un valor negativo.");
        }

        $("#formMessages").html(""); //Limpiar alertas previas

        //Muestra errores o envía los datos al servidor
        if (errores.length > 0) {
            mostrarAlerta(errores.join("<br>"), "danger");
        } else {
            //Enviamos los datos a PHP 
            $.post(urlBase, {
                origen: origen,
                destino: destino,
                fecha: fecha,
                hora: hora,
                espacios: espacios,
                precio: precio,
                comentarios: comentarios,
                option: "publicarViaje" //Esta opción le dirá al index.php qué hacer
            }, function (data, status) {
                //Esto se ejecuta cuando PHP nos responde
                const respuesta = JSON.parse(data);
                
                if (respuesta.response === "00") {
                    mostrarAlerta("¡Viaje publicado con éxito en la base de datos!", "success");
                    form[0].reset(); //Limpia el formulario
                } else {
                    mostrarAlerta("Hubo un error al guardar: " + respuesta.message, "danger");
                }
            });
        }
    });

    //Función auxiliar para renderizar alertas de Bootstrap con jQuery
    function mostrarAlerta(mensaje, tipo) {
        const alerta = `
            <div class="alert alert-${tipo} alert-dismissible fade show mt-3" role="alert">
                ${mensaje}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        `;
        $("#formMessages").append(alerta);
    }
});