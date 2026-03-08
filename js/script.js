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
  getAvailableTrips();

  const zone = document.getElementById("zone");

  zone.addEventListener("input", (e) => {
    filtrarRide(e);
  });
});

const filtrarRide = (e) => {
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
};
