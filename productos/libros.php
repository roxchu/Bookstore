<?php
session_start();

// 1. Incluimos los archivos del DAO y el Modelo
require_once __DIR__ . '/../models/Productos.php';
require_once __DIR__ . '/../DAO/ProductoDAO.php';
require_once __DIR__ . '/../models/genero.php';
require_once __DIR__ . '/../DAO/generoDAO.php';
require_once __DIR__ . '/../conexion.php';
// Conexión centralizada — la misma instancia mysqli que usan todas las vistas
$conexion = Conexion::conectar();

// 2. Instanciamos los DAO pasándoles la conexión
$productoDAO = new ProductoDAO($conexion);
$generoDAO   = new GeneroDAO($conexion);

// 3. Capturamos los filtros de la URL
$busqueda = isset($_GET['q']) ? trim($_GET['q']) : '';
$id_genero = isset($_GET['genero']) ? intval($_GET['genero']) : 0;

// Obtener nombre del género para el encabezado — vía GeneroDAO::getById()
$nombre_genero = "Todos los libros";
if ($id_genero > 0) {
    $genero = $generoDAO->getById($id_genero);
    if ($genero !== null) {
        $nombre_genero = $genero->getNombreGenero();
    }
}

// 4. ¡ACA USAMOS EL DAO! Traemos la lista de objetos Producto
$lista_libros = $productoDAO->buscarYFiltrar($busqueda, $id_genero);

// Obtener todos los géneros para el menú lateral — vía GeneroDAO::getAll()
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

        /* Estilo del desplegable */
.book-description {
    margin-top: 10px;
    font-size: 0.85rem;
    color: #444;
    border-top: 1px solid var(--green-pale);
    padding-top: 5px;
}

.book-description summary {
    cursor: pointer;
    font-weight: bold;
    color: var(--green-mid);
    outline: none;
    list-style: none; /* Quita la flechita por defecto en algunos navegadores */
}

.book-description summary::-webkit-details-marker {
    display: none; /* Quita la flechita en Safari/Chrome */
}

.book-description summary:hover {
    color: var(--gold);
}

.book-description p {
    background: var(--green-pale);
    padding: 10px;
    border-radius: 5px;
    margin-top: 5px;
    line-height: 1.4;
}
.invoice-overlay {
    position: fixed;
    top: 0; left: 0; width: 100%; height: 100%;
    background: rgba(0,0,0,0.7);
    display: none; /* Se activa con JS */
    justify-content: center;
    align-items: center;
    z-index: 3000;
}

.invoice-box {
    background: white;
    width: 90%;
    max-width: 450px;
    border-radius: 10px;
    overflow: hidden;
    box-shadow: 0 10px 30px rgba(0,0,0,0.3);
}

.invoice-header {
    background: var(--green-deep);
    color: var(--gold);
    padding: 1.5rem;
    text-align: center;
    position: relative;
}

.invoice-body {
    padding: 2rem;
    color: var(--text-dark);
    font-family: 'Courier New', Courier, monospace; /* Estilo ticket */
}

.invoice-item {
    display: flex;
    justify-content: space-between;
    margin-bottom: 8px;
    border-bottom: 1px dashed #ccc;
}

.confirm-btn {
    width: 100%;
    background: var(--gold);
    color: var(--green-deep);
    border: none;
    padding: 15px;
    font-weight: bold;
    cursor: pointer;
    font-size: 1rem;
}

.confirm-btn:hover { background: #b8973d; }
/* Esto hace que el modal de checkout se comporte como el del carrito */
#checkoutModal {
    display: none; 
    position: fixed;
    top: 0;
    right: 0;
    width: 350px;
    height: 100vh;
    background: white;
    z-index: 2500;
    box-shadow: -5px 0 15px rgba(0,0,0,0.2);
    flex-direction: column;
}

