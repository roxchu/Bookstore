<?php
session_start();
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Bookstore — Bienvenidos</title>
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@400;700;900&family=Lato:wght@300;400;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="styles.css">
    <style>
        /* AJUSTES PARA EL HEADER FLEXIBLE */
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
            flex-wrap: wrap;
        }

        .logo {
            flex-shrink: 0;
            display: flex;
            flex-direction: column;
            text-decoration: none;
        }

        /* BARRA DE BÚSQUEDA CENTRADA */
        .search-container {
            flex: 1;
            display: flex;
            justify-content: center;
            max-width: 550px;
            min-width: 200px;
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
            color: white;
            width: 100%;
            padding: 8px 0;
            font-size: 0.95rem;
        }

        .search-box:focus-within input {
            color: #1a2e1e;
        }

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

        a.book-card {
            text-decoration: none;
            color: inherit;
            display: block;
            cursor: pointer;
        }

        /* ── CSS PARA EL MENÚ DESPLEGABLE DE USUARIO MEJORADO ── */
        .user-dropdown {
            position: relative;
            display: inline-block;
        }
        
        .dropdown-toggle {
            background: white;
            color: #1a3d2b;
            border: 1px solid #1a3d2b;
            padding: 8px 15px;
            font-weight: bold;
            border-radius: 20px;
            cursor: pointer;
            font-size: 0.9rem;
            z-index: 1001;
            position: relative;
        }
        
        .dropdown-menu {
            display: none;
            position: absolute;
            right: 0;
            background-color: white;
            min-width: 160px;
            box-shadow: 0px 8px 16px rgba(0,0,0,0.15);
            z-index: 1100;
            border-radius: 8px;
            overflow: hidden;
            margin-top: 5px;
            top: 100%;
        }
        
        .dropdown-menu a {
            color: #333;
            padding: 12px 16px;
            text-decoration: none;
            display: block;
            font-size: 0.85rem;
            text-align: left;
            cursor: pointer;
            transition: background-color 0.2s;
        }
        
        .dropdown-menu a:hover {
            background-color: #f5f5f5;
        }
        
        .dropdown-menu hr {
            margin: 5px 0;
            border: 0;
            border-top: 1px solid #eee;
        }
        
        /* Mostrar menú al hacer click (con clase "open") */
        .dropdown-menu.open {
            display: block !important;
        }
        
        /* También mantener visible en hover */
        .user-dropdown:hover .dropdown-menu {
            display: block;
        }

        /* ── BOTÓN ADMIN (solo para admins) ── */
        .admin-panel-btn {
            background: var(--gold);
            color: var(--green-deep);
            border: none;
            padding: 8px 16px;
            border-radius: 20px;
            font-weight: bold;
            cursor: pointer;
            font-size: 0.9rem;
            text-decoration: none;
            display: inline-block;
            transition: 0.3s;
        }
        .admin-panel-btn:hover {
            background: #b8973d;
            color: white;
        }

        /* ── ESTILO MEJORADO PARA LOS BOTONES ── */
        .nav-btn {
            background: white;
            color: #1a3d2b;
            border: 1.5px solid transparent;
            padding: 8px 22px;
            border-radius: 20px;
            font-weight: bold;
            font-size: 0.9rem;
            text-decoration: none;
            display: inline-block;
            transition: all 0.3s ease;
            box-shadow: 0 2px 6px rgba(0,0,0,0.1);
        }
        .nav-btn:hover {
            background: var(--gold);
            color: white;
            box-shadow: 0 4px 12px rgba(201, 168, 76, 0.4);
            transform: translateY(-1px);
        }
        .nav-btn:active {
            transform: translateY(0);
        }

        /* HERO SECTION MÁS PEQUEÑO */
        .hero-section {
            background: linear-gradient(135deg, var(--green-deep) 0%, var(--green-mid) 100%);
            padding: 2rem 3rem;
            text-align: center;
            position: relative;
            overflow: hidden;
        }

        .hero-section::before {
            content: '📚';
            position: absolute;
            font-size: 12rem;
            opacity: 0.04;
            top: -2rem;
            right: -1.5rem;
        }

        .hero-title {
            font-family: 'Playfair Display', serif;
            font-size: 2.5rem;
            font-weight: 900;
            color: var(--cream);
            margin-bottom: 0.5rem;
        }

        .hero-subtitle {
            font-size: 0.95rem;
            color: var(--green-light);
            letter-spacing: 2px;
            text-transform: uppercase;
        }

        .hero-line {
            width: 50px;
            height: 3px;
            background: var(--gold);
            margin: 1rem auto;
        }

        /* CARRUSEL DE LIBROS */
        .carousel-section {
            max-width: 1200px;
            margin: 3rem auto;
            padding: 0 2rem;
        }

        .carousel-container {
            position: relative;
            overflow: hidden;
            border-radius: 8px;
        }

        .carousel-wrapper {
            display: flex;
            transition: transform 0.5s ease;
        }

        .carousel-slide {
            min-width: 100%;
            height: auto;
            display: flex;
            align-items: center;
            justify-content: center;
            background: white;
            padding: 2rem;
            gap: 2rem;
            position: relative;
        }

        .carousel-book {
            display: flex;
            flex-direction: column;
            align-items: center;
            gap: 1rem;
            flex: 1;
            max-width: 250px;
        }

        .carousel-book img {
            max-height: 300px;
            max-width: 100%;
            object-fit: contain;
            border-radius: 8px;
            box-shadow: 0 4px 12px rgba(0,0,0,0.1);
        }

        .carousel-book-info {
            text-align: center;
            width: 100%;
        }

        .carousel-book-title {
            font-family: 'Playfair Display', serif;
            font-size: 1.2rem;
            color: var(--green-deep);
            margin-bottom: 0.3rem;
        }

        .carousel-book-author {
            font-size: 0.85rem;
            color: #666;
            margin-bottom: 0.5rem;
        }

        .carousel-book-price {
            font-size: 1.3rem;
            font-weight: bold;
            color: var(--green-mid);
            margin-bottom: 0.8rem;
        }

        .carousel-book-btn {
            background: var(--green-deep);
            color: white;
            border: none;
            padding: 0.7rem 1.5rem;
            border-radius: 6px;
            cursor: pointer;
            font-weight: bold;
            transition: background 0.3s;
            width: 100%;
        }

        .carousel-book-btn:hover {
            background: var(--green-mid);
        }

        .carousel-nav {
            position: absolute;
            bottom: 15px;
            left: 50%;
            transform: translateX(-50%);
            display: flex;
            gap: 8px;
            z-index: 10;
        }

    .carousel-dot {
        width: 10px;
        height: 10px;
        border-radius: 50%;
        background: rgba(255,255,255,0.5);
        cursor: pointer;
        transition: all 0.3s;
        border: none;
        padding: 0;
        flex-shrink: 0;
    }

    .carousel-dot:hover {
        background: rgba(255,255,255,0.8);
    }

    .carousel-dot.active {
        background: var(--gold);
        transform: scale(1.2);
    }

    .carousel-btn {
        position: absolute;
        top: 50%;
        transform: translateY(-50%);
        background: rgba(0,0,0,0.5);
        color: white;
        border: none;
        width: 45px;
        height: 45px;
        border-radius: 50%;
        cursor: pointer;
        font-size: 1.4rem;
        z-index: 10;
        transition: background 0.3s;
        display: flex;
        align-items: center;
        justify-content: center;
        padding: 0;
        line-height: 1;
    }

    .carousel-btn:hover {
        background: rgba(0,0,0,0.8);
    }

    .carousel-btn.prev {
        left: 15px;
    }

    .carousel-btn.next {
        right: 15px;
    }

        /* RESPONSIVE */
        @media (max-width: 1024px) {
            header {
                padding: 0.8rem 1.5rem;
            }
            .search-container {
                order: 3;
                flex-basis: 100%;
                margin-top: 0.5rem;
                max-width: 100%;
            }
            .hero-title {
                font-size: 2rem;
            }
            .carousel-slide {
                flex-wrap: wrap;
                padding: 1.5rem;
            }
        }

        .cart-modal {
            display: none;
            position: fixed;
            top: 0;
            right: 0;
            width: 350px;
            height: 100vh;
            background: white;
            z-index: 2000;
            box-shadow: -5px 0 15px rgba(0,0,0,0.1);
            flex-direction: column;
        }
        .cart-modal.open {
            display: flex;
        }

        @media (max-width: 768px) {
            header {
                padding: 0.8rem 1rem;
                gap: 10px;
            }
            .logo-main {
                font-size: 1.3rem;
            }
            .search-container {
                order: 3;
                flex-basis: 100%;
                margin-top: 0.5rem;
            }
            .hero-section {
                padding: 1.5rem 1rem;
            }
            .hero-title {
                font-size: 1.8rem;
                margin-bottom: 0.3rem;
            }
            .hero-line {
                margin: 0.8rem auto;
            }
            .carousel-slide {
                flex-direction: column;
                padding: 1rem;
            }
            .carousel-book {
                max-width: 100%;
                width: 100%;
            }
            .carousel-btn {
                width: 36px;
                height: 36px;
                font-size: 1rem;
            }
            .cart-modal {
                width: 100%;
            }
            .book-showcase {
                grid-template-columns: repeat(auto-fill, minmax(200px, 1fr));
            }
        }
    </style>
