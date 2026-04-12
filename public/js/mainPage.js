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
});