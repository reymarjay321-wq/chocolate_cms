<?php
/**
 * Chocolate Management System — Edit Chocolate (edit.php)
 * Pre-fills form with existing data, supports image replacement
 */
require_once 'db.php';

$pageTitle  = 'Edit Chocolate';
$activePage = 'list';

// ── Fetch existing record ────────────────────────────────────
$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
if (!$id) { header('Location: index.php'); exit; }

$stmt = $conn->prepare("SELECT * FROM chocolates WHERE id = ?");
$stmt->bind_param('i', $id);
$stmt->execute();
$result = $stmt->get_result();
$ch = $result->fetch_assoc();
$stmt->close();

if (!$ch) {
    setFlash('error', 'Chocolate not found.');
    header('Location: index.php');
    exit;
}

$errors = [];

// ── Handle POST ──────────────────────────────────────────────
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name   = trim($_POST['name']    ?? '');
    $brand  = trim($_POST['brand']   ?? '');
    $cat    = trim($_POST['category']?? '');
    $flavor = trim($_POST['flavor']  ?? '');
    $price  = trim($_POST['price']   ?? '');
    $qty    = trim($_POST['quantity']?? '');
    $expiry = trim($_POST['expiration_date'] ?? '');
    $desc   = trim($_POST['description']     ?? '');

    if (!$name)   $errors['name']   = 'Chocolate name is required.';
    if (!$brand)  $errors['brand']  = 'Brand is required.';
    if (!$cat)    $errors['cat']    = 'Category is required.';
    if (!$flavor) $errors['flavor'] = 'Flavor is required.';
    if (!is_numeric($price) || $price < 0) $errors['price'] = 'Enter a valid price.';
    if (!is_numeric($qty)   || $qty < 0)   $errors['qty']   = 'Enter a valid quantity.';
    if (!$expiry) $errors['expiry'] = 'Expiration date is required.';

    // Image upload (optional replacement)
    $imageName = $ch['image']; // Keep existing unless new uploaded
    if (!empty($_FILES['image']['name'])) {
        $allowed = ['jpg','jpeg','png','gif','webp'];
        $ext     = strtolower(pathinfo($_FILES['image']['name'], PATHINFO_EXTENSION));
        $maxSize = 5 * 1024 * 1024;
        if (!in_array($ext, $allowed)) {
            $errors['image'] = 'Only JPG, PNG, GIF, WEBP allowed.';
        } elseif ($_FILES['image']['size'] > $maxSize) {
            $errors['image'] = 'Image must be under 5 MB.';
        } else {
            $newName = uniqid('choco_', true) . '.' . $ext;
            if (move_uploaded_file($_FILES['image']['tmp_name'], UPLOAD_DIR . $newName)) {
                // Delete old image file if exists
                if ($ch['image'] && file_exists(UPLOAD_DIR . $ch['image'])) {
                    @unlink(UPLOAD_DIR . $ch['image']);
                }
                $imageName = $newName;
            } else {
                $errors['image'] = 'Upload failed — check folder permissions.';
            }
        }
    }

    if (empty($errors)) {
        // Duplicate check: same name + brand (excluding the current record)
        $dup = $conn->prepare("SELECT id FROM chocolates WHERE LOWER(name)=LOWER(?) AND LOWER(brand)=LOWER(?) AND id != ? LIMIT 1");
        $dup->bind_param('ssi', $name, $brand, $id);
        $dup->execute();
        $dupResult = $dup->get_result();
        if ($dupResult->num_rows > 0) {
            $errors['name'] = "A chocolate named \"{$name}\" by \"{$brand}\" already exists.";
        }
        $dup->close();
    }

    if (empty($errors)) {
        $upd = $conn->prepare(
            "UPDATE chocolates SET name=?,brand=?,category=?,flavor=?,price=?,quantity=?,
             expiration_date=?,description=?,image=? WHERE id=?"
        );
        $upd->bind_param('ssssdisssi', $name, $brand, $cat, $flavor, $price, $qty, $expiry, $desc, $imageName, $id);
        if ($upd->execute()) {
            setFlash('success', "✏️ \"{$name}\" updated successfully!");
            header('Location: index.php');
            exit;
        } else {
            $errors['db'] = 'Database error: ' . $upd->error;
        }
        $upd->close();
    } else {
        // Update $ch with posted values for repopulating
        $ch = array_merge($ch, [
            'name'=>$name,'brand'=>$brand,'category'=>$cat,'flavor'=>$flavor,
            'price'=>$price,'quantity'=>$qty,'expiration_date'=>$expiry,'description'=>$desc
        ]);
    }
}

