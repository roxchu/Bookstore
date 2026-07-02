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

        /* ── CSS PARA EL MENÚ DESPLEGABLE DE USUARIO ── */
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
            text-align: left;
        }
        .dropdown-menu a:hover {
            background-color: #f5f5f5;
        }
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

        /* ── ESTILO MEJORADO PARA EL BOTÓN INGRESAR ── */
        .nav-btn {
            background: white;
            color: #1a3d2b; /* Alineado con el color de tu diseño */
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
                    <button class="dropdown-toggle">
                        👤 ¡Hola, <?= htmlspecialchars($_SESSION['username']) ?>! ▼
                    </button>
                    <div class="dropdown-menu">
                        <a href="#" onclick="verMisCompras()">Mis Compras</a>
                        <hr style="border: 0; border-top: 1px solid #eee; margin: 5px 0;">
                        <a href="registro/logout.php" style="color: #c0392b;">Cerrar Sesión</a>
                    </div>
                </div>
            <?php else: ?>
                <a href="registro/login.html" class="nav-btn">Ingresar</a>
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

    <main>
        <h2 class="section-label">Novedades por Género</h2>
        <div class="section-divider"></div>

  <div class="book-showcase">
            <?php
            try {
                // Conexión 
                $pdo = new PDO('mysql:host=localhost;dbname=books_store;charset=utf8', 'root', '');
                $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
                
                
                $imagenes_generos = [
                    1 => "https://encrypted-tbn0.gstatic.com/images?q=tbn:ANd9GcQWx-rnxpTtxMdg2AtIMLEQBBAJtdLpepvEhg&s", // Fantasía
                    2 => "https://encrypted-tbn0.gstatic.com/images?q=tbn:ANd9GcQHezajmRuG5FyayYnDrEEtEeuexc6GuESgOxm_uMQxUF04oFBsyb8Tz_6B&s=10",         // Terror
                    3 => "https://encrypted-tbn0.gstatic.com/images?q=tbn:ANd9GcSSRLoOoI0K8Xq-BKcwFq-mK4GCcDY4w8W54A&s", // Romance
                    4 => "https://thumbs.dreamstime.com/b/m%C3%A1scaras-del-teatro-de-la-comedia-y-de-la-tragedia-21958013.jpg", // Comedia
                    5 => "https://encrypted-tbn0.gstatic.com/images?q=tbn:ANd9GcQs8OSuskP5BtLo0KGXt0JuCU1tibDCpdxvXg&s", // Poesía
                    6 => "https://encrypted-tbn0.gstatic.com/images?q=tbn:ANd9GcR6Jt02Bx6Dt3Q-GqrRCdD5Qj3IpfVwWm4p2A3iRU8Kwev3x9EG-bE57D4&s=10" // Aventura
                ];

                
                $stmt = $pdo->query("SELECT id_genero, nombre_genero, destacado FROM genero ORDER BY destacado DESC, id_genero ASC");
                
                while ($genero = $stmt->fetch(PDO::FETCH_ASSOC)) {
                    $id_genero = $genero['id_genero'];
                    $nombre_genero = htmlspecialchars($genero['nombre_genero']);
                    $es_destacado = ($genero['destacado'] == 1);
                    $img_g = $imagenes_generos[$id_genero] ?? "https://images.pexels.com/photos/7974/pexels-photo.jpg?auto=compress&cs=tinysrgb&w=600";
                    ?>
                    
                    <a href="productos/libros.php?genero=<?= $id_genero ?>" class="book-card">
                        <div class="book-image-wrap">
                            <img src="<?= $img_g ?>" alt="<?= $nombre_genero ?>" class="book-image">
                            <?php if ($es_destacado): ?>
                                <span class="book-badge">Destacado</span>
                            <?php endif; ?>
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
        <button class="checkout-btn">Finalizar Compra</button>
    </div>

    <div id="comprasModal" style="display:none; position:fixed; top:0; left:0; width:100%; height:100%; background:rgba(0,0,0,0.6); z-index:5000; justify-content:center; align-items:center;">
        <div style="background:white; padding:25px; border-radius:12px; max-width:500px; width:90%; max-height:80vh; overflow-y:auto; box-shadow: 0 5px 20px rgba(0,0,0,0.3); font-family: 'Lato', sans-serif;">
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

        function addToCart(name, price) {
            if(!usuarioLogueado) {
                alert("Debes iniciar sesión para comprar.");
                window.location.href = 'registro/login.html';
                return;
            }
            cart.push({ name, price });
            total += price;
            document.getElementById('cart-count').textContent = cart.length;
            renderCart();
            showToast(`"${name}" agregado al carrito`);
        }

        function renderCart() {
            const container = document.getElementById('cartItems');
            if (cart.length === 0) {
                container.innerHTML = '<p style="color:#aaa;text-align:center;margin-top:2rem">El carrito está vacío</p>';
            } else {
                container.innerHTML = cart.map(item => `
                    <div class="cart-item">
                        <span class="cart-item-name">${item.name}</span>
                        <span class="cart-item-price">$${item.price.toFixed(2)}</span>
                    </div>
                `).join('');
            }
            document.getElementById('cartTotal').textContent = `$${total.toFixed(2)}`;
        }

        function toggleCart() {
            document.getElementById('cartModal').classList.toggle('open');
        }

        function showToast(msg) {
            const t = document.getElementById('toast');
            t.textContent = msg;
            t.classList.add('show');
            setTimeout(() => t.classList.remove('show'), 2500);
        }
    </script>
</body>
</html>