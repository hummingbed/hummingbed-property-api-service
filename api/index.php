<?php

// Vercel executes this file as /api/index.php. Without normalizing the script
// name, Symfony may treat /api as the application base path and strip it from
// incoming /api/* requests before Laravel's router sees them.
$_SERVER['SCRIPT_NAME'] = '/index.php';
$_SERVER['PHP_SELF'] = '/index.php';

// Vercel's deployment filesystem is read-only. SQLite can only write under
// /tmp, which is local to the current serverless function instance.
$sqlitePath = $_ENV['DB_DATABASE'] ?? $_SERVER['DB_DATABASE'] ?? '/tmp/database.sqlite';
if (! file_exists($sqlitePath)) {
    touch($sqlitePath);
}

require __DIR__.'/../public/index.php';
