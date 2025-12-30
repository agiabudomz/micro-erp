<?php
$host = "localhost";
$user = "root";
$pass = ""; 
$dbname = "microerp_db";

$conn = new mysqli($host, $user, $pass);
$conn->query("CREATE DATABASE IF NOT EXISTS $dbname");
$conn->select_db($dbname);

// Tabela de Usuários
$conn->query("CREATE TABLE IF NOT EXISTS users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100),
    phone VARCHAR(20) UNIQUE,
    password VARCHAR(255),
    role ENUM('admin', 'reseller') DEFAULT 'reseller'
)");

// Tabela de Produtos (Adicionado campos de controle)
$conn->query("CREATE TABLE IF NOT EXISTS products (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT,
    name VARCHAR(100),
    buy_price DECIMAL(10,2),
    sell_price DECIMAL(10,2),
    stock INT DEFAULT 0,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
)");

// Tabela de Vendas (IMPORTANTE: Adicionado campo 'quantity' e 'total_profit')
$conn->query("CREATE TABLE IF NOT EXISTS sales (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT,
    product_id INT,
    product_name VARCHAR(100),
    quantity INT DEFAULT 1,
    sale_price DECIMAL(10,2),
    total_profit DECIMAL(10,2),
    sale_date TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
)");
?>