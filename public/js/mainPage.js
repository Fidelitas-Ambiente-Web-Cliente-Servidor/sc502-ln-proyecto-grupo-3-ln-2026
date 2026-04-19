$(document).ready(function () {
    const btnSearch = $("#btnSearchSubmit");
    const inputZone = $("#zoneSearch");
    const filterForm = $("#filterForm");

    //Función central para redirigir
    const goToSearch = () => {
        const zone = inputZone.val().trim();
        window.location.href = `index.php?page=buscar_viaje&zone=${encodeURIComponent(zone)}`;
    };

    //1.Si hacen clic directamente en el botón de la lupa
    btnSearch.on("click", function (e) {
        e.preventDefault();
        goToSearch();
    });

    //2.Si presionan Enter (esto dispara el 'submit' del formulario)
    filterForm.on("submit", function (e) {
        e.preventDefault(); //Detiene la recarga automática
        goToSearch();
    });

    //Variable global para que Google Maps la encuentre
    window.initMap = function() {
    //Coordenadas centrales (San José, Costa Rica)
    const centroCR = { lat: 9.93224, lng: -84.07952 };

    //Estilo personalizado para que se vea limpio
    const mapStyles = [
        { featureType: "poi", stylers: [{ visibility: "off" }] }, //Oculta negocios
        { featureType: "transit", stylers: [{ visibility: "off" }] } //Oculta paradas de bus
    ];

    const map = new google.maps.Map(document.getElementById("rutasMap"), {
        zoom: 10,
        center: centroCR,
        styles: mapStyles,
        disableDefaultUI: true, //Quita los controles feos para que se vea como banner
        zoomControl: true
    });

    //Marcadores simulando las ubicaciones de la base de datos
    const locaciones = [
        { nombre: "Sabana Sur, San José", lat: 9.9312, lng: -84.1066 },
        { nombre: "Universidad Nacional, Heredia", lat: 9.9986, lng: -84.1114 },
        { nombre: "City Mall, Alajuela", lat: 10.0051, lng: -84.2017 },
        { nombre: "TEC, Cartago", lat: 9.8543, lng: -83.9080 }
    ];

    //Colocar los pines en el mapa
    locaciones.forEach(loc => {
        new google.maps.Marker({
            position: { lat: loc.lat, lng: loc.lng },
            map: map,
            title: loc.nombre,
            icon: "http://maps.google.com/mapfiles/ms/icons/blue-dot.png" // Color del pin
        });
    });
};


});