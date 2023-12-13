<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

session_start(); // démarraage de la session PHP, nécessaire pour utiliser les variables de session
define('BASE_PATH', __DIR__); // constante pour stocker le chemin de base du projet

// fonction pour gérer les erreurs PHP
function customErrorHandler($errno, $errstr, $errfile, $errline) {
    // enregistrement dans un fichier log spécifique
    error_log("Erreur [$errno] : $errstr dans le fichier $errfile à la ligne $errline", 3, BASE_PATH. '/logs/error.log');
    // retourne true pour indiquer qu'on a géré l'erreur
    return true;
}
set_error_handler('customErrorHandler'); // config de PHP pour utiliser la fonct personnalisée comme gestionnaire d'erreurs
// config de l'auto-chargement des classes PHP
spl_autoload_register(function ($class_name) {
    // chemin possible pour les fichiers de classes
    $controllerPath = BASE_PATH . '/controller/' . $class_name . '.php';
    $modelPath = BASE_PATH. '/model/'. $class_name. '.php';
    $configPath = BASE_PATH. '/config/'. $class_name. '.php';
// inclusion du fichier de classe si il existe
    if (file_exists($controllerPath)) {
        require_once $controllerPath;
    } elseif (file_exists($modelPath)) {
        require_once $modelPath;
    } elseif (file_exists($configPath)) {
        require_once $configPath;
    }
});

$routes = include BASE_PATH . '/config/routes.php'; // chargement du fichier routes.php
// récupération du contrôleur et de l'action depuis l'URL avec des valeurs par défaut
$controller = isset($_GET['controller']) ? $_GET['controller'] : 'home';
$action = isset($_GET['action']) ? $_GET['action'] : 'home';
// construction des noms de la classe du contrôleur et de la méthode à appeler
$controllerName = ucfirst($controller) . "Controller";
$actionName = $action . "Action";

//echo "controller: $controllerName <br>";
//echo "action: $actionName <br>";

// chemin vers le fichier du contrôleur
$controllerFilePath = BASE_PATH . '/controller/' . $controllerName . '.php';
if (file_exists($controllerFilePath)) {
    // création d'une instance du contrôleur
    $controllerInstance = new $controllerName();
// appel de l'action si elle existe sinon une exception est levée
    if (method_exists($controllerInstance, $actionName)) {
        $controllerInstance->$actionName();
    } else {
        throw new Exception('Action non trouvée !');
    }
} else {
    // le contrôleur n'existe pas une exception est levée
    throw new Exception('Controller non trouvé !');
}
// nom de la page à inclure
$page = 'index';
// inclusion du fichier de vue
require BASE_PATH . '/View/' . $page . '.phtml';

$dsn = "mysql:host=localhost;dbname=Figarauto;charset=utf8";
$pdo = new PDO($dsn, 'root', '');

$adminModel = new AdminModel($pdo);
$adminModel->createAdminUser('steven.martin@espaces-atypiques.com', 'FiG_arAu!to*20-24');
?>