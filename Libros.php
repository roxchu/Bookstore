<?php
session_start();

// Conexión a la base de datos corregida
$conexion = new mysqli("localhost", "root", "", "books_store");
if ($conexion->connect_error) {
    die("Error de conexión: " . $conexion->connect_error);
}

// 1. LÓGICA DE BÚSQUEDA Y FILTRADO
$busqueda = isset($_GET['q']) ? trim($_GET['q']) : '';
$id_genero = isset($_GET['genero']) ? intval($_GET['genero']) : 0;
$term = "%$busqueda%";

// Obtener nombre del género para el encabezado
$nombre_genero = "Todos los libros";
if ($id_genero > 0) {
    $stmt_g = $conexion->prepare("SELECT nombre_genero FROM genero WHERE id_genero = ?");
    $stmt_g->bind_param("i", $id_genero);
    $stmt_g->execute();
    $res_g = $stmt_g->get_result();
    if ($row_g = $res_g->fetch_assoc()) {
        $nombre_genero = $row_g['nombre_genero'];
    }
    $stmt_g->close();
}

// Consulta de libros con búsqueda integrada
if ($id_genero > 0) {
    $sql = "SELECT * FROM producto WHERE id_genero = ? AND (nombre LIKE ? OR autor LIKE ?) ORDER BY id DESC";
    $stmt = $conexion->prepare($sql);
    $stmt->bind_param("iss", $id_genero, $term, $term);
} else {
    $sql = "SELECT * FROM producto WHERE (nombre LIKE ? OR autor LIKE ?) ORDER BY id DESC";
    $stmt = $conexion->prepare($sql);
    $stmt->bind_param("ss", $term, $term);
}
$stmt->execute();
$libros = $stmt->get_result();
$stmt->close();