#checkoutModal.open {
    display: flex;
}
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
.dropdown-menu a:hover {
    background-color: #f5f5f5;
}
.user-dropdown:hover .dropdown-menu {
    display: block;
}
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
                    <p style="font-size: 0.8rem; color: #666; margin-bottom: 5px;">por <?= htmlspecialchars($libro->getAutor()) ?></p>

                    <details class="book-description">
                        <summary>Ver descripción</summary>
                        <div class="description-text">
                            <?= htmlspecialchars($libro->getDetalle() ?? 'Sin descripción disponible.') ?>
                        </div>
                    </details>

                    <div style="margin-top: auto; display: flex; justify-content: space-between; align-items: center; padding-top: 15px;">
                        <span class="book-price">$<?= number_format($libro->getPrecio(), 2) ?></span>
                        <button class="add-btn" 
                            data-name="<?= htmlspecialchars(json_encode($libro->getNombre()), ENT_QUOTES) ?>"
                            data-price="<?= htmlspecialchars($libro->getPrecio(), ENT_QUOTES) ?>"
                            onclick="addToCart(this.dataset.name, parseFloat(this.dataset.price))">
                            + Carrito
                        </button>                    
                    </div>
                </div>
            </div>
        <?php endforeach; ?>
    <?php endif; ?>
</section>
    
</div>

<div class="cart-modal" id="cartModal">
    <div style="background:var(--green-deep); color:white; padding:1rem; display:flex; justify-content:space-between;">
        <h3>Carrito</h3>
        <button onclick="toggleCart()" style="background:none; border:none; color:white; cursor:pointer;">✕</button>
    </div>
    
    <div id="cartItems" style="flex:1; padding:1rem; overflow-y:auto;">
        </div>
    
    <div style="padding:1rem; border-top:1px solid #eee;">
        <strong>Total: <span id="cartTotal">$0.00</span></strong>
        
        <button onclick="finalizarCompra()" style="width:100%; background:var(--gold); border:none; padding:10px; margin-top:10px; cursor:pointer; font-weight:bold; color:var(--green-deep);">
            FINALIZAR COMPRA
        </button>
    </div>
</div>
<div id="invoiceModal" class="invoice-overlay">
    <div class="invoice-box">
        <div class="invoice-header">
            <h2>PROCESANDO COMPRA</h2>
            <button onclick="closeInvoice()" class="close-invoice">✕</button>
        </div>
        <div id="invoiceContent" class="invoice-body">
            </div>
        <div class="invoice-footer">
            <button onclick="confirmarPedido()" class="confirm-btn">CONFIRMAR Y FINALIZAR</button>
        </div>
    </div>
</div>
<div id="toast" class="toast"></div>

<script>
    const usuarioLogueado = <?= isset($_SESSION['usuario_id']) ? 'true' : 'false' ?>;
    let cart = [], total = 0;

function addToCart(name, price) {
    if (!usuarioLogueado) {
        alert("Debes iniciar sesión para comprar.");
        // Corregimos la ruta para que vaya a la carpeta registro
        window.location.href = '../registro/login.html';
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
        container.innerHTML = `<p style="text-align:center; color:#888; margin-top:2rem;">El carrito está vacío</p>`;
    } else {
        container.innerHTML = cart.map((item, index) => `
            <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:12px; font-size:0.9rem; border-bottom:1px solid #eee; padding-bottom:5px;">
                <div style="flex:1;">
                    <span style="display:block; font-weight:bold;">${item.name}</span>
                    <span style="color:var(--green-mid);">$${item.price.toFixed(2)}</span>
                </div>
                <button onclick="removeFromCart(${index})" style="background: #ff4d4d; color: white; border: none; border-radius: 4px; padding: 2px 8px; cursor: pointer; margin-left: 10px; font-size: 0.8rem;">
                    ✕
                </button>
            </div>
        `).join('');
    }
    
    document.getElementById('cartTotal').textContent = `$${total.toFixed(2)}`;

    // Si el modal de checkout está abierto, actualizamos sus totales también
    if(document.getElementById('checkoutModal').classList.contains('open')) {
        actualizarTotalFinal();
    }
}
    function toggleCart() { document.getElementById('cartModal').classList.toggle('open'); }

    function showToast(msg) {
        const t = document.getElementById('toast');
        t.textContent = msg; t.style.display = 'block';
        setTimeout(() => t.style.display = 'none', 2000);
    }
    function finalizarCompra() {
    if (cart.length === 0) {
        alert("El carrito está vacío");
        return;
    }
    
    // Cerramos el carrito lateral y abrimos el de finalizar
    toggleCart(); 
    document.getElementById('checkoutModal').classList.add('open');
    
    // Llenamos el resumen
    const summary = document.getElementById('checkoutSummary');
    summary.innerHTML = cart.map(item => `
        <div style="display:flex; justify-content:space-between; margin-bottom:5px;">
            <span>${item.name}</span>
            <span>$${item.price.toFixed(2)}</span>
        </div>
    `).join('');
    
    actualizarTotalFinal();
}

