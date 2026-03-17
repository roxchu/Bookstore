<?php
session_start();

$conexion = new mysqli("localhost", "root", "", "libreria");
if ($conexion->connect_error) {
    die("Error de conexión: " . $conexion->connect_error);
}

// Obtener y validar el género recibido
$id_genero = isset($_GET['genero']) ? intval($_GET['genero']) : 0;

// Obtener nombre del género
$nombre_genero = "Todos los libros";
if ($id_genero > 0) {
    $stmt_g = $conexion->prepare("SELECT genero FROM genero WHERE id_genero = ?");
    $stmt_g->bind_param("i", $id_genero);
    $stmt_g->execute();
    $res_g = $stmt_g->get_result();
    if ($row_g = $res_g->fetch_assoc()) {
        $nombre_genero = $row_g['genero'];
    }
    $stmt_g->close();
}

// Obtener todos los géneros para el filtro lateral
$generos = $conexion->query("SELECT * FROM genero ORDER BY genero ASC");

// Obtener libros del género seleccionado
if ($id_genero > 0) {
    $stmt = $conexion->prepare("SELECT * FROM libros WHERE id_genero = ? ORDER BY fecha_publicacion DESC");
    $stmt->bind_param("i", $id_genero);
} else {
    $stmt = $conexion->prepare("SELECT * FROM libros ORDER BY fecha_publicacion DESC");
}
$stmt->execute();
$libros = $stmt->get_result();
$stmt->close();
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

        /* ── HEADER ── */
        header {
            background: var(--green-deep);
            padding: 1.2rem 3rem;
            display: flex;
            justify-content: space-between;
            align-items: center;
            position: sticky;
            top: 0;
            z-index: 100;
            border-bottom: 3px solid var(--gold);
        }

        .logo { display: flex; flex-direction: column; line-height: 1; text-decoration: none; }
        .logo-main {
            font-family: 'Playfair Display', serif;
            font-size: 2rem;
            font-weight: 900;
            color: var(--cream);
            letter-spacing: 2px;
        }
        .logo-sub {
            font-size: 0.65rem;
            letter-spacing: 6px;
            color: var(--gold);
            text-transform: uppercase;
            margin-top: 2px;
        }

        .header-right { display: flex; align-items: center; gap: 1.5rem; }

        .login-btn {
            background: transparent;
            color: var(--cream);
            border: 1.5px solid var(--green-accent);
            padding: 0.6rem 1.5rem;
            border-radius: 3px;
            font-family: 'Lato', sans-serif;
            font-size: 0.85rem;
            letter-spacing: 2px;
            text-transform: uppercase;
            cursor: pointer;
            text-decoration: none;
            transition: all 0.3s ease;
        }
        .login-btn:hover { background: var(--green-accent); color: var(--green-deep); }

        .cart-container { position: relative; cursor: pointer; }
        #carrito { width: 32px; height: 32px; filter: invert(1); transition: transform 0.3s; }
        .cart-container:hover #carrito { transform: scale(1.1); }
        .cart-counter {
            position: absolute; top: -6px; right: -6px;
            background: var(--gold); color: var(--green-deep);
            border-radius: 50%; width: 18px; height: 18px;
            display: flex; align-items: center; justify-content: center;
            font-size: 0.7rem; font-weight: 700;
        }

        /* ── HERO BANNER ── */
        .genero-hero {
            background: linear-gradient(135deg, var(--green-deep) 0%, var(--green-mid) 100%);
            padding: 3.5rem 3rem;
            position: relative;
            overflow: hidden;
        }

        .genero-hero::before {
            content: attr(data-icon);
            position: absolute;
            font-size: 18rem;
            right: 3rem;
            top: 50%;
            transform: translateY(-50%);
            opacity: 0.06;
            pointer-events: none;
        }

        .hero-breadcrumb {
            font-size: 0.75rem;
            letter-spacing: 3px;
            text-transform: uppercase;
            color: var(--green-light);
            margin-bottom: 0.8rem;
        }

        .hero-breadcrumb a {
            color: var(--gold);
            text-decoration: none;
        }
        .hero-breadcrumb a:hover { text-decoration: underline; }

        .hero-title {
            font-family: 'Playfair Display', serif;
            font-size: 3.5rem;
            font-weight: 900;
            color: var(--cream);
            animation: fadeDown 0.6s ease-out both;
        }

        .hero-line {
            width: 50px; height: 3px;
            background: var(--gold);
            margin: 1.2rem 0;
            animation: expandLine 0.7s ease-out 0.2s both;
        }

        .hero-count {
            color: var(--green-light);
            font-size: 0.9rem;
            letter-spacing: 1px;
        }

        /* ── LAYOUT ── */
        .page-wrapper {
            max-width: 1300px;
            margin: 0 auto;
            padding: 2.5rem 2rem;
            display: grid;
            grid-template-columns: 240px 1fr;
            gap: 2.5rem;
            align-items: start;
        }

        /* ── SIDEBAR ── */
        .sidebar {
            position: sticky;
            top: 90px;
        }

        .sidebar-title {
            font-family: 'Playfair Display', serif;
            font-size: 1.1rem;
            color: var(--green-deep);
            margin-bottom: 0.5rem;
            padding-bottom: 0.5rem;
            border-bottom: 2px solid var(--gold);
        }

        .genero-list { list-style: none; margin-top: 1rem; }

        .genero-list li a {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 0.6rem 0.8rem;
            text-decoration: none;
            color: var(--text-dark);
            font-size: 0.9rem;
            border-radius: 3px;
            transition: all 0.2s;
            border-left: 3px solid transparent;
        }

        .genero-list li a:hover {
            background: var(--green-pale);
            border-left-color: var(--green-accent);
            padding-left: 1.2rem;
        }

        .genero-list li a.active {
            background: var(--green-deep);
            color: var(--cream);
            border-left-color: var(--gold);
            font-weight: 700;
        }

        .genero-list li a .icon { margin-right: 6px; }

        /* ── BOOKS GRID ── */
        .books-area {}

        .books-toolbar {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 1.5rem;
        }

        .books-toolbar h2 {
            font-family: 'Playfair Display', serif;
            font-size: 1.5rem;
            color: var(--green-deep);
        }

        .books-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(220px, 1fr));
            gap: 1.8rem;
        }

        /* ── BOOK CARD ── */
        .book-card {
            background: white;
            border-radius: 4px;
            overflow: hidden;
            border: 1px solid var(--green-pale);
            box-shadow: 0 4px 16px rgba(26,61,43,0.07);
            transition: all 0.3s ease;
            display: flex;
            flex-direction: column;
            animation: fadeUp 0.5s ease-out both;
        }

        .book-card:hover {
            transform: translateY(-8px);
            box-shadow: 0 20px 40px rgba(26,61,43,0.18);
        }

        .book-img-wrap {
            height: 200px;
            overflow: hidden;
            background: var(--green-pale);
            position: relative;
        }

        .book-img-wrap img {
            width: 100%;
            height: 100%;
            object-fit: cover;
            transition: transform 0.4s ease;
        }

        .book-card:hover .book-img-wrap img { transform: scale(1.07); }

        .book-img-placeholder {
            width: 100%;
            height: 100%;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            gap: 0.5rem;
            color: var(--green-mid);
            font-size: 3rem;
        }

        .book-body {
            padding: 1.2rem;
            flex: 1;
            display: flex;
            flex-direction: column;
            gap: 0.3rem;
        }

        .book-title {
            font-family: 'Playfair Display', serif;
            font-size: 1.05rem;
            color: var(--green-deep);
            font-weight: 700;
            line-height: 1.3;
        }

        .book-author {
            font-size: 0.8rem;
            color: #888;
        }

        .book-date {
            font-size: 0.75rem;
            color: var(--green-accent);
            letter-spacing: 1px;
            text-transform: uppercase;
            margin-top: 0.2rem;
        }

        .book-footer {
            display: flex;
            align-items: center;
            justify-content: space-between;
            margin-top: auto;
            padding-top: 0.8rem;
            border-top: 1px solid var(--green-pale);
        }

        .book-price {
            font-family: 'Playfair Display', serif;
            font-size: 1.3rem;
            color: var(--green-mid);
            font-weight: 700;
        }

        .add-btn {
            background: var(--green-deep);
            color: var(--cream);
            border: none;
            padding: 0.5rem 1rem;
            border-radius: 3px;
            font-size: 0.75rem;
            font-weight: 700;
            letter-spacing: 1px;
            text-transform: uppercase;
            cursor: pointer;
            transition: all 0.3s;
        }

        .add-btn:hover { background: var(--green-accent); color: var(--green-deep); }

        /* ── EMPTY STATE ── */
        .empty-state {
            grid-column: 1 / -1;
            text-align: center;
            padding: 5rem 2rem;
            color: #aaa;
        }

        .empty-state .empty-icon { font-size: 4rem; margin-bottom: 1rem; }

        .empty-state h3 {
            font-family: 'Playfair Display', serif;
            font-size: 1.5rem;
            color: var(--green-mid);
            margin-bottom: 0.5rem;
        }

        /* ── CART MODAL ── */
        .cart-modal {
            display: none;
            position: fixed;
            top: 0; right: 0;
            width: 380px; height: 100vh;
            background: white;
            box-shadow: -10px 0 40px rgba(0,0,0,0.15);
            z-index: 999;
            flex-direction: column;
            animation: slideIn 0.3s ease;
        }

        .cart-modal.open { display: flex; }

        @keyframes slideIn {
            from { transform: translateX(100%); }
            to   { transform: translateX(0); }
        }

        .cart-header {
            background: var(--green-deep);
            padding: 1.5rem;
            display: flex;
            justify-content: space-between;
            align-items: center;
            border-bottom: 3px solid var(--gold);
        }

        .cart-header h2 { font-family: 'Playfair Display', serif; color: var(--cream); }
        .cart-close { background: none; border: none; color: var(--cream); font-size: 1.5rem; cursor: pointer; }
        .cart-items { flex: 1; overflow-y: auto; padding: 1.5rem; }

        .cart-item {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 0.8rem 0;
            border-bottom: 1px solid var(--green-pale);
        }

        .cart-item-name { font-size: 0.9rem; color: var(--green-deep); font-weight: 600; }
        .cart-item-price { color: var(--green-mid); font-weight: 700; }

        .cart-total {
            padding: 1.5rem;
            border-top: 2px solid var(--green-pale);
            font-family: 'Playfair Display', serif;
            font-size: 1.2rem;
            color: var(--green-deep);
            display: flex;
            justify-content: space-between;
        }

        .checkout-btn {
            margin: 0 1.5rem 1.5rem;
            background: var(--green-mid);
            color: white;
            border: none;
            padding: 1rem;
            border-radius: 3px;
            font-size: 0.9rem;
            font-weight: 700;
            cursor: pointer;
            letter-spacing: 2px;
            text-transform: uppercase;
            transition: background 0.3s;
        }
        .checkout-btn:hover { background: var(--green-deep); }

        /* ── TOAST ── */
        .toast {
            position: fixed;
            bottom: 2rem; left: 50%;
            transform: translateX(-50%) translateY(100px);
            background: var(--green-mid);
            color: white;
            padding: 0.8rem 2rem;
            border-radius: 3px;
            font-size: 0.9rem;
            transition: transform 0.3s ease;
            z-index: 9999;
        }
        .toast.show { transform: translateX(-50%) translateY(0); }

        /* ── FOOTER ── */
        footer {
            background: var(--green-deep);
            color: var(--green-light);
            text-align: center;
            padding: 2rem;
            font-size: 0.85rem;
            letter-spacing: 1px;
            border-top: 3px solid var(--gold);
        }
        footer span { color: var(--gold); }

        /* ── ANIMATIONS ── */
        @keyframes fadeDown {
            from { opacity: 0; transform: translateY(-20px); }
            to   { opacity: 1; transform: translateY(0); }
        }
        @keyframes expandLine {
            from { width: 0; opacity: 0; }
            to   { width: 50px; opacity: 1; }
        }
        @keyframes fadeUp {
            from { opacity: 0; transform: translateY(20px); }
            to   { opacity: 1; transform: translateY(0); }
        }

        /* ── RESPONSIVE ── */
        @media (max-width: 900px) {
            .page-wrapper { grid-template-columns: 1fr; }
            .sidebar { position: static; }
            header { padding: 1rem 1.5rem; }
            .hero-title { font-size: 2.5rem; }
            .cart-modal { width: 100%; }
        }
    </style>
