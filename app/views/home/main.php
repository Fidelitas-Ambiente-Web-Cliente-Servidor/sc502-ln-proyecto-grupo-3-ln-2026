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
<body>
    <header class="text-dark text-left py-3 px-4 d-flex flex-row justify-content-between align-items-center position-relative z-3">
            <h1><a href="index.php" class="text-dark text-decoration-none">RideShare</a></h1>
            
          <div class="d-flex align-items-center">
            <?php if(isset($_SESSION['id_usuario'])): ?>
                <span class="me-3 fw-bold">Hola, <?php echo $_SESSION['nombre']; ?></span>
                <a class="btn btn-sm text-decoration-none" href="index.php?page=logout">Cerrar Sesión</a>
                <?php else: ?>
                    <a class="btn-ride primary text-decoration-none" id="btnSearch" href="index.php?page=login">Ingresar</a>
                <?php endif; ?>
            </div>
        </header>

    <main class="mainWrapper">
        <div id="carouselExampleSlidesOnly" class="carousel slide" data-bs-ride="carousel">
            <div class="carousel-inner">
                <div class="carousel-item active">
                    <img src="public/img/friends-having-fun-inside-car.jpg" class="d-block w-100" alt="Amigos divirtiéndose" />
                </div>
                <div class="carousel-item">
                    <img src="public/img/man-driving-looking-girlfriend-s-tablet.jpg" class="d-block w-100" alt="Pareja en auto" />
                </div>
                <div class="carousel-item">
                    <img src="public/img/young-uber-driver-car-interior.jpg" class="d-block w-100" alt="Conductor joven" />
                </div>
            </div>
        </div>

        <form id="filterForm" class="filter-container">
            <div class="input-group shadow">
                <span class="input-group-text"><i class="bi bi-geo-alt"></i></span>
                <input type="text" class="form-control" placeholder="¿Dónde vas?" id="zoneSearch" />
            </div>
            <button type="button" class="btn-ride primary search-btn shadow" id="btnSearchSubmit">
                <i class="bi bi-search"></i>
            </button>
        </form>
    </main>

    <script src="public/js/jquery-4.0.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/js/bootstrap.bundle.min.js" crossorigin="anonymous"></script>
    <script src="public/js/utils.js"></script>
    <script src="public/js/mainPage.js"></script>
</body>
</html>