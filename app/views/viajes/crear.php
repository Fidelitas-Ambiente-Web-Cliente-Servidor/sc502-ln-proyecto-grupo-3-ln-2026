<!doctype html>
<html lang="es">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Crear Viaje - RideShare</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css" rel="stylesheet" crossorigin="anonymous" />
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" crossorigin="anonymous" />

    <link rel="stylesheet" href="public/css/style.css" />
    <link rel="stylesheet" href="public/css/CrearViaje.css" />

    <!-- Google Maps Places API -->
    <script
        src="https://maps.googleapis.com/maps/api/js?key=AIzaSyAAWuQ09BiDmII2sIRKjH6N0DmBDhFhoR4&libraries=places"
        defer>
    </script>
</head>

<body>
    <header class="bg-dark text-white py-3 px-4 d-flex flex-row justify-content-between align-items-center">
        <h1>
            <a href="index.php" class="text-white text-decoration-none">RideShare</a>
        </h1>

        <div class="d-flex align-items-center">
            <?php if(isset($_SESSION['id_usuario'])): ?>
                <span class="me-3 fw-bold text-light">
                    Hola, <?php echo $_SESSION['nombre']; ?>
                </span>
                <a class="btn btn-outline-light btn-sm text-decoration-none" href="index.php?page=logout">
                    Cerrar Sesión
                </a>
            <?php else: ?>
                <a class="btn-ride primary text-decoration-none" href="index.php?page=login">
                    Ingresar
                </a>
            <?php endif; ?>
        </div>
    </header>

    <main class="container my-5">
        <section class="form-section mx-auto shadow p-4 rounded bg-white" style="max-width: 800px">

            <h2 class="text-center mb-4">Publicar un nuevo viaje</h2>

            <div id="formMessages" class="col-12"></div>

            <form id="viajeForm" class="row g-3" novalidate>

                <!-- ORIGEN -->
                <div class="col-md-6">
                    <label for="origen" class="form-label">Punto de Salida</label>
                    <input type="text" class="form-control" id="origen" placeholder="Ej: San José Centro" required />
                </div>

                <!-- DESTINO -->
                <div class="col-md-6">
                    <label for="destino" class="form-label">Destino</label>
                    <input type="text" class="form-control" id="destino" placeholder="Ej: Heredia Centro" required />
                </div>

                <!-- FECHA -->
                <div class="col-md-6">
                    <label for="fecha" class="form-label">Fecha del viaje</label>
                    <input type="date" class="form-control" id="fecha" required />
                </div>

                <!-- HORA -->
                <div class="col-md-6">
                    <label for="hora" class="form-label">Hora de salida</label>
                    <input type="time" class="form-control" id="hora" required />
                </div>

                <!-- ESPACIOS -->
                <div class="col-md-6">
                    <label for="espacios" class="form-label">Espacios disponibles</label>
                    <input type="number" class="form-control" id="espacios" min="1" max="4" required />
                </div>

                <!-- PRECIO -->
                <div class="col-md-6">
                    <label for="precio" class="form-label">Precio por pasajero (CRC)</label>
                    <input type="number" class="form-control" id="precio" min="0" required />
                </div>

                <!-- COMENTARIOS -->
                <div class="col-12">
                    <label for="comentarios" class="form-label">Detalles adicionales</label>
                    <textarea class="form-control" id="comentarios" rows="2"></textarea>
                </div>

                <!-- BOTONES -->
                <div class="col-12 d-flex justify-content-center gap-3 mt-4">
                    <a href="index.php?page=buscar_viaje" class="btn btn-outline-secondary px-4 fw-bold">
                        Ver viajes publicados
                    </a>

                    <button type="submit" class="btn-ride primary px-4 fw-bold" id="btnPublicar">
                        Publicar Viaje
                    </button>
                </div>

            </form>
        </section>
    </main>

    <!-- JS -->
    <script src="public/js/jquery-4.0.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/js/bootstrap.bundle.min.js"></script>
    <script src="public/js/utils.js"></script>
    <script src="public/js/CrearViaje.js"></script>

</body>
</html>