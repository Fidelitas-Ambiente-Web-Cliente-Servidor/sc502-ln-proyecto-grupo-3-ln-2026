<!doctype html>
<html lang="es">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>RideShare - Inicio</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css" rel="stylesheet" crossorigin="anonymous" />
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" crossorigin="anonymous" />
    
    <link rel="stylesheet" href="public/css/style.css" />
    <link rel="stylesheet" href="public/css/mainPage.css" /> 
</head>
<body class="d-flex flex-column min-vh-100">
    
    <header class="bg-white text-dark py-3 px-4 d-flex flex-row justify-content-between align-items-center shadow-sm z-3 position-relative">
        <h1><a href="index.php" class="text-dark text-decoration-none fw-bold">RideShare</a></h1>
        
        <div class="d-flex align-items-center">
            <?php if(isset($_SESSION['id_usuario'])): ?>
                <span class="me-3 fw-bold text-dark">Hola, <?php echo $_SESSION['nombre']; ?></span>
                <a class="btn btn-outline-dark btn-sm text-decoration-none fw-bold" href="index.php?page=logout">Cerrar Sesión</a>
            <?php else: ?>
                <a class="btn btn-dark text-white fw-bold text-decoration-none rounded-pill px-4 py-2" id="btnSearch" href="index.php?page=login">Ingresar</a>
            <?php endif; ?>
        </div>
    </header>

    <main class="flex-grow-1">
        <section class="position-relative w-100 d-flex justify-content-center align-items-center" style="height: 60vh; overflow: hidden;">
            
            <div id="carouselExampleSlidesOnly" class="carousel slide position-absolute w-100 h-100" style="top: 0; left: 0; z-index: 1;" data-bs-ride="carousel">
                <div class="carousel-inner h-100">
                    <div class="carousel-item active h-100">
                        <img src="public/img/friends-having-fun-inside-car.jpg" class="d-block w-100 h-100" style="object-fit: cover;" alt="Amigos divirtiéndose" />
                    </div>
                    <div class="carousel-item h-100">
                        <img src="public/img/man-driving-looking-girlfriend-s-tablet.jpg" class="d-block w-100 h-100" style="object-fit: cover;" alt="Pareja en auto" />
                    </div>
                    <div class="carousel-item h-100">
                        <img src="public/img/young-uber-driver-car-interior.jpg" class="d-block w-100 h-100" style="object-fit: cover;" alt="Conductor joven" />
                    </div>
                </div>
                <div class="position-absolute top-0 start-0 w-100 h-100" style="background: rgba(0,0,0,0.3);"></div>
            </div>

            <form id="filterForm" class="w-75" style="max-width: 600px; z-index: 2;">
                <div class="input-group shadow-lg rounded overflow-hidden">
                    <span class="input-group-text bg-white border-0 py-3"><i class="bi bi-geo-alt fs-5"></i></span>
                    <input type="text" class="form-control border-0 fs-5" placeholder="¿Dónde vas?" id="zoneSearch" />
                    <button type="button" class="btn btn-dark px-4 border-0" id="btnSearchSubmit">
                        <i class="bi bi-search fs-5"></i>
                    </button>
                </div>
            </form>
        </section>

        <section class="container my-5">
            <div class="row mb-4">
                <div class="col d-flex justify-content-between align-items-end">
                    <h2 class="fw-bold m-0 fs-2">Rutas cerca de ti</h2>
                    <a href="index.php?page=buscar_viaje" class="text-dark fw-bold text-decoration-underline">Ver todos los viajes</a>
                </div>
            </div>

            <div id="rutasMap" class="shadow-sm" style="height: 450px; width: 100%; border-radius: 15px; background-color: #f8f9fa;"></div>
        </section>
    </main>

    <footer class="bg-white pt-5 pb-4 border-top mt-auto">
        <div class="container">
            <div class="row mb-5">
                <div class="col-md-4 mb-4 mb-md-0">
                    <h3 class="fw-bold mb-4">RideShare</h3>
                    <div class="d-flex flex-column flex-sm-row gap-3">
                        <button class="btn btn-dark d-flex align-items-center gap-2 px-3 py-2 rounded-3 border-0">
                            <i class="bi bi-apple fs-3"></i>
                            <div class="text-start" style="line-height: 1.2;">
                                <small style="font-size: 0.65rem; color: #ccc;">Consíguelo en el</small><br>
                                <span class="fw-bold">App Store</span>
                            </div>
                        </button>
                        <button class="btn btn-dark d-flex align-items-center gap-2 px-3 py-2 rounded-3 border-0">
                            <i class="bi bi-google-play fs-3"></i>
                            <div class="text-start" style="line-height: 1.2;">
                                <small style="font-size: 0.65rem; color: #ccc;">DISPONIBLE EN</small><br>
                                <span class="fw-bold">Google Play</span>
                            </div>
                        </button>
                    </div>
                </div>

                <div class="col-md-4 mb-4 mb-md-0">
                    <ul class="list-unstyled d-flex flex-column gap-3 mb-0">
                        <li><a href="#" class="text-dark text-decoration-none hover-underline">Obtén ayuda</a></li>
                        <li><a href="#" class="text-dark text-decoration-none hover-underline">Regístrate como conductor</a></li>
                        <li><a href="#" class="text-dark text-decoration-none hover-underline">Crear una cuenta institucional</a></li>
                    </ul>
                </div>

                <div class="col-md-4">
                    <ul class="list-unstyled d-flex flex-column gap-3 mb-0">
                        <li><a href="#" class="text-dark text-decoration-none hover-underline">Viajes cerca de mí</a></li>
                        <li><a href="#" class="text-dark text-decoration-none hover-underline">Conoce todas las ciudades</a></li>
                        <li><a href="#" class="text-dark text-decoration-none hover-underline">Acerca de RideShare</a></li>
                        <li><a href="#" class="text-dark text-decoration-none hover-underline"><i class="bi bi-globe me-2"></i>Español</a></li>
                    </ul>
                </div>
            </div>

            <hr class="mb-4" style="border-color: #ddd;">

            <div class="row align-items-center">
                <div class="col-md-4 d-flex gap-4 mb-3 mb-md-0">
                    <a href="#" class="text-dark"><i class="bi bi-facebook fs-5"></i></a>
                    <a href="#" class="text-dark"><i class="bi bi-twitter-x fs-5"></i></a>
                    <a href="#" class="text-dark"><i class="bi bi-instagram fs-5"></i></a>
                </div>
                <div class="col-md-8 d-flex justify-content-md-end gap-3 flex-wrap text-muted" style="font-size: 0.85rem;">
                    <a href="#" class="text-muted text-decoration-none hover-underline">Política de privacidad</a>
                    <a href="#" class="text-muted text-decoration-none hover-underline">Términos</a>
                    <a href="#" class="text-muted text-decoration-none hover-underline">Tarifas</a>
                    <span class="ms-md-3">&copy; 2026 RideShare Technologies Inc.</span>
                </div>
            </div>
        </div>
    </footer>

    <script src="public/js/jquery-4.0.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/js/bootstrap.bundle.min.js" crossorigin="anonymous"></script>
    <script src="public/js/utils.js"></script>
    <script src="public/js/mainPage.js"></script>
    <script src="https://maps.googleapis.com/maps/api/js?key=AIzaSyAAWuQ09BiDmII2sIRKjH6N0DmBDhFhoR4&callback=initMap&loading=async" async defer></script>
</body>
</html>