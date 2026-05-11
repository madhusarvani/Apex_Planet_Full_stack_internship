// Loading state for forms
document.querySelectorAll('form').forEach(form => {
    form.addEventListener('submit', function(e) {
        const submitBtn = this.querySelector('button[type="submit"]');
        if (submitBtn && !submitBtn.classList.contains('btn-loading')) {
            submitBtn.classList.add('btn-loading');
            const originalText = submitBtn.innerHTML;
            submitBtn.innerHTML = '<span class="spinner"></span> Loading...';
            submitBtn.disabled = true;
            // Store original text to restore on error (optional)
            setTimeout(() => {
                if (submitBtn.disabled) {
                    submitBtn.classList.remove('btn-loading');
                    submitBtn.innerHTML = originalText;
                    submitBtn.disabled = false;
                }
            }, 10000);
        }
    });
});

// Shake animation on error
if (document.querySelector('.alert-danger')) {
    document.querySelector('.alert-danger').classList.add('shake');
    document.querySelectorAll('.form-control').forEach(el => el.classList.add('shake'));
    setTimeout(() => {
        document.querySelectorAll('.shake').forEach(el => el.classList.remove('shake'));
    }, 500);
}

// Auto-hide alerts after 5 seconds
document.querySelectorAll('.alert').forEach(alert => {
    setTimeout(() => {
        alert.style.transition = 'opacity 0.5s';
        alert.style.opacity = '0';
        setTimeout(() => alert.remove(), 500);
    }, 5000);
});

// Responsive table data-label
document.querySelectorAll('.table tbody tr').forEach(row => {
    const headers = Array.from(row.closest('table').querySelectorAll('thead th')).map(th => th.innerText);
    row.querySelectorAll('td').forEach((cell, idx) => {
        if (headers[idx]) cell.setAttribute('data-label', headers[idx]);
    });
});