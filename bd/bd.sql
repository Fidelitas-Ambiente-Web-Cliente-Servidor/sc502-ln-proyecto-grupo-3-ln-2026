-- Desactivamos revisiones de llaves foráneas para evitar errores al borrar
SET FOREIGN_KEY_CHECKS = 0;

-- 0. Eliminación de tablas en orden inverso de dependencia
DROP TABLE IF EXISTS CALIFICACION;
DROP TABLE IF EXISTS RESERVA;
DROP TABLE IF EXISTS VIAJE;
DROP TABLE IF EXISTS VEHICULO;
DROP TABLE IF EXISTS USUARIO_ROL;
DROP TABLE IF EXISTS USUARIO;
DROP TABLE IF EXISTS UBICACION;
DROP TABLE IF EXISTS ESTADO;
DROP TABLE IF EXISTS ROL;

SET FOREIGN_KEY_CHECKS = 1;

-- 1. Tablas Independientes (Catálogos y Usuarios)
CREATE TABLE ROL (
    id_rol INT AUTO_INCREMENT PRIMARY KEY,
    nombre_rol VARCHAR(50) NOT NULL
);

CREATE TABLE ESTADO (
    id_estado INT AUTO_INCREMENT PRIMARY KEY,
    nombre_estado VARCHAR(50) NOT NULL
);

CREATE TABLE UBICACION (
    id_ubicacion INT AUTO_INCREMENT PRIMARY KEY,
    provincia VARCHAR(50) NOT NULL,
    canton VARCHAR(50) NOT NULL,
    distrito VARCHAR(50) NOT NULL,
    detalle TEXT
);

CREATE TABLE USUARIO (
    id_usuario INT AUTO_INCREMENT PRIMARY KEY,
    nombre VARCHAR(50) NOT NULL,
    apellidos VARCHAR(100) NOT NULL,
    correo VARCHAR(100) NOT NULL UNIQUE,
    contrasena VARCHAR(255) NOT NULL,
    telefono VARCHAR(20),
    fecha_registro DATETIME DEFAULT CURRENT_TIMESTAMP
);

-- 2.Tablas Intermedias y Dependientes (Dependen de Usuarios, Roles y Vehículos)
CREATE TABLE USUARIO_ROL (
    id_usuario INT,
    id_rol INT,
    PRIMARY KEY (id_usuario, id_rol),
    FOREIGN KEY (id_usuario) REFERENCES USUARIO(id_usuario) ON DELETE CASCADE,
    FOREIGN KEY (id_rol) REFERENCES ROL(id_rol) ON DELETE CASCADE
);

CREATE TABLE VEHICULO (
    id_vehiculo INT AUTO_INCREMENT PRIMARY KEY,
    id_usuario INT NOT NULL,
    marca VARCHAR(50) NOT NULL,
    modelo VARCHAR(50) NOT NULL,
    placa VARCHAR(20) NOT NULL UNIQUE,
    capacidad_pasajeros INT NOT NULL,
    FOREIGN KEY (id_usuario) REFERENCES USUARIO(id_usuario) ON DELETE CASCADE
);

CREATE TABLE VIAJE (
    id_viaje INT AUTO_INCREMENT PRIMARY KEY,
    id_conductor INT NOT NULL,
    id_origen INT NOT NULL,
    id_destino INT NOT NULL,
    id_estado_viaje INT NOT NULL,
    fecha_viaje DATE NOT NULL,
    hora_salida TIME NOT NULL,
    precio DECIMAL(10,2) NOT NULL,
    asientos_disponibles INT NOT NULL,
    detalle TEXT,
    FOREIGN KEY (id_conductor) REFERENCES USUARIO(id_usuario),
    FOREIGN KEY (id_origen) REFERENCES UBICACION(id_ubicacion),
    FOREIGN KEY (id_destino) REFERENCES UBICACION(id_ubicacion),
    FOREIGN KEY (id_estado_viaje) REFERENCES ESTADO(id_estado)
);

CREATE TABLE RESERVA (
    id_reserva INT AUTO_INCREMENT PRIMARY KEY,
    id_viaje INT NOT NULL,
    id_pasajero INT NOT NULL,
    id_estado INT NOT NULL,
    fecha_reserva DATETIME DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (id_viaje) REFERENCES VIAJE(id_viaje) ON DELETE CASCADE,
    FOREIGN KEY (id_pasajero) REFERENCES USUARIO(id_usuario),
    FOREIGN KEY (id_estado) REFERENCES ESTADO(id_estado)
);

