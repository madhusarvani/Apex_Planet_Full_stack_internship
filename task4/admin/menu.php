<?php
require_once '../config/db.php';
require_once '../inc/auth.php';
if (!isAdmin()) redirect('../customer/index.php');

$action = $_GET['action'] ?? 'list';
$id = (int)($_GET['id'] ?? 0);
$message = '';

// Handle delete
if ($action === 'delete' && $id) {
    // Get the image path to delete the file
    $stmt = $pdo->prepare("SELECT image FROM menu_items WHERE id = ?");
    $stmt->execute([$id]);
    $item_to_delete = $stmt->fetch();
    
    if ($item_to_delete && $item_to_delete['image']) {
        // Delete uploaded image file if it exists (not a URL)
        if (strpos($item_to_delete['image'], 'http') !== 0) {  // Not a URL
            $image_path = '../assets/uploads/' . basename($item_to_delete['image']);
            if (file_exists($image_path)) {
                unlink($image_path);
            }
        }
    }
    
    // Delete from menu_items
    $stmt = $pdo->prepare("DELETE FROM menu_items WHERE id = ?");
    $stmt->execute([$id]);
    
    header("Location: menu.php?msg=deleted");
    exit;
}

// Handle add/edit form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = trim($_POST['name']);
    $desc = trim($_POST['description']);
    $price = (float)$_POST['price'];
    $category_id = (int)$_POST['category_id'];
    $image = '';
    
    // Handle image upload
    if (isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
        $upload_dir = '../assets/uploads/';
        if (!is_dir($upload_dir)) {
            mkdir($upload_dir, 0755, true);
        }
        
        $file_tmp = $_FILES['image']['tmp_name'];
        $file_name = time() . '_' . preg_replace('/[^a-zA-Z0-9._-]/', '', basename($_FILES['image']['name']));
        $file_path = $upload_dir . $file_name;
        
        // Only allow image files
        $allowed_types = ['image/jpeg', 'image/png', 'image/gif', 'image/webp'];
        $file_type = mime_content_type($file_tmp);
        
        if (in_array($file_type, $allowed_types) && move_uploaded_file($file_tmp, $file_path)) {
            $image = $file_name;  // Store only filename, not the 'uploads/' prefix
        } else {
            $image = trim($_POST['image_url'] ?? ''); // Fallback to URL if upload fails
        }
    } else {
        // If no new image uploaded, use existing image or URL fallback
        if ($action === 'edit' && $id) {
            $stmt = $pdo->prepare("SELECT image FROM menu_items WHERE id = ?");
            $stmt->execute([$id]);
            $existing = $stmt->fetch();
            $image = $existing['image'] ?? trim($_POST['image_url'] ?? '');
        } else {
            $image = trim($_POST['image_url'] ?? '');
        }
    }

    if ($action === 'edit' && $id) {
        // Delete old image if updating with new one
        if (isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
            $stmt = $pdo->prepare("SELECT image FROM menu_items WHERE id = ?");
            $stmt->execute([$id]);
            $old_item = $stmt->fetch();
            if ($old_item && $old_item['image'] && strpos($old_item['image'], 'http') !== 0) {  // Not a URL
                $old_path = '../assets/uploads/' . basename($old_item['image']);
                if (file_exists($old_path)) {
                    unlink($old_path);
                }
            }
        }
        
        $stmt = $pdo->prepare("UPDATE menu_items SET name=?, description=?, price=?, category_id=?, image=? WHERE id=?");
        $stmt->execute([$name, $desc, $price, $category_id, $image, $id]);
        header("Location: menu.php?msg=updated");
    } else {
        $stmt = $pdo->prepare("INSERT INTO menu_items (name, description, price, category_id, image) VALUES (?,?,?,?,?)");
        $stmt->execute([$name, $desc, $price, $category_id, $image]);
        header("Location: menu.php?msg=added");
    }
    exit;
}

// Fetch item for editing
$item = null;
if ($action === 'edit' && $id) {
    $stmt = $pdo->prepare("SELECT * FROM menu_items WHERE id = ?");
    $stmt->execute([$id]);
    $item = $stmt->fetch();
    if (!$item) header("Location: menu.php");
}

