<?php

// Configuration des limites d'exécution
ini_set('max_execution_time', 120);
ini_set('memory_limit', '256M');
ini_set('display_errors', 0);
error_reporting(0);

// Désactivation du mode debug en production
$_ENV['APP_ENV'] = 'prod';
$_ENV['APP_DEBUG'] = '0';

use App\Kernel;

require_once dirname(__DIR__).'/vendor/autoload_runtime.php';

return function (array $context) {
    return new Kernel($context['APP_ENV'], (bool) $context['APP_DEBUG']);
};