CREATE TABLE CALIFICACION (
    id_calificacion INT AUTO_INCREMENT PRIMARY KEY,
    id_viaje INT NOT NULL,
    id_calificador INT NOT NULL, -- quien califica
    id_calificado INT NOT NULL, -- a quien califican
    tipo ENUM('CONDUCTOR', 'PASAJERO') NOT NULL,
    puntuacion INT NOT NULL CHECK (puntuacion BETWEEN 1 AND 5),
    comentario TEXT,
    fecha DATETIME DEFAULT CURRENT_TIMESTAMP,
    UNIQUE (id_viaje, id_calificador, id_calificado),
    FOREIGN KEY (id_viaje) REFERENCES VIAJE(id_viaje) ON DELETE CASCADE,
    FOREIGN KEY (id_calificador) REFERENCES USUARIO(id_usuario) ON DELETE CASCADE,
    FOREIGN KEY (id_calificado) REFERENCES USUARIO(id_usuario) ON DELETE CASCADE
);

-- 3. Inserción de Datos de Prueba (Seed Data)
INSERT INTO ROL (nombre_rol) VALUES ('Conductor'), ('Pasajero'), ('Administrador');
INSERT INTO ESTADO (nombre_estado) VALUES ('Activo'), ('Completado'), ('Cancelado'), ('Pendiente');

-- Usuarios (1 Conductor, 1 Pasajero)
INSERT INTO USUARIO (id_usuario, nombre, apellidos, correo, contrasena, telefono) VALUES 
(1, 'Steven', 'Fonseca', 'steven@rideshare.com', '$2y$10$e0MYzXy..falsopasswordhash', '8888-8888'),
(2, 'Valeria', 'Solis', 'valeria@rideshare.com', '$2y$10$e0MYzXy..falsopasswordhash', '7777-7777');

INSERT INTO USUARIO_ROL (id_usuario, id_rol) VALUES (1, 1), (2, 2);

-- Ubicaciones
INSERT INTO UBICACION (provincia, canton, distrito, detalle) VALUES
('San José', 'Central', 'Mata Redonda', 'Sabana Sur'),
('Heredia', 'Central', 'Heredia', 'Universidad Nacional'),
('Alajuela', 'Central', 'Alajuela', 'City Mall'),
('Cartago', 'Central', 'Occidental', 'TEC');

-- Viajes
INSERT INTO VIAJE (id_conductor, id_origen, id_destino, id_estado_viaje, fecha_viaje, hora_salida, precio, asientos_disponibles, detalle) VALUES
(1, 1, 2, 1, '2026-04-20', '08:00:00', 1500.00, 3, 'Viaje directo hacia la UNA.'),
(1, 2, 3, 1, '2026-04-21', '14:30:00', 1200.00, 2, 'Aire acondicionado y espacio para equipaje.');

-- Reservas (Valeria reserva el primer viaje de Steven)
INSERT INTO RESERVA (id_viaje, id_pasajero, id_estado) VALUES (1, 2, 1);

-- Calificaciones (Valeria califica a Steven como conductor)
INSERT INTO CALIFICACION (id_viaje, id_calificador, id_calificado, tipo, puntuacion, comentario) VALUES 
(1, 2, 1, 'CONDUCTOR', 5, 'Excelente conductor, muy puntual.');

-- ==========================================
-- MÁS DATOS DE PRUEBA (SEED DATA EXTENDIDO)
-- ==========================================

-- 1. Insertar 3 Ubicaciones nuevas (IDs 5, 6 y 7)
INSERT INTO UBICACION (provincia, canton, distrito, detalle) VALUES
('San José', 'Montes de Oca', 'San Pedro', 'Universidad de Costa Rica (UCR)'),
('Heredia', 'San Pablo', 'San Pablo', 'Paseo de las Flores'),
('San José', 'Escazú', 'San Rafael', 'Multiplaza Escazú');

-- 2. Insertar 3 Usuarios nuevos (IDs 3, 4 y 5)
INSERT INTO USUARIO (id_usuario, nombre, apellidos, correo, contrasena, telefono) VALUES 
(3, 'Carlos', 'Brenes', 'carlos@rideshare.com', '$2y$10$e0MYzXy..falsopasswordhash', '6666-6666'),
(4, 'María', 'Fernández', 'maria@rideshare.com', '$2y$10$e0MYzXy..falsopasswordhash', '5555-5555'),
(5, 'Andrés', 'Castro', 'andres@rideshare.com', '$2y$10$e0MYzXy..falsopasswordhash', '4444-4444');

