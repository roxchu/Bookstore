<?php
session_start();

// Conexión a la base de datos corregida
$conexion = new mysqli("localhost", "root", "", "books_store");
if ($conexion->connect_error) {
    die("Error de conexión: " . $conexion->connect_error);
}

// Obtener y validar el género recibido por URL
$id_genero = isset($_GET['genero']) ? intval($_GET['genero']) : 0;

// Obtener nombre del género para el encabezado
$nombre_genero = "Todos los libros";
if ($id_genero > 0) {
    // Usamos 'nombre_genero' que es el nombre en tu tabla corregida
    $stmt_g = $conexion->prepare("SELECT nombre_genero FROM genero WHERE id_genero = ?");
    $stmt_g->bind_param("i", $id_genero);
    $stmt_g->execute();
    $res_g = $stmt_g->get_result();
    if ($row_g = $res_g->fetch_assoc()) {
        $nombre_genero = $row_g['nombre_genero'];
    }
    $stmt_g->close();
}

// Obtener todos los géneros para el menú lateral
$generos = $conexion->query("SELECT * FROM genero ORDER BY nombre_genero ASC");

// Obtener libros (productos) filtrados
if ($id_genero > 0) {
    // Cambiado 'libros' por 'producto' y 'fecha_publicacion' por 'id' para orden
    $stmt = $conexion->prepare("SELECT * FROM producto WHERE id_genero = ? ORDER BY id DESC");
    $stmt->bind_param("i", $id_genero);
} else {
    $stmt = $conexion->prepare("SELECT * FROM producto ORDER BY id DESC");
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
        /* Contenedor para centrar */
.search-container {
    flex: 1;
    display: flex;
    justify-content: center;
    max-width: 500px; /* Ancho máximo para que no se estire demasiado */
    margin: 0 2rem;
}

.search-box {
    display: flex;
    align-items: center;
    background: rgba(255, 255, 255, 0.1); /* Fondo sutil */
    border: 1.5px solid var(--green-accent);
    border-radius: 50px;
    padding: 4px 6px 4px 18px;
    width: 100%;
    transition: all 0.3s ease;
}

/* Efecto al hacer clic para escribir */
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
    font-family: 'Lato', sans-serif;
    font-size: 0.95rem;
    width: 100%;
    padding: 8px 0;
}

/* Cambia el color del texto cuando el fondo es blanco (focus) */
.search-box:focus-within input {
    color: var(--text-dark);
}

/* Estilo del ícono/botón */
.search-box button {
    background: var(--gold);
    color: var(--green-deep);
    border: none;
    width: 36px;
    height: 36px;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    cursor: pointer;
    transition: transform 0.2s ease, background 0.2s;
}

.search-box button:hover {
    background: var(--green-accent);
    transform: scale(1.05);
}

/* Placeholder (el texto gris de fondo) */
.search-box input::placeholder {
    color: var(--green-light);
    opacity: 0.7;
}

.search-box:focus-within input::placeholder {
    color: #999;
}

/* Ajuste para celulares */
@media (max-width: 768px) {
    .search-container {
        order: 3; /* La manda abajo en móviles si no hay espacio */
        margin: 10px 0 0 0;
        max-width: 100%;
    }
}
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

        /* ── BOOKS GRID ── */
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

        .book-img-wrap {
            height: 280px; /* Ajustado para portadas de libros */
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
            cursor: pointer;
            transition: all 0.3s;
        }
        .add-btn:hover { background: var(--green-accent); color: var(--green-deep); }

        /* ── ANIMATIONS ── */
        @keyframes fadeDown { from { opacity: 0; transform: translateY(-20px); } to { opacity: 1; transform: translateY(0); } }
        @keyframes expandLine { from { width: 0; opacity: 0; } to { width: 50px; opacity: 1; } }
        @keyframes fadeUp { from { opacity: 0; transform: translateY(20px); } to { opacity: 1; transform: translateY(0); } }

        /* ── TOAST & MODAL (Simplificados para brevedad) ── */
        .cart-modal { display: none; position: fixed; top: 0; right: 0; width: 350px; height: 100vh; background: white; z-index: 1000; box-shadow: -5px 0 15px rgba(0,0,0,0.1); flex-direction: column; }
        .cart-modal.open { display: flex; }
        .cart-header { background: var(--green-deep); color: white; padding: 1rem; display: flex; justify-content: space-between; }
        .toast { position: fixed; bottom: 20px; left: 50%; transform: translateX(-50%); background: var(--green-mid); color: white; padding: 10px 20px; border-radius: 5px; display: none; }
    </style>
</head>
<body>

<header>
    <form action="Libros.php" method="GET" class="search-container">
    <?php if(isset($id_genero) && $id_genero > 0): ?>
        <input type="hidden" name="genero" value="<?= $id_genero ?>">
    <?php endif; ?>
    
    <div class="search-box">
        <input type="text" name="q" placeholder="Buscar por título o autor..." value="<?= isset($_GET['q']) ? htmlspecialchars($_GET['q']) : '' ?>">
        <button type="submit">
            <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="11" cy="11" r="8"></circle><line x1="21" y1="21" x2="16.65" y2="16.65"></line></svg>
        </button>
    </div>
</form>
    <a href="index.html" class="logo">
        <span class="logo-main">Bookstore</span>
        <span class="logo-sub">Tu librería de confianza</span>
    </a>
    <div class="header-right">
    <?php if(isset($_SESSION['username'])): ?>
        <span style="color: var(--cream); font-size: 0.9rem;">Hola, <?= htmlspecialchars($_SESSION['username']) ?></span>
        <a href="logout.php" class="login-btn" style="margin-left: 10px;">Salir</a>
    <?php else: ?>
        <a href="login.html" class="login-btn">Ingresar</a>
    <?php endif; ?>

    <div class="cart-container" onclick="toggleCart()">
        <img src="https://cdn-icons-png.flaticon.com/512/5412/5412512.png" id="carrito">
        <span class="cart-counter" id="cart-count">0</span>
    </div>
