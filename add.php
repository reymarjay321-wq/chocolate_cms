<?php
/**
 * Chocolate Management System — Add Chocolate (add.php)
 * Full form with image upload, validation, and prepared statements
 */
require_once 'db.php';

$pageTitle  = 'Add Chocolate';
$activePage = 'add';

$errors = [];
$old    = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Sanitize
    $name    = trim($_POST['name']    ?? '');
    $brand   = trim($_POST['brand']   ?? '');
    $cat     = trim($_POST['category']?? '');
    $flavor  = trim($_POST['flavor']  ?? '');
    $price   = trim($_POST['price']   ?? '');
    $qty     = trim($_POST['quantity']?? '');
    $expiry  = trim($_POST['expiration_date'] ?? '');
    $desc    = trim($_POST['description']     ?? '');

    $old = compact('name','brand','cat','flavor','price','qty','expiry','desc');

    // Validate
    if (!$name)   $errors['name']   = 'Chocolate name is required.';
    if (!$brand)  $errors['brand']  = 'Brand is required.';
    if (!$cat)    $errors['cat']    = 'Category is required.';
    if (!$flavor) $errors['flavor'] = 'Flavor is required.';
    if (!is_numeric($price) || $price < 0) $errors['price'] = 'Enter a valid price.';
    if (!is_numeric($qty)   || $qty < 0)   $errors['qty']   = 'Enter a valid quantity.';
    if (!$expiry) $errors['expiry'] = 'Expiration date is required.';

    // Handle image upload
    $imageName = null;
    if (!empty($_FILES['image']['name'])) {
        $allowed   = ['jpg','jpeg','png','gif','webp'];
        $ext       = strtolower(pathinfo($_FILES['image']['name'], PATHINFO_EXTENSION));
        $maxSize   = 5 * 1024 * 1024; // 5 MB

        if (!in_array($ext, $allowed)) {
            $errors['image'] = 'Only JPG, PNG, GIF, WEBP allowed.';
        } elseif ($_FILES['image']['size'] > $maxSize) {
            $errors['image'] = 'Image must be under 5 MB.';
        } else {
            $imageName = uniqid('choco_', true) . '.' . $ext;
            if (!move_uploaded_file($_FILES['image']['tmp_name'], UPLOAD_DIR . $imageName)) {
                $errors['image'] = 'Upload failed — check folder permissions.';
                $imageName = null;
            }
        }
    }

    if (empty($errors)) {
        // Duplicate check: same name + brand (case-insensitive)
        $dup = $conn->prepare("SELECT id FROM chocolates WHERE LOWER(name)=LOWER(?) AND LOWER(brand)=LOWER(?) LIMIT 1");
        $dup->bind_param('ss', $name, $brand);
        $dup->execute();
        $dupResult = $dup->get_result();
        if ($dupResult->num_rows > 0) {
            $errors['name'] = "A chocolate named \"{$name}\" by \"{$brand}\" already exists.";
        }
        $dup->close();
    }

    if (empty($errors)) {
        $stmt = $conn->prepare(
            "INSERT INTO chocolates (name,brand,category,flavor,price,quantity,expiration_date,description,image)
             VALUES (?,?,?,?,?,?,?,?,?)"
        );
        $stmt->bind_param('ssssdisss', $name, $brand, $cat, $flavor, $price, $qty, $expiry, $desc, $imageName);
        if ($stmt->execute()) {
            setFlash('success', "🍫 \"{$name}\" added successfully!");
            header('Location: index.php');
            exit;
        } else {
            $errors['db'] = 'Database error: ' . $stmt->error;
        }
        $stmt->close();
    }
}

require_once 'components/header.php';
?>

