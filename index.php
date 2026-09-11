<?php
require_once 'conexion.php';

// CONFIGURA AQUÍ TU NÚMERO DE WHATSAPP (Incluye código de país, ej: 504XXXXXXXX)
$telefono_whatsapp = "50433474590"; 

try {
    $stmt = $pdo->query("SELECT * FROM productos ORDER BY id DESC");
    $productos = $stmt->fetchAll();
} catch (PDOException $e) {
    echo "Error en la consulta: " . $e->getMessage();
    $productos = [];
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Perla's Shop | Tienda en Línea</title>
    <!-- Fuentes e Iconos -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

    <style>
        :root {
            --primary-color: #e84393;
            --secondary-color: #fd79a8;
            --dark-color: #2d3436;
            --light-bg: #f9f9f9;
            --white: #ffffff;
            --gray: #636e72;
            --success: #00b894;
            --danger: #d63031;
            --whatsapp-color: #25D366;
            --whatsapp-hover: #1eb855;
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: 'Poppins', sans-serif;
        }

        body {
            background-color: var(--light-bg);
            color: var(--dark-color);
        }

        /* --- Header & Nav --- */
        header {
            background-color: var(--white);
            box-shadow: 0 2px 10px rgba(0,0,0,0.05);
            position: sticky;
            top: 0;
            z-index: 100;
        }

        .navbar {
            max-width: 1200px;
            margin: 0 auto;
            padding: 1rem 2rem;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .logo {
            font-size: 1.8rem;
            font-weight: 700;
            color: var(--primary-color);
            text-decoration: none;
        }

        .logo span {
            color: var(--dark-color);
        }

        .nav-links {
            list-style: none;
            display: flex;
            gap: 1.5rem;
        }

        .nav-links a {
            text-decoration: none;
            color: var(--dark-color);
            font-weight: 500;
            transition: color 0.3s;
        }

        .nav-links a:hover {
            color: var(--primary-color);
        }

        .nav-icons {
            display: flex;
            gap: 1rem;
            align-items: center;
        }

        .icon-btn {
            background: none;
            border: none;
            font-size: 1.2rem;
            color: var(--dark-color);
            cursor: pointer;
            text-decoration: none;
        }

        /* --- Hero Banner --- */
        .hero {
            background: linear-gradient(135deg, var(--secondary-color), var(--primary-color));
            color: var(--white);
            text-align: center;
            padding: 4rem 2rem;
        }

        .hero h1 {
            font-size: 2.5rem;
            margin-bottom: 0.5rem;
        }

        .hero p {
            font-size: 1.1rem;
            opacity: 0.9;
        }

        /* --- Contenedor Principal --- */
        .container {
            max-width: 1200px;
            margin: 2rem auto;
            padding: 0 2rem;
        }

        .section-title {
            text-align: center;
            margin-bottom: 2rem;
            font-size: 1.8rem;
            color: var(--dark-color);
        }

        /* --- Grid de Productos --- */
        .products-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(250px, 1fr));
            gap: 2rem;
        }

        .product-card {
            background: var(--white);
            border-radius: 12px;
            overflow: hidden;
            box-shadow: 0 4px 15px rgba(0,0,0,0.05);
            transition: transform 0.3s, box-shadow 0.3s;
            display: flex;
            flex-direction: column;
            cursor: pointer;
        }

        .product-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 8px 25px rgba(0,0,0,0.1);
        }

        .product-img {
            width: 100%;
            height: 220px;
            object-fit: cover;
            background-color: #f1f1f1;
        }

        .product-info {
            padding: 1.2rem;
            display: flex;
            flex-direction: column;
            flex-grow: 1;
        }

        .product-title {
            font-size: 1.1rem;
            font-weight: 600;
            margin-bottom: 0.5rem;
        }

        .product-price {
            font-size: 1.2rem;
            font-weight: 700;
            color: var(--primary-color);
            margin-bottom: 0.5rem;
        }

        .product-stock {
            font-size: 0.85rem;
            margin-bottom: 1rem;
            display: inline-block;
            padding: 0.2rem 0.5rem;
            border-radius: 4px;
            width: fit-content;
        }

        .stock-available {
            background-color: #e6fffa;
            color: var(--success);
        }

        .stock-out {
            background-color: #ffe3e3;
            color: var(--danger);
        }

        /* Estilo Botón WhatsApp */
        .btn-whatsapp {
            margin-top: auto;
            background-color: var(--whatsapp-color);
            color: var(--white);
            border: none;
            padding: 0.7rem;
            border-radius: 6px;
            font-weight: 600;
            cursor: pointer;
            width: 100%;
            transition: background 0.3s;
            display: flex;
            justify-content: center;
            align-items: center;
            gap: 0.5rem;
            text-decoration: none;
            font-size: 0.95rem;
        }

        .btn-whatsapp:hover {
            background-color: var(--whatsapp-hover);
            color: var(--white);
        }

        .btn-whatsapp.disabled {
            background-color: #ccc;
            cursor: not-allowed;
            pointer-events: none;
        }

        /* --- Ventana Modal --- */
        .modal {
            display: none;
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background-color: rgba(0, 0, 0, 0.6);
            z-index: 1000;
            justify-content: center;
            align-items: center;
            padding: 1rem;
        }

        .modal-content {
            background-color: var(--white);
            border-radius: 12px;
            max-width: 600px;
            width: 100%;
            overflow: hidden;
            box-shadow: 0 5px 25px rgba(0,0,0,0.2);
            position: relative;
            animation: fadeIn 0.3s ease-in-out;
        }

        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(-20px); }
            to { opacity: 1; transform: translateY(0); }
        }

        .close-modal {
            position: absolute;
            top: 15px;
            right: 20px;
            font-size: 1.8rem;
            font-weight: bold;
            color: var(--gray);
            cursor: pointer;
            transition: color 0.2s;
        }

        .close-modal:hover {
            color: var(--danger);
        }

        .modal-body {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 1.5rem;
            padding: 2rem;
        }

        @media (max-width: 600px) {
            .modal-body {
                grid-template-columns: 1fr;
            }
        }

        .modal-img {
            width: 100%;
            height: 250px;
            object-fit: cover;
            border-radius: 8px;
        }

        .modal-details {
            display: flex;
            flex-direction: column;
        }

        .modal-details h2 {
            font-size: 1.4rem;
            margin-bottom: 0.5rem;
        }

        .modal-desc-title {
            font-weight: 600;
            margin-top: 1rem;
            font-size: 0.95rem;
        }

        .modal-desc {
            font-size: 0.9rem;
            color: var(--gray);
            margin-top: 0.3rem;
            line-height: 1.4;
            max-height: 120px;
            overflow-y: auto;
        }

        /* --- Footer --- */
        footer {
            background-color: var(--dark-color);
            color: var(--white);
            text-align: center;
            padding: 2rem;
            margin-top: 4rem;
        }
    </style>
