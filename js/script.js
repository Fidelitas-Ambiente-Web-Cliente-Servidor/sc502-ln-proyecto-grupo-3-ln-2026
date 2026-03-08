const tripsContainer = document.querySelector(".trip-cards");

const card = (trip) => {
  return `<div class="card-body">
            <h5 class="card-title">${trip.destination}</h5>
            <p class="card-text"><strong>Conductor:</strong> ${trip.driver}</p>
            <p class="card-text">${new Date(trip.date).toLocaleDateString()} a las ${trip.time}</p>
            <p class="card-text"><strong>Espacios disponibles:</strong> ${trip.seatsAvailable}</p>
          </div>`;
};

const getAvailableTrips = () => {
  fetch("js/mock-data.json")
    .then((response) => response.json())
    .then((data) => {
      tripsContainer.innerHTML = "";
      const row = document.createElement("div");
      row.classList.add("row", "g-4");

      data.forEach((trip) => {
        const col = document.createElement("div");
        col.classList.add("col-md-4");

        const tripCard = document.createElement("div");
        tripCard.classList.add("card", "shadow", "h-100");

        tripCard.innerHTML = card(trip);

        col.appendChild(tripCard);
        row.appendChild(col);
      });

      tripsContainer.appendChild(row);
    })
    .catch((error) => console.error("Error loading trips:", error));
};

document.addEventListener("DOMContentLoaded", () => {
  const form = document.getElementById("viajeForm");
  const mensajeContenedor = document.getElementById("formMessages");

  form.addEventListener("submit", (e) => {
    e.preventDefault(); //Evita recargar la página

    //Capturamos y limpiamos los valores
    const origen = document.getElementById("origen").value.trim();
    const destino = document.getElementById("destino").value.trim();
    const fecha = document.getElementById("fecha").value;
    const hora = document.getElementById("hora").value;
    const espacios = parseInt(document.getElementById("espacios").value);
    const precio = parseFloat(document.getElementById("precio").value);
    const comentarios = document.getElementById("comentarios").value.trim();

    let errores = [];

    //-----------Validaciones------------

    //Valida campos vacíos
    if (
      !origen ||
      !destino ||
      !fecha ||
      !hora ||
      isNaN(espacios) ||
      isNaN(precio)
    ) {
      errores.push("Por favor, completa todos los campos obligatorios.");
    }

    //Valida fecha y hora (no puede ser en el pasado)
    const ahora = new Date();
    const fechaHoraViaje = new Date(`${fecha}T${hora}`); //La T es de

    if (fechaHoraViaje <= ahora) {
      errores.push("La fecha y hora de salida no pueden ser en el pasado.");
    }

    //Valida números lógicos (asumiendo que el carro es de 5 pasajeros)
    if (espacios <= 0 || espacios > 4) {
      errores.push("La cantidad de espacios debe ser entre 1 y 4.");
    }
    if (precio < 0) {
      errores.push("El precio no puede ser un valor negativo.");
    }

    mensajeContenedor.innerHTML = ""; //Limpiar alertas previas

    //Muestra errores o mensaje de éxito
    if (errores.length > 0) {
      mostrarAlerta(errores.join("<br>"), "danger");
    } else {
      const confirmacion = `
                <h4>¡Viaje publicado con éxito!</h4>
                <p><strong>Ruta:</strong> ${origen} ➔ ${destino}</p>
                <p><strong>Fecha y Hora:</strong> ${fecha} a las ${hora}</p>
                <p><strong>Espacios:</strong> ${espacios}</p>
                <p><strong>Precio:</strong> ₡${precio}</p>
                ${comentarios ? `<p><strong>Notas:</strong> ${comentarios}</p>` : ""}
            `;
      mostrarAlerta(confirmacion, "success");
      form.reset(); //Limpia el formulario para un nuevo registro
    }
  });

  //Función auxiliar para renderizar alertas de Bootstrap
  function mostrarAlerta(mensaje, tipo) {
    const div = document.createElement("div");
    div.className = `alert alert-${tipo} alert-dismissible fade show mt-3`;
    div.role = "alert";
    div.innerHTML = `
            ${mensaje}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        `;
    mensajeContenedor.appendChild(div);
  }

  getAvailableTrips();

  const zone = document.getElementById("zone");

  zone.addEventListener("input", (e) => {
    e.preventDefault();

    const zoneValue = e.target.value.toLowerCase();

    fetch("js/mock-data.json")
      .then((response) => response.json())
      .then((data) => {
        const filteredTrips = data.filter((trip) => {
          const matchesZone = zoneValue
            ? trip.destination.toLowerCase().includes(zoneValue)
            : true;

          return matchesZone;
        });

        tripsContainer.innerHTML = "";
        const row = document.createElement("div");
        row.classList.add("row", "g-4");

        filteredTrips.forEach((trip) => {
          const col = document.createElement("div");
          col.classList.add("col-md-4");

          const tripCard = document.createElement("div");
          tripCard.classList.add("card", "shadow", "h-100");

          tripCard.innerHTML = card(trip);

          col.appendChild(tripCard);
          row.appendChild(col);
        });

        tripsContainer.appendChild(row);
      })
      .catch((error) => console.error("Error filtering trips:", error));
  });
});