</head>
<body>

<!-- HEADER -->
<header>
    <a href="inicio.php" class="logo">
        <span class="logo-main">Bookstore</span>
        <span class="logo-sub">Tu librería de confianza</span>
    </a>
    <div class="header-right">
        <a href="login.php" class="login-btn">Ingresar</a>
        <div class="cart-container" onclick="toggleCart()">
            <img src="https://cdn-icons-png.flaticon.com/512/5412/5412512.png" id="carrito">
            <span class="cart-counter" id="cart-count">0</span>
        </div>
    </div>
</header>

<!-- HERO -->
<?php
$iconos = [1=>'📖', 2=>'👻', 3=>'🌹', 4=>'😄', 5=>'🔍', 6=>'✍️'];
$icono_actual = isset($iconos[$id_genero]) ? $iconos[$id_genero] : '📚';
?>
<div class="genero-hero" data-icon="<?= $icono_actual ?>">
    <p class="hero-breadcrumb">
        <a href="inicio.php">Inicio</a> &nbsp;/&nbsp; <?= htmlspecialchars($nombre_genero) ?>
    </p>
    <h1 class="hero-title"><?= $icono_actual ?> <?= htmlspecialchars($nombre_genero) ?></h1>
    <div class="hero-line"></div>
    <p class="hero-count"><?= $libros->num_rows ?> libro<?= $libros->num_rows != 1 ? 's' : '' ?> encontrado<?= $libros->num_rows != 1 ? 's' : '' ?></p>