</div>
</header>

<?php
$iconos = [1=>'📖', 2=>'👻', 3=>'🌹', 4=>'😄'];
$icono_actual = isset($iconos[$id_genero]) ? $iconos[$id_genero] : '📚';
?>
<div class="genero-hero" data-icon="<?= $icono_actual ?>">
    <p class="hero-breadcrumb">
        <a href="index.php">Inicio</a> &nbsp;/&nbsp; <?= htmlspecialchars($nombre_genero) ?>
    </p>
    <h1 class="hero-title"><?= $icono_actual ?> <?= htmlspecialchars($nombre_genero) ?></h1>
    <div class="hero-line"></div>
    <p class="hero-count"><?= $libros->num_rows ?> libro<?= $libros->num_rows != 1 ? 's' : '' ?> encontrado<?= $libros->num_rows != 1 ? 's' : '' ?></p>
</div>

<div class="page-wrapper">

    <aside class="sidebar">
        <h3 class="sidebar-title">Géneros</h3>
        <ul class="genero-list">
            <li>
                <a href="index.php">🏠 <span>Inicio</span></a>
            </li>
            <?php
            $generos->data_seek(0);
            while ($g = $generos->fetch_assoc()):
                $ic = isset($iconos[$g['id_genero']]) ? $iconos[$g['id_genero']] : '📚';
                $activo = ($g['id_genero'] == $id_genero) ? 'active' : '';
            ?>
            <li>
                <a href="Libros.php?genero=<?= $g['id_genero'] ?>" class="<?= $activo ?>">
                    <span><?= $ic ?> <?= htmlspecialchars($g['nombre_genero']) ?></span>
                </a>
            </li>
            <?php endwhile; ?>
        </ul>
    </aside>

    <section class="books-area">
        <div class="books-grid">
            <?php if ($libros->num_rows === 0): ?>
                <div style="grid-column: 1/-1; text-align: center; padding: 5rem;">
                    <h3>📭 Sin libros por el momento</h3>
                </div>
            <?php else: ?>
                <?php while ($libro = $libros->fetch_assoc()): ?>
                <div class="book-card">
                    <div class="book-img-wrap">
                        <?php if (!empty($libro['imagen'])): ?>
                            <img src="img/<?= htmlspecialchars($libro['imagen']) ?>" alt="<?= htmlspecialchars($libro['nombre']) ?>">
                        <?php else: ?>
                            <div style="height:100%; display:flex; align-items:center; justify-content:center; background:#eee;">📚</div>
                        <?php endif; ?>
                    </div>
                    <div class="book-body">
                        <h3 class="book-title"><?= htmlspecialchars($libro['nombre']) ?></h3>
                        <p style="font-size: 0.8rem; color: #666;">por <?= htmlspecialchars($libro['autor'] ?? 'Anónimo') ?></p>
                        <div style="margin-top:auto; display:flex; justify-content:space-between; align-items:center; padding-top:10px;">
                            <span class="book-price">$<?= number_format($libro['precio'], 2) ?></span>
                            <button class="add-btn" onclick="addToCart('<?= htmlspecialchars(addslashes($libro['nombre'])) ?>', <?= $libro['precio'] ?>)">+ Carrito</button>
                        </div>
                    </div>
                </div>
                <?php endwhile; ?>
            <?php endif; ?>
        </div>
    </section>
</div>

<div class="cart-modal" id="cartModal">
    <div class="cart-header">
        <h2>Tu Carrito</h2>
        <button onclick="toggleCart()" style="background:none; border:none; color:white; cursor:pointer;">✕</button>
    </div>
    <div id="cartItems" style="padding:1rem; flex:1; overflow-y:auto;"></div>
    <div style="padding:1rem; border-top:1px solid #eee;">
        <strong>Total: <span id="cartTotal">$0.00</span></strong>
        <button style="width:100%; background:var(--green-mid); color:white; border:none; padding:10px; margin-top:10px;">Finalizar</button>
    </div>
</div>

<div id="toast" class="toast"></div>

<footer>
    <p><span>Bookstore</span> &mdash; Rocio Monzon · Nicole Roglich · Denise Roglich</p>
</footer>

<script>
    const usuarioLogueado = <?= isset($_SESSION['usuario_id']) ? 'true' : 'false' ?>;

    let cart = [], total = 0;

function addToCart(name, price) {
    if (!usuarioLogueado) {
        alert("Debes iniciar sesión para poder comprar.");
        // Opcional: redirigir al login
        // window.location.href = 'login.html';
        return;
    }

    cart.push({ name, price: parseFloat(price) });
    total += parseFloat(price);
    document.getElementById('cart-count').textContent = cart.length;
    renderCart();
    showToast('"' + name + '" agregado al carrito');
}

    function renderCart() {
        const container = document.getElementById('cartItems');
        container.innerHTML = cart.map(item => `<div style="display:flex; justify-content:space-between; margin-bottom:10px;"><span>${item.name}</span><span>$${item.price.toFixed(2)}</span></div>`).join('');
        document.getElementById('cartTotal').textContent = '$' + total.toFixed(2);
    }

    function toggleCart() { document.getElementById('cartModal').classList.toggle('open'); }

    function showToast(msg) {
        const t = document.getElementById('toast');
        t.textContent = msg; t.style.display = 'block';
        setTimeout(() => { t.style.display = 'none'; }, 2000);
    }
</script>

</body>
</html>
<?php $conexion->close(); ?>