
document.addEventListener('DOMContentLoaded', function () {
    // Listen for changes on inputs with class 'qty-input'
    const inputs = document.querySelectorAll('.qty-input');

    inputs.forEach(input => {
        // Guardar valor anterior para revertir en caso de error
        input.dataset.oldValue = input.value;

        input.addEventListener('change', function () {
            const id = this.getAttribute('data-id');
            let qty = parseInt(this.value);

            // Validación Local Inmediata (Clamp 1-10)
            if (isNaN(qty) || qty < 1) qty = 1;
            if (qty > 10) qty = 10;

            // Actualizar UI si cambió por la validación
            if (this.value != qty) {
                this.value = qty;
            }

            // Si es diferente al anterior, enviar
            if (qty != this.dataset.oldValue) {
                sendUpdate(id, qty, this);
            }
        });
    });
});

function updateQty(id, change) {
    const input = document.getElementById('qty-' + id);
    if (!input) return;

    // Guardar valor anterior
    if (!input.dataset.oldValue) input.dataset.oldValue = input.value;

    let currentVal = parseInt(input.value) || 0;
    let newVal = currentVal + change;

    // Validar rango
    if (newVal < 1) newVal = 1;
    if (newVal > 10) {
        alert('Máximo 10 unidades por producto.');
        newVal = 10;
    }

    // Solo actualizar si hay cambio
    if (newVal !== currentVal) {
        input.value = newVal;
        sendUpdate(id, newVal, input);
    }
}

function sendUpdate(id, quantity, inputElement = null) {
    // Obtener token de forma segura del meta tag
    const metaToken = document.querySelector('meta[name="csrf-token"]');
    const csrfToken = metaToken ? metaToken.getAttribute('content') : document.querySelector('input[name="_token"]').value;

    fetch('/shop/update-cart', {
        method: 'PATCH',
        headers: {
            'Content-Type': 'application/json',
            'Accept': 'application/json', // CRÍTICO: Para que Laravel sepa que queremos JSON
            'X-Requested-With': 'XMLHttpRequest',
            'X-CSRF-TOKEN': csrfToken
        },
        body: JSON.stringify({
            id: id,
            quantity: quantity
        })
    })
        .then(response => {
            if (!response.ok) {
                // Si el servidor devuelve error (ej. 422, 500)
                return response.json().then(errData => {
                    throw new Error(errData.error || 'Error en el servidor');
                });
            }
            return response.json();
        })
        .then(data => {
            if (data.success) {
                // Éxito: Actualizar DOM

                // 1. Subtotal del Item
                const subtotalItemEl = document.getElementById('subtotal-item-' + id);
                if (subtotalItemEl) subtotalItemEl.textContent = data.item_subtotal;

                // 2. Subtotal Global
                const cartSubtotalEl = document.getElementById('cart-subtotal');
                if (cartSubtotalEl) cartSubtotalEl.textContent = data.cart_subtotal;

                // 3. IVA
                const cartIvaEl = document.getElementById('cart-iva');
                if (cartIvaEl) cartIvaEl.textContent = data.cart_iva;

                // 4. Total Final
                const cartTotalEl = document.getElementById('cart-total');
                if (cartTotalEl) cartTotalEl.textContent = data.cart_total;

                // 5. Badge del Carrito
                const cartCountEl = document.getElementById('cart-count');
                if (cartCountEl) cartCountEl.textContent = data.cart_count;

                // Actualizar old value para referencia futura
                if (inputElement) inputElement.dataset.oldValue = quantity;
            }
        })
        .catch(error => {
            console.error('Error AJAX:', error);
            alert('No se pudo actualizar el carrito: ' + error.message);

            // Revertir valor visual si falló
            if (inputElement && inputElement.dataset.oldValue) {
                inputElement.value = inputElement.dataset.oldValue;
            }
        });
}
