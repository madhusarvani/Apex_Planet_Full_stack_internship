<?php
require_once '../config/db.php';
require_once '../inc/functions.php';

if (!isLoggedIn()) {
    redirect(BASE_URL . '/login.php');
}

$search = $_GET['search'] ?? '';
$category = $_GET['category'] ?? '';
$user_id = $_SESSION['user_id'];

// Fetch recommended items (based on popularity or user logs)
$sql = "SELECT m.*, c.name as cat_name 
        FROM menu_items m 
        LEFT JOIN categories c ON m.category_id = c.id 
        WHERE m.is_available = 1";
$params = [];
if ($search) {
    $sql .= " AND m.name LIKE ?";
    $params[] = "%$search%";
}
if ($category) {
    $sql .= " AND m.category_id = ?";
    $params[] = $category;
}
$sql .= " ORDER BY m.calories ASC LIMIT 6";
$stmt = $pdo->prepare($sql);
$stmt->execute($params);
$recommended = $stmt->fetchAll();

// For nutrition summary (e.g., last logged meal)
$lastLog = $pdo->prepare("SELECT ml.*, m.name, m.calories, m.carbs, m.fat, m.protein 
    FROM meal_logs ml 
    JOIN menu_items m ON ml.menu_item_id = m.id 
    WHERE ml.user_id = ? 
    ORDER BY ml.log_date DESC LIMIT 1");
$lastLog->execute([$user_id]);
$lastMeal = $lastLog->fetch();

// Default daily targets
$target_carbs = 120;
$target_fat = 60;
$target_protein = 60;

if ($lastMeal) {
    $carbs_pct = min(100, ($lastMeal['carbs'] / $target_carbs) * 100);
    $fat_pct = min(100, ($lastMeal['fat'] / $target_fat) * 100);
    $protein_pct = min(100, ($lastMeal['protein'] / $target_protein) * 100);
    $overall_score = round(($carbs_pct + $fat_pct + $protein_pct) / 3);
    $score = min(10, floor($overall_score / 10));
} else {
    $carbs_pct = $fat_pct = $protein_pct = 0;
    $score = 8; // default
}

include '../inc/header.php';
?>

<!-- AI Hero Section -->
<div class="ai-hero">
    <div class="container">
        <div class="row align-items-center">
            <div class="col-md-8">
                <span class="ai-badge mb-2">✨ AI Food Insights</span>
                <h1 class="mt-2">Discover. Track. Eat Better.</h1>
                <p class="text-light-emphasis">Powered by AI</p>
                <div class="ai-search">
                    <i class="fas fa-search text-muted"></i>
                    <form method="GET" action="" class="flex-grow-1 d-flex">
                        <input type="text" name="search" placeholder="Search Food..." value="<?= htmlspecialchars($search) ?>">
                        <button type="submit">Search</button>
                    </form>
                </div>
            </div>
            <div class="col-md-4 text-center">
                <i class="fas fa-robot fa-4x" style="opacity:0.8"></i>
                <p class="mt-2 small">AI food assistant<br>Smart insights for healthier choices</p>
            </div>
        </div>
    </div>
</div>

<div class="container">
    <!-- Category Pills -->
    <div class="d-flex flex-wrap gap-2 mb-4 justify-content-center">
        <span class="category-pill <?= !$category ? 'active' : '' ?>" data-cat="">All</span>
        <?php 
        $cats = $pdo->query("SELECT * FROM categories")->fetchAll();
        foreach($cats as $c): 
        ?>
        <span class="category-pill <?= $category == $c['id'] ? 'active' : '' ?>" data-cat="<?= $c['id'] ?>"><?= $c['name'] ?></span>
        <?php endforeach; ?>
    </div>

    <div class="row g-4">
        <!-- Recommended column (left) -->
        <div class="col-lg-7">
            <div class="d-flex justify-content-between align-items-center mb-3">
                <h5 class="fw-bold">Recommended for you</h5>
                <span class="text-muted small">Based on your taste</span>
            </div>
            <div class="row g-3">
                <?php foreach ($recommended as $item): ?>
                <div class="col-md-6">
                    <div class="reco-card position-relative">
                        <img src="<?= htmlspecialchars($item['image']) ?>" class="reco-img" alt="<?= $item['name'] ?>">
                        <?php if ($item['calories'] < 500): ?>
                        <span class="reco-badge">20% Off</span>
                        <?php endif; ?>
                        <div class="reco-content">
                            <div class="d-flex justify-content-between">
                                <h6 class="reco-title"><?= htmlspecialchars($item['name']) ?></h6>
                                <span class="reco-calories"><?= $item['calories'] ?> Kcal</span>
                            </div>
                            <div class="d-flex justify-content-between mt-2">
                                <span class="fw-bold text-primary">£<?= number_format($item['price'],2) ?></span>
                                <button class="btn btn-sm btn-primary add-to-log" data-id="<?= $item['id'] ?>">+ Add</button>
                            </div>
                        </div>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>
        </div>

        <!-- Nutrition Summary & AI Assistant (right) -->
        <div class="col-lg-5">
            <div class="nutrition-card">
                <h5 class="fw-bold">Nutrition Summary</h5>
                <p class="text-muted small">Per serving</p>
                <div class="mb-3">
                    <div class="d-flex justify-content-between small mb-1">
                        <span>Carbs</span><span><?= $lastMeal ? $lastMeal['carbs'] : 0 ?>g / <?= $target_carbs ?>g</span>
                    </div>
                    <div class="progress-bar-custom"><div class="progress-fill" style="width: <?= $carbs_pct ?>%"></div></div>
                </div>
                <div class="mb-3">
                    <div class="d-flex justify-content-between small mb-1">
                        <span>Fat</span><span><?= $lastMeal ? $lastMeal['fat'] : 0 ?>g / <?= $target_fat ?>g</span>
                    </div>
                    <div class="progress-bar-custom"><div class="progress-fill" style="width: <?= $fat_pct ?>%"></div></div>
                </div>
                <div class="mb-3">
                    <div class="d-flex justify-content-between small mb-1">
                        <span>Protein</span><span><?= $lastMeal ? $lastMeal['protein'] : 0 ?>g / <?= $target_protein ?>g</span>
                    </div>
                    <div class="progress-bar-custom"><div class="progress-fill" style="width: <?= $protein_pct ?>%"></div></div>
                </div>
                <hr>
                <div class="text-center">
                    <span class="nutri-score"><?= $score ?>.2/10</span>
                    <div class="ai-message mt-2">
                        <i class="fas fa-lightbulb text-primary me-2"></i>
                        Great meal choice!<br>Try adding a side salad for more fiber.
                    </div>
                </div>
                <div class="d-flex gap-2 mt-3">
                    <button class="btn btn-primary w-50" id="addToLogBtn">Add to Log</button>
                    <button class="btn btn-outline-primary w-50" id="addToFavBtn">Add to Favourites</button>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
// Category filter redirect
document.querySelectorAll('.category-pill').forEach(pill => {
    pill.addEventListener('click', function() {
        let cat = this.dataset.cat;
        let url = new URL(window.location.href);
        if(cat) url.searchParams.set('category', cat);
        else url.searchParams.delete('category');
        window.location.href = url.toString();
    });
});

// Add to log / favourites (AJAX)
document.querySelectorAll('.add-to-log').forEach(btn => {
    btn.addEventListener('click', function() {
        let itemId = this.dataset.id;
        fetch('<?= BASE_URL ?>/api/add_to_log.php', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({ id: itemId, meal_type: 'lunch' })
        })
        .then(res => res.json())
        .then(data => {
            if(data.success) alert('Added to your food log!');
            else alert('Error');
        });
    });
});

document.getElementById('addToLogBtn')?.addEventListener('click', () => {
    // Use the last selected item? Or implement a default – we can show a modal.
    alert('Select a meal first, then click Add to Log');
});
</script>

<?php include '../inc/footer.php'; ?>