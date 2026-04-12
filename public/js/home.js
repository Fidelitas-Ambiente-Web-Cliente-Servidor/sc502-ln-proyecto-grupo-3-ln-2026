$(document).ready(function () {
  const tripsContainer = $(".trip-cards");
  const urlBase = "index.php";

  //1.Centralizamos la creación del HTML de la tarjeta
  const createCardHTML = (trip) => {
    return `<div class="card shadow h-100">
              <div class="card-body">
                <h5 class="card-title">${trip.destination}</h5>
                <h6 class="text-muted"><i class="bi bi-geo-alt"></i> Desde: ${trip.origen}</h6>
                <p class="card-text mt-3"><strong>Conductor:</strong> ${trip.driver}</p>
                <p class="card-text">${trip.date} a las ${trip.time}</p>
                <p class="card-text"><strong>Espacios disponibles:</strong> ${trip.seatsAvailable}</p>
                <p class="card-text text-success fw-bold">Precio: ₡${trip.precio}</p>
                <button class="btn-ride primary w-100 mt-2 btn-reservar" data-id="${trip.id_viaje}">Reservar</button>
              </div>
            </div>`;
  };

  //2.Función para pintar los datos en pantalla
  const renderTrips = (trips) => {
    tripsContainer.html(""); //Limpiamos con jQuery
    
    if (trips.length === 0) {
        tripsContainer.html("<p class='text-muted'>No se encontraron viajes para este destino.</p>");
        return;
    }

    const row = $("<div class='row g-4'></div>");

    trips.forEach((trip) => {
      const col = $("<div class='col-md-4'></div>");
      col.html(createCardHTML(trip));
      row.append(col);
    });

    tripsContainer.append(row);
  };

  //3.Carga inicial y filtrado usando AJAX (jQuery)
  const loadTrips = (filterValue = "") => {
    $.get(urlBase + "?option=buscarViajes", function (data) {
      try {
        const allTrips = JSON.parse(data);
        
        //Filtramos en el frontend (ignorando mayúsculas y minúsculas)
        const filtered = allTrips.filter(trip => 
          trip.destination.toLowerCase().includes(filterValue.toLowerCase())
        );
        
        renderTrips(filtered);
      } catch (error) {
        console.error("Error procesando los datos JSON: ", error);
      }
    }).fail(function() {
      console.error("Error al cargar los viajes desde la base de datos.");
    });
  };

  //4.Inicialización: Leemos si viene una zona desde la URL (MainPage)
  const urlParams = new URLSearchParams(window.location.search);
  const initialZone = urlParams.get('zone') || "";
  
  if (initialZone) {
      $("#zone").val(initialZone); //Llenamos el input automáticamente
  }

  //Ejecutamos la primera carga
  loadTrips(initialZone); 

  //==========================================
  //EVENTOS DE LA INTERFAZ
  //==========================================

  //Búsqueda dinámica al escribir en el input
  $("#zone").on("input", function () {
    loadTrips($(this).val());
  });

  //Evitar que el "Enter" recargue la página en el buscador
  $("#filterForm").on("submit", function (e) {
    e.preventDefault();
  });

  //Evento para el botón de Reservar (Usamos delegación de eventos porque los botones se crean dinámicamente)
  tripsContainer.on("click", ".btn-reservar", function () {
    const btn = $(this);
    const idViaje = btn.data("id");

    //Cambiamos el texto temporalmente para que el usuario sepa que está cargando
    btn.prop("disabled", true).text("Reservando...");

    //Enviamos la petición AJAX para reservar
    $.post(urlBase, {
      option: "reservarViaje",
      id_viaje: idViaje
    }, function (data) {
      try {
        const res = JSON.parse(data);
        if (res.response === "00") {
          alert("¡Reserva confirmada con éxito!");
          //Cambiamos el estilo del botón a éxito
          btn.text("Reservado").removeClass("primary").addClass("btn-success");
          
          //Recargamos los viajes para actualizar los espacios disponibles
          loadTrips($("#zone").val()); 
        } else {
          alert(res.message);
          //Si falló , regresamos el botón a la normalidad
          btn.prop("disabled", false).text("Reservar");
        }
      } catch (error) {
        alert("Ocurrió un error inesperado.");
        btn.prop("disabled", false).text("Reservar");
      }
    });
  });

});