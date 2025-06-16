<?php

try {
    $config = require_once __DIR__ . '/config/database.php';
    $dbConfig = $config['connections']['pgsql'];
    
    // Connexion à la base de données travel_paradise
    $dsn = "pgsql:host={$dbConfig['host']};port={$dbConfig['port']};dbname={$dbConfig['database']};user={$dbConfig['username']};password={$dbConfig['password']}";
    $options = [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        PDO::ATTR_EMULATE_PREPARES => false,
    ];
    
    $pdo = new PDO($dsn, null, null, $options);
    echo "Connexion à la base de données travel_paradise réussie !\n\n";
    
    // Vérifier les tables existantes
    $query = "SELECT table_name 
              FROM information_schema.tables 
              WHERE table_schema = 'public'
              ORDER BY table_name;";
    
    $stmt = $pdo->query($query);
    $tables = $stmt->fetchAll();
    
    echo "Tables dans la base de données :\n";
    echo "-------------------------\n";
    if (empty($tables)) {
        echo "Aucune table n'existe encore dans la base de données.\n";
    } else {
        foreach ($tables as $table) {
            echo "- " . $table['table_name'] . "\n";
        }
    }
    
} catch (PDOException $e) {
    echo "Erreur de connexion : " . $e->getMessage() . "\n";
} 