// Obtener todos los géneros para el menú lateral
$generos = $conexion->query("SELECT * FROM genero ORDER BY nombre_genero ASC");
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

        /* Logo a la izquierda */
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

        /* Barra de búsqueda en el medio */
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

        /* Derecha: Login y Carrito */
        .header-right {
            display: flex;
            align-items: center;
            gap: 1.2rem;
            flex-shrink: 0;
        }

        .login-btn {
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
        .login-btn:hover { background: var(--green-accent); color: var(--green-deep); }

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
            padding: 3rem;
            color: var(--cream);
        }
        .hero-title { font-family: 'Playfair Display', serif; font-size: 3rem; }
        .hero-line { width: 50px; height: 3px; background: var(--gold); margin: 1rem 0; }

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
            display: grid; grid-template-columns: repeat(auto-fill, minmax(200px, 1fr)); gap: 1.5rem;
        }
        .book-card {
            background: white; border-radius: 8px; overflow: hidden;
            box-shadow: 0 4px 10px rgba(0,0,0,0.05); display: flex; flex-direction: column;
        }
        .book-img-wrap { height: 250px; background: #eee; }
        .book-img-wrap img { width: 100%; height: 100%; object-fit: cover; }
        .book-body { padding: 1rem; flex: 1; display: flex; flex-direction: column; }
        .book-title { font-family: 'Playfair Display', serif; font-size: 1rem; margin-bottom: 5px; }
        .book-price { font-weight: bold; color: var(--green-mid); font-size: 1.1rem; }
        
        .add-btn {
            background: var(--green-deep); color: white; border: none;
            padding: 8px; border-radius: 4px; cursor: pointer; margin-top: 10px;
        }

        .cart-modal { display: none; position: fixed; top: 0; right: 0; width: 350px; height: 100vh; background: white; z-index: 2000; box-shadow: -5px 0 15px rgba(0,0,0,0.1); flex-direction: column; }
        .cart-modal.open { display: flex; }
        .toast { position: fixed; bottom: 20px; left: 50%; transform: translateX(-50%); background: var(--green-mid); color: white; padding: 10px 20px; border-radius: 50px; display: none; z-index: 3000; }
    </style>
</head>
<body>

<header>
    <a href="index.php" class="logo">
        <span class="logo-main">Bookstore</span>
        <span class="logo-sub">Tu librería de confianza</span>
    </a>

    <form action="Libros.php" method="GET" class="search-container">
        <?php if($id_genero > 0): ?>
            <input type="hidden" name="genero" value="<?= $id_genero ?>">
        <?php endif; ?>
        <div class="search-box">
            <input type="text" name="q" placeholder="Buscar título o autor..." value="<?= htmlspecialchars($busqueda) ?>">
            <button type="submit">🔍</button>
        </div>
    </form>

    <div class="header-right">
        <?php if(isset($_SESSION['username'])): ?>
            <span style="color: var(--cream); font-weight: bold;">Hola, <?= htmlspecialchars($_SESSION['username']) ?></span>
            <a href="logout.php" class="login-btn">Salir</a>
        <?php else: ?>
            <a href="login.html" class="login-btn">Ingresar</a>
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
    <p><?= $libros->num_rows ?> libros encontrados</p>
</div>

<div class="page-wrapper">
    <aside>
        <h3 style="border-bottom: 2px solid var(--gold); padding-bottom: 5px;">Géneros</h3>
        <ul class="genero-list">
            <li><a href="Libros.php">📚 Todos los libros</a></li>
            <?php while ($g = $generos->fetch_assoc()): ?>
                <li>
                    <a href="Libros.php?genero=<?= $g['id_genero'] ?>" class="<?= ($g['id_genero'] == $id_genero) ? 'active' : '' ?>">
                        <?= htmlspecialchars($g['nombre_genero']) ?>
                    </a>
                </li>
            <?php endwhile; ?>
        </ul>
    </aside>

    <section class="books-grid">
        <?php if ($libros->num_rows === 0): ?>
            <p style="grid-column: 1/-1; text-align: center; padding: 3rem;">No se encontraron libros para tu búsqueda.</p>
        <?php else: ?>
            <?php while ($libro = $libros->fetch_assoc()): ?>
                <div class="book-card">
                    <div class="book-img-wrap">
                        <img src="img/<?= htmlspecialchars($libro['imagen']) ?>" onerror="this.src='https://via.placeholder.com/200x300?text=Libro'">
                    </div>
                    <div class="book-body">
                        <h3 class="book-title"><?= htmlspecialchars($libro['nombre']) ?></h3>
                        <p style="font-size: 0.8rem; color: #666; margin-bottom: 10px;">por <?= htmlspecialchars($libro['autor']) ?></p>
                        <p class="book-price">$<?= number_format($libro['precio'], 2) ?></p>
                        <button class="add-btn" onclick="addToCart('<?= addslashes($libro['nombre']) ?>', <?= $libro['precio'] ?>)">+ Agregar</button>
                    </div>
                </div>
            <?php endwhile; ?>
        <?php endif; ?>
    </section>
</div>

<div class="cart-modal" id="cartModal">
    <div style="background:var(--green-deep); color:white; padding:1rem; display:flex; justify-content:space-between;">
        <h3>Carrito</h3>
        <button onclick="toggleCart()" style="background:none; border:none; color:white; cursor:pointer;">✕</button>
    </div>
    <div id="cartItems" style="flex:1; padding:1rem; overflow-y:auto;"></div>
    <div style="padding:1rem; border-top:1px solid #eee;">
        <strong>Total: <span id="cartTotal">$0.00</span></strong>
        <button style="width:100%; background:var(--gold); border:none; padding:10px; margin-top:10px; cursor:pointer; font-weight:bold;">COMPRAR</button>
    </div>
</div>

<div id="toast" class="toast"></div>

<script>
    const usuarioLogueado = <?= isset($_SESSION['usuario_id']) ? 'true' : 'false' ?>;
    let cart = [], total = 0;

    function addToCart(name, price) {
        if (!usuarioLogueado) {
            alert("Debes iniciar sesión para comprar.");
            window.location.href = 'login.html';
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
        container.innerHTML = cart.map(item => `
            <div style="display:flex; justify-content:space-between; margin-bottom:10px; font-size:0.9rem;">
                <span>${item.name}</span>
                <span>$${item.price.toFixed(2)}</span>
            </div>
        `).join('');
        document.getElementById('cartTotal').textContent = `$${total.toFixed(2)}`;
    }

    function toggleCart() { document.getElementById('cartModal').classList.toggle('open'); }

    function showToast(msg) {
        const t = document.getElementById('toast');
        t.textContent = msg; t.style.display = 'block';
        setTimeout(() => t.style.display = 'none', 2000);
    }
</script>

</body>
</html>
<?php $conexion->close(); ?>