<?php

// Cargar el autoload de Composer para incluir las dependencias necesarias
require_once('vendor/autoload.php');

// Importar las clases necesarias del paquete de WebDriver
use Facebook\WebDriver\Remote\RemoteWebDriver;
use Facebook\WebDriver\Remote\DesiredCapabilities;
use Facebook\WebDriver\WebDriverBy;
use Facebook\WebDriver\WebDriverExpectedCondition;
use Facebook\WebDriver\WebDriverWait;
use Facebook\WebDriver\WebDriverKeys;

// Cambia esto por la URL de tu servidor Selenium
$host = 'http://localhost:4444'; // URL del servidor Selenium, asegúrate de que este puerto sea correcto

// Configuración de capacidades para el navegador Chrome
$capabilities = DesiredCapabilities::chrome(); // Establecer las capacidades del navegador
$capabilities->setCapability('chromeOptions', ['args' => ['--start-maximized', '--disable-popup-blocking']]); // Opciones para Chrome: maximizar ventana y desactivar bloqueador de popups

// Crear una instancia de WebDriver con las capacidades especificadas
$driver = RemoteWebDriver::create($host, $capabilities);

// Inicializar la espera, configurando un tiempo máximo de espera de 20 segundos
$wait = new WebDriverWait($driver, 20); // Aumentar el tiempo de espera general

try {
    // 1. Abrir Google
    echo "Abriendo Google...\n";
    $driver->get('https://www.google.com'); // Navegar a la página de Google

    // 2. Esperar a que el botón de aceptar cookies aparezca
    echo "Esperando a que aparezca el botón de aceptar cookies...\n";
    // Esperar hasta que el botón de aceptar cookies sea clickeable
    $acceptCookiesButton = $wait->until(WebDriverExpectedCondition::elementToBeClickable(WebDriverBy::id('L2AGLb')));

    // 3. Comprobar si el botón de aceptación de cookies está presente y hacer clic
    if ($acceptCookiesButton) {
        echo "Aceptando las cookies...\n";
        // Haciendo clic en el botón de aceptar cookies
        $acceptCookiesButton->click();
    } else {
        echo "No se encontró el botón de aceptación de cookies.\n"; // Mensaje si el botón no se encuentra
    }

    // 4. Esperar un poco más para asegurar que la página se cargue
    sleep(3); // Esperar 3 segundos para permitir la carga completa de la página

    // 5. Esperar a que el campo de búsqueda sea visible
    echo "Esperando que el campo de búsqueda esté visible...\n";
    // Esperar hasta que el campo de búsqueda (input) sea visible
    $searchBox = $wait->until(WebDriverExpectedCondition::visibilityOfElementLocated(WebDriverBy::name('q')));

    // 6. Desplazarse al campo de búsqueda para asegurarse de que está en vista
    echo "Desplazándose al campo de búsqueda...\n";
    // Usar JavaScript para desplazar el campo de búsqueda a la vista
    $driver->executeScript("arguments[0].scrollIntoView(true);", [$searchBox]);

    // 7. Hacer clic en el campo de búsqueda
    echo "Haciendo clic en el campo de búsqueda...\n";
    // Usar JavaScript para hacer clic en el campo de búsqueda
    $driver->executeScript("arguments[0].click();", [$searchBox]);

    // 8. Limpiar el campo de búsqueda de manera más explícita
    echo "Limpiando el campo de búsqueda...\n";
    // Limpiar el campo de búsqueda usando JavaScript
    $driver->executeScript("arguments[0].value = '';", [$searchBox]); 

    // 9. Enviar texto al campo de búsqueda
    echo "Enviando texto al campo de búsqueda...\n";
    // Usar sendKeys para enviar texto seguido de Enter
    $searchBox->sendKeys('Selenium WebDriver' . WebDriverKeys::ENTER); // Enviar la tecla "Enter"

    // 10. Aumentar el tiempo de espera para los resultados
    echo "Esperando a que los resultados de búsqueda estén visibles...\n";
    sleep(5); // Esperar 5 segundos para permitir que los resultados se carguen

    // 11. Intentar localizar los resultados de búsqueda usando un selector alternativo
    echo "Esperando a que los resultados de búsqueda estén disponibles...\n";
    // Esperar hasta que los resultados sean visibles en la página
    $results = $wait->until(WebDriverExpectedCondition::visibilityOfElementLocated(WebDriverBy::cssSelector('div#search')));

    // 12. Verificar la presencia de resultados
    $resultsList = $driver->findElements(WebDriverBy::cssSelector('div#search div.g')); // Localizar los elementos de resultados de búsqueda

    // 13. Comprobar si se encontraron resultados
    if (count($resultsList) > 0) {
        echo "Resultados de búsqueda encontrados.\n"; // Mensaje si se encontraron resultados
    } else {
        echo "No se encontraron resultados.\n"; // Mensaje si no se encontraron resultados
    }

} catch (Exception $e) {
    // Manejo de errores
    echo "Ocurrió un error: " . $e->getMessage() . "\n"; // Imprimir el mensaje de error
    // Captura de pantalla para depuración
    $driver->takeScreenshot('screenshot.png'); // Tomar una captura de pantalla
    echo "Se ha guardado una captura de pantalla como 'screenshot.png'.\n"; // Mensaje de éxito de captura de pantalla
} finally {
    $driver->quit(); // Cerrar el navegador al final del script
}

?>
