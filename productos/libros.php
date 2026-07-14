<?php
session_start();

// 1. Incluimos los archivos del DAO y el Modelo
require_once __DIR__ . '/../models/Productos.php';
require_once __DIR__ . '/../DAO/ProductoDAO.php';
require_once __DIR__ . '/../models/genero.php';
require_once __DIR__ . '/../DAO/generoDAO.php';
require_once __DIR__ . '/../conexion.php';
require_once __DIR__ . '/../DAO/CarruselDAO.php';

// Conexión centralizada
$conexion = Conexion::conectar();

// 2. Instanciamos los DAO pasándoles la conexión
$productoDAO = new ProductoDAO($conexion);
$generoDAO   = new GeneroDAO($conexion);

// 3. API PARA CARRUSEL - Obtener libros del carrusel
if (isset($_GET['api']) && $_GET['api'] === 'carrusel') {
    header('Content-Type: application/json');
    
    $carruselDAO = new CarruselDAO($conexion);
    $libros = $carruselDAO->obtenerLibrosCarrusel();
    
    echo json_encode(['libros' => $libros]);
    exit;
}

// 4. Capturamos los filtros de la URL
$busqueda = isset($_GET['q']) ? trim($_GET['q']) : '';
$id_genero = isset($_GET['genero']) ? intval($_GET['genero']) : 0;

// Obtener nombre del género para el encabezado
$nombre_genero = "Todos los libros";
if ($id_genero > 0) {
    $genero = $generoDAO->getById($id_genero);
    if ($genero !== null) {
        $nombre_genero = $genero->getNombreGenero();
    }
}

// 5. Traemos la lista de objetos Producto
$lista_libros = $productoDAO->buscarYFiltrar($busqueda, $id_genero);

