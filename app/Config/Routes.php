<?php

use App\Controllers\BukuTamu;
use CodeIgniter\Router\RouteCollection;

/**
 * @var RouteCollection $routes
 */
$routes->get('/', 'BukuTamu::index'); // Halaman utama/form
$routes->post('/', 'BukuTamu::create'); // Submit form

$routes->get('/set_admin', 'BukuTamu::setAdmin');
$routes->get('/unset_admin', 'BukuTamu::unsetAdmin');

$routes->get('/admin', 'BukuTamu::admin'); // Halaman admin
$routes->get('/export-csv', 'BukuTamu::exportCsv'); // Export CSV

// Rute contoh untuk halaman akses ditolak (bisa berupa view sederhana)
$routes->get('/admin/access_denied', function () {
    return view('errors/html/access_denied_message'); // Buat view ini jika perlu
});


// Rute untuk Halaman EDIT (Form EDIT)
$routes->get('/admin/edit/(:num)', 'BukuTamu::edit/$1'); // Menampilkan form edit berdasarkan ID
$routes->post('/admin/update/(:num)', 'BukuTamu::update/$1'); // untuk handle submit
$routes->post('tamu/delete/(:num)', 'BukuTamu::delete/$1');
