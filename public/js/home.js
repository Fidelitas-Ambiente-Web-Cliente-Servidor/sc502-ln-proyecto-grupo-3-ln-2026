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
    const wasHidden = tripsSection.hasClass("d-none");

    if (!loadedMyRides) {
      loadMyRides();
      loadedMyRides = true;
    }

    btn.text(wasHidden ? "Buscar Rides" : "Mis Rides");
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
        ratingHTML = `<span class="text-warning">Viaje en curso</span>`;
      } else if (trip.ya_calificado) {
        ratingHTML = `
          <div class="d-flex justify-content-center">
            ${[1, 2, 3, 4, 5]
              .map(
                (n) => `
                <i class="bi ${
                  n <= trip.mi_calificacion
                    ? "bi-star-fill text-warning"
                    : "bi-star text-muted"
                }"></i>
              `,
              )
              .join("")}
          </div>
        `;
      } else {
        ratingHTML = `
          <div class="rating-container d-flex flex-column align-items-center" data-viaje="${trip.id_viaje}">
            
            <div class="rating-stars">
              ${[1, 2, 3, 4, 5]
                .map(
                  (n) => `
                  <i class="bi bi-star rating-star" data-value="${n}"></i>
                `,
                )
                .join("")}
            </div>

            <div class="rating-form d-none mt-2 w-100">
              <textarea 
                class="form-control rating-comment" 
                placeholder="¿Cómo fue el viaje?"
                rows="2"></textarea>

              <button class="btn-ride primary mt-2 w-100 btn-send-rating">
                Enviar
              </button>
            </div>

          </div>
        `;
      }

      table.append(`
        <tr>
          <td>${trip.destino}</td>
          <td>${trip.fecha_viaje}</td>
          <td>${trip.hora_salida}</td>
          <td class="text-center">
            ${ratingHTML}
          </td>
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

          if (data.ok) {
            container.html(`
              <div class="d-flex justify-content-center">
                ${[1, 2, 3, 4, 5]
                  .map(
                    (n) => `
                    <i class="bi ${
                      n <= puntuacion
                        ? "bi-star-fill text-warning"
                        : "bi-star text-muted"
                    }"></i>
                  `,
                  )
                  .join("")}
              </div>
            `);
          } else {
            Toast.show(data.message, "error");
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
      table.html(`
      <tr>
        <td colspan="6" class="text-muted text-center">
          No tienes viajes como conductor
        </td>
      </tr>
    `);
      return;
    }

    driverSection.removeClass("d-none");

    trips.forEach((trip) => {
      let actionHTML = "";

      if (trip.id_estado_viaje == 2) {
        actionHTML = `
        <span class="badge bg-success">Finalizado</span>
      `;
      } else {
        actionHTML = `
        <button class="btn-ride primary btn-end" data-id="${trip.id_viaje}">
          Finalizar
        </button>
      `;
      }

      table.append(`
      <tr>
        <td>${trip.destino}</td>
        <td>${trip.fecha_viaje}</td>
        <td>${trip.hora_salida}</td>
        <td>₡${trip.precio}</td>
        <td>${trip.pasajeros}</td>
        <td class="d-flex justify-content-center align-items-center">
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