// List all items
$items = $pdo->query("SELECT m.*, c.name as cat_name FROM menu_items m LEFT JOIN categories c ON m.category_id = c.id ORDER BY m.id DESC")->fetchAll();
$categories = $pdo->query("SELECT id, name FROM categories ORDER BY name")->fetchAll();

include '../inc/header.php';
?>
<div class="card">
    <div class="card-header d-flex justify-content-between">
        <h4>Manage Menu</h4>
        <a href="?action=add" class="btn btn-primary">Add New Item</a>
    </div>
    <div class="card-body">
        <?php if (isset($_GET['msg'])): ?>
            <div class="alert alert-success">Item <?= $_GET['msg'] ?> successfully.</div>
        <?php endif; ?>

        <?php if ($action === 'add' || ($action === 'edit' && $item)): ?>
            <h5><?= $action === 'add' ? 'Add Item' : 'Edit Item' ?></h5>
            <form method="POST" enctype="multipart/form-data">
                <div class="mb-3"><label>Name</label><input type="text" name="name" class="form-control" value="<?= $item['name'] ?? '' ?>" required></div>
                <div class="mb-3"><label>Description</label><textarea name="description" class="form-control"><?= $item['description'] ?? '' ?></textarea></div>
                <div class="mb-3"><label>Price (USD)</label><input type="number" step="0.01" name="price" class="form-control" value="<?= $item['price'] ?? '' ?>" required></div>
                <div class="mb-3"><label>Category</label><select name="category_id" class="form-select"><?php foreach($categories as $c): ?><option value="<?= $c['id'] ?>" <?= (isset($item) && $item['category_id']==$c['id']) ? 'selected' : '' ?>><?= $c['name'] ?></option><?php endforeach; ?></select></div>
                
                <div class="mb-3">
                    <label>Item Image</label>
                    <div class="mb-2">
                        <?php if (isset($item) && $item['image']): ?>
                            <div class="mb-2">
                                <small class="text-muted">Current image:</small><br>
                                <?php 
                                    $img_src = (strpos($item['image'], 'http') === 0) 
                                        ? $item['image']
                                        : BASE_URL . '/assets/uploads/' . $item['image'];
                                ?>
                                <img src="<?= $img_src ?>" width="100" height="100" style="object-fit:cover; border-radius: 8px;">
                            </div>
                        <?php endif; ?>
                    </div>
                    <input type="file" name="image" class="form-control" accept="image/*" <?= $action === 'add' ? 'required' : '' ?>>
                    <small class="text-muted">Supported formats: JPG, PNG, GIF, WebP</small>
                    <?php if ($action === 'add'): ?>
                        <div class="mt-2">
                            <input type="text" name="image_url" class="form-control" placeholder="Or paste image URL as fallback">
                        </div>
                    <?php endif; ?>
                </div>
                
                <button type="submit" class="btn btn-primary">Save</button>
                <a href="menu.php" class="btn btn-secondary">Cancel</a>
            </form>
        <?php else: ?>
            <table class="table table-bordered">
                <thead><tr><th>ID</th><th>Image</th><th>Name</th><th>Price</th><th>Category</th><th>Actions</th></tr></thead>
                <tbody>
                    <?php foreach($items as $row): 
                        // Construct proper image URL
                        $img_url = (strpos($row['image'], 'http') === 0) 
                            ? $row['image']
                            : BASE_URL . '/assets/uploads/' . $row['image'];
                    ?>
                    <tr>
                        <td><?= $row['id'] ?></td>
                        <td><img src="<?= $img_url ?>" width="50" height="50" style="object-fit:cover;"></td>
                        <td><?= htmlspecialchars($row['name']) ?></td>
                        <td>$<?= $row['price'] ?></td>
                        <td><?= $row['cat_name'] ?></td>
                        <td>
                            <a href="?action=edit&id=<?= $row['id'] ?>" class="btn btn-sm btn-warning">Edit</a>
                            <a href="?action=delete&id=<?= $row['id'] ?>" onclick="return confirm('Delete item?')" class="btn btn-sm btn-danger">Delete</a>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        <?php endif; ?>
    </div>
</div>
<?php include '../inc/footer.php'; ?>