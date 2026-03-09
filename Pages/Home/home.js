const tripsContainer = document.querySelector(".trip-cards");

// 1. Centralizamos la creación del HTML de la tarjeta
const createCardHTML = (trip) => {
  return `<div class="card shadow h-100">
            <div class="card-body">
              <h5 class="card-title">${trip.destination}</h5>
              <p class="card-text"><strong>Conductor:</strong> ${trip.driver}</p>
              <p class="card-text">${new Date(trip.date).toLocaleDateString()} a las ${trip.time}</p>
              <p class="card-text"><strong>Espacios disponibles:</strong> ${trip.seatsAvailable}</p>
            </div>
          </div>`;
};

// 2. Función única para pintar los datos
const renderTrips = (trips) => {
  tripsContainer.innerHTML = "";
  const row = document.createElement("div");
  row.classList.add("row", "g-4");

  trips.forEach((trip) => {
    const col = document.createElement("div");
    col.classList.add("col-md-4");
    col.innerHTML = createCardHTML(trip);
    row.appendChild(col);
  });

  tripsContainer.appendChild(row);
};

// 3. Carga inicial y filtrado
const loadTrips = (filterValue = "") => {
  fetch("../../js/mock-data.json") 
    .then((response) => response.json())
    .then((data) => {
      const filtered = data.filter(trip => 
        trip.destination.toLowerCase().includes(filterValue.toLowerCase())
      );
      renderTrips(filtered);
    })
    .catch((error) => console.error("Error:", error));
};

document.addEventListener("DOMContentLoaded", () => {
  loadTrips(); // Carga inicial

  const zoneInput = document.getElementById("zone");
  zoneInput.addEventListener("input", (e) => {
    loadTrips(e.target.value); // Filtra mientras escribes
  });
});