<?php

declare(strict_types=1);

use App\Controllers\HomeController;
use App\Controllers\BienController;
use App\Controllers\AuthController;
use App\Controllers\FavoriController;
use App\Controllers\ReservationController;
use App\Controllers\CompteController;
use App\Controllers\Admin\DashboardController;
use App\Controllers\Admin\BienController as AdminBienController;
use App\Controllers\Admin\ReservationController as AdminReservationController;

/** @var App\Core\Router $router */

// --- Public ---
$router->get('/', [HomeController::class, 'index']);
$router->get('/biens', [BienController::class, 'index']);
$router->get('/biens/{id}', [BienController::class, 'show']);
$router->get('/services', [HomeController::class, 'services']);
$router->get('/a-propos', [HomeController::class, 'apropos']);
$router->get('/contact', [HomeController::class, 'contact']);
$router->post('/contact', [HomeController::class, 'contactEnvoi']);

// --- Authentification ---
$router->get('/connexion', [AuthController::class, 'showLogin']);
$router->post('/connexion', [AuthController::class, 'login']);
$router->get('/inscription', [AuthController::class, 'showRegister']);
$router->post('/inscription', [AuthController::class, 'register']);
$router->post('/deconnexion', [AuthController::class, 'logout']);

// --- Espace client ---
$router->get('/compte', [CompteController::class, 'index']);
$router->post('/compte/desactiver', [CompteController::class, 'desactiver']);
$router->get('/favoris', [FavoriController::class, 'index']);
$router->post('/favoris/{id}', [FavoriController::class, 'toggle']);
$router->get('/mes-reservations', [ReservationController::class, 'index']);
$router->post('/reservations', [ReservationController::class, 'store']);
$router->post('/reservations/{id}/annuler', [ReservationController::class, 'annuler']);

// --- Espace commercial / admin ---
$router->get('/admin', [DashboardController::class, 'index']);
$router->get('/admin/biens', [AdminBienController::class, 'index']);
$router->get('/admin/biens/nouveau', [AdminBienController::class, 'create']);
$router->post('/admin/biens', [AdminBienController::class, 'store']);
$router->get('/admin/biens/{id}/modifier', [AdminBienController::class, 'edit']);
$router->post('/admin/biens/{id}', [AdminBienController::class, 'update']);
$router->post('/admin/biens/{id}/supprimer', [AdminBienController::class, 'destroy']);
$router->get('/admin/reservations', [AdminReservationController::class, 'index']);
$router->post('/admin/reservations/{id}/statut', [AdminReservationController::class, 'updateStatut']);
