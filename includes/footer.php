<!-- FOOTER -->
<footer>
    <div class="container">
        <div class="row align-items-center">
            <div class="col-md-6 mb-3 mb-md-0">
                <div class="fw-bold fs-5 text-white mb-1">
                    <i class="bi bi-fire text-warning me-2"></i>GasKu
                </div>
                <small>Platform jual beli gas LPG terpercaya</small>
            </div>
            <div class="col-md-6 text-md-end">
                <small>&copy; <?= date('Y') ?> GasKu. Semua hak dilindungi.</small>
            </div>
        </div>
    </div>
</footer>

<!-- Toast Container -->
<div class="toast-container position-fixed bottom-0 end-0 p-3">
    <div id="mainToast" class="toast align-items-center border-0" role="alert">
        <div class="d-flex">
            <div class="toast-body" id="toastMessage"></div>
            <button type="button" class="btn-close me-2 m-auto" data-bs-dismiss="toast"></button>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script>
function showToast(message, type = 'success') {
    const toast = document.getElementById('mainToast');
    const msg = document.getElementById('toastMessage');
    msg.textContent = message;
    toast.className = `toast align-items-center text-white border-0 bg-${type === 'success' ? 'success' : 'danger'}`;
    new bootstrap.Toast(toast, { delay: 3000 }).show();
}

// QTY Controls
document.querySelectorAll('.qty-minus').forEach(btn => {
    btn.addEventListener('click', function() {
        const input = this.nextElementSibling;
        if (parseInt(input.value) > 1) input.value = parseInt(input.value) - 1;
        updateSubtotal(input);
    });
});

document.querySelectorAll('.qty-plus').forEach(btn => {
    btn.addEventListener('click', function() {
        const input = this.previousElementSibling;
        const max = parseInt(input.getAttribute('max') || 9999);
        if (parseInt(input.value) < max) input.value = parseInt(input.value) + 1;
        updateSubtotal(input);
    });
});

function updateSubtotal(input) {
    const harga = parseFloat(input.getAttribute('data-harga') || 0);
    const qty = parseInt(input.value);
    const subtotalEl = input.closest('.product-row, .cart-item, tr')?.querySelector('.subtotal');
    if (subtotalEl && harga) {
        subtotalEl.textContent = 'Rp ' + (harga * qty).toLocaleString('id-ID');
    }
    updateTotal();
}

function updateTotal() {
    let total = 0;
    document.querySelectorAll('.qty-input').forEach(input => {
        const harga = parseFloat(input.getAttribute('data-harga') || 0);
        const qty = parseInt(input.value || 0);
        total += harga * qty;
    });
    const totalEl = document.getElementById('totalHarga');
    if (totalEl) totalEl.textContent = 'Rp ' + total.toLocaleString('id-ID');
}
</script>
</body>
</html>