// Obtener todos los géneros para el menú lateral
$generos = $generoDAO->getAll();
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($nombre_genero) ?> — Bookstore</title>
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@400;700;900&family=Lato:wght@300;400;700&display=swap" rel="stylesheet">
    <style>
        :root {
            --green-deep:   #1a3d2b;
            --green-mid:    #2d6a4f;
            --green-accent: #52b788;
            --green-light:  #b7e4c7;
            --green-pale:   #d8f3dc;
            --cream:        #f8f5ef;
            --gold:         #c9a84c;
            --text-dark:    #1a2e1e;
        }

        * { margin: 0; padding: 0; box-sizing: border-box; }

        body {
            font-family: 'Lato', sans-serif;
            background-color: var(--cream);
            color: var(--text-dark);
            min-height: 100vh;
        }

        /* ── HEADER ORGANIZADO ── */
        header {
            background: var(--green-deep);
            padding: 0.8rem 3rem;
            display: flex;
            justify-content: space-between;
            align-items: center;
            position: sticky;
            top: 0;
            z-index: 1000;
            border-bottom: 3px solid var(--gold);
            gap: 20px;
        }

        .logo {
            flex-shrink: 0;
            display: flex;
            flex-direction: column;
            text-decoration: none;
        }
        .logo-main {
            font-family: 'Playfair Display', serif;
            font-size: 1.8rem;
            font-weight: 900;
            color: var(--cream);
            letter-spacing: 1px;
        }
        .logo-sub {
            font-size: 0.6rem;
            letter-spacing: 4px;
            color: var(--gold);
            text-transform: uppercase;
        }

        .search-container {
            flex: 1;
            display: flex;
            justify-content: center;
            max-width: 550px;
        }

        .search-box {
            display: flex;
            align-items: center;
            background: rgba(255, 255, 255, 0.15);
            border: 1.5px solid var(--green-accent);
            border-radius: 50px;
            padding: 2px 5px 2px 18px;
            width: 100%;
            transition: all 0.3s ease;
        }

        .search-box:focus-within {
            background: white;
            border-color: var(--gold);
            box-shadow: 0 0 12px rgba(201, 168, 76, 0.3);
        }

        .search-box input {
            background: transparent;
            border: none;
            outline: none;
            color: var(--cream);
            width: 100%;
            padding: 8px 0;
            font-size: 0.95rem;
        }

        .search-box:focus-within input { color: var(--text-dark); }

        .search-box button {
            background: var(--gold);
            border: none;
            width: 34px;
            height: 34px;
            border-radius: 50%;
            cursor: pointer;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .header-right {
            display: flex;
            align-items: center;
            gap: 1.2rem;
            flex-shrink: 0;
        }

        .login-btn, .nav-btn {
            background: transparent;
            color: var(--cream);
            border: 1.5px solid var(--green-accent);
            padding: 0.5rem 1.2rem;
            border-radius: 3px;
            font-size: 0.8rem;
            text-transform: uppercase;
            cursor: pointer;
            text-decoration: none;
            transition: 0.3s;
        }
        .login-btn:hover, .nav-btn:hover { background: var(--green-accent); color: var(--green-deep); }

        .cart-container { position: relative; cursor: pointer; }
        #carrito { width: 30px; filter: invert(1); }
        .cart-counter {
            position: absolute; top: -5px; right: -5px;
            background: var(--gold); color: var(--green-deep);
            border-radius: 50%; width: 18px; height: 18px;
            display: flex; align-items: center; justify-content: center;
            font-size: 0.7rem; font-weight: bold;
        }

        /* ── RESTO DEL DISEÑO ── */
        .genero-hero {
            background: linear-gradient(135deg, var(--green-deep) 0%, var(--green-mid) 100%);
            padding: 2rem 3rem;
            color: var(--cream);
        }
        .hero-title { font-family: 'Playfair Display', serif; font-size: 2rem; }
        .hero-line { width: 50px; height: 3px; background: var(--gold); margin: 0.8rem 0; }

        .page-wrapper {
            max-width: 1300px; margin: 0 auto; padding: 2rem;
            display: grid; grid-template-columns: 240px 1fr; gap: 2rem;
        }

        .genero-list { list-style: none; margin-top: 1rem; }
        .genero-list li a {
            display: block; padding: 0.8rem; text-decoration: none; color: var(--text-dark);
            border-radius: 5px; transition: 0.3s;
        }
        .genero-list li a:hover, .genero-list li a.active {
            background: var(--green-pale); border-left: 4px solid var(--gold);
        }

        .books-grid {
            display: grid; grid-template-columns: repeat(auto-fill, minmax(210px, 1fr)); gap: 1.5rem;
        }
        .book-card {
            background: white; border-radius: 8px; overflow: hidden;
            box-shadow: 0 4px 10px rgba(0,0,0,0.05); display: flex; flex-direction: column;
        }
        .book-img-wrap { height: 250px; background: #eee; }
        .book-img-wrap img { width: 100%; height: 100%; object-fit: cover; }
        .book-body { padding: 1rem; flex: 1; display: flex; flex-direction: column; }
        .book-title { font-family: 'Playfair Display', serif; font-size: 1rem; margin-bottom: 5px; }
        .book-author { font-size: 0.8rem; color: #666; margin-bottom: 10px; }

        /* ── ZONA DE PRECIO Y BOTÓN — apiladas para que el botón nunca se achique ── */
        .book-footer {
            margin-top: auto;
            padding-top: 15px;
        }
        .book-price {
            display: block;
            font-weight: bold;
            color: var(--green-mid);
            font-size: 1.15rem;
            margin-bottom: 10px;
        }

        .view-desc-btn {
            width: 100%;
            background: var(--green-deep);
            color: white;
            border: none;
            padding: 10px 12px;
            border-radius: 6px;
            cursor: pointer;
            font-size: 0.9rem;
            font-weight: 600;
            white-space: nowrap;
            transition: 0.3s;
        }

        .view-desc-btn:hover {
            background: var(--green-mid);
        }

        .cart-modal { display: none; position: fixed; top: 0; right: 0; width: 350px; height: 100vh; background: white; z-index: 2000; box-shadow: -5px 0 15px rgba(0,0,0,0.1); flex-direction: column; }
        .cart-modal.open { display: flex; }
        .toast { position: fixed; bottom: 20px; left: 50%; transform: translateX(-50%); background: var(--green-mid); color: white; padding: 10px 20px; border-radius: 50px; display: none; z-index: 9999; }

        /* ── MODAL DESCRIPCIÓN (OVERLAY) ── */
        .description-overlay {
            display: none;
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0, 0, 0, 0.75);
            z-index: 4000;
            justify-content: center;
            align-items: center;
            padding: 20px;
            animation: fadeIn 0.3s ease;
        }

        .description-overlay.open {
            display: flex;
        }

        @keyframes fadeIn {
            from { opacity: 0; }
            to { opacity: 1; }
        }

        .description-modal-content {
            background: white;
            border-radius: 12px;
            display: flex;
            max-width: 700px;
            width: 100%;
            max-height: 90vh;
            overflow-y: auto;
            box-shadow: 0 10px 40px rgba(0, 0, 0, 0.3);
            animation: slideUp 0.4s cubic-bezier(0.25, 1, 0.5, 1);
        }

        @keyframes slideUp {
            from { opacity: 0; transform: translateY(40px); }
            to { opacity: 1; transform: translateY(0); }
        }

        /* ── CARRUSEL DE IMÁGENES (tapa / contratapa / perspectiva) ── */
        .description-modal-carousel {
            flex-shrink: 0;
            width: 280px;
            height: 380px;
            border-radius: 12px 0 0 12px;
            background: #eee;
            position: relative;
            overflow: hidden;
        }

        .carousel-container {
            position: relative;
            width: 100%;
            height: 100%;
            overflow: hidden;
        }

        .carousel-slide {
            position: absolute;
            top: 0; left: 0;
            width: 100%;
            height: 100%;
            opacity: 0;
            transition: opacity 0.4s ease;
        }

        .carousel-slide.active {
            opacity: 1;
        }

        .carousel-slide img {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }

        .carousel-nav {
            position: absolute;
            bottom: 10px;
            left: 50%;
            transform: translateX(-50%);
            display: flex;
            gap: 8px;
            z-index: 10;
        }

        .carousel-dot {
            width: 8px;
            height: 8px;
            border-radius: 50%;
            background: rgba(255, 255, 255, 0.5);
            cursor: pointer;
            transition: 0.3s;
            border: none;
            padding: 0;
        }

        .carousel-dot.active {
            background: white;
            transform: scale(1.3);
        }

        .carousel-btn {
            position: absolute;
            top: 50%;
            transform: translateY(-50%);
            background: rgba(0, 0, 0, 0.4);
            color: white;
            border: none;
            width: 34px;
            height: 34px;
            border-radius: 50%;
            cursor: pointer;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.1rem;
            z-index: 10;
            transition: background 0.3s;
        }

        .carousel-btn:hover { background: rgba(0, 0, 0, 0.7); }
        .carousel-btn.prev { left: 10px; }
        .carousel-btn.next { right: 10px; }

        .description-modal-info {
            flex: 1;
            padding: 2rem;
            display: flex;
            flex-direction: column;
            gap: 15px;
        }

        .description-modal-title {
            font-family: 'Playfair Display', serif;
            font-size: 1.8rem;
            color: var(--green-deep);
            margin: 0;
        }

        .description-modal-author {
            font-size: 1rem;
            color: var(--green-mid);
            font-weight: 600;
            margin: 0;
        }

        .description-modal-price {
            font-size: 1.4rem;
            font-weight: bold;
            color: var(--green-mid);
        }

        .description-modal-description {
            color: #555;
            line-height: 1.6;
            flex: 1;
            border-top: 1px solid var(--green-pale);
            border-bottom: 1px solid var(--green-pale);
            padding: 1rem 0;
        }

        .description-modal-actions {
            display: flex;
            gap: 10px;
        }

        .description-modal-actions button {
            flex: 1;
            padding: 12px;
            border: none;
            border-radius: 6px;
            font-weight: bold;
            cursor: pointer;
            transition: 0.3s;
        }

        .btn-close-desc { background: #eee; color: var(--text-dark); }
        .btn-close-desc:hover { background: #ddd; }
        .btn-add-desc { background: var(--green-deep); color: white; }
        .btn-add-desc:hover { background: var(--green-mid); }

        @media (max-width: 768px) {
            .description-modal-content { flex-direction: column; max-width: 95%; }
            .description-modal-carousel { width: 100%; height: 300px; border-radius: 12px 12px 0 0; }
            .description-modal-info { padding: 1.5rem; }
            .description-modal-title { font-size: 1.4rem; }
        }

        /* ── MODAL CHECKOUT MEJORADO (HORIZONTAL) ── */
        #checkoutModal {
            display: none;
            position: fixed;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            width: 90%;
            max-width: 800px;
            background: white;
            z-index: 3500;
            box-shadow: 0 10px 40px rgba(0,0,0,0.3);
            flex-direction: column;
            border-radius: 12px;
            max-height: 90vh;
            overflow-y: auto;
        }

        #checkoutModal.open { display: flex; }

        .checkout-overlay {
            display: none;
            position: fixed;
            top: 0; left: 0;
            width: 100%; height: 100%;
            background: rgba(0,0,0,0.7);
            z-index: 3400;
        }

        .checkout-overlay.open { display: block; }

        .user-dropdown { position: relative; display: inline-block; }
        .dropdown-toggle {
            background: white;
            color: #1a3d2b;
            border: 1px solid #1a3d2b;
            padding: 8px 15px;
            font-weight: bold;
            border-radius: 20px;
            cursor: pointer;
        }
        .dropdown-menu {
            display: none;
            position: absolute;
            right: 0;
            background-color: white;
            min-width: 160px;
            box-shadow: 0px 8px 16px rgba(0,0,0,0.15);
            z-index: 1000;
            border-radius: 8px;
            overflow: hidden;
            margin-top: 5px;
        }
        .dropdown-menu a {
            color: #333;
            padding: 12px 16px;
            text-decoration: none;
            display: block;
            font-size: 0.85rem;
        }
        .dropdown-menu a:hover { background-color: #f5f5f5; }
        .user-dropdown:hover .dropdown-menu { display: block; }
    </style>
</head>
<body>

<header>
    <a href="../index.php" class="logo">
        <span class="logo-main">Bookstore</span>
        <span class="logo-sub">Tu librería de confianza</span>
    </a>

    <form action="libros.php" method="GET" class="search-container">
        <?php if($id_genero > 0): ?>
            <input type="hidden" name="genero" value="<?= $id_genero ?>">
        <?php endif; ?>
        <div class="search-box">
            <input type="text" name="q" placeholder="Buscar título o autor..." value="<?= htmlspecialchars($busqueda) ?>">
            <button type="submit">🔍</button>
        </div>
    </form>

    <div class="header-right">
       <?php if (isset($_SESSION['username'])): ?>
    <div class="user-dropdown">
        <button class="dropdown-toggle">
            👤 ¡Hola, <?= htmlspecialchars($_SESSION['username']) ?>! ▼
        </button>
        <div class="dropdown-menu">
            <a href="#" onclick="verMisCompras()">Mis Compras</a>
            <hr style="border: 0; border-top: 1px solid #eee; margin: 5px 0;">
            <a href="../registro/logout.php" style="color: #c0392b;">Cerrar Sesión</a>
        </div>
    </div>
<?php else: ?>
    <a href="../registro/login.html" class="nav-btn">Ingresar</a>
<?php endif; ?>

        <div class="cart-container" onclick="toggleCart()">
            <img src="https://cdn-icons-png.flaticon.com/512/5412/5412512.png" id="carrito">
            <span class="cart-counter" id="cart-count">0</span>
        </div>
    </div>
</header>

<div class="genero-hero">
    <h1 class="hero-title">
        <?= !empty($busqueda) ? "🔍 " . htmlspecialchars($busqueda) : htmlspecialchars($nombre_genero) ?>
    </h1>
    <div class="hero-line"></div>
    <p><?= count($lista_libros) ?> libros encontrados</p>
</div>

<div class="page-wrapper">
    <aside>
        <h3 style="border-bottom: 2px solid var(--gold); padding-bottom: 5px;">Géneros</h3>
        <ul class="genero-list">
            <li><a href="libros.php">📚 Todos los libros</a></li>
            <?php foreach ($generos as $g): ?>
                <li>
                    <a href="libros.php?genero=<?= $g->getIdGenero() ?>" class="<?= ($g->getIdGenero() == $id_genero) ? 'active' : '' ?>">
                        <?= htmlspecialchars($g->getNombreGenero()) ?>
                    </a>
                </li>
            <?php endforeach; ?>
        </ul>
    </aside>

    <section class="books-grid">
    <?php if (count($lista_libros) === 0): ?>
        <p style="grid-column: 1/-1; text-align: center; padding: 3rem;">No se encontraron libros para tu búsqueda.</p>
    <?php else: ?>
        <?php foreach ($lista_libros as $libro): ?>
            <div class="book-card">
                <div class="book-img-wrap">
                    <img src="../img/<?= htmlspecialchars($libro->getImagen()) ?>" onerror="this.src='https://via.placeholder.com/200x300?text=Libro'">
                </div>

                <div class="book-body">
                    <h3 class="book-title"><?= htmlspecialchars($libro->getNombre()) ?></h3>
                    <p class="book-author">por <?= htmlspecialchars($libro->getAutor()) ?></p>

                    <div class="book-footer">
                        <span class="book-price">$<?= number_format($libro->getPrecio(), 2, ',', '.') ?></span>
                        <button class="view-desc-btn"
                            data-id="<?= $libro->getId() ?>"
                            data-nombre="<?= htmlspecialchars(json_encode($libro->getNombre()), ENT_QUOTES) ?>"
                            data-autor="<?= htmlspecialchars($libro->getAutor(), ENT_QUOTES) ?>"
                            data-detalle="<?= htmlspecialchars($libro->getDetalle() ?? 'Sin descripción disponible.', ENT_QUOTES) ?>"
                            data-precio="<?= htmlspecialchars($libro->getPrecio(), ENT_QUOTES) ?>"
                            data-imagen1="<?= htmlspecialchars($libro->getImagen() ?? '', ENT_QUOTES) ?>"
                            data-imagen2="<?= htmlspecialchars($libro->getImagen2() ?? '', ENT_QUOTES) ?>"
                            data-imagen3="<?= htmlspecialchars($libro->getImagen3() ?? '', ENT_QUOTES) ?>"
                            onclick="abrirModal(this)">
                            Ver descripción
                        </button>
                    </div>
                </div>
            </div>
        <?php endforeach; ?>
    <?php endif; ?>
</section>

</div>

<!-- MODAL DE DESCRIPCIÓN -->
<div class="description-overlay" id="descriptionOverlay">
    <div class="description-modal-content">
        <div class="description-modal-carousel" id="modalCarousel">
            <div class="carousel-container" id="carouselContainer">
                <!-- Los slides se generan dinámicamente por JS -->
            </div>
            <div class="carousel-nav" id="carouselNav"></div>
        </div>
        <div class="description-modal-info">
            <h2 class="description-modal-title" id="modalTitle">Título</h2>
            <p class="description-modal-author" id="modalAuthor">Autor</p>
            <p class="description-modal-price" id="modalPrice">$0,00</p>
            <p class="description-modal-description" id="modalDescription">Descripción</p>
            <div class="description-modal-actions">
                <button class="btn-close-desc" onclick="cerrarModal()">Cerrar</button>
                <button class="btn-add-desc" onclick="agregarAlCarrito()" id="btnAddToCart">+ Carrito</button>
            </div>
        </div>
    </div>
</div>

<div class="cart-modal" id="cartModal">
    <div style="background:var(--green-deep); color:white; padding:1rem; display:flex; justify-content:space-between;">
        <h3>Carrito</h3>
        <button onclick="toggleCart()" style="background:none; border:none; color:white; cursor:pointer;">✕</button>
    </div>

    <div id="cartItems" style="flex:1; padding:1rem; overflow-y:auto;">
        </div>

    <div style="padding:1rem; border-top:1px solid #eee;">
        <strong>Total: <span id="cartTotal">$0,00</span></strong>

        <button onclick="finalizarCompra()" style="width:100%; background:var(--gold); border:none; padding:10px; margin-top:10px; cursor:pointer; font-weight:bold; color:var(--green-deep);">
            FINALIZAR COMPRA
        </button>
    </div>
</div>

<!-- OVERLAY PARA MODAL CHECKOUT -->
<div class="checkout-overlay" id="checkoutOverlay"></div>

<!-- MODAL CHECKOUT (HORIZONTAL COMO REGISTRO) -->
<div id="checkoutModal">
    <div style="background:var(--green-deep); color:white; padding:1.5rem; display:flex; justify-content:space-between; align-items:center; border-radius: 12px 12px 0 0;">
        <h2 style="font-family:'Playfair Display'; margin:0;">Finalizar Compra</h2>
        <button onclick="closeCheckout()" style="background:none; border:none; color:white; cursor:pointer; font-size:1.5rem;">✕</button>
    </div>

    <div style="display:grid; grid-template-columns: 1fr 1fr; gap:2rem; padding:2rem; flex:1; overflow-y:auto;">
        <!-- LADO IZQUIERDO: RESUMEN DEL PEDIDO -->
        <div>
            <h3 style="color:var(--green-mid); border-bottom:2px solid var(--gold); padding-bottom:10px; margin-bottom:15px;">Resumen del Pedido</h3>
            <div id="checkoutSummary" style="font-size:0.9rem;"></div>
        </div>

        <!-- LADO DERECHO: FORMULARIO -->
        <div>
            <h3 style="color:var(--green-mid); border-bottom:2px solid var(--gold); padding-bottom:10px; margin-bottom:15px;">Datos de Envío</h3>
            <form id="form-datos-envio" style="display: flex; flex-direction: column; gap: 1rem;">
                <div>
                    <label for="nombre" style="display: block; margin-bottom: 0.3rem; font-weight: bold; font-size:0.9rem;">Nombre</label>
                    <input type="text" id="nombre" placeholder="Tu nombre" style="width: 100%; padding: 0.6rem; border: 1px solid #ddd; border-radius: 6px; font-size:0.9rem;">
                </div>

                <div>
                    <label for="apellido" style="display: block; margin-bottom: 0.3rem; font-weight: bold; font-size:0.9rem;">Apellido</label>
                    <input type="text" id="apellido" placeholder="Tu apellido" style="width: 100%; padding: 0.6rem; border: 1px solid #ddd; border-radius: 6px; font-size:0.9rem;">
                </div>

                <div>
                    <label for="email" style="display: block; margin-bottom: 0.3rem; font-weight: bold; font-size:0.9rem;">Email</label>
                    <input type="email" id="email" placeholder="tu@email.com" style="width: 100%; padding: 0.6rem; border: 1px solid #ddd; border-radius: 6px; font-size:0.9rem;">
                </div>

                <div>
                    <label for="telefono" style="display: block; margin-bottom: 0.3rem; font-weight: bold; font-size:0.9rem;">Teléfono</label>
                    <input type="tel" id="telefono" placeholder="1122334455" style="width: 100%; padding: 0.6rem; border: 1px solid #ddd; border-radius: 6px; font-size:0.9rem;">
                </div>

                <div>
                    <label for="direccion" style="display: block; margin-bottom: 0.3rem; font-weight: bold; font-size:0.9rem;">Dirección de Envío</label>
                    <input type="text" id="direccion" placeholder="Calle, número, ciudad" style="width: 100%; padding: 0.6rem; border: 1px solid #ddd; border-radius: 6px; font-size:0.9rem;">
                </div>

                <div>
                    <label for="metodo-pago" style="display: block; margin-bottom: 0.3rem; font-weight: bold; font-size:0.9rem;">Método de Pago</label>
                    <select id="metodo-pago" style="width: 100%; padding: 0.6rem; border: 1px solid #ddd; border-radius: 6px; font-size:0.9rem;">
                        <option value="transferencia">🏦 Transferencia (10% Descuento)</option>
                        <option value="tarjeta">💳 Tarjeta Débito/Crédito</option>
                        <option value="efectivo">💵 Efectivo</option>
                    </select>
                </div>

                <div style="border-top: 1px solid #eee; padding-top: 1rem; margin-top: 0.5rem;">
                    <div style="background:var(--green-pale); padding:1rem; border-radius:6px; margin-bottom:1rem;">
                        <div style="display:flex; justify-content:space-between; margin-bottom:5px;">
                            <span>Subtotal:</span>
                            <span id="st-total" style="font-weight:bold;"></span>
                        </div>
                        <div style="display:flex; justify-content:space-between; margin-bottom:10px; color:red;">
                            <span>Descuento:</span>
                            <span id="desc-total" style="font-weight:bold;"></span>
                        </div>
                        <div style="display:flex; justify-content:space-between; font-weight:bold; font-size:1.1rem; color:var(--green-deep);">
                            <span>TOTAL:</span>
                            <span id="final-total"></span>
                        </div>
                    </div>
                </div>

                <div style="display: flex; gap: 1rem;">
                    <button type="button" onclick="closeCheckout()" style="flex: 1; padding: 0.8rem; background: #ddd; color: #333; border: none; border-radius: 6px; cursor: pointer; font-weight: bold;">Cancelar</button>
                    <button type="submit" style="flex: 1; padding: 0.8rem; background: var(--green-deep); color: white; border: none; border-radius: 6px; cursor: pointer; font-weight: bold;">PAGAR</button>
                </div>
            </form>
        </div>
    </div>
</div>

<div id="toast" class="toast"></div>

<script>
    const usuarioLogueado = <?= isset($_SESSION['usuario_id']) ? 'true' : 'false' ?>;
    let cart = [], total = 0;
    let libroActualModal = null;

    // ── Estado del carrusel ──
    let currentSlide = 0;
    let totalSlides = 0;

    // CARGAR CARRITO DESDE LOCALSTORAGE AL INICIAR LA PÁGINA
    function cargarCarritoAlIniciar() {
        const carritoGuardado = localStorage.getItem('bookstore_cart');
        if (carritoGuardado) {
            try {
                cart = JSON.parse(carritoGuardado);
                total = cart.reduce((sum, item) => sum + item.price, 0);
                document.getElementById('cart-count').textContent = cart.length;
                renderCart();
            } catch (e) {
                console.log('Error cargando carrito:', e);
            }
        } else {
            document.getElementById('cart-count').textContent = '0';
        }
    }

    // Ejecutar al cargar la página
    document.addEventListener('DOMContentLoaded', cargarCarritoAlIniciar);

    // Formatea números en estilo argentino: 25000 -> "25.000,00"
    function formatPrecio(num) {
        return Number(num).toLocaleString('es-AR', { minimumFractionDigits: 2, maximumFractionDigits: 2 });
    }

    function generarCarrusel(imagenes) {
        const container = document.getElementById('carouselContainer');
        const nav = document.getElementById('carouselNav');
        container.innerHTML = '';
        nav.innerHTML = '';

        imagenes.forEach((img, i) => {
            const slide = document.createElement('div');
            slide.className = 'carousel-slide' + (i === 0 ? ' active' : '');
            slide.innerHTML = `<img src="../img/${img}" onerror="this.src='https://via.placeholder.com/280x380?text=Libro'">`;
            container.appendChild(slide);

            const dot = document.createElement('button');
            dot.className = 'carousel-dot' + (i === 0 ? ' active' : '');
            dot.onclick = () => goToSlide(i);
            nav.appendChild(dot);
        });

        // Flechas prev/next solo si hay más de 1 imagen
        if (imagenes.length > 1) {
            const prevBtn = document.createElement('button');
            prevBtn.className = 'carousel-btn prev';
            prevBtn.innerHTML = '‹';
            prevBtn.onclick = prevSlide;
            container.parentElement.appendChild(prevBtn);

            const nextBtn = document.createElement('button');
            nextBtn.className = 'carousel-btn next';
            nextBtn.innerHTML = '›';
            nextBtn.onclick = nextSlide;
            container.parentElement.appendChild(nextBtn);
        }

        currentSlide = 0;
        totalSlides = imagenes.length;
    }

    function goToSlide(index) {
        const slides = document.querySelectorAll('.carousel-slide');
        const dots = document.querySelectorAll('.carousel-dot');
        if (!slides.length) return;

        slides[currentSlide].classList.remove('active');
        dots[currentSlide].classList.remove('active');

        currentSlide = index;

        slides[currentSlide].classList.add('active');
        dots[currentSlide].classList.add('active');
    }

    function nextSlide() {
        goToSlide((currentSlide + 1) % totalSlides);
    }

    function prevSlide() {
        goToSlide((currentSlide - 1 + totalSlides) % totalSlides);
    }

    function abrirModal(btn) {
        // Recolectamos las 3 imágenes posibles (tapa, contratapa, perspectiva) y descartamos las vacías
        const imagenes = [
            btn.dataset.imagen1,
            btn.dataset.imagen2,
            btn.dataset.imagen3
        ].filter(img => img && img.length > 0);

        libroActualModal = {
            id: btn.dataset.id,
            nombre: JSON.parse(btn.dataset.nombre),
            autor: btn.dataset.autor,
            detalle: btn.dataset.detalle,
            precio: parseFloat(btn.dataset.precio)
        };

        document.getElementById('modalTitle').textContent = libroActualModal.nombre;
        document.getElementById('modalAuthor').textContent = 'por ' + libroActualModal.autor;
        document.getElementById('modalPrice').textContent = '$' + formatPrecio(libroActualModal.precio);
        document.getElementById('modalDescription').textContent = libroActualModal.detalle;

        generarCarrusel(imagenes.length > 0 ? imagenes : ['']);

        document.getElementById('descriptionOverlay').classList.add('open');
    }

    function cerrarModal() {
        document.getElementById('descriptionOverlay').classList.remove('open');
        libroActualModal = null;
    }

    function agregarAlCarrito() {
        if (libroActualModal) {
            cart.push({
                name: libroActualModal.nombre,
                price: libroActualModal.precio
            });
            total = cart.reduce((sum, item) => sum + item.price, 0);

            // Guardar en localStorage
            localStorage.setItem('bookstore_cart', JSON.stringify(cart));

            // Actualizar contador y contenido del drawer
            document.getElementById('cart-count').textContent = cart.length;
            renderCart();

            showToast(`"${libroActualModal.nombre}" agregado al carrito`);
            cerrarModal();
        }
    }

    // Cerrar modal al hacer click fuera
    document.getElementById('descriptionOverlay').addEventListener('click', function(e) {
        if (e.target === this) {
            cerrarModal();
        }
    });

    function renderCart() {
        const container = document.getElementById('cartItems');

        if (cart.length === 0) {
            container.innerHTML = `<p style="text-align:center; color:#888; margin-top:2rem;">El carrito está vacío</p>`;
        } else {
            container.innerHTML = cart.map((item, index) => `
                <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:12px; font-size:0.9rem; border-bottom:1px solid #eee; padding-bottom:5px;">
                    <div style="flex:1;">
                        <span style="display:block; font-weight:bold;">${item.name}</span>
                        <span style="color:var(--green-mid);">$${formatPrecio(item.price)}</span>
                    </div>
                    <button onclick="removeFromCart(${index})" style="background: #ff4d4d; color: white; border: none; border-radius: 4px; padding: 2px 8px; cursor: pointer; margin-left: 10px; font-size:0.8rem;">
                        ✕
                    </button>
                </div>
            `).join('');
        }

        document.getElementById('cartTotal').textContent = `$${formatPrecio(total)}`;
    }

    function toggleCart() { 
        document.getElementById('cartModal').classList.toggle('open'); 
    }

    function showToast(msg) {
        const t = document.getElementById('toast');
        t.textContent = msg; t.style.display = 'block';
        setTimeout(() => t.style.display = 'none', 2000);
    }

    function finalizarCompra() {
        if (cart.length === 0) {
            showToast('El carrito está vacío');
            return;
        }

        if (!usuarioLogueado) {
            showToast('Debes ingresar para continuar');
            setTimeout(() => window.location.href = '../registro/login.html', 1000);
            return;
        }

        // Cerrar carrito y abrir modal checkout
        toggleCart();
        
        // Mostrar overlay y modal
        document.getElementById('checkoutOverlay').classList.add('open');
        document.getElementById('checkoutModal').classList.add('open');

        // Renderizar resumen
        const summary = document.getElementById('checkoutSummary');
        summary.innerHTML = cart.map(item => `
            <div style="display:flex; justify-content:space-between; margin-bottom:10px; padding-bottom:10px; border-bottom:1px solid #eee;">
                <span>${item.name}</span>
                <span style="font-weight:bold;">$${formatPrecio(item.price)}</span>
            </div>
        `).join('');

        // Actualizar totales
        actualizarTotalFinal();

        // Agregar listener al formulario
        document.getElementById('form-datos-envio').addEventListener('submit', handleFormSubmit, { once: true });
    }

    function actualizarTotalFinal() {
        const metodo = document.getElementById('metodo-pago').value;
        let descuento = (metodo === 'transferencia') ? total * 0.10 : 0;
        let neto = total - descuento;

        document.getElementById('st-total').textContent = `$${formatPrecio(total)}`;
        document.getElementById('desc-total').textContent = `-$${formatPrecio(descuento)}`;
        document.getElementById('final-total').textContent = `$${formatPrecio(neto)}`;
    }

    // Listener para cambios en método de pago
    document.getElementById('metodo-pago').addEventListener('change', actualizarTotalFinal);

    function closeCheckout() {
        document.getElementById('checkoutModal').classList.remove('open');
        document.getElementById('checkoutOverlay').classList.remove('open');
    }

    async function handleFormSubmit(e) {
        e.preventDefault();
        
        const nombre = document.getElementById('nombre').value.trim();
        const apellido = document.getElementById('apellido').value.trim();
        const email = document.getElementById('email').value.trim();
        const telefono = document.getElementById('telefono').value.trim();
        const direccion = document.getElementById('direccion').value.trim();
        const metodo = document.getElementById('metodo-pago').value;

        if (!nombre || !apellido || !email || !telefono || !direccion) {
            showToast('Por favor completa todos los campos');
            return;
        }

        // Procesar la compra
        await procesarCompra({
            nombre,
            apellido,
            email,
            telefono,
            direccion,
            metodo
        });
    }

    async function procesarCompra(datos) {
        if (!cart || cart.length === 0) {
            showToast('El carrito está vacío.');
            return;
        }

        let descuento = (datos.metodo === 'transferencia') ? total * 0.10 : 0;
        let neto = total - descuento;

        try {
            const response = await fetch('guardar_venta.php', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({
                    total: total,
                    neto: neto,
                    descuento: descuento,
                    metodo: datos.metodo,
                    items: cart,
                    cliente: datos
                })
            });

            const result = await response.json();

            if (result.success) {
                closeCheckout();
                
                // Limpiar carrito
                localStorage.removeItem('bookstore_cart');
                cart = [];
                total = 0;
                document.getElementById('cart-count').textContent = '0';
                renderCart();
                
                // Mostrar resumen
                mostrarResumenCompra(result);
            } else {
                showToast('Error: ' + (result.error || 'No se pudo procesar la venta.'));
            }

        } catch (error) {
            console.error("Error:", error);
            showToast('Error en el script de compra: ' + error.message);
        }
    }

    function mostrarResumenCompra(result) {
        const modal = document.createElement('div');
        modal.style.cssText = `
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0,0,0,0.85);
            display: flex;
            justify-content: center;
            align-items: center;
            z-index: 6000;
        `;

        modal.innerHTML = `
            <div style="
                background: white;
                padding: 2rem;
                border-radius: 12px;
                max-width: 500px;
                width: 90%;
                font-family: 'Courier New', monospace;
                box-shadow: 0 10px 40px rgba(0,0,0,0.3);
            ">
                <div style="text-align: center; border-bottom: 2px dashed #333; padding-bottom: 1rem; margin-bottom: 1rem;">
                    <h2 style="margin: 0; color: var(--green-deep);">BOOKSTORE</h2>
                    <p style="margin: 0.5rem 0 0 0; font-size: 0.9rem;">Comprobante de Pago</p>
                </div>

                <div style="margin-bottom: 1rem;">
                    <p><b>Factura N°:</b> ${result.id_factura || 'N/A'}</p>
                    <p><b>Cliente:</b> ${result.cliente.nombre} ${result.cliente.apellido}</p>
                    <p><b>Email:</b> ${result.cliente.email}</p>
                    <p><b>Dirección:</b> ${result.cliente.direccion}</p>
                </div>

                <div style="border-top: 1px dashed #333; border-bottom: 1px dashed #333; padding: 1rem 0; margin-bottom: 1rem;">
                    <p><b>Método de Pago:</b> ${result.metodo}</p>
                    <p><b>Fecha:</b> ${new Date().toLocaleDateString('es-AR')}</p>
                </div>

                <div style="text-align: right;">
                    <p style="margin: 0.5rem 0;"><b>Subtotal:</b> $${formatPrecio(result.total)}</p>
                    ${result.descuento > 0 ? `<p style="margin: 0.5rem 0; color: green;"><b>Descuento:</b> -$${formatPrecio(result.descuento)}</p>` : ''}
                    <h3 style="margin: 1rem 0 0 0; color: var(--green-deep); font-size: 1.3rem;">Total: $${formatPrecio(result.neto)}</h3>
                </div>

                <button onclick="window.location.href='../index.php'" style="
                    width: 100%;
                    background: var(--green-deep);
                    color: white;
                    border: none;
                    padding: 1rem;
                    margin-top: 1.5rem;
                    border-radius: 6px;
                    cursor: pointer;
                    font-weight: bold;
                ">VOLVER AL INICIO</button>
            </div>
        `;

        document.body.appendChild(modal);
    }

    function removeFromCart(index) {
        total -= cart[index].price;
        cart.splice(index, 1);
        document.getElementById('cart-count').textContent = cart.length;
        renderCart();
        showToast("Producto eliminado");
    }

    async function verMisCompras() {
        const modal = document.getElementById('comprasModal');
        const content = document.getElementById('comprasContent');
        modal.style.display = 'flex';
        content.innerHTML = '<p style="text-align:center; color:#666;">Cargando historial...</p>';

        try {
            const response = await fetch('mis_compras.php');
            const result = await response.json();

            if (result.success) {
                if (result.compras.length === 0) {
                    content.innerHTML = '<p style="text-align:center; padding:20px; color:#666;">Aún no realizaste ninguna compra.</p>';
                    return;
                }
                let html = '<div style="display:flex; flex-direction:column; gap:12px;">';
                result.compras.forEach(compra => {
                    html += `
                        <div style="border:1px solid #e0e0e0; padding:12px; border-radius:8px; background:#fafafa;">
                            <div style="display:flex; justify-content:space-between; margin-bottom:5px;">
                                <span style="font-weight:bold; color:#1a3d2b;">Pedido #${compra.id}</span>
                                <span style="color:#666; font-size:0.8rem;">${compra.fecha}</span>
                            </div>
                            <div style="display:flex; justify-content:space-between; font-size:0.9rem;">
                                <span>Pago: ${compra.metodo}</span>
                                <span style="font-weight:bold; color:#c0392b;">$${formatPrecio(compra.total)}</span>
                            </div>
                        </div>`;
                });
                html += '</div>';
                content.innerHTML = html;
            } else {
                content.innerHTML = `<p style="color:red; text-align:center;">${result.error}</p>`;
            }
        } catch (error) {
            content.innerHTML = '<p style="color:red; text-align:center;">Error al conectar con el servidor.</p>';
        }
    }
</script>
<div id="comprasModal" style="display:none; position:fixed; top:0; left:0; width:100%; height:100%; background:rgba(0,0,0,0.6); z-index:5000; justify-content:center; align-items:center;">
    <div style="background:white; padding:25px; border-radius:12px; max-width:500px; width:90%; max-height:80vh; overflow-y:auto;">
        <div style="display:flex; justify-content:space-between; align-items:center; border-bottom:1px solid #eee; padding-bottom:10px; margin-bottom:15px;">
            <h3 style="margin:0; color:#1a3d2b;">Mi Historial de Compras</h3>
            <button onclick="document.getElementById('comprasModal').style.display='none'" style="background:none; border:none; font-size:1.5rem; cursor:pointer;">&times;</button>
        </div>
        <div id="comprasContent"></div>
    </div>
</div>
</body>
</html>