-- Asignar Roles: Carlos es Conductor(1), María es Pasajero(2), Andrés es Ambos(1 y 2)
INSERT INTO USUARIO_ROL (id_usuario, id_rol) VALUES 
(3, 1), 
(4, 2), 
(5, 1), 
(5, 2);

-- 3. Insertar 3 Vehículos (Para Steven(1), Carlos(3) y Andrés(5))
INSERT INTO VEHICULO (id_usuario, marca, modelo, placa, capacidad_pasajeros) VALUES
(1, 'Toyota', 'Yaris', 'BCK-123', 4),
(3, 'Hyundai', 'Accent', 'HSD-456', 4),
(5, 'Suzuki', 'Swift', 'SWM-789', 4);

-- 4. Insertar 3 Viajes nuevos (IDs 3, 4 y 5)
-- Las fechas deben ser futuras a abril 2026 para que pasen las validaciones
INSERT INTO VIAJE (id_conductor, id_origen, id_destino, id_estado_viaje, fecha_viaje, hora_salida, precio, asientos_disponibles, detalle) VALUES
(3, 5, 6, 1, '2026-05-10', '17:00:00', 1800.00, 3, 'Salgo de la UCR hacia Heredia. Cero paradas.'),
(1, 4, 1, 1, '2026-05-12', '15:00:00', 2000.00, 4, 'Viaje tranquilo desde el TEC hasta Sabana.'),
(5, 7, 5, 1, '2026-05-15', '08:00:00', 2500.00, 2, 'Voy de Multiplaza a la UCR, puedo llevar a 2 personas.');

-- 5. Insertar 3 Reservas
-- María(4) reserva el viaje de Carlos(3)
-- Andrés(5) reserva el viaje de Steven(1)
-- Valeria(2) reserva el viaje de Andrés(5)
INSERT INTO RESERVA (id_viaje, id_pasajero, id_estado) VALUES 
(3, 4, 1),
(4, 5, 1),
(5, 2, 1);

-- Restar manualmente los asientos de esas reservas en la tabla VIAJE (como lo haría tu PHP)
UPDATE VIAJE SET asientos_disponibles = asientos_disponibles - 1 WHERE id_viaje IN (3, 4, 5);

-- 6. Insertar 3 Calificaciones variadas
-- María califica a Carlos (Pasajero evalúa a Conductor)
INSERT INTO CALIFICACION (id_viaje, id_calificador, id_calificado, tipo, puntuacion, comentario) VALUES 
(3, 4, 3, 'CONDUCTOR', 5, 'Super amable y el carro olía muy bien.');

-- Steven califica a Andrés (Conductor evalúa a Pasajero)
INSERT INTO CALIFICACION (id_viaje, id_calificador, id_calificado, tipo, puntuacion, comentario) VALUES 
(4, 1, 5, 'PASAJERO', 4, 'Buen viaje, pero llegó unos 5 minutos tarde al punto de encuentro.');

-- Valeria califica a Andrés (Pasajero evalúa a Conductor)
INSERT INTO CALIFICACION (id_viaje, id_calificador, id_calificado, tipo, puntuacion, comentario) VALUES 
(5, 2, 5, 'CONDUCTOR', 5, 'Excelente ruta, evitó todas las presas de la pista.');



------Consultas-------

--Ver el promedio de calificación por usuario
SELECT 
    u.nombre, 
    u.apellidos, 
    AVG(c.puntuacion) AS promedio_estrellas, 
    COUNT(c.id_calificacion) AS total_calificaciones
FROM USUARIO u
JOIN CALIFICACION c ON u.id_usuario = c.id_calificado
GROUP BY u.id_usuario;

--Ver el detalle de quién calificó a quién
SELECT 
    v.fecha_viaje,
    orig.detalle AS origen,
    dest.detalle AS destino,
    calificador.nombre AS quien_califica,
    calificado.nombre AS a_quien_califican,
    c.tipo AS rol_evaluado,
    c.puntuacion,
    c.comentario
FROM CALIFICACION c
JOIN VIAJE v ON c.id_viaje = v.id_viaje
JOIN USUARIO calificador ON c.id_calificador = calificador.id_usuario
JOIN USUARIO calificado ON c.id_calificado = calificado.id_usuario
JOIN UBICACION orig ON v.id_origen = orig.id_ubicacion
JOIN UBICACION dest ON v.id_destino = dest.id_ubicacion;

