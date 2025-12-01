-- Script SQL para crear la base de datos y tablas
CREATE DATABASE IF NOT EXISTS mini_library CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE mini_library;

CREATE TABLE IF NOT EXISTS users (
  id INT AUTO_INCREMENT PRIMARY KEY,
  email VARCHAR(255) NOT NULL,
  nombre VARCHAR(255) NOT NULL,
  google_id VARCHAR(255) NOT NULL,
  fecha_registro DATETIME NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS libros_guardados (
  id INT AUTO_INCREMENT PRIMARY KEY,
  user_id INT NOT NULL,
  google_books_id VARCHAR(255) NOT NULL,
  titulo VARCHAR(512) NOT NULL,
  autor VARCHAR(512),
  imagen_portada VARCHAR(1024),
  `reseña_personal` TEXT,
  fecha_guardado DATETIME NOT NULL,
  FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
