<?php
/**
 * Chocolate Management System — About Page (about.php)
 * System info, technologies, and creator profile
 */
require_once 'db.php';

$pageTitle  = 'About the System';
$activePage = 'about';

// ── Handle creator photo uploads ─────────────────────────────
$allowed = ['jpg','jpeg','png','gif','webp'];

foreach ([1,2] as $n) {
    if (!empty($_FILES["creator_photo_{$n}"]['name'])) {
        $file = $_FILES["creator_photo_{$n}"];
        $ext  = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
        if (in_array($ext, $allowed) && $file['size'] <= 5*1024*1024) {
            // Remove old photo if exists
            $old = UPLOAD_DIR . "creator_{$n}." . $ext;
            foreach (glob(UPLOAD_DIR . "creator_{$n}.*") as $f) { @unlink($f); }
            $dest = UPLOAD_DIR . "creator_{$n}." . $ext;
            move_uploaded_file($file['tmp_name'], $dest);
        }
    }
}

// ── Load saved creator photos ────────────────────────────────
function creatorPhoto(int $n): string {
    foreach (['jpg','jpeg','png','gif','webp'] as $ext) {
        $file = UPLOAD_DIR . "creator_{$n}.{$ext}";
        $url  = UPLOAD_URL  . "creator_{$n}.{$ext}";
        if (file_exists($file)) return $url;
    }
    return '';
}
$photo1 = creatorPhoto(1);
$photo2 = creatorPhoto(2);

require_once 'components/header.php';
?>

<!-- ══ HERO ══════════════════════════════════════════════════ -->
<div class="about-hero">
    <span class="about-choco-icon">🍫</span>
    <h1 class="about-title">Chocolate Management System</h1>
    <p class="about-subtitle">
        A premium inventory management solution purpose-built for chocolate products —
        elegant, fast, and fully featured.
    </p>
</div>