--Auditoría de Usuarios y Roles
SELECT 
    u.id_usuario, 
    u.nombre, 
    u.apellidos, 
    u.correo, 
    r.nombre_rol AS rol,
    u.fecha_registro
FROM USUARIO u
JOIN USUARIO_ROL ur ON u.id_usuario = ur.id_usuario
JOIN ROL r ON ur.id_rol = r.id_rol
ORDER BY u.fecha_registro DESC;

--Tablero de Viajes Disponibles (Vista del Pasajero)
SELECT 
    v.id_viaje, 
    c.nombre AS conductor, 
    o.detalle AS origen, 
    d.detalle AS destino, 
    v.fecha_viaje, 
    v.hora_salida,
    v.precio, 
    v.asientos_disponibles,
    e.nombre_estado AS estado_del_viaje
FROM VIAJE v
JOIN USUARIO c ON v.id_conductor = c.id_usuario
JOIN UBICACION o ON v.id_origen = o.id_ubicacion
JOIN UBICACION d ON v.id_destino = d.id_ubicacion
JOIN ESTADO e ON v.id_estado_viaje = e.id_estado
WHERE v.asientos_disponibles > 0
ORDER BY v.fecha_viaje ASC;

--Control de Reservas Realizadas
SELECT 
    r.id_reserva,
    pasajero.nombre AS nombre_pasajero,
    conductor.nombre AS nombre_conductor,
    v.fecha_viaje,
    orig.detalle AS punto_salida,
    dest.detalle AS punto_llegada,
    r.fecha_reserva
FROM RESERVA r
JOIN USUARIO pasajero ON r.id_pasajero = pasajero.id_usuario
JOIN VIAJE v ON r.id_viaje = v.id_viaje
JOIN USUARIO conductor ON v.id_conductor = conductor.id_usuario
JOIN UBICACION orig ON v.id_origen = orig.id_ubicacion
JOIN UBICACION dest ON v.id_destino = dest.id_ubicacion
ORDER BY r.fecha_reserva DESC;

--Sistema de Reputación (Promedios)
SELECT 
    u.nombre, 
    u.apellidos, 
    c.tipo AS rol_evaluado,
    ROUND(AVG(c.puntuacion), 1) AS promedio_estrellas, 
    COUNT(c.id_calificacion) AS total_opiniones
FROM USUARIO u
JOIN CALIFICACION c ON u.id_usuario = c.id_calificado
GROUP BY u.id_usuario, c.tipo;

--Inventario de Vehículos por Conductor
SELECT 
    u.nombre AS dueño, 
    u.apellidos,
    v.marca, 
    v.modelo, 
    v.placa, 
    v.capacidad_pasajeros
FROM VEHICULO v
JOIN USUARIO u ON v.id_usuario = u.id_usuario;

--Últimos Comentarios y Feedback
SELECT 
    calificador.nombre AS de,
    calificado.nombre AS para,
    c.puntuacion AS estrellas,
    c.comentario,
    c.fecha AS fecha_comentario
FROM CALIFICACION c
JOIN USUARIO calificador ON c.id_calificador = calificador.id_usuario
JOIN USUARIO calificado ON c.id_calificado = calificado.id_usuario
ORDER BY c.fecha DESC;





--Validación de Registro Exitoso
SELECT 
    id_usuario, 
    nombre, 
    apellidos, 
    correo, 
    telefono, 
    fecha_registro 
FROM USUARIO 
ORDER BY fecha_registro DESC 
LIMIT 1;


-- Validar que la contraseña está protegida (Hash)
-- Sustituye el correo por el que acabas de registrar
SELECT 
    correo, 
    contrasena AS password_hash_protegido 
FROM USUARIO 
WHERE correo = 'Mauricio@gmail.com';

-- Validar los permisos/roles asignados al usuario
SELECT 
    u.nombre, 
    u.correo, 
    r.nombre_rol AS rol_del_sistema
FROM USUARIO u
JOIN USUARIO_ROL ur ON u.id_usuario = ur.id_usuario
JOIN ROL r ON ur.id_rol = r.id_rol
WHERE u.correo = 'Mauricio@gmail.com';

-- Validar unicidad de cuenta
SELECT 
    correo, 
    COUNT(*) as coincidencias 
FROM USUARIO 
GROUP BY correo 
HAVING coincidencias > 1; 