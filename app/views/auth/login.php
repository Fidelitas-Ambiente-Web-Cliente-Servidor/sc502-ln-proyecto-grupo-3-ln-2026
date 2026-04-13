<!doctype html>
<html lang="es">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Login - RideShare</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css" rel="stylesheet" crossorigin="anonymous"/>
    <link rel="stylesheet" href="public/css/style.css" />
    <link rel="stylesheet" href="public/css/ingreso.css" />
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

    <main class="container my-5">
        <section class="mx-auto shadow p-4 rounded bg-white" style="max-width: 600px">
            <div class="d-flex justify-content-center mb-4">
                <a href="#" id="login-box-link" class="me-4 fw-bold active text-decoration-none">Iniciar Sesión</a>
                <a href="#" id="signup-box-link" class="fw-bold text-decoration-none">Registrarse</a>
            </div>

            <div id="authMessages"></div>

            <form id="loginForm" class="email-login row g-3">
                <div class="col-12">
                    <label class="form-label">Correo electrónico</label>
                    <input type="email" id="login_correo" class="form-control" placeholder="correo@ejemplo.com" required />
                </div>
                <div class="col-12">
                    <label class="form-label">Contraseña</label>
                    <input type="password" id="login_password" class="form-control" placeholder="********" required />
                </div>
                <div class="col-12 text-center mt-4">
                    <button type="submit" class="btn-ride primary w-50">Iniciar Sesión</button>
                </div>
            </form>

            <form id="signupForm" class="email-signup row g-3" style="display: none;">
                <div class="col-md-6">
                    <label class="form-label">Nombre</label>
                    <input type="text" id="reg_nombre" class="form-control" required />
                </div>
                <div class="col-md-6">
                    <label class="form-label">Apellidos</label>
                    <input type="text" id="reg_apellidos" class="form-control" required />
                </div>
                <div class="col-md-6">
                    <label class="form-label">Teléfono</label>
                    <input type="text" id="reg_telefono" class="form-control" required />
                </div>
                <div class="col-md-6">
                    <label class="form-label">Correo</label>
                    <input type="email" id="reg_correo" class="form-control" required />
                </div>
                <div class="col-12">
                    <label class="form-label">Contraseña</label>
                    <input type="password" id="reg_password" class="form-control" placeholder="********" required />
                </div>
                <div class="col-12 text-center mt-4">
                    <button type="submit" class="btn-ride primary w-50">Crear cuenta</button>
                </div>
            </form>
        </section>
    </main>

    <script src="public/js/jquery-4.0.0.min.js"></script>
    <script src="public/js/utils.js"></script>
    <script src="public/js/ingreso.js"></script>
</body>
</html>