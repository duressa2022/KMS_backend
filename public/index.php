<?php

namespace Backend\Routes;

require_once __DIR__ . '/../vendor/autoload.php';
require_once __DIR__ . '/IndividualRoutes.php';
require_once __DIR__ . '/FamilyRoutes.php';
require_once __DIR__ . '/HouseRoutes.php';
require_once __DIR__ . '/IdCardRoutes.php';
require_once __DIR__ . '/SearchRoutes.php';
require_once __DIR__ . '/SettingsRoutes.php';
require_once __DIR__ . '/DashboardRoutes.php';

use Dotenv\Dotenv;
use PDO;

$dotenv = Dotenv::createImmutable(__DIR__ . '/../');
$dotenv->load();

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

$dsn = "mysql:host={$_ENV['DB_HOST']};port={$_ENV['DB_PORT']};dbname={$_ENV['DB_DATABASE']}";
$username = $_ENV['DB_USERNAME'];
$password = $_ENV['DB_PASSWORD'];
$options = [
    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    PDO::MYSQL_ATTR_SSL_CA => 'C:/Users/HP/Desktop/cbtp 11/backend/ca.pem',
    PDO::MYSQL_ATTR_SSL_VERIFY_SERVER_CERT => true,
    PDO::ATTR_TIMEOUT => 30
];

try {
    $pdo = new PDO($dsn, $username, $password, $options);
} catch (PDOException $e) {
    die("Connection failed: " . $e->getMessage());
}

use Src\Adapter\Controllers\IndividualController;
use Src\Adapter\Presenters\JsonPresenter;
use Src\Usecase\IndividualUsecase;
use Src\Adapter\Gateways\Database\IndividualRepository;
use Src\Adapter\Controllers\FamilyController;
use Src\Usecase\FamilyUsecase;
use Src\Adapter\Gateways\Database\FamilyRepository;
use Src\Adapter\Controllers\HouseController;
use Src\Usecase\HouseUsecase;
use Src\Adapter\Gateways\Database\HouseRepository;
use Src\Adapter\Controllers\IdCardController;
use Src\Usecase\IdCardUsecase;
use Src\Adapter\Gateways\Database\IdCardRepository;
use Src\Adapter\Controllers\SearchController;
use Src\Usecase\SearchUsecase;
use Src\Adapter\Gateways\Database\SearchRepository;
use Src\Adapter\Controllers\SettingsController;
use Src\Usecase\SettingsUsecase;
use Src\Adapter\Gateways\Database\SettingsRepository;
use Src\Adapter\Controllers\DashboardController;
use Src\Usecase\DashboardUsecase;
use Src\Adapter\Gateways\Database\DashboardRepository;

$individualRepository = new IndividualRepository($pdo);
$individualUsecase = new IndividualUsecase($individualRepository);
$familyRepository = new FamilyRepository($pdo);
$familyUsecase = new FamilyUsecase($familyRepository);
$houseRepository = new HouseRepository($pdo);
$houseUsecase = new HouseUsecase($houseRepository);
$idCardRepository = new IdCardRepository($pdo);
$idCardUsecase = new IdCardUsecase($idCardRepository);
$searchRepository = new SearchRepository($pdo);
$searchUsecase = new SearchUsecase($searchRepository);
$settingsRepository = new SettingsRepository($pdo);
$settingsUsecase = new SettingsUsecase($settingsRepository);
$dashboardRepository = new DashboardRepository($pdo);
$dashboardUsecase = new DashboardUsecase($dashboardRepository);
$jsonPresenter = new JsonPresenter();
$individualController = new IndividualController($individualUsecase, $jsonPresenter);
$familyController = new FamilyController($familyUsecase, $jsonPresenter);
$houseController = new HouseController($houseUsecase, $jsonPresenter);
$idCardController = new IdCardController($idCardUsecase, $jsonPresenter);
$searchController = new SearchController($searchUsecase, $jsonPresenter);
$settingsController = new SettingsController($settingsUsecase, $jsonPresenter);
$dashboardController = new DashboardController($dashboardUsecase, $jsonPresenter, $pdo);

header("Access-Control-Allow-Origin: http://127.0.0.1:5500");
header("Access-Control-Allow-Methods: GET, POST, PATCH, DELETE, OPTIONS, PUT");
header("Access-Control-Allow-Headers: Content-Type, Authorization, Accept");
header("Access-Control-Allow-Credentials: true");
header("Content-Type: application/json");

// Handle preflight request
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit();
}

$requestMethod = $_SERVER['REQUEST_METHOD'];
$requestUri = $_SERVER['REQUEST_URI'];

if (str_starts_with($requestUri, '/individuals')) {
    INDIVIDUAL_ROUTES($requestMethod, $requestUri, $individualController);
} elseif (str_starts_with($requestUri, '/families')) {
    FAMILY_ROUTES($requestMethod, $requestUri, $familyController);
} elseif (str_starts_with($requestUri, '/houses')) {
    HOUSE_ROUTES($requestMethod, $requestUri, $houseController);
} elseif (str_starts_with($requestUri, '/idcards')) {
    IDCARD_ROUTES($requestMethod, $requestUri, $idCardController);
} elseif (str_starts_with($requestUri, '/search')) {
    SEARCH_ROUTES($requestMethod, $requestUri, $searchController);
} elseif (str_starts_with($requestUri, '/settings')) {
    SETTINGS_ROUTES($requestMethod, $requestUri, $settingsController);
} elseif (str_starts_with($requestUri, '/dashboard')) {
    DASHBOARD_ROUTES($requestMethod, $requestUri, $dashboardController);
} else {
    http_response_code(404);
    echo json_encode(["message" => "Route not found"]);
}