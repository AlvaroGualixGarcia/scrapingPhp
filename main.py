from selenium import webdriver
from selenium.webdriver.chrome.service import Service
from selenium.webdriver.chrome.options import Options
from webdriver_manager.chrome import ChromeDriverManager
from selenium.webdriver.common.by import By
from selenium.webdriver.support.ui import WebDriverWait
from selenium.webdriver.support import expected_conditions as EC
from bs4 import BeautifulSoup

def obtenerContenido(url):
    chrome_options = Options()
    # chrome_options.add_argument("--headless")  # Descomentar para ejecutar sin abrir ventana
    chrome_options.add_argument("--no-sandbox")
    chrome_options.add_argument("--disable-dev-shm-usage")
    chrome_options.add_argument("user-agent=Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/91.0.4472.124 Safari/537.36")

    driver = webdriver.Chrome(service=Service(ChromeDriverManager().install()), options=chrome_options)

    try:
        print(f"Obteniendo contenido de la URL: {url}")
        driver.get(url)

        # Esperar a que el contenido se cargue
        WebDriverWait(driver, 10).until(
            EC.presence_of_all_elements_located((By.TAG_NAME, 'body'))
        )

        html = driver.page_source
        return html
    except Exception as e:
        print(f"Ocurrió un error al obtener el contenido: {str(e)}")
        return None
    finally:
        driver.quit()

def analizarVividSeats(url):
    html = obtenerContenido(url)

    if html is None:
        print("No se pudo obtener el contenido de la URL.")
        return

    # Crear un objeto BeautifulSoup y cargar el HTML
    soup = BeautifulSoup(html, 'html.parser')

    # Encontrar todas las entradas
    listings = soup.find_all('a', class_='styles_linkNoStyle__bZgvi')

    if not listings:
        print("No se encontraron listados.")
        return

    print("<h2>Entradas disponibles en VividSeats</h2>")
    print("<table border='1'><tr><th>Categoría</th><th>Fila</th><th>Precio</th></tr>")

    for listing in listings:
        # Extraer la categoría
        categoria = listing.find('div', class_='MuiTypography-root MuiTypography-small-medium styles_nowrap___p2Eb')
        categoria_value = categoria.text.strip() if categoria else "Desconocido"

        # Extraer la fila
        fila = listing.find('span', {'data-testid': 'row'})
        fila_value = fila.text.strip() if fila else "Desconocido"

        # Extraer el precio
        precio = listing.find('span', {'data-testid': 'listing-price'})
        precio_value = precio.text.strip() if precio else "Desconocido"

        # Imprimir la información en la tabla
        print("<tr>")
        print(f"<td>{categoria_value}</td>")
        print(f"<td>{fila_value}</td>")
        print(f"<td>{precio_value}</td>")
        print("</tr>")

    print("</table>")

# Comprobar si hay un parámetro de URL
if __name__ == "__main__":
    url = "https://www.vividseats.com/real-madrid-tickets-estadio-santiago-bernabeu-12-22-2024--sports-soccer/production/5045935"
    analizarVividSeats(url)