</head>
<body>

    <!-- Navegación -->
    <header>
        <nav class="navbar">
            <a href="index.php" class="logo">Perla's <span>Shop</span></a>
            <ul class="nav-links">
                <li><a href="index.php">Inicio</a></li>
                <li><a href="#productos">Productos</a></li>
            </ul>
            <div class="nav-icons">
                <a href="admin/index.php" title="Panel Administrador" class="icon-btn"><i class="fa-solid fa-user-gear"></i></a>
            </div>
        </nav>
    </header>

    <!-- Banner Principal -->
    <section class="hero">
        <h1>Bienvenidos a Perla's Shop</h1>
        <p>Encuentra los mejores productos al mejor precio</p>
    </section>

    <!-- Sección de Productos -->
    <main class="container" id="productos">
        <h2 class="section-title">Nuestros Productos</h2>

        <div class="products-grid">
            <?php if (!empty($productos)): ?>
                <?php foreach ($productos as $producto): ?>
                    <?php 
                        $rutaImagen = "assets/img/" . $producto['imagen'];
                        if (!file_exists($rutaImagen) || empty($producto['imagen'])) {
                            $rutaImagen = "https://via.placeholder.com/300x220?text=Perlas+Shop";
                        }

                        // Construir mensaje dinámico de WhatsApp
                        $mensajeWS = "Hola Perla's Shop, me interesa comprar *" . $producto['nombre'] . "* por un precio de L. " . number_format($producto['precio'], 2) . ". ¿Tienen disponible?";
                        $urlWhatsApp = "https://wa.me/" . $telefono_whatsapp . "?text=" . urlencode($mensajeWS);
                    ?>
                    
                    <div class="product-card"
                         data-nombre="<?php echo htmlspecialchars($producto['nombre']); ?>"
                         data-precio="<?php echo number_format($producto['precio'], 2); ?>"
                         data-stock="<?php echo $producto['stock']; ?>"
                         data-descripcion="<?php echo htmlspecialchars($producto['descripcion']); ?>"
                         data-imagen="<?php echo $rutaImagen; ?>"
                         data-urlws="<?php echo $urlWhatsApp; ?>"
                         onclick="verDetalles(this)">

                        <img src="<?php echo $rutaImagen; ?>" alt="<?php echo htmlspecialchars($producto['nombre']); ?>" class="product-img">
                        <div class="product-info">
                            <h3 class="product-title"><?php echo htmlspecialchars($producto['nombre']); ?></h3>
                            <div class="product-price">L. <?php echo number_format($producto['precio'], 2); ?></div>
                            
                            <?php if ($producto['stock'] > 0): ?>
                                <span class="product-stock stock-available">
                                    Stock: <?php echo $producto['stock']; ?> disponibles
                                </span>
                                <a href="<?php echo $urlWhatsApp; ?>" target="_blank" class="btn-whatsapp" onclick="event.stopPropagation();">
                                    <i class="fa-brands fa-whatsapp"></i> Comprar por WhatsApp
                                </a>
                            <?php else: ?>
                                <span class="product-stock stock-out">Agotado</span>
                                <a href="#" class="btn-whatsapp disabled">
                                    <i class="fa-solid fa-ban"></i> Sin Stock
                                </a>
                            <?php endif; ?>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php else: ?>
                <p style="grid-column: 1 / -1; text-align: center;">No hay productos disponibles por el momento.</p>
            <?php endif; ?>
        </div>
    </main>

    <!-- Ventana Modal -->
    <div id="modalProducto" class="modal">
        <div class="modal-content">
            <span class="close-modal" onclick="cerrarModal()">&times;</span>
            <div class="modal-body">
                <img id="modalImg" src="" alt="Producto" class="modal-img">
                <div class="modal-details">
                    <h2 id="modalNombre"></h2>
                    <div id="modalPrecio" class="product-price"></div>
                    <span id="modalStock" class="product-stock"></span>
                    
                    <p class="modal-desc-title">Descripción:</p>
                    <p id="modalDescripcion" class="modal-desc"></p>
                    
                    <a id="modalBtnWhatsApp" href="#" target="_blank" class="btn-whatsapp" style="margin-top: 1.5rem;">
                        <i class="fa-brands fa-whatsapp"></i> Comprar por WhatsApp
                    </a>
                </div>
            </div>
        </div>
    </div>

    <!-- Footer -->
    <footer>
        <p>&copy; <?php echo date('Y'); ?> Perla's Shop. Todos los derechos reservados.</p>
    </footer>

    <script>
        function verDetalles(card) {
            const nombre = card.getAttribute('data-nombre');
            const precio = card.getAttribute('data-precio');
            const stock = parseInt(card.getAttribute('data-stock'));
            const descripcion = card.getAttribute('data-descripcion');
            const imagen = card.getAttribute('data-imagen');
            const urlWS = card.getAttribute('data-urlws');

            document.getElementById('modalNombre').innerText = nombre;
            document.getElementById('modalPrecio').innerText = 'L. ' + precio;
            document.getElementById('modalDescripcion').innerText = descripcion ? descripcion : 'Este producto no cuenta con una descripción detallada por el momento.';
            document.getElementById('modalImg').src = imagen;

            const stockTag = document.getElementById('modalStock');
            const btnWS = document.getElementById('modalBtnWhatsApp');

            if (stock > 0) {
                stockTag.innerText = 'Stock: ' + stock + ' disponibles';
                stockTag.className = 'product-stock stock-available';
                btnWS.className = 'btn-whatsapp';
                btnWS.href = urlWS;
                btnWS.innerHTML = '<i class="fa-brands fa-whatsapp"></i> Comprar por WhatsApp';
            } else {
                stockTag.innerText = 'Agotado';
                stockTag.className = 'product-stock stock-out';
                btnWS.className = 'btn-whatsapp disabled';
                btnWS.href = '#';
                btnWS.innerHTML = '<i class="fa-solid fa-ban"></i> Sin Stock';
            }

            document.getElementById('modalProducto').style.display = 'flex';
        }

        function cerrarModal() {
            document.getElementById('modalProducto').style.display = 'none';
        }

        window.onclick = function(event) {
            const modal = document.getElementById('modalProducto');
            if (event.target === modal) {
                modal.style.display = 'none';
            }
        }
    </script>

</body>
</html>