</head>

<body>
    <header>
        <a href="index.php" class="logo">
            <span class="logo-main">Bookstore</span>
            <span class="logo-sub">Tu librería de confianza</span>
        </a>

        <form action="productos/libros.php" method="GET" class="search-container">
            <div class="search-box">
                <input type="text" name="q" placeholder="¿Qué historia buscas hoy?">
                <button type="submit">🔍</button>
            </div>
        </form>

        <div class="header-right">
            <?php if (isset($_SESSION['username'])): ?>
                <?php if (isset($_SESSION['rol_id']) && $_SESSION['rol_id'] == 1): ?>
                    <a href="paneles/panel_admin.html" class="admin-panel-btn">⚙️ Panel Admin</a>
                <?php endif; ?>

                <div class="user-dropdown">
                    <button class="dropdown-toggle" type="button">
                        👤 ¡Hola, <?= htmlspecialchars($_SESSION['username']) ?>! ▼
                    </button>
                    <div class="dropdown-menu">
                        <a href="#" onclick="event.preventDefault(); verMisCompras()">Mis Compras</a>
                        <hr>
                        <a href="registro/logout.php" style="color: #c0392b;">Cerrar Sesión</a>
                    </div>
                </div>
            <?php else: ?>
                <a href="registro/login.html" class="nav-btn">Ingresar</a>
                <a href="registro/registro.html" class="nav-btn" style="background: var(--gold); color: var(--green-deep);">Crear Cuenta</a>
            <?php endif; ?>

            <div class="cart-container" onclick="toggleCart()">
                <img src="https://cdn-icons-png.flaticon.com/512/5412/5412512.png" id="carrito">
                <span class="cart-counter" id="cart-count">0</span>
            </div>
        </div>
    </header>

    <div class="hero-section">
        <h1 class="hero-title">Bienvenidos</h1>
        <div class="hero-line"></div>
        <p class="hero-subtitle">Descubrí tu próxima historia</p>
    </div>

    <!-- CARRUSEL DE LIBROS DESTACADOS -->
    <div class="carousel-section">
        <div class="carousel-container">
            <div class="carousel-wrapper" id="carouselWrapper">
                <!-- Los slides se generan dinámicamente -->
            </div>
            <button class="carousel-btn prev" onclick="prevSlide()">❮</button>
            <button class="carousel-btn next" onclick="nextSlide()">❯</button>
            <div class="carousel-nav" id="carouselNav"></div>
        </div>
    </div>

    <main>
        <h2 class="section-label">Novedades por Género</h2>
        <div class="section-divider"></div>

        <div class="book-showcase">
            <?php
            try {
                $pdo = new PDO('mysql:host=localhost;dbname=books_store;charset=utf8', 'root', '');
                $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
                
                $imagenes_generos = [
                    1 => "https://encrypted-tbn0.gstatic.com/images?q=tbn:ANd9GcQWx-rnxpTtxMdg2AtIMLEQBBAJtdLpepvEhg&s",
                    2 => "https://encrypted-tbn0.gstatic.com/images?q=tbn:ANd9GcQHezajmRuG5FyayYnDrEEtEeuexc6GuESgOxm_uMQxUF04oFBsyb8Tz_6B&s=10",
                    3 => "https://encrypted-tbn0.gstatic.com/images?q=tbn:ANd9GcSSRLoOoI0K8Xq-BKcwFq-mK4GCcDY4w8W54A&s",
                    4 => "https://thumbs.dreamstime.com/b/m%C3%A1scaras-del-teatro-de-la-comedia-y-de-la-tragedia-21958013.jpg",
                    5 => "https://encrypted-tbn0.gstatic.com/images?q=tbn:ANd9GcQs8OSuskP5BtLo0KGXt0JuCU1tibDCpdxvXg&s",
                    6 => "https://encrypted-tbn0.gstatic.com/images?q=tbn:ANd9GcR6Jt02Bx6Dt3Q-GqrRCdD5Qj3IpfVwWm4p2A3iRU8Kwev3x9EG-bE57D4&s=10"
                ];

                $stmt = $pdo->query("SELECT id_genero, nombre_genero FROM genero ORDER BY id_genero ASC");
                
                while ($genero = $stmt->fetch(PDO::FETCH_ASSOC)) {
                    $id_genero = $genero['id_genero'];
                    $nombre_genero = htmlspecialchars($genero['nombre_genero']);
                    $img_g = $imagenes_generos[$id_genero] ?? "https://images.pexels.com/photos/7974/pexels-photo.jpg?auto=compress&cs=tinysrgb&w=600";
                    ?>
                    
                    <a href="productos/libros.php?genero=<?= $id_genero ?>" class="book-card">
                        <div class="book-image-wrap">
                            <img src="<?= $img_g ?>" alt="<?= $nombre_genero ?>" class="book-image">
                        </div>
                        <div class="book-info">
                            <h3 class="book-name"><?= $nombre_genero ?></h3>
                        </div>
                    </a>

                    <?php
                }
            } catch (PDOException $e) {
                echo "<p style='color:red; text-align:center;'>Error al cargar los géneros: " . $e->getMessage() . "</p>";
            }
            ?>
        </div>

        <h2 class="section-label">Por qué elegirnos</h2>
        <div class="section-divider"></div>

        <div class="features">
            <div class="feature">
                <div class="feature-icon">📖</div>
                <h3 class="feature-title">Gran Variedad</h3>
                <p>Miles de títulos de todos los géneros para cada lector</p>
            </div>
            <div class="feature">
                <div class="feature-icon">🚚</div>
                <h3 class="feature-title">Envío Rápido</h3>
                <p>Recibí tus libros en la puerta de tu casa en tiempo récord</p>
            </div>
            <div class="feature">
                <div class="feature-icon">⭐</div>
                <h3 class="feature-title">Calidad Garantizada</h3>
                <p>Libros seleccionados con el más alto estándar editorial</p>
            </div>
        </div>
    </main>

    <div class="cart-modal" id="cartModal">
        <div class="cart-header">
            <h2>Tu Carrito</h2>
            <button class="cart-close" onclick="toggleCart()">✕</button>
        </div>
        <div class="cart-items" id="cartItems">
            <p style="color:#aaa; text-align:center; margin-top:2rem">El carrito está vacío</p>
        </div>
        <div class="cart-total">
            <span>Total</span>
            <span id="cartTotal">$0.00</span>
        </div>
        <button class="checkout-btn" onclick="irACheckout()">Finalizar Compra</button>
    </div>

    <div id="comprasModal" style="display:none; position:fixed; top:0; left:0; width:100%; height:100%; background:rgba(0,0,0,0.6); z-index:5000; justify-content:center; align-items:center;">
        <div style="background:white; padding:25px; border-radius:12px; max-width:500px; width:90%; max-height:80vh; overflow-y:auto; box-shadow: 0 5px 20px rgba(0,0,0,0.3);">
            <div style="display:flex; justify-content:space-between; align-items:center; border-bottom:1px solid #eee; padding-bottom:10px; margin-bottom:15px;">
                <h3 style="margin:0; color:#1a3d2b; font-family:'Playfair Display', serif;">Mi Historial de Compras</h3>
                <button onclick="document.getElementById('comprasModal').style.display='none'" style="background:none; border:none; font-size:1.5rem; cursor:pointer;">&times;</button>
            </div>
            <div id="comprasContent"></div>
        </div>
    </div>

    <div class="toast" id="toast"></div>

    <footer>
        <p><span>Bookstore</span> &mdash; Rocio Monzon · Nicole Roglich · Denise Roglich · Abril Veron</p>
    </footer>

    <script>
        let cart = [];
        let total = 0;
        const usuarioLogueado = <?= isset($_SESSION['username']) ? 'true' : 'false' ?>;
        let currentSlide = 0;
        let slides = [];

        // Mejorar el dropdown con click
        document.addEventListener('DOMContentLoaded', function() {
            const toggles = document.querySelectorAll('.dropdown-toggle');
            toggles.forEach(toggle => {
                toggle.addEventListener('click', function(e) {
                    e.preventDefault();
                    e.stopPropagation();
                    const menu = this.nextElementSibling;
                    if (menu) {
                        menu.classList.toggle('open');
                    }
                });
            });
            
            // Cerrar menú al hacer click afuera
            document.addEventListener('click', function(e) {
                if (!e.target.closest('.user-dropdown')) {
                    document.querySelectorAll('.dropdown-menu').forEach(menu => {
                        menu.classList.remove('open');
                    });
                }
            });
        });

        // Cargar carrito desde localStorage (MODO INVITADO)
        function cargarCarrito() {
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
            }
        }

        // Guardar carrito en localStorage
        function guardarCarrito() {
            localStorage.setItem('bookstore_cart', JSON.stringify(cart));
        }

        // Cargar libros destacados para el carrusel
        async function cargarCarrusel() {
            try {
                const response = await fetch('productos/libros.php?api=carrusel');
                const data = await response.json();
                
                if (data && data.libros && data.libros.length > 0) {
                    slides = data.libros;
                    generarCarrusel();
                }
            } catch (e) {
                console.log('No se pudo cargar el carrusel:', e);
            }
        }

        function generarCarrusel() {
            const wrapper = document.getElementById('carouselWrapper');
            const nav = document.getElementById('carouselNav');
            wrapper.innerHTML = '';
            nav.innerHTML = '';

            // Agrupar libros en slides de 3
            for (let i = 0; i < slides.length; i += 3) {
                const slide = document.createElement('div');
                slide.className = 'carousel-slide' + (i === 0 ? ' active' : '');
                
                let html = '';
                for (let j = i; j < i + 3 && j < slides.length; j++) {
                    const libro = slides[j];
                    html += `
                        <div class="carousel-book">
                            <img src="img/${libro.imagen}" onerror="this.src='https://via.placeholder.com/200x300?text=Libro'" alt="${libro.nombre}">
                            <div class="carousel-book-info">
                                <div class="carousel-book-title">${libro.nombre}</div>
                                <div class="carousel-book-author">por ${libro.autor}</div>
                                <div class="carousel-book-price">$${parseFloat(libro.precio).toFixed(2)}</div>
                                <button class="carousel-book-btn" onclick="addToCart('${libro.nombre.replace(/'/g, "\\'")}', ${libro.precio})">+ Carrito</button>
                            </div>
                        </div>
                    `;
                }
                slide.innerHTML = html;
                wrapper.appendChild(slide);

                // Crear punto de navegación
                const dotContainer = Math.floor(i / 3);
                const dot = document.createElement('button');
                dot.className = 'carousel-dot' + (dotContainer === 0 ? ' active' : '');
                dot.onclick = () => goToSlide(dotContainer);
                nav.appendChild(dot);
            }

            currentSlide = 0;
        }

        function goToSlide(index) {
            const slides = document.querySelectorAll('.carousel-slide');
            if (index >= 0 && index < slides.length) {
                currentSlide = index;
                const wrapper = document.getElementById('carouselWrapper');
                wrapper.style.transform = `translateX(-${currentSlide * 100}%)`;

                document.querySelectorAll('.carousel-dot').forEach((dot, i) => {
                    dot.classList.toggle('active', i === currentSlide);
                });
            }
        }

        function nextSlide() {
            const slides = document.querySelectorAll('.carousel-slide');
            goToSlide((currentSlide + 1) % slides.length);
        }

        function prevSlide() {
            const slides = document.querySelectorAll('.carousel-slide');
            goToSlide((currentSlide - 1 + slides.length) % slides.length);
        }

        // AGREGAR AL CARRITO - FUNCIONA SIN LOGIN (MODO INVITADO)
        function addToCart(name, price) {
            cart.push({ name, price });
            total += price;
            document.getElementById('cart-count').textContent = cart.length;
            guardarCarrito();
            renderCart();
            showToast(`"${name}" agregado al carrito`);
        }

        // ELIMINAR DEL CARRITO
        function removeFromCart(index) {
            total -= cart[index].price;
            cart.splice(index, 1);
            document.getElementById('cart-count').textContent = cart.length;
            guardarCarrito();
            renderCart();
            showToast("Producto eliminado");
        }

        function renderCart() {
            const container = document.getElementById('cartItems');
            if (cart.length === 0) {
                container.innerHTML = '<p style="color:#aaa;text-align:center;margin-top:2rem">El carrito está vacío</p>';
            } else {
                container.innerHTML = cart.map((item, idx) => `
                    <div class="cart-item">
                        <div>
                            <span class="cart-item-name">${item.name}</span><br>
                            <span style="color:#666; font-size:0.85rem;">$${item.price.toFixed(2)}</span>
                        </div>
                        <button onclick="removeFromCart(${idx})" style="background:#ff4d4d; color:white; border:none; border-radius:4px; padding:4px 8px; cursor:pointer;">✕</button>
                    </div>
                `).join('');
            }
            document.getElementById('cartTotal').textContent = `$${total.toFixed(2)}`;
        }

        function toggleCart() {
            document.getElementById('cartModal').classList.toggle('open');
        }

        function irACheckout() {
            if (cart.length === 0) {
                showToast('El carrito está vacío');
                return;
            }

            if (!usuarioLogueado) {
                showToast('Debes ingresar para continuar');
                setTimeout(() => window.location.href = 'registro/login.html', 1000);
                return;
            }

            guardarCarrito();
            window.location.href = 'productos/libros.php?checkout=1';
        }

        function showToast(msg) {
            const t = document.getElementById('toast');
            t.textContent = msg;
            t.classList.add('show');
            setTimeout(() => t.classList.remove('show'), 2500);
        }

        async function verMisCompras() {
            const modal = document.getElementById('comprasModal');
            const content = document.getElementById('comprasContent');
            modal.style.display = 'flex';
            content.innerHTML = '<p style="text-align:center; color:#666;">Cargando historial...</p>';

            try {
                const response = await fetch('productos/mis_compras.php');
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
                                    <span style="font-weight:bold; color:#c0392b;">$${compra.total.toFixed(2)}</span>
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

        // Inicializar
        document.addEventListener('DOMContentLoaded', () => {
            cargarCarrito();
            cargarCarrusel();
        });
    </script>
</body>
</html>