<div class="row g-4">

    <!-- ══ SYSTEM DESCRIPTION ══════════════════════════ -->
    <div class="col-12">
        <div class="glass-card">
            <div class="card-title mb-3"><i class="fa-solid fa-scroll"></i> System Description</div>
            <p style="color:var(--text-secondary);line-height:1.8;font-size:.92rem;">
                The <strong style="color:var(--gold)">Chocolate Management System (CMS)</strong> is a comprehensive web-based
                inventory management application designed to streamline the management of chocolate products for businesses,
                retailers, and confectionery enthusiasts. It provides a centralized platform for tracking product details,
                monitoring stock levels, and maintaining product records with ease and elegance.
            </p>
            <p style="color:var(--text-secondary);line-height:1.8;font-size:.92rem;margin-top:12px;">
                Built with a dark, chocolate-inspired aesthetic and modern glassmorphism design principles, this system
                delivers a premium experience while remaining fully functional, responsive, and accessible on any device.
            </p>
        </div>

        <!-- ══ PURPOSE & OBJECTIVES ══════════════════════ -->
        <div class="glass-card">
            <div class="card-title mb-4"><i class="fa-solid fa-bullseye"></i> Purpose & Objectives</div>
            <div style="display:grid;gap:14px;">
                <?php
                $objectives = [
                    ['fa-database',      'Centralized Inventory',  'Maintain a complete, searchable database of all chocolate products in one place.'],
                    ['fa-chart-bar',     'Real-time Analytics',    'Monitor inventory value, stock levels, and low-stock warnings at a glance.'],
                    ['fa-pen-to-square', 'Effortless CRUD',        'Create, read, update, and delete product records with intuitive, validated forms.'],
                    ['fa-shield-halved', 'Data Integrity',         'All database operations use prepared statements to prevent SQL injection.'],
                    ['fa-mobile-screen', 'Responsive Design',      'Works seamlessly on desktop, tablet, and mobile devices with Bootstrap 5.'],
                    ['fa-bolt',          'Fast & Lightweight',     'Built on Core PHP and MySQL — no heavy frameworks, maximum performance.'],
                ];
                foreach ($objectives as $o): ?>
                <div style="display:flex;gap:14px;align-items:flex-start;padding:14px;background:var(--bg-glass);border:1px solid var(--border-color);border-radius:var(--radius-sm);">
                    <div style="width:40px;height:40px;background:rgba(200,129,58,.15);border-radius:8px;display:grid;place-items:center;flex-shrink:0;color:var(--choco-200);">
                        <i class="fa-solid <?= $o[0] ?>"></i>
                    </div>
                    <div>
                        <div style="font-weight:600;font-size:.88rem;color:var(--text-primary);margin-bottom:4px;"><?= $o[1] ?></div>
                        <div style="font-size:.82rem;color:var(--text-muted);"><?= $o[2] ?></div>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>
        </div>

        <!-- ══ TECHNOLOGIES ══════════════════════════════ -->
        <div class="glass-card">
            <div class="card-title mb-4"><i class="fa-solid fa-code"></i> Technologies Used</div>
            <div class="tech-grid">
                <div class="tech-card">
                    <i class="fa-brands fa-php" style="color:#7a86c9;"></i>
                    <span>PHP 8</span>
                </div>
                <div class="tech-card">
                    <i class="fa-solid fa-database" style="color:#f29111;"></i>
                    <span>MySQL</span>
                </div>
                <div class="tech-card">
                    <i class="fa-brands fa-bootstrap" style="color:#7952b3;"></i>
                    <span>Bootstrap 5</span>
                </div>
                <div class="tech-card">
                    <i class="fa-brands fa-js" style="color:#f0db4f;"></i>
                    <span>JavaScript</span>
                </div>
                <div class="tech-card">
                    <i class="fa-brands fa-html5" style="color:#e34f26;"></i>
                    <span>HTML5</span>
                </div>
                <div class="tech-card">
                    <i class="fa-brands fa-css3-alt" style="color:#264de4;"></i>
                    <span>CSS3</span>
                </div>
                <div class="tech-card">
                    <i class="fa-brands fa-font-awesome" style="color:var(--gold);"></i>
                    <span>Font Awesome</span>
                </div>
                <div class="tech-card">
                    <i class="fa-solid fa-bell" style="color:#e53935;"></i>
                    <span>SweetAlert2</span>
                </div>
            </div>
        </div>

        <!-- ══ FEATURES LIST ═════════════════════════════ -->
        <div class="glass-card">
            <div class="card-title mb-4"><i class="fa-solid fa-star"></i> Key Features</div>
            <div style="display:grid;grid-template-columns:1fr 1fr;gap:10px;">
                <?php
                $features = [
                    'Full CRUD Operations','Live Search & Filter','Column Sorting','Pagination',
                    'Image Upload & Preview','Low Stock Alerts','SweetAlert Confirmations',
                    'Toast Notifications','Dark / Light Theme','Responsive Layout',
                    'Prepared Statements','Animated UI Elements',
                ];
                foreach ($features as $f): ?>
                <div style="display:flex;align-items:center;gap:8px;font-size:.85rem;color:var(--text-secondary);">
                    <i class="fa-solid fa-check-circle" style="color:#3ecf66;"></i> <?= $f ?>
                </div>
                <?php endforeach; ?>
            </div>
        </div>

    <!-- ══ CREATOR PROFILES ═══════════════════════════════ -->
    <div class="col-12">
        <div class="glass-card">
            <div class="card-title mb-4"><i class="fa-solid fa-users"></i> Creators</div>
            <div class="row g-4">

                <!-- Creator 1 -->
                <div class="col-12 col-md-6">
                    <div class="profile-card">
                        <form method="POST" enctype="multipart/form-data" style="margin:0;">
                            <label for="uploadPhoto1" title="Click to upload photo" style="cursor:pointer;display:block;margin:0 auto 16px;width:100px;height:100px;position:relative;">
                                <div class="profile-avatar" style="margin:0;overflow:hidden;width:100px;height:100px;">
                                    <?php if ($photo1): ?>
                                    <img src="<?= htmlspecialchars($photo1) ?>?v=<?= time() ?>" alt="Reymar" style="width:100%;height:100%;object-fit:cover;border-radius:50%;position:absolute;top:0;left:0;">
                                    <?php else: ?>
                                    <i class="fa-solid fa-user"></i>
                                    <?php endif; ?>
                                    <div style="position:absolute;bottom:0;left:0;right:0;background:rgba(0,0,0,.55);font-size:.5rem;text-align:center;padding:3px 0;color:#fff;letter-spacing:.06em;border-radius:0 0 50px 50px;">UPLOAD</div>
                                </div>
                            </label>
                            <input type="file" id="uploadPhoto1" name="creator_photo_1" accept="image/*" style="display:none;" onchange="this.form.submit()">
                        </form>
                        <div class="profile-name">Reymar Jay V. Bayot</div>
                        <div class="profile-role">Web Developer / Student</div>
                        <div class="profile-info">
                            <p><i class="fa-solid fa-graduation-cap"></i> Bachelor of Science in Information System</p>
                            <p><i class="fa-solid fa-users-line"></i> Section: BSIS-2B</p>
                            <p><i class="fa-solid fa-school"></i> Inabanga College of Arts and Sciences</p>
                            <p><i class="fa-solid fa-calendar-days"></i> Academic Year 2025–2026</p>
                            <p><i class="fa-solid fa-envelope"></i> reymarjay@gmail.com</p>
                        </div>
                    </div>
                </div>

                <!-- Creator 2 -->
                <div class="col-12 col-md-6">
                    <div class="profile-card">
                        <form method="POST" enctype="multipart/form-data" style="margin:0;">
                            <label for="uploadPhoto2" title="Click to upload photo" style="cursor:pointer;display:block;margin:0 auto 16px;width:100px;height:100px;position:relative;">
                                <div class="profile-avatar" style="margin:0;overflow:hidden;width:100px;height:100px;">
                                    <?php if ($photo2): ?>
                                    <img src="<?= htmlspecialchars($photo2) ?>?v=<?= time() ?>" alt="Gichelle" style="width:100%;height:100%;object-fit:cover;border-radius:50%;position:absolute;top:0;left:0;">
                                    <?php else: ?>
                                    <i class="fa-solid fa-user"></i>
                                    <?php endif; ?>
                                    <div style="position:absolute;bottom:0;left:0;right:0;background:rgba(0,0,0,.55);font-size:.5rem;text-align:center;padding:3px 0;color:#fff;letter-spacing:.06em;border-radius:0 0 50px 50px;">UPLOAD</div>
                                </div>
                            </label>
                            <input type="file" id="uploadPhoto2" name="creator_photo_2" accept="image/*" style="display:none;" onchange="this.form.submit()">
                        </form>
                        <div class="profile-name">Gichelle M. Parojinog</div>
                        <div class="profile-role">Web Developer / Student</div>
                        <div class="profile-info">
                            <p><i class="fa-solid fa-graduation-cap"></i> Bachelor of Science in Information System</p>
                            <p><i class="fa-solid fa-users-line"></i> Section: BSIS-2B</p>
                            <p><i class="fa-solid fa-school"></i> Inabanga College of Arts and Sciences</p>
                            <p><i class="fa-solid fa-calendar-days"></i> Academic Year 2025–2026</p>
                            <p><i class="fa-solid fa-envelope"></i> gichelle@gmail.com</p>
                        </div>
                    </div>
                </div>

<script>
function previewPhoto(input, imgId, iconId) {
    if (input.files && input.files[0]) {
        const reader = new FileReader();
        reader.onload = function(e) {
            const img = document.getElementById(imgId);
            const icon = document.getElementById(iconId);
            img.src = e.target.result;
            img.style.display = 'block';
            icon.style.display = 'none';
        };
        reader.readAsDataURL(input.files[0]);
    }
}
</script>

            </div>
        </div>
    </div>

</div>

<?php require_once 'components/footer.php'; ?>
