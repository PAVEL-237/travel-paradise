<?php

try {
    $config = require_once 'config/database.php';
    $dbConfig = $config['connections']['pgsql'];
    
    $dsn = "pgsql:host={$dbConfig['host']};port={$dbConfig['port']};dbname={$dbConfig['database']};user={$dbConfig['username']};password={$dbConfig['password']}";
    $options = [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        PDO::ATTR_EMULATE_PREPARES => false,
    ];
    
    $pdo = new PDO($dsn, null, null, $options);
    echo "Connexion à la base de données PostgreSQL réussie !\n";
    
} catch (PDOException $e) {
    echo "Erreur de connexion : " . $e->getMessage() . "\n";
} 