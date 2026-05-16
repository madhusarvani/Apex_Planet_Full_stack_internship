function updateCart(itemId, quantity) {
    fetch(BASE_URL + '/api/update_cart.php', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify({ id: itemId, quantity: quantity })
    })
    .then(res => res.json())
    .then(data => {
        if(data.success) {
            document.getElementById('cart-count').innerText = data.total_items;
            location.reload(); // or update subtotal dynamically
        }
    });
}