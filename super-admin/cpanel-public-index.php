<?php

/**
 * cPanel deployment entry point.
 *
 * Upload this file as  public_html/admin/index.php  (or into whatever
 * public_html subdirectory / subdomain folder you are using).
 *
 * The rest of the Laravel application (everything EXCEPT the public/ folder)
 * should be uploaded to a directory OUTSIDE public_html, e.g.:
 *
 *   /home/<cpanel-user>/laravel_admin/
 *
 * Update the LARAVEL_ROOT constant below to match that path.
 */

define('LARAVEL_ROOT', dirname(__DIR__) . '/laravel_admin');   // ← adjust this path

use Illuminate\Foundation\Application;
use Illuminate\Http\Request;

define('LARAVEL_START', microtime(true));

if (file_exists($maintenance = LARAVEL_ROOT . '/storage/framework/maintenance.php')) {
    require $maintenance;
}

require LARAVEL_ROOT . '/vendor/autoload.php';

/** @var Application $app */
$app = require_once LARAVEL_ROOT . '/bootstrap/app.php';

$app->handleRequest(Request::capture());
