<?php
session_start();
if (!isset($_SESSION['usuario_id'])) {
    header('Location: ../registro/login.html');
    exit;
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Finalizar compra — Bookstore</title>
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@400;700;900&family=Lato:wght@300;400;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="estilos_checkout.css">
</head>
<body>
<header>
    <a href="../index.php" class="logo"><span class="logo-main">Bookstore</span><span class="logo-sub">Tu librería de confianza</span></a>
    <a class="header-link" href="libros.php">← Seguir comprando</a>
</header>

<main class="page-body">
    <section class="card" id="checkoutCard">
        <div class="progress-header">
            <h1>Finalizar compra</h1>
            <div class="steps-track"><div class="step-dot active" id="dot-1">1</div><div class="step-line" id="line-1"></div><div class="step-dot" id="dot-2">2</div><div class="step-line" id="line-2"></div><div class="step-dot" id="dot-3">3</div><div class="step-line" id="line-3"></div><div class="step-dot" id="dot-4">4</div></div>
            <div class="step-labels"><span id="label-1" class="active">Nombre</span><span id="label-2">Contacto</span><span id="label-3">Envío</span><span id="label-4">Pago</span></div>
        </div>
        <div class="card-body">
            <p id="globalError" class="alert-error"></p>
            <form id="checkoutForm" novalidate>
                <div class="step-panel active" id="step-1">
                    <p class="step-title">¿A nombre de quién hacemos el pedido?</p>
                    <div class="form-group"><label for="nombre">Nombre</label><input id="nombre" name="nombre" autocomplete="given-name" required><small id="err-nombre"></small></div>
                    <div class="form-group"><label for="apellido">Apellido</label><input id="apellido" name="apellido" autocomplete="family-name" required><small id="err-apellido"></small></div>
                    <div class="btn-row"><button class="btn btn-primary" type="button" data-next>Siguiente →</button></div>
                </div>
                <div class="step-panel" id="step-2">
                    <p class="step-title">Datos de contacto</p>
                    <div class="form-group"><label for="email">Email</label><input id="email" name="email" type="email" autocomplete="email" required><small id="err-email"></small></div>
                    <div class="form-group"><label for="telefono">Teléfono</label><input id="telefono" name="telefono" type="tel" autocomplete="tel" required><small id="err-telefono"></small></div>
                    <div class="btn-row"><button class="btn btn-secondary" type="button" data-back>← Atrás</button><button class="btn btn-primary" type="button" data-next>Siguiente →</button></div>
                </div>
                <div class="step-panel" id="step-3">
                    <p class="step-title">Datos de envío</p>
                    <div class="form-group"><label for="direccion">Dirección</label><input id="direccion" name="direccion" autocomplete="street-address" placeholder="Calle, número, localidad" required><small id="err-direccion"></small></div>
                    <div class="btn-row"><button class="btn btn-secondary" type="button" data-back>← Atrás</button><button class="btn btn-primary" type="button" data-next>Siguiente →</button></div>
                </div>
                <div class="step-panel" id="step-4">
                    <p class="step-title">Pago y resumen</p>
                    <div class="form-group"><label for="metodo">Método de pago</label><select id="metodo" name="metodo"><option value="transferencia">Transferencia (10% de descuento)</option><option value="tarjeta">Tarjeta de débito/crédito</option><option value="efectivo">Efectivo</option></select></div>
                    <div id="resumen" class="summary"></div>
                    <div class="btn-row"><button class="btn btn-secondary" type="button" data-back>← Atrás</button><button class="btn btn-primary" id="submitButton" type="submit">Confirmar compra</button></div>
                </div>
            </form>
            <div id="successPanel" class="success-panel"><div class="success-icon">✓</div><h2>¡Compra confirmada!</h2><p id="successText"></p><a class="btn btn-primary" href="../index.php">Volver al inicio</a></div>
        </div>
    </section>
</main>
<footer><span>Bookstore</span> — Tu librería de confianza</footer>
<script>
const cart = JSON.parse(localStorage.getItem('bookstore_cart') || '[]');
let currentStep = 1;
const money = value => new Intl.NumberFormat('es-AR', {style:'currency', currency:'ARS'}).format(value);
const total = () => cart.reduce((sum, item) => sum + Number(item.price || 0), 0);

if (!cart.length) { document.getElementById('checkoutForm').style.display = 'none'; document.getElementById('globalError').textContent = 'Tu carrito está vacío. Agregá un libro antes de finalizar la compra.'; document.getElementById('globalError').style.display = 'block'; }

function validateStep(step) {
    const fields = {1:['nombre','apellido'], 2:['email','telefono'], 3:['direccion']}[step] || [];
    let valid = true;
    fields.forEach(id => { const input = document.getElementById(id); const error = document.getElementById('err-' + id); const message = !input.value.trim() ? 'Este campo es obligatorio.' : (id === 'email' && !input.checkValidity() ? 'Ingresá un email válido.' : ''); input.classList.toggle('error', Boolean(message)); error.textContent = message; valid = valid && !message; });
    return valid;
}
function showStep(step) {
    document.getElementById('step-' + currentStep).classList.remove('active'); currentStep = step; document.getElementById('step-' + currentStep).classList.add('active');
    for (let i=1; i<=4; i++) { const done = i < step; document.getElementById('dot-'+i).className = 'step-dot' + (done ? ' done' : i === step ? ' active' : ''); document.getElementById('dot-'+i).textContent = done ? '✓' : i; document.getElementById('label-'+i).className = done ? 'done' : i === step ? 'active' : ''; if (i < 4) document.getElementById('line-'+i).classList.toggle('done', i < step); }
    if (step === 4) renderSummary();
}
function renderSummary() { const subtotal = total(); const discount = document.getElementById('metodo').value === 'transferencia' ? subtotal * .10 : 0; document.getElementById('resumen').innerHTML = `<h3>Resumen del pedido</h3>${cart.map(item => `<div class="summary-row"><span>${escapeHtml(item.name)}</span><strong>${money(item.price)}</strong></div>`).join('')}<div class="summary-total"><span>Subtotal</span><strong>${money(subtotal)}</strong><span>Descuento</span><strong>-${money(discount)}</strong><span>Total</span><strong>${money(subtotal-discount)}</strong></div>`; }
function escapeHtml(text) { const div = document.createElement('div'); div.textContent = text; return div.innerHTML; }
document.querySelectorAll('[data-next]').forEach(button => button.onclick = () => validateStep(currentStep) && showStep(currentStep + 1));
document.querySelectorAll('[data-back]').forEach(button => button.onclick = () => showStep(currentStep - 1));
document.getElementById('metodo').onchange = renderSummary;
document.getElementById('checkoutForm').onsubmit = async event => { event.preventDefault(); const button = document.getElementById('submitButton'); const subtotal = total(); const metodo = document.getElementById('metodo').value; const descuento = metodo === 'transferencia' ? subtotal * .10 : 0; button.disabled = true; button.textContent = 'Procesando...'; try { const response = await fetch('guardar_venta.php', {method:'POST', headers:{'Content-Type':'application/json'}, body:JSON.stringify({items:cart, metodo, total:subtotal, descuento, neto:subtotal-descuento, cliente:Object.fromEntries(new FormData(event.target))})}); const result = await response.json(); if (!response.ok || !result.success) throw new Error(result.error || 'No se pudo registrar la compra.'); localStorage.removeItem('bookstore_cart'); document.getElementById('checkoutForm').style.display='none'; document.getElementById('successText').textContent=`Pedido #${result.id_factura} confirmado por ${money(result.neto)}.`; document.getElementById('successPanel').style.display='block'; } catch(error) { document.getElementById('globalError').textContent=error.message; document.getElementById('globalError').style.display='block'; button.disabled=false; button.textContent='Confirmar compra'; } };
</script>
</body>
</html>
