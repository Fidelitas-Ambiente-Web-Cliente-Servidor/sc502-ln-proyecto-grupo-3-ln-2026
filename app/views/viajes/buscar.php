<!doctype html>
<html lang="es">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Buscar Viaje - RideShare</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css" rel="stylesheet" crossorigin="anonymous" />
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" crossorigin="anonymous" />
    <link rel="stylesheet" href="public/css/style.css" />
    <link rel="stylesheet" href="public/css/home.css" />
</head>
<body>
      <header class="bg-dark text-white py-3 px-4 d-flex flex-row justify-content-between align-items-center">
              <h1><a href="index.php" class="text-white text-decoration-none">RideShare</a></h1>
              
              <div class="d-flex align-items-center">
                  <?php if(isset($_SESSION['id_usuario'])): ?>
                      <span class="me-3 fw-bold text-light">Hola, <?php echo $_SESSION['nombre']; ?></span>
                      <a class="btn btn-outline-light btn-sm text-decoration-none" href="index.php?page=logout">Cerrar Sesión</a>
                  <?php elseif($page !== 'login'): ?>
                      <a class="btn-ride primary text-decoration-none" href="index.php?page=login">Ingresar</a>
                  <?php endif; ?>
              </div>
          </header>

    <main class="container my-5 flex-grow">
        <div class="container">
            <div class="row g-3 align-items-end">

                <!-- INPUT -->
                <div class="col-12 col-md-6">
                    <form id="filterForm">
                        <label for="zone" class="form-label">Zona de destino</label>
                        <div class="input-group">
                            <span class="input-group-text">
                                <i class="bi bi-geo-alt"></i>
                            </span>
                            <input 
                                type="text" 
                                class="form-control" 
                                placeholder="Ej: Heredia, San Pedro..." 
                                id="zone" 
                            />
                        </div>
                    </form>
                </div>

                <!-- BUTTONS -->
                <div class="col-12 col-md-6">
                    <div class="d-flex flex-column flex-md-row gap-2">

                        <a href="index.php?page=crear_viaje" 
                          class="btn-ride primary w-100 text-center">
                            Publicar ride
                        </a>

                        <button 
                            class="btn-ride secondary w-100" 
                            id="btnMisRides">
                            Mis rides
                        </button>

                    </div>
                </div>

            </div>
        </div>

        <section id="trips" class="mt-4">
            <h2>Busca tu Ride</h2>
            <div class="trip-cards mt-3"></div>
        </section>

        <div id="trips-container">
        </div>

        <div id="tabs-container" class="d-none mt-4">

          <!-- TABS -->
          <ul class="nav nav-tabs flex-nowrap overflow-auto" role="tablist" style="white-space: nowrap;">
              
              <li class="nav-item">
                  <button 
                      class="nav-link active" 
                      id="passenger-tab"
                      data-bs-toggle="tab" 
                      data-bs-target="#passenger-trips">
                      Pasajero
                  </button>
              </li>

              <li class="nav-item" id="driver-tab-container">
                  <button 
                      class="nav-link" 
                      id="driver-tab"
                      data-bs-toggle="tab" 
                      data-bs-target="#driver-trips">
                      Conductor
                  </button>
              </li>

          </ul>

          <!-- CONTENT -->
          <div class="tab-content mt-3">

              <!-- PASSENGER -->
              <div class="tab-pane fade show active" id="passenger-trips">

                  <div class="table-responsive">
                      <table class="table table-sm align-middle">
                          <thead class="small">
                              <tr>
                                  <th>Destino</th>
                                  <th>Fecha</th>
                                  <th>Hora</th>
                                  <th class="text-center"><i class="bi bi-heart"></i></th>
                              </tr>
                          </thead>
                          <tbody id="passenger-trips-table"></tbody>
                      </table>
                  </div>

              </div>

              <!-- DRIVER -->
              <div class="tab-pane fade" id="driver-trips">

                  <div class="table-responsive">
                      <table class="table table-sm align-middle">
                          <thead class="small">
                              <tr>
                                  <th>Destino</th>
                                  <th>Fecha</th>
                                  <th>Hora</th>
                                  <th>Precio</th>
                                  <th>Pasajeros</th>
                                  <th class="text-center"><i class="bi bi-lightning-charge"></i></th>
                              </tr>
                          </thead>
                          <tbody id="driver-trips-table"></tbody>
                      </table>
                  </div>

              </div>

          </div>

      </div>

        <div class="modal fade" id="commentsModal" tabindex="-1" aria-hidden="true">
          <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable">
            <div class="modal-content border-0 shadow-lg">
              <div class="modal-header bg-dark text-white">
                <h5 class="modal-title fw-bold"><i class="bi bi-chat-quote me-2"></i>Feedback de los pasajeros</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
              </div>
              <div class="modal-body" id="commentsModalBody">
                </div>
            </div>
          </div>
        </div>

    </main>

    <footer class="bg-dark text-white text-center py-3 mt-auto">
        <p>&copy; 2026 RideShare App - Todos los derechos reservados</p>
    </footer>

    <script src="public/js/jquery-4.0.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/js/bootstrap.bundle.min.js" crossorigin="anonymous"></script>
    <script src="public/js/utils.js"></script>
    <script src="public/js/home.js"></script>
</body>
</html>