require_once 'components/header.php';
?>

<div class="row justify-content-center">
    <div class="col-12 col-xl-10">

        <div class="d-flex align-items-center gap-3 mb-2">
            <a href="index.php" style="color:var(--text-muted);text-decoration:none;font-size:.85rem;">
                <i class="fa-solid fa-chevron-left"></i> Back to Dashboard
            </a>
        </div>

        <div class="section-heading">Edit Chocolate</div>
        <div class="section-sub">Update the information for <strong style="color:var(--gold)"><?= htmlspecialchars($ch['name']) ?></strong></div>

        <?php if (!empty($errors['db'])): ?>
        <div class="alert-choco mb-4"><i class="fa-solid fa-circle-xmark"></i> <?= htmlspecialchars($errors['db']) ?></div>
        <?php endif; ?>

        <form method="POST" enctype="multipart/form-data" novalidate>
            <div class="glass-card">
                <div class="card-title mb-4"><i class="fa-solid fa-circle-info"></i> Basic Information</div>

                <div class="form-grid">

                    <div class="field-wrap">
                        <label class="field-label" for="name">Chocolate Name <span style="color:#ff6464">*</span></label>
                        <input type="text" id="name" name="name" class="field-input"
                               value="<?= htmlspecialchars($ch['name']) ?>" required>
                        <?php if (isset($errors['name'])): ?><div style="color:#ff8080;font-size:.78rem;margin-top:4px;"><?= $errors['name'] ?></div><?php endif; ?>
                    </div>

                    <div class="field-wrap">
                        <label class="field-label" for="brand">Brand <span style="color:#ff6464">*</span></label>
                        <input type="text" id="brand" name="brand" class="field-input"
                               value="<?= htmlspecialchars($ch['brand']) ?>" required>
                        <?php if (isset($errors['brand'])): ?><div style="color:#ff8080;font-size:.78rem;margin-top:4px;"><?= $errors['brand'] ?></div><?php endif; ?>
                    </div>

                    <div class="field-wrap">
                        <label class="field-label" for="category">Category <span style="color:#ff6464">*</span></label>
                        <select id="category" name="category" class="field-select">
                            <option value="">— Select Category —</option>
                            <?php
                            $categories = ['Dark Chocolate','Milk Chocolate','White Chocolate','Praline',
                                           'Caramel Chocolate','Nut Chocolate','Fruit Chocolate','Mint Chocolate','Other'];
                            foreach ($categories as $c):
                                $sel = ($ch['category'] === $c) ? 'selected' : '';
                            ?>
                            <option value="<?= $c ?>" <?= $sel ?>><?= $c ?></option>
                            <?php endforeach; ?>
                        </select>
                        <?php if (isset($errors['cat'])): ?><div style="color:#ff8080;font-size:.78rem;margin-top:4px;"><?= $errors['cat'] ?></div><?php endif; ?>
                    </div>

                    <div class="field-wrap">
                        <label class="field-label" for="flavor">Flavor <span style="color:#ff6464">*</span></label>
                        <input type="text" id="flavor" name="flavor" class="field-input"
                               value="<?= htmlspecialchars($ch['flavor']) ?>" required>
                        <?php if (isset($errors['flavor'])): ?><div style="color:#ff8080;font-size:.78rem;margin-top:4px;"><?= $errors['flavor'] ?></div><?php endif; ?>
                    </div>

                    <div class="field-wrap">
                        <label class="field-label" for="price">Price (USD) <span style="color:#ff6464">*</span></label>
                        <input type="number" id="price" name="price" step="0.01" min="0" class="field-input"
                               value="<?= htmlspecialchars($ch['price']) ?>" required>
                        <?php if (isset($errors['price'])): ?><div style="color:#ff8080;font-size:.78rem;margin-top:4px;"><?= $errors['price'] ?></div><?php endif; ?>
                    </div>

                    <div class="field-wrap">
                        <label class="field-label" for="quantity">Stock Quantity <span style="color:#ff6464">*</span></label>
                        <input type="number" id="quantity" name="quantity" min="0" class="field-input"
                               value="<?= htmlspecialchars($ch['quantity']) ?>" required>
                        <?php if (isset($errors['qty'])): ?><div style="color:#ff8080;font-size:.78rem;margin-top:4px;"><?= $errors['qty'] ?></div><?php endif; ?>
                    </div>

                    <div class="field-wrap">
                        <label class="field-label" for="expiration_date">Expiration Date <span style="color:#ff6464">*</span></label>
                        <input type="date" id="expiration_date" name="expiration_date" class="field-input"
                               value="<?= htmlspecialchars($ch['expiration_date']) ?>" required>
                        <?php if (isset($errors['expiry'])): ?><div style="color:#ff8080;font-size:.78rem;margin-top:4px;"><?= $errors['expiry'] ?></div><?php endif; ?>
                    </div>

                </div>
            </div>

            <div class="glass-card">
                <div class="card-title mb-4"><i class="fa-solid fa-image"></i> Description & Image</div>

                <div class="form-grid">
                    <div class="field-wrap" style="grid-column:1/-1">
                        <label class="field-label" for="description">Description</label>
                        <textarea id="description" name="description" class="field-textarea"><?= htmlspecialchars($ch['description'] ?? '') ?></textarea>
                    </div>

                    <div class="field-wrap">
                        <label class="field-label">Chocolate Image</label>
                        <?php if (!empty($ch['image']) && file_exists(UPLOAD_DIR . $ch['image'])): ?>
                        <div style="margin-bottom:12px;">
                            <img src="<?= UPLOAD_URL . htmlspecialchars($ch['image']) ?>"
                                 alt="Current image"
                                 style="max-height:120px;border-radius:var(--radius-sm);border:2px solid var(--border-glass);">
                            <div style="font-size:.75rem;color:var(--text-muted);margin-top:6px;">Current image — upload new to replace</div>
                        </div>
                        <?php endif; ?>
                        <div class="upload-zone">
                            <input type="file" id="imgFile" name="image" accept="image/*">
                            <div class="upload-icon"><i class="fa-solid fa-cloud-arrow-up"></i></div>
                            <div class="upload-text"><strong>Click to browse</strong> or drag & drop<br><small>JPG, PNG, WEBP — max 5MB</small></div>
                            <img id="imgPreview" src="" alt="Preview">
                        </div>
                        <?php if (isset($errors['image'])): ?><div style="color:#ff8080;font-size:.78rem;margin-top:4px;"><?= $errors['image'] ?></div><?php endif; ?>
                    </div>
                </div>
            </div>

            <div class="d-flex gap-3 mb-4 flex-wrap">
                <button type="submit" class="btn-submit">
                    <i class="fa-solid fa-floppy-disk"></i> Update Chocolate
                </button>
                <a href="index.php" class="btn-cancel">
                    <i class="fa-solid fa-xmark"></i> Cancel
                </a>
                <a href="#" class="btn-danger ms-auto delete-btn"
                   data-href="delete.php?id=<?= $id ?>"
                   data-name="<?= htmlspecialchars($ch['name']) ?>">
                    <i class="fa-solid fa-trash"></i> Delete This
                </a>
            </div>
        </form>

    </div>
</div>

<?php require_once 'components/footer.php'; ?>
