$(document).ready(function () {
  const tripsContainer = $(".trip-cards");
  const urlBase = "index.php";
  const tabsContainer = $("#tabs-container");
  const tripsSection = $("#trips");
  const btn = $("#btnMisRides");

  let loadedMyRides = false;

btn.on("click", function () {
    tripsSection.toggleClass("d-none");
    tabsContainer.toggleClass("d-none");
    const isShowingMyRides = tripsSection.hasClass("d-none");

    if (isShowingMyRides) {
        //Si entramos a "Mis Rides", cargamos los datos del usuario
        if (!loadedMyRides) {
            loadMyRides();
            loadedMyRides = true;
        }
        btn.text("Buscar Rides");
    } else {
        //Si volvemos a "Buscar", refrescamos la lista pública de viajes
        loadTrips($("#zone").val()); 
        btn.text("Mis Rides");
    }
});

  const loadMyRides = () => {
    $.ajax({
      url: urlBase + "?option=misRides",
      method: "GET",
      dataType: "json",
      success: (res) => {
        renderPassenger(res.passenger);
        renderDriver(res.driver);
      },
      error: (err) => {
        console.error("Error cargando mis rides:", err);
        Toast.show("Error al cargar tus rides", "error");
      },
    });
  };

const renderPassenger = (trips) => {
    const table = $("#passenger-trips-table");
    table.html("");

    trips.forEach((trip) => {
      let ratingHTML = "";

      if (trip.id_estado_viaje != 2) {
        ratingHTML = `<span class="text-warning fw-bold">Viaje en curso</span>`;
      } else if (trip.mi_rating) { 
        // AQUI ESTÁ EL FIX: Usamos el nombre correcto 'mi_rating' que viene de SQL
        ratingHTML = `
          <div class="d-flex justify-content-center">
            ${[1, 2, 3, 4, 5].map(n => `
                <i class="bi ${n <= trip.mi_rating ? "bi-star-fill text-warning" : "bi-star text-muted"}"></i>
              `).join("")}
          </div>
        `;
      } else {
        ratingHTML = `
          <div class="rating-container d-flex flex-column align-items-center" data-viaje="${trip.id_viaje}">
            <div class="rating-stars">
              ${[1, 2, 3, 4, 5].map(n => `<i class="bi bi-star rating-star" data-value="${n}"></i>`).join("")}
            </div>
            <div class="rating-form d-none mt-2 w-100">
              <textarea class="form-control rating-comment" placeholder="¿Cómo fue el viaje?" rows="2"></textarea>
              <button class="btn-ride primary mt-2 w-100 btn-send-rating">Enviar</button>
            </div>
          </div>
        `;
      }

      // Agregamos 'align-middle' a los td para que no se descuadre la tabla
      table.append(`
        <tr>
          <td class="align-middle">${trip.destino}</td>
          <td class="align-middle">${trip.fecha_viaje}</td>
          <td class="align-middle">${trip.hora_salida}</td>
          <td class="align-middle text-center">${ratingHTML}</td>
        </tr>
      `);
    });
  };

  // ==========================================
  // INTERACCIÓN DE ESTRELLAS
  // ==========================================
  $(document).on("mouseenter", ".rating-star", function () {
    const val = $(this).data("value");
    const stars = $(this).parent().find(".rating-star");

    stars.each(function () {
      $(this).toggleClass("hover", $(this).data("value") <= val);
    });
  });

  $(document).on("mouseleave", ".rating-stars", function () {
    $(this).find(".rating-star").removeClass("hover");
  });

  $(document).on("click", ".rating-star", function () {
    const val = $(this).data("value");
    const container = $(this).closest(".rating-container");

    container.data("rating", val);

    const stars = container.find(".rating-star");
    stars.each(function () {
      $(this).toggleClass("active", $(this).data("value") <= val);
    });

    container.find(".rating-form").removeClass("d-none");
  });

  // ==========================================
  // ENVIAR CALIFICACIÓN
  // ==========================================
  $(document).on("click", ".btn-send-rating", function () {
    const container = $(this).closest(".rating-container");

    const idViaje = container.data("viaje");
    const puntuacion = container.data("rating");
    const comentario = container.find(".rating-comment").val();

    if (!puntuacion) {
      Toast.show("Por favor, seleccione una puntuación.", "error");
      return;
    }

    $.post(
      urlBase,
      {
        option: "calificar",
        id_viaje: idViaje,
        puntuacion,
        comentario,
      },
      function (res) {
        try {
          const data = JSON.parse(res);

        if (data.ok || data.response === "00") {
            Toast.show("Calificación enviada", "success");
            container.html(`
              <div class="d-flex justify-content-center">
                ${[1, 2, 3, 4, 5].map(n => `
                    <i class="bi ${n <= puntuacion ? "bi-star-fill text-warning" : "bi-star text-muted"}"></i>
                  `).join("")}
              </div>
            `);
        } else {
            // Si data.message no existe, mostramos un error genérico
            Toast.show(data.message || "Error al procesar la calificación", "error");
        }
        } catch {
          Toast.show("Error inesperado", "error");
        }
      },
    );
  });

    const renderDriver = (trips) => {
    const table = $("#driver-trips-table");
    const driverSection = $("#driver-trips-section");

    table.html("");

    if (!trips || trips.length === 0) {
      driverSection.removeClass("d-none");
      table.html(`<tr><td colspan="6" class="text-muted text-center">No tienes viajes publicados</td></tr>`);
      return;
    }

    driverSection.removeClass("d-none");

    trips.forEach((trip) => {
      let actionHTML = "";

      if (trip.id_estado_viaje == 2) {
        //Si el viaje está finalizado, verificamos si ya nos calificaron
          if (trip.rating_recibido) {
            const estrellas = Math.round(trip.rating_recibido);
            actionHTML = `
              <div class="d-flex flex-column align-items-center">
                <span class="badge bg-success mb-1">Finalizado</span>
                <div class="d-flex text-warning mb-1" style="font-size: 0.8rem;">
                  ${[1, 2, 3, 4, 5].map(n => `<i class="bi ${n <= estrellas ? "bi-star-fill" : "bi-star text-muted"}"></i>`).join("")}
                </div>
                <button class="btn btn-outline-dark btn-sm rounded-pill btn-ver-comentarios" data-id="${trip.id_viaje}" style="font-size: 0.7rem; padding: 2px 10px;">
                  Leer comentarios
                </button>
              </div>`;
        } else {
            actionHTML = `<span class="badge bg-success">Finalizado</span><br><small class="text-muted" style="font-size:0.7rem;">Sin calificar</small>`;
        }
      } else {
        actionHTML = `<button class="btn-ride primary btn-end btn-sm" data-id="${trip.id_viaje}">Finalizar</button>`;
      }

      table.append(`
      <tr>
        <td class="align-middle">${trip.destino}</td>
        <td class="align-middle">${trip.fecha_viaje}</td>
        <td class="align-middle">${trip.hora_salida}</td>
        <td class="align-middle">₡${trip.precio}</td>
        <td class="align-middle text-center">${trip.pasajeros}</td>
        <td class="align-middle text-center">
          ${actionHTML}
        </td>
      </tr>
    `);
    });
  };

  $(document).on("click", ".btn-end", function () {
    const id = $(this).data("id");
    const row = $(this).closest("tr");

    $.post(
      urlBase,
      {
        option: "finalizar",
        id_viaje: id,
      },
      function (res) {
        try {
          const data = JSON.parse(res);

          if (data.response === "00") {
            row.addClass("table-success");

            setTimeout(() => {
              row.fadeOut(300, function () {
                $(this).remove();

                if ($("#driver-trips-table tr").length === 0) {
                  loadMyRides();
                }
              });
            }, 600);
          } else {
            alert(data.message || "No se pudo finalizar el viaje");
          }
        } catch {
          alert("Error inesperado");
        }
      },
    );
  });

  // ==========================================
  // VER COMENTARIOS DEL VIAJE (CONDUCTOR)
  // ==========================================
  $(document).on("click", ".btn-ver-comentarios", function () {
    const idViaje = $(this).data("id");
    const modalBody = $("#commentsModalBody");
    
    //Mostramos el modal de Bootstrap
    const modal = new bootstrap.Modal(document.getElementById('commentsModal'));
    modal.show();
    
    modalBody.html('<div class="text-center text-muted my-4"><div class="spinner-border spinner-border-sm me-2"></div>Cargando...</div>');

    //Pedimos los comentarios a PHP
    $.get(urlBase + "?option=verComentarios&id_viaje=" + idViaje, function(data) {
        try {
            const comentarios = JSON.parse(data);
            
            if (comentarios.length === 0) {
                modalBody.html('<p class="text-muted text-center my-4">Los pasajeros dejaron estrellas, pero no escribieron texto.</p>');
                return;
            }
            
            let html = '<ul class="list-group list-group-flush">';
            let comentariosValidos = 0;

            comentarios.forEach(c => {
                //Solo mostramos si realmente escribieron algo
                if(c.comentario && c.comentario.trim() !== '') {
                    comentariosValidos++;
                    html += `
                    <li class="list-group-item px-0 py-3">
                        <div class="d-flex justify-content-between align-items-center mb-2">
                            <strong class="text-dark"><i class="bi bi-person-circle me-2 text-muted"></i>${c.pasajero}</strong>
                            <span class="text-warning fw-bold"><i class="bi bi-star-fill"></i> ${c.puntuacion}</span>
                        </div>
                        <p class="mb-0 text-muted" style="font-size: 0.95rem;">"${c.comentario}"</p>
                    </li>`;
                }
            });
            html += '</ul>';
            
            if(comentariosValidos === 0) {
                 modalBody.html('<p class="text-muted text-center my-4">Los pasajeros dejaron estrellas, pero no escribieron texto.</p>');
            } else {
                 modalBody.html(html);
            }
            
        } catch(e) {
            modalBody.html('<div class="alert alert-danger my-3">Error al cargar los comentarios del servidor.</div>');
        }
    });
  });


  // ==========================================
  // CARDS
  // ==========================================
  const createCardHTML = (trip) => {
    const fullStars = Math.floor(trip.driverRating);
    const hasHalfStar = trip.driverRating % 1 !== 0;
    const emptyStars = 5 - fullStars - (hasHalfStar ? 1 : 0);

    const starsHTML = `
      ${'<i class="bi bi-star-fill text-warning me-1"></i>'.repeat(fullStars)}
      ${hasHalfStar ? '<i class="bi bi-star-half text-warning me-1"></i>' : ""}
      ${'<i class="bi bi-star text-muted me-1"></i>'.repeat(emptyStars)}
    `;

    return `<div class="card shadow h-100">
              <div class="card-body">
                <h5 class="card-title">${trip.destination}</h5>
                <h6 class="text-muted"><i class="bi bi-geo-alt"></i> Desde: ${trip.origen}</h6>
                <p class="card-text mt-3"><strong>Conductor:</strong> ${trip.driver}</p>
                <p class="card-text">${trip.date} a las ${trip.time}</p>
                <p class="card-text"><strong><i class="bi bi-people"></i></strong> ${trip.seatsAvailable}</p>
                <p class="card-text fw-bold"><i class="bi bi-cash-stack"></i> ₡${trip.precio}</p>
                <p class="card-text"><i class="bi bi-car-front text-muted me-1"></i> ${starsHTML}</p>
                <button class="btn-ride primary w-100 mt-2 btn-reservar" data-id="${trip.id_viaje}">Reservar</button>
              </div>
            </div>`;
  };

  const renderTrips = (trips) => {
    tripsContainer.html("");

    if (trips.length === 0) {
      tripsContainer.html(
        "<p class='text-muted'>No se encontraron viajes.</p>",
      );
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

  const loadTrips = (filterValue = "") => {
    $.get(urlBase + "?option=buscarViajes", function (data) {
      try {
        const allTrips = JSON.parse(data);

        const filtered = allTrips.filter((trip) =>
          trip.destination.toLowerCase().includes(filterValue.toLowerCase()),
        );

        renderTrips(filtered);
      } catch (error) {
        console.error("Error JSON:", error);
      }
    });
  };

  const urlParams = new URLSearchParams(window.location.search);
  const initialZone = urlParams.get("zone") || "";

  if (initialZone) {  
    $("#zone").val(initialZone);
  }

  //Revisa si venimos desde el enlace directo de "Mis Viajes"
  const initialView = urlParams.get("view");
  if (initialView === "mis_rides") {
    $("#btnMisRides").trigger("click"); //Simulamos el clic automáticamente
  }

  loadTrips(initialZone);

  $("#zone").on("input", function () {
    loadTrips($(this).val());
  });

  $("#filterForm").on("submit", function (e) {
    e.preventDefault();
  });

  tripsContainer.on("click", ".btn-reservar", function () {
    const btn = $(this);
    const idViaje = btn.data("id");

    btn.prop("disabled", true).text("Reservando...");

    $.post(
      urlBase,
      {
        option: "reservarViaje",
        id_viaje: idViaje,
      },
      function (data) {
        try {
          const res = JSON.parse(data);

          if (res.response === "00") {
            Toast.show("Reserva confirmada con éxito", "success");

            btn
              .text("Reservado")
              .removeClass("primary")
              .addClass("btn-success");

            loadTrips($("#zone").val());
          } else {
            Toast.show(res.message || "Error al reservar", "error");

            btn.prop("disabled", false).text("Reservar");
          }
        } catch (e) {
          Toast.show("Error inesperado", "error");

          btn.prop("disabled", false).text("Reservar");
        }
      },
    );
  });
});