</div>

<!-- BODY -->
<div class="page-wrapper">

    <!-- SIDEBAR -->
    <aside class="sidebar">
        <h3 class="sidebar-title">Géneros</h3>
        <ul class="genero-list">
            <li>
                <a href="inicio.php">🏠 <span>Inicio</span></a>
            </li>
            <?php
            $iconos_lista = [1=>'📖', 2=>'👻', 3=>'🌹', 4=>'😄', 5=>'🔍', 6=>'✍️'];
            $generos->data_seek(0);
            while ($g = $generos->fetch_assoc()):
                $ic = isset($iconos_lista[$g['id_genero']]) ? $iconos_lista[$g['id_genero']] : '📚';
                $activo = ($g['id_genero'] == $id_genero) ? 'active' : '';
            ?>
            <li>
                <a href="libros.php?genero=<?= $g['id_genero'] ?>" class="<?= $activo ?>">
                    <span><?= $ic ?> <?= htmlspecialchars($g['genero']) ?></span>
                </a>
            </li>
            <?php endwhile; ?>
        </ul>
    </aside>

    <!-- BOOKS -->
    <section class="books-area">
        <div class="books-toolbar">
            <h2><?= htmlspecialchars($nombre_genero) ?></h2>
        </div>

        <div class="books-grid">
            <?php if ($libros->num_rows === 0): ?>
                <div class="empty-state">
                    <div class="empty-icon">📭</div>
                    <h3>Sin libros por el momento</h3>
                    <p>Todavía no hay títulos cargados en este género. ¡Volvé pronto!</p>
                </div>
            <?php else: ?>
                <?php $delay = 0; while ($libro = $libros->fetch_assoc()): ?>
                <div class="book-card" style="animation-delay: <?= $delay ?>ms">
                    <div class="book-img-wrap">
                        <?php if (!empty($libro['imagen'])): ?>
                            <img src="<?= htmlspecialchars($libro['imagen']) ?>" alt="<?= htmlspecialchars($libro['titulo']) ?>">
                        <?php else: ?>
                            <div class="book-img-placeholder">📚</div>
                        <?php endif; ?>
                    </div>
                    <div class="book-body">
                        <h3 class="book-title"><?= htmlspecialchars($libro['titulo']) ?></h3>
                        <p class="book-author">por <?= htmlspecialchars($libro['autor'] ?? 'Autor desconocido') ?></p>
                        <p class="book-date"><?= date('d M Y', strtotime($libro['fecha_publicacion'])) ?></p>
                        <div class="book-footer">
                            <span class="book-price">$<?= number_format($libro['precio'], 2) ?></span>
                            <button class="add-btn" onclick="addToCart('<?= htmlspecialchars(addslashes($libro['titulo'])) ?>', <?= $libro['precio'] ?>)">+ Carrito</button>
                        </div>
                    </div>
                </div>
                <?php $delay += 80; endwhile; ?>
            <?php endif; ?>
        </div>
    </section>

</div>

<!-- CART MODAL -->
<div class="cart-modal" id="cartModal">
    <div class="cart-header">
        <h2>Tu Carrito</h2>
        <button class="cart-close" onclick="toggleCart()">✕</button>
    </div>
    <div class="cart-items" id="cartItems">
        <p style="color:#aaa;text-align:center;margin-top:2rem">El carrito está vacío</p>
    </div>
    <div class="cart-total">
        <span>Total</span>
        <span id="cartTotal">$0.00</span>
    </div>
    <button class="checkout-btn">Finalizar Compra</button>
</div>

<div class="toast" id="toast"></div>

<footer>
    <p><span>Bookstore</span> &mdash; Rocio Monzon · Nicole Roglich · Denise Roglich</p>
</footer>

<script>
    let cart = [], total = 0;

    function addToCart(name, price) {
        cart.push({ name, price: parseFloat(price) });
        total += parseFloat(price);
        document.getElementById('cart-count').textContent = cart.length;
        renderCart();
        showToast('"' + name + '" agregado al carrito');
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
        document.getElementById('cartTotal').textContent = '$' + total.toFixed(2);
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
<?php $conexion->close(); ?>