<!--Comandos e instalaciones previas -->
<!--instalar dependencias -->
<!--composer require php-webdriver/webdriver-->
<!--https://www.selenium.dev/downloads/-->
<!--java -jar selenium-server-standalone-x.xx.x.jar-->
<!--export PATH=$PATH:/ruta/donde/esta/chromedriver-->
<!--php index.php-->
<!---->



<?php
// Cargar las dependencias de Composer
require_once('vendor/autoload.php');

// Importar las clases necesarias de Selenium WebDriver
use Facebook\WebDriver\Remote\RemoteWebDriver;
use Facebook\WebDriver\Remote\DesiredCapabilities;
use Facebook\WebDriver\WebDriverBy;
use Facebook\WebDriver\WebDriverExpectedCondition;
use Facebook\WebDriver\WebDriverWait;
use Facebook\WebDriver\Interactions\Actions;

// Definir la URL del servidor Selenium local
$host = 'http://localhost:4444';

// Configurar el navegador Chrome con opciones específicas
$capabilities = DesiredCapabilities::chrome();
$capabilities->setCapability('chromeOptions', ['args' => ['--start-maximized', '--disable-popup-blocking']]);

// Crear una instancia de WebDriver con las capacidades especificadas
$driver = RemoteWebDriver::create($host, $capabilities);

// URL de la página a la que se desea navegar
$url = "https://www.vividseats.com/real-madrid-tickets-estadio-santiago-bernabeu-12-22-2024--sports-soccer/production/5045935";

// Navegar a la URL especificada
$driver->get($url);

// Inicializar una espera explícita de hasta 30 segundos
$wait = new WebDriverWait($driver, 30);

try {
    // Esperar a que el botón de CAPTCHA esté presente en la página
    $wait->until(WebDriverExpectedCondition::presenceOfElementLocated(
        WebDriverBy::cssSelector('.SGLNzoiRnFzPfmn[aria-label="Pulsar y mantener pulsado"]')
    ));

    // Localizar el botón de CAPTCHA usando un selector CSS
    $button = $driver->findElement(WebDriverBy::cssSelector('.SGLNzoiRnFzPfmn[aria-label="Pulsar y mantener pulsado"]'));
    
    // Verificar que el botón esté visible y habilitado antes de intentar interactuar con él
    if ($button->isDisplayed() && $button->isEnabled()) {
        // Imprimir un mensaje indicando que se va a mantener pulsado el botón
        echo "Manteniendo pulsado el botón...\n";

        // Crear una nueva instancia de Actions para realizar una secuencia de acciones avanzadas
        $actions = new Actions($driver);
        $actions->moveToElement($button) // Mover el ratón al botón
                ->clickAndHold($button) // Mantener pulsado el botón
                ->pause(10000) // Mantenerlo pulsado durante 10 segundos (ajusta si es necesario)
                ->release() // Soltar el botón
                ->perform(); // Ejecutar la secuencia de acciones

        // Esperar un tiempo adicional para asegurar que el CAPTCHA se procesa correctamente
        sleep(15); // Pausa adicional de 15 segundos
    } else {
        // Imprimir un mensaje si el botón no está habilitado o visible
        echo "El botón no está habilitado o no es visible.\n";
    }

    // Localizar y obtener las categorías de entradas disponibles en la página
    $categorias = $driver->findElements(WebDriverBy::cssSelector('.MuiTypography-root.styles_nowrap___p2Eb'));
    
    // Localizar y obtener las filas de cada entrada
    $filas = $driver->findElements(WebDriverBy::cssSelector('[data-testid="row"]'));

    // Localizar y obtener los precios de cada entrada
    $precios = $driver->findElements(WebDriverBy::cssSelector('.MuiTypography-root[data-testid="listing-price"]'));

    // Imprimir el encabezado HTML de la tabla de resultados
    echo "<h2>Entradas disponibles en VividSeats</h2>";
    echo "<table border='1'><tr><th>Categoría</th><th>Fila</th><th>Precio</th></tr>";

    // Determinar la cantidad mínima de entradas disponibles para evitar errores de índice
    $numEntradas = min(count($categorias), count($filas), count($precios));

    // Recorrer y mostrar los detalles de cada entrada
    for ($i = 0; $i < $numEntradas; $i++) {
        // Obtener y limpiar el texto de la categoría, fila y precio
        $categoria = trim($categorias[$i]->getText());
        $fila = trim($filas[$i]->getText());
        $precio = trim($precios[$i]->getText());

        // Filtrar para evitar mostrar entradas vacías
        if (!empty($categoria) && !empty($fila) && !empty($precio)) {
            // Normalizar espacios adicionales en las categorías y filas
            $categoria = preg_replace('/\s+/', ' ', $categoria);
            $fila = preg_replace('/\s+/', ' ', $fila);

            // Imprimir cada entrada como una fila en la tabla HTML
            echo "<tr>";
            echo "<td>" . htmlspecialchars($categoria) . "</td>"; // Escapar caracteres especiales en categoría
            echo "<td>" . htmlspecialchars($fila) . "</td>"; // Escapar caracteres especiales en fila
            echo "<td>" . htmlspecialchars($precio) . "</td>"; // Escapar caracteres especiales en precio
            echo "</tr>";
        }
    }

    // Cerrar la tabla HTML
    echo "</table>";

} catch (Exception $e) {
    // En caso de error, imprimir el mensaje de excepción
    echo "Ocurrió un error: " . $e->getMessage() . "\n";
} finally {
    // Esperar un tiempo adicional antes de cerrar el navegador (opcional)
    sleep(10);

    // Cerrar el navegador al finalizar el script
    $driver->quit();
}
?>