function actualizarTotalFinal() {
    const metodo = document.querySelector('input[name="metodoPago"]:checked').value;
    let descuento = (metodo === 'transferencia') ? total * 0.10 : 0;
    let neto = total - descuento;

    document.getElementById('st-total').textContent = `$${total.toFixed(2)}`;
    document.getElementById('desc-total').textContent = `-$${descuento.toFixed(2)}`;
    document.getElementById('final-total').textContent = `$${neto.toFixed(2)}`;
}

function closeCheckout() {
    document.getElementById('checkoutModal').classList.remove('open');
}

async function confirmarVentaFinal() {
    if (!cart || cart.length === 0) {
        alert("El carrito está vacío.");
        return;
    }

    let metodoPago = "Efectivo";
    const selectMetodo = document.getElementById("metodo-pago") || document.getElementById("metodo_pago");
    if (selectMetodo) {
        metodoPago = selectMetodo.value;
    }

    try {
        const response = await fetch('guardar_venta.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json'
            },
            body: JSON.stringify({
                total: total,
                metodo: metodoPago,
                items: cart
            })
        });

        const result = await response.json();

        if (result.success) {
            alert("¡Compra guardada con éxito!");
            
            // BUSCAMOS EL CONTENEDOR DEL TICKET (Ajustado a los campos en español del backend)
            const ticketContent = document.getElementById("ticketContent");
            if (ticketContent && result.usuario) {
                ticketContent.innerHTML = `
                    <p><b>Factura N°:</b> ${result.id_factura}</p>
                    <p><b>Cliente:</b> ${result.usuario.realname}</p>
                    <p><b>Dirección:</b> ${result.usuario.direccion}</p>
                    <p><b>Email:</b> ${result.usuario.email}</p>
                    <hr style="border-top:1px dashed #333; margin: 10px 0;">
                    <p><b>Método de Pago:</b> ${result.compra.metodo}</p>
                    <p><b>Fecha:</b> ${result.compra.fecha}</p>
                    <h3 style="text-align:right; margin-top:15px; color:#1a3d2b;">Total: $${result.compra.total.toFixed(2)}</h3>
                `;
                
                // Cerramos el modal de confirmación y abrimos el del ticket impreso
                if (document.getElementById("checkoutModal")) document.getElementById("checkoutModal").style.display = "none";
                if (document.getElementById("ticketModal")) document.getElementById("ticketModal").style.display = "flex";
            } else {
                // Si por alguna razón no tenés el contenedor de ticket, te avisa y recarga
                alert("Venta procesada. Redirigiendo...");
                location.reload();
            }

            // Limpiamos el carrito local tras el éxito
            cart = [];
            total = 0;
            if (document.getElementById('cart-count')) document.getElementById('cart-count').textContent = '0';
            
        } else {
            alert("Error en el servidor: " + (result.error || "No se pudo procesar la venta."));
        }

    } catch (error) {
        console.error("Error capturado:", error);
        alert("Error en el script de compra: " + error.message);
    }
}
function confirmarPedido() {
    alert("¡Pedido confirmado con éxito!");
    // Aquí es donde luego limpiarías el carrito y guardarías en la BD
    cart = [];
    total = 0;
    document.getElementById('cart-count').textContent = '0';
    renderCart();
    closeInvoice();
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
</script>
<div class="cart-modal" id="checkoutModal">
    <div style="background:var(--green-deep); color:white; padding:1.5rem; display:flex; justify-content:space-between; align-items:center;">
        <h2 style="font-family:'Playfair Display';">Finalizar Compra</h2>
        <button onclick="closeCheckout()" style="background:none; border:none; color:white; cursor:pointer; font-size:1.5rem;">✕</button>
    </div>

    <div style="padding:1.5rem; flex:1; overflow-y:auto;">
        <h3 style="color:var(--green-mid); border-bottom:1px solid var(--gold); padding-bottom:10px;">1. Resumen del Pedido</h3>
        <div id="checkoutSummary" style="margin:15px 0; font-size:0.9rem;"></div>

        <h3 style="color:var(--green-mid); border-bottom:1px solid var(--gold); padding-bottom:10px; margin-top:20px;">2. Método de Pago</h3>
        <div style="margin-top:15px;" class="metodos-pagos">
            <label style="display:block; margin-bottom:10px; cursor:pointer;">
                <input type="radio" name="metodoPago" value="transferencia" checked onchange="actualizarTotalFinal()"> 
                🏦 Transferencia (10% OFF)
            </label>
            <label style="display:block; cursor:pointer;">
                <input type="radio" name="metodoPago" value="tarjeta" onchange="actualizarTotalFinal()"> 
                💳 Tarjeta Débito/Crédito
            </label>
        </div>
    </div>

    <div style="padding:1.5rem; border-top:2px solid var(--gold); background:var(--green-pale);">
        <div style="display:flex; justify-content:space-between; margin-bottom:5px;">
            <span>Subtotal:</span>
            <span id="st-total"></span>
        </div>
        <div style="display:flex; justify-content:space-between; margin-bottom:10px; color:red;">
            <span>Descuento:</span>
            <span id="desc-total"></span>
        </div>
        <div style="display:flex; justify-content:space-between; font-weight:bold; font-size:1.2rem;">
            <span>TOTAL:</span>
            <span id="final-total" style="color:var(--green-deep);"></span>
        </div>
        <button onclick="confirmarVentaFinal()" style="width:100%; background:var(--green-deep); color:white; border:none; padding:15px; margin-top:15px; cursor:pointer; font-weight:bold; border-radius:5px;">
            CONFIRMAR Y PAGAR
        </button>
    </div>
</div>
<div id="ticketModal" style="display:none; position:fixed; top:0; left:0; width:100%; height:100%; background:rgba(0,0,0,0.85); z-index:5000; justify-content:center; align-items:center;">
    <div style="background:white; padding:30px; border-radius:15px; max-width:450px; width:90%; font-family: 'Courier New', Courier, monospace; box-shadow: 0 10px 30px rgba(0,0,0,0.5);">
        
        <div style="text-align:center; border-bottom:2px dashed #333; padding-bottom:15px;">
            <h2 style="margin:0; color:#1a3d2b;">BOOKSTORE</h2>
            <p style="margin:5px 0; font-size:0.9rem;">Comprobante de Pago</p>
        </div>

        <div id="ticketContent" style="margin-top:20px; font-size:0.95rem; color:#333;">
            </div>

        <button onclick="window.location.href='../index.php'" style="width:100%; background:#1a3d2b; color:white; border:none; padding:12px; margin-top:25px; cursor:pointer; border-radius:8px; font-weight:bold; font-family: 'Lato', sans-serif;">
            LISTO, VOLVER AL INICIO
        </button>
    </div>
</div>
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