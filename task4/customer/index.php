<?php
require_once '../config/db.php';
require_once '../inc/functions.php';

$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$limit = 9;
$offset = ($page - 1) * $limit;

$search = trim($_GET['search'] ?? '');
$category_raw = trim($_GET['category'] ?? '');
// Only set $category if it's a valid integer > 0
$category = is_numeric($category_raw) && $category_raw > 0 ? (int)$category_raw : null;

// Main query with prepared statement
$sql = "SELECT m.*, c.name as cat_name 
        FROM menu_items m 
        LEFT JOIN categories c ON m.category_id = c.id 
        WHERE m.is_available = 1";
$params = [];

if ($search !== '') {
    $sql .= " AND m.name LIKE ?";
    $params[] = "%$search%";
}
if ($category !== null) {
    $sql .= " AND m.category_id = ?";
    $params[] = $category;
}
$sql .= " ORDER BY m.name LIMIT $limit OFFSET $offset";
$stmt = $pdo->prepare($sql);
$stmt->execute($params);
$items = $stmt->fetchAll();

// Count total for pagination (using prepared statement)
$countSql = "SELECT COUNT(*) FROM menu_items m WHERE m.is_available = 1";
$countParams = [];
if ($search !== '') {
    $countSql .= " AND m.name LIKE ?";
    $countParams[] = "%$search%";
}
if ($category !== null) {
    $countSql .= " AND m.category_id = ?";
    $countParams[] = $category;
}
$stmtCount = $pdo->prepare($countSql);
$stmtCount->execute($countParams);
$total = $stmtCount->fetchColumn();
$totalPages = ceil($total / $limit);

$categories = $pdo->query("SELECT id, name FROM categories ORDER BY name")->fetchAll();

// Show hero section only on this page
$show_hero = true;
include '../inc/header.php';
?>

<!-- Hero Section – Swiggy/Zomato style -->
<div class="hero">
    <div class="container">
        <h1>Craving something delicious? 🍔🍕</h1>
        <p>Browse our mouth‑watering menu and order your favourite meal online.</p>
    </div>
</div>

<div class="row g-4">
    <!-- Sidebar Filters – modern card -->
    <div class="col-md-3">
        <div class="card filter-card">
            <div class="card-header">
                <i class="fas fa-sliders-h me-2"></i> Filter Menu
            </div>
            <div class="card-body">
                <form method="GET" action="">
                    <div class="mb-3">
                        <label class="form-label">Search</label>
                        <input type="text" name="search" class="form-control" 
                               placeholder="e.g. Pizza, Burger, Pasta" 
                               value="<?= htmlspecialchars($search) ?>">
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Category</label>
                        <select name="category" class="form-select">
                            <option value="">All Categories</option>
                            <?php foreach ($categories as $cat): ?>
                                <option value="<?= $cat['id'] ?>" <?= ($category !== null && $category == $cat['id']) ? 'selected' : '' ?>>
                                    <?= htmlspecialchars($cat['name']) ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <button type="submit" class="btn btn-primary w-100">
                        <i class="fas fa-search me-1"></i> Apply Filters
                    </button>
                </form>
            </div>
        </div>
    </div>

    <!-- Product Grid – 3 columns on desktop, 2 on tablet, 1 on mobile -->
    <div class="col-md-9">
        <?php if (empty($items)): ?>
            <div class="alert alert-info text-center">
                <i class="fas fa-utensils fa-2x mb-2 d-block"></i>
                No items found. Try adjusting your filters.
            </div>
        <?php else: ?>
            <div class="row g-4">
                <?php foreach ($items as $item): 
                    // Check if image is a URL (CDN) or local file
                    $imageUrl = (strpos($item['image'], 'http') === 0) 
                        ? $item['image'] 
                        : BASE_URL . '/assets/uploads/' . $item['image'];
                ?>
                    <div class="col-md-4 col-sm-6">
                        <div class="card food-card h-100 product-card">
                            <div class="product-image">
                                <img src="<?= $imageUrl ?>" 
                                     class="card-img-top food-img" 
                                     alt="<?= htmlspecialchars($item['name']) ?>"
                                     loading="lazy"
                                     onerror="this.src='data:image/svg+xml,<svg xmlns=%22http://www.w3.org/2000/svg%22 width=%22400%22 height=%22300%22><rect fill=%22%23ddd%22 width=%22400%22 height=%22300%22/><text x=%2250%25%22 y=%2250%25%22 text-anchor=%22middle%22 dy=%22.3em%22 font-size=%2224%22 fill=%22%23999%22>Food Image</text></svg>'">
                            </div>
                            <div class="card-body">
                                <h5 class="product-name"><?= htmlspecialchars($item['name']) ?></h5>
                                <p class="product-description">
                                    <?= htmlspecialchars(substr($item['description'], 0, 60)) ?>...
                                </p>
                                <div class="product-footer">
                                    <span class="product-price">$<?= number_format($item['price'], 2) ?></span>
                                    <button class="product-add-btn add-to-cart" 
                                            data-id="<?= $item['id'] ?>"
                                            title="Add to cart">
                                        <i class="fas fa-plus"></i>
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>

            <!-- Pagination -->
            <?php if ($totalPages > 1): ?>
                <nav class="mt-5">
                    <ul class="pagination justify-content-center">
                        <?php for ($i = 1; $i <= $totalPages; $i++): ?>
                            <li class="page-item <?= $i == $page ? 'active' : '' ?>">
                                <a class="page-link" href="?page=<?= $i ?>&search=<?= urlencode($search) ?>&category=<?= $category ?? '' ?>">
                                    <?= $i ?>
                                </a>
                            </li>
                        <?php endfor; ?>
                    </ul>
                </nav>
            <?php endif; ?>
        <?php endif; ?>
    </div>
</div>

<script>
document.querySelectorAll('.add-to-cart').forEach(btn => {
    btn.addEventListener('click', function() {
        let id = this.dataset.id;
        let originalHtml = this.innerHTML;
        this.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Adding...';
        this.disabled = true;
        
        fetch('<?= BASE_URL ?>/api/add_to_cart.php', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({ id: id })
        })
        .then(res => res.json())
        .then(data => {
            if (data.success) {
                document.getElementById('cart-count').innerText = data.total_items;
                this.innerHTML = '<i class="fas fa-check"></i> Added!';
                setTimeout(() => {
                    this.innerHTML = originalHtml;
                    this.disabled = false;
                }, 1500);
            } else {
                this.innerHTML = originalHtml;
                this.disabled = false;
                alert('Failed to add item.');
            }
        })
        .catch(err => {
            console.error(err);
            this.innerHTML = originalHtml;
            this.disabled = false;
            alert('Error adding to cart.');
        });
    });
});
</script>

<?php include '../inc/footer.php'; ?>