CREATE TABLE usuarios (
       id_usuario INT AUTO_INCREMENT PRIMARY KEY,
       nombre VARCHAR(100) NOT NULL,
       correo VARCHAR(100) NOT NULL UNIQUE,
       password VARCHAR(255) NOT NULL,
       fecha_registro TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
   ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

   CREATE TABLE contactos (
       id_contacto INT AUTO_INCREMENT PRIMARY KEY,
       id_usuario INT NOT NULL,
       nombre VARCHAR(100) NOT NULL,
       telefono VARCHAR(20) NOT NULL,
       correo VARCHAR(100),
       ciudad VARCHAR(50),
       FOREIGN KEY (id_usuario) REFERENCES usuarios(id_usuario) ON DELETE CASCADE
   )ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;