<div class="row justify-content-center">
    <div class="col-12 col-xl-10">

        <div class="section-heading">Add New Chocolate</div>
        <div class="section-sub">Fill in the details below to add a new chocolate to the inventory.</div>

        <?php if (!empty($errors['db'])): ?>
        <div class="alert-choco mb-4"><i class="fa-solid fa-circle-xmark"></i> <?= htmlspecialchars($errors['db']) ?></div>
        <?php endif; ?>

        <form method="POST" enctype="multipart/form-data" novalidate>
            <div class="glass-card">
                <div class="card-title mb-4"><i class="fa-solid fa-circle-info"></i> Basic Information</div>

                <div class="form-grid">

                    <!-- Name -->
                    <div class="field-wrap">
                        <label class="field-label" for="name">Chocolate Name <span style="color:#ff6464">*</span></label>
                        <input type="text" id="name" name="name" class="field-input <?= isset($errors['name'])?'is-invalid':'' ?>"
                               value="<?= htmlspecialchars($old['name'] ?? '') ?>"
                               placeholder="e.g. Silk Dark Bar" required>
                        <?php if (isset($errors['name'])): ?>
                        <div style="color:#ff8080;font-size:.78rem;margin-top:4px;"><?= $errors['name'] ?></div>
                        <?php endif; ?>
                    </div>

                    <!-- Brand -->
                    <div class="field-wrap">
                        <label class="field-label" for="brand">Brand <span style="color:#ff6464">*</span></label>
                        <input type="text" id="brand" name="brand" class="field-input <?= isset($errors['brand'])?'is-invalid':'' ?>"
                               value="<?= htmlspecialchars($old['brand'] ?? '') ?>"
                               placeholder="e.g. Cadbury" required>
                        <?php if (isset($errors['brand'])): ?>
                        <div style="color:#ff8080;font-size:.78rem;margin-top:4px;"><?= $errors['brand'] ?></div>
                        <?php endif; ?>
                    </div>

                    <!-- Category -->
                    <div class="field-wrap">
                        <label class="field-label" for="category">Category <span style="color:#ff6464">*</span></label>
                        <select id="category" name="category" class="field-select <?= isset($errors['cat'])?'is-invalid':'' ?>">
                            <option value="">— Select Category —</option>
                            <?php
                            $categories = ['Dark Chocolate','Milk Chocolate','White Chocolate','Praline',
                                           'Caramel Chocolate','Nut Chocolate','Fruit Chocolate','Mint Chocolate','Other'];
                            foreach ($categories as $c):
                                $sel = (($old['cat'] ?? '') === $c) ? 'selected' : '';
                            ?>
                            <option value="<?= $c ?>" <?= $sel ?>><?= $c ?></option>
                            <?php endforeach; ?>
                        </select>
                        <?php if (isset($errors['cat'])): ?>
                        <div style="color:#ff8080;font-size:.78rem;margin-top:4px;"><?= $errors['cat'] ?></div>
                        <?php endif; ?>
                    </div>

                    <!-- Flavor -->
                    <div class="field-wrap">
                        <label class="field-label" for="flavor">Flavor <span style="color:#ff6464">*</span></label>
                        <input type="text" id="flavor" name="flavor" class="field-input <?= isset($errors['flavor'])?'is-invalid':'' ?>"
                               value="<?= htmlspecialchars($old['flavor'] ?? '') ?>"
                               placeholder="e.g. Hazelnut, Sea Salt…" required>
                        <?php if (isset($errors['flavor'])): ?>
                        <div style="color:#ff8080;font-size:.78rem;margin-top:4px;"><?= $errors['flavor'] ?></div>
                        <?php endif; ?>
                    </div>

                    <!-- Price -->
                    <div class="field-wrap">
                        <label class="field-label" for="price">Price (USD) <span style="color:#ff6464">*</span></label>
                        <input type="number" id="price" name="price" step="0.01" min="0" class="field-input <?= isset($errors['price'])?'is-invalid':'' ?>"
                               value="<?= htmlspecialchars($old['price'] ?? '') ?>"
                               placeholder="0.00" required>
                        <?php if (isset($errors['price'])): ?>
                        <div style="color:#ff8080;font-size:.78rem;margin-top:4px;"><?= $errors['price'] ?></div>
                        <?php endif; ?>
                    </div>

                    <!-- Quantity -->
                    <div class="field-wrap">
                        <label class="field-label" for="quantity">Stock Quantity <span style="color:#ff6464">*</span></label>
                        <input type="number" id="quantity" name="quantity" min="0" class="field-input <?= isset($errors['qty'])?'is-invalid':'' ?>"
                               value="<?= htmlspecialchars($old['qty'] ?? '') ?>"
                               placeholder="e.g. 100" required>
                        <?php if (isset($errors['qty'])): ?>
                        <div style="color:#ff8080;font-size:.78rem;margin-top:4px;"><?= $errors['qty'] ?></div>
                        <?php endif; ?>
                    </div>

                    <!-- Expiration Date -->
                    <div class="field-wrap">
                        <label class="field-label" for="expiration_date">Expiration Date <span style="color:#ff6464">*</span></label>
                        <input type="date" id="expiration_date" name="expiration_date" class="field-input <?= isset($errors['expiry'])?'is-invalid':'' ?>"
                               value="<?= htmlspecialchars($old['expiry'] ?? '') ?>"
                               min="<?= date('Y-m-d') ?>" required>
                        <?php if (isset($errors['expiry'])): ?>
                        <div style="color:#ff8080;font-size:.78rem;margin-top:4px;"><?= $errors['expiry'] ?></div>
                        <?php endif; ?>
                    </div>

                </div><!-- form-grid -->
            </div>

            <!-- Description & Image -->
            <div class="glass-card">
                <div class="card-title mb-4"><i class="fa-solid fa-image"></i> Description & Image</div>

                <div class="form-grid">
                    <div class="field-wrap" style="grid-column: 1/-1">
                        <label class="field-label" for="description">Description</label>
                        <textarea id="description" name="description" class="field-textarea"
                                  placeholder="Write a short description about this chocolate…"><?= htmlspecialchars($old['desc'] ?? '') ?></textarea>
                    </div>

                    <div class="field-wrap">
                        <label class="field-label">Chocolate Image</label>
                        <div class="upload-zone" id="uploadZone">
                            <input type="file" id="imgFile" name="image" accept="image/*">
                            <div class="upload-icon"><i class="fa-solid fa-cloud-arrow-up"></i></div>
                            <div class="upload-text">
                                <strong>Click to browse</strong> or drag & drop<br>
                                <small>JPG, PNG, WEBP — max 5MB</small>
                            </div>
                            <img id="imgPreview" src="" alt="Preview">
                        </div>
                        <?php if (isset($errors['image'])): ?>
                        <div style="color:#ff8080;font-size:.78rem;margin-top:4px;"><?= $errors['image'] ?></div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>

            <div class="d-flex gap-3 mb-4 flex-wrap">
                <button type="submit" class="btn-submit">
                    <i class="fa-solid fa-floppy-disk"></i> Save Chocolate
                </button>
                <a href="index.php" class="btn-cancel">
                    <i class="fa-solid fa-xmark"></i> Cancel
                </a>
            </div>
        </form>

    </div>
</div>

<?php require_once 'components/footer.php'; ?>
