-- 1.Tablas Independientes (Catálogos y Usuarios)
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

-- 3.Tabla Principal de Viajes (Depende de Usuarios, Ubicaciones y Estados)
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

-- 4.Tabla de Reservas (Depende de Viajes, Usuarios y Estados)
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
    id_calificador INT NOT NULL,  -- quien califica
    id_calificado INT NOT NULL,   -- a quien califican
    tipo ENUM('CONDUCTOR', 'PASAJERO') NOT NULL,
    puntuacion INT NOT NULL CHECK (puntuacion BETWEEN 1 AND 5),
    comentario TEXT,
    fecha DATETIME DEFAULT CURRENT_TIMESTAMP,
    UNIQUE (id_viaje, id_calificador, id_calificado),
    FOREIGN KEY (id_viaje) REFERENCES VIAJE(id_viaje) ON DELETE CASCADE,
    FOREIGN KEY (id_calificador) REFERENCES USUARIO(id_usuario) ON DELETE CASCADE,
    FOREIGN KEY (id_calificado) REFERENCES USUARIO(id_usuario) ON DELETE CASCADE
);

-- Opcional: Datos básicos para probar luego
INSERT INTO ROL (nombre_rol) VALUES ('Conductor'), ('Pasajero'), ('Administrador');
INSERT INTO ESTADO (nombre_estado) VALUES ('Activo'), ('Completado'), ('Cancelado'), ('Pendiente');

-- Insertamos un usuario de prueba forzando el ID 1
INSERT INTO USUARIO (id_usuario, nombre, apellidos, correo, contrasena, telefono) 
VALUES (1, 'Conductor', 'De Prueba', 'conductor@rideshare.com', 'password', '8888-8888');

-- Le asignamos el rol de Conductor (ID 1 en tu tabla ROL)
INSERT INTO USUARIO_ROL (id_usuario, id_rol) 
VALUES (1, 1);


-- 1.Insertamos algunas ubicaciones de prueba
INSERT INTO UBICACION (provincia, canton, distrito, detalle) VALUES
('-', '-', '-', 'San José, Sabana Sur'),
('-', '-', '-', 'Heredia, Universidad Nacional'),
('-', '-', '-', 'Alajuela, City Mall'),
('-', '-', '-', 'Cartago, TEC');

-- 2.Insertamos viajes simulando que el conductor es el usuario 1
-- Las fechas deben ser futuras a abril 2026 para que pasen las validaciones
INSERT INTO VIAJE (id_conductor, id_origen, id_destino, id_estado_viaje, fecha_viaje, hora_salida, precio, asientos_disponibles, detalle) VALUES
(1, 1, 2, 1, '2026-04-20', '08:00:00', 1500.00, 3, 'Viaje directo, pongo buena música.'),
(1, 2, 3, 1, '2026-04-21', '14:30:00', 1200.00, 2, 'Aire acondicionado al máximo.'),
(1, 4, 1, 1, '2026-04-22', '06:15:00', 2000.00, 4, 'Salgo puntual, no espero a nadie atrasado.');


-- Ver todas las reservas (Quién reservó qué viaje)
SELECT 
    r.id_reserva, 
    u.nombre AS pasajero, 
    v.fecha_viaje, 
    ori.detalle AS origen, 
    des.detalle AS destino, 
    e.nombre_estado AS estado_reserva
FROM RESERVA r
JOIN USUARIO u ON r.id_pasajero = u.id_usuario
JOIN VIAJE v ON r.id_viaje = v.id_viaje
JOIN UBICACION ori ON v.id_origen = ori.id_ubicacion
JOIN UBICACION des ON v.id_destino = des.id_ubicacion
JOIN ESTADO e ON r.id_estado = e.id_estado;

-- Ver cómo bajaron los espacios del viaje
SELECT 
    v.id_viaje, 
    u.nombre AS conductor, 
    v.fecha_viaje, 
    v.hora_salida, 
    v.asientos_disponibles 
FROM VIAJE v
JOIN USUARIO u ON v.id_conductor = u.id_usuario;

-- Ver todos los usuarios registrados y su Rol
SELECT 
    u.id_usuario, 
    u.nombre, 
    u.apellidos, 
    u.correo, 
    u.telefono, 
    r.nombre_rol AS rol_asignado,
    u.fecha_registro
FROM USUARIO u
JOIN USUARIO_ROL ur ON u.id_usuario = ur.id_usuario
JOIN ROL r ON ur.id_rol = r.id_rol;

-- Resumen General de Viajes
SELECT 
    v.id_viaje, 
    c.nombre AS conductor, 
    o.detalle AS origen, 
    d.detalle AS destino, 
    v.fecha_viaje, 
    v.precio, 
    v.asientos_disponibles, 
    e.nombre_estado
FROM VIAJE v
JOIN USUARIO c ON v.id_conductor = c.id_usuario
JOIN UBICACION o ON v.id_origen = o.id_ubicacion
JOIN UBICACION d ON v.id_destino = d.id_ubicacion
JOIN ESTADO e ON v.id_estado_viaje = e.id_estado;