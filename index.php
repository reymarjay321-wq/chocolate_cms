<?php
/**
 * Chocolate Management System — Dashboard (index.php)
 * Shows stat cards, low stock warnings, and full chocolate table with CRUD actions
 */
require_once 'db.php';

$pageTitle  = 'Dashboard';
$activePage = 'dashboard';

// ── Fetch statistics ─────────────────────────────────────────
$totalChoc = $conn->query("SELECT COUNT(*) AS c FROM chocolates")->fetch_assoc()['c'];
$totalQty  = $conn->query("SELECT SUM(quantity) AS s FROM chocolates")->fetch_assoc()['s'] ?? 0;
$lowStock  = $conn->query("SELECT COUNT(*) AS c FROM chocolates WHERE quantity <= 20")->fetch_assoc()['c'];
$totalVal  = $conn->query("SELECT SUM(price * quantity) AS v FROM chocolates")->fetch_assoc()['v'] ?? 0;

// ── Low stock items ──────────────────────────────────────────
$lowItems = $conn->query("SELECT id, name, brand, quantity FROM chocolates WHERE quantity <= 20 ORDER BY quantity ASC LIMIT 6");

// ── All chocolates ───────────────────────────────────────────
$chocolates = $conn->query("SELECT * FROM chocolates ORDER BY date_added DESC");

// ── Unique categories & brands for filter dropdowns ──────────
$cats   = $conn->query("SELECT DISTINCT category FROM chocolates ORDER BY category");
$brands = $conn->query("SELECT DISTINCT brand FROM chocolates ORDER BY brand");

require_once 'components/header.php';
?>

<!-- Page Loader -->
<div class="page-loader" id="pageLoader"><div class="loader-ring"></div></div>

<!-- ══ STAT CARDS ══════════════════════════════════ -->
<div class="stat-grid">

    <div class="stat-card choco-1">
        <div class="stat-icon"><i class="fa-solid fa-cookie-bite"></i></div>
        <div class="stat-value" data-target="<?= $totalChoc ?>"><?= $totalChoc ?></div>
        <div class="stat-label">Total Products</div>
        <div class="stat-change up"><i class="fa-solid fa-arrow-trend-up"></i> Active inventory</div>
    </div>

    <div class="stat-card choco-2">
        <div class="stat-icon"><i class="fa-solid fa-boxes-stacked"></i></div>
        <div class="stat-value" data-target="<?= $totalQty ?>"><?= number_format($totalQty) ?></div>
        <div class="stat-label">Total Stock Units</div>
        <div class="stat-change up"><i class="fa-solid fa-layer-group"></i> Across all items</div>
    </div>

    <div class="stat-card choco-3">
        <div class="stat-icon"><i class="fa-solid fa-dollar-sign"></i></div>
        <div class="stat-value">$<?= number_format($totalVal, 0) ?></div>
        <div class="stat-label">Inventory Value</div>
        <div class="stat-change up"><i class="fa-solid fa-chart-line"></i> Total worth</div>
    </div>

    <div class="stat-card choco-4">
        <div class="stat-icon"><i class="fa-solid fa-triangle-exclamation"></i></div>
        <div class="stat-value" data-target="<?= $lowStock ?>"><?= $lowStock ?></div>
        <div class="stat-label">Low Stock Alerts</div>
        <div class="stat-change <?= $lowStock > 0 ? 'warn' : 'up' ?>">
            <i class="fa-solid fa-<?= $lowStock > 0 ? 'exclamation' : 'check' ?>"></i>
            <?= $lowStock > 0 ? 'Needs restocking' : 'All stocked well' ?>
        </div>
    </div>

</div>

<div class="row g-4" id="chocolates">

    <!-- ══ LOW STOCK WARNINGS ══ -->
    <?php if ($lowItems->num_rows > 0): ?>
    <div class="col-12 col-xl-4">
        <div class="glass-card h-100">
            <div class="card-header-line">
                <div class="card-title">
                    <i class="fa-solid fa-triangle-exclamation"></i> Low Stock
                </div>
                <span class="badge-choco badge-low"><?= $lowStock ?> items</span>
            </div>
            <div class="low-stock-list">
                <?php while ($ls = $lowItems->fetch_assoc()): ?>
                <div class="low-stock-item">
                    <i class="fa-solid fa-circle-dot warn-icon"></i>
                    <span class="low-stock-name"><?= htmlspecialchars($ls['name']) ?></span>
                    <span class="low-stock-qty"><?= $ls['quantity'] ?> left</span>
                    <a href="edit.php?id=<?= $ls['id'] ?>" class="btn-icon btn-edit" title="Edit">
                        <i class="fa-solid fa-pen"></i>
                    </a>
                </div>
                <?php endwhile; ?>
            </div>
        </div>
    </div>
    <div class="col-12 col-xl-8">
    <?php else: ?>
    <div class="col-12">
    <?php endif; ?>

        <!-- ══ CHOCOLATES TABLE ══ -->
        <div class="glass-card">
            <div class="card-header-line">
                <div class="card-title">
                    <i class="fa-solid fa-list"></i> All Chocolates
                </div>
                <div class="d-flex gap-2 flex-wrap">
                    <a href="add.php" class="btn-submit" style="padding:8px 18px;font-size:.82rem;">
                        <i class="fa-solid fa-plus"></i> Add New
                    </a>
                    <button id="deleteAllBtn" class="btn-danger">
                        <i class="fa-solid fa-trash-can"></i> Delete All
                    </button>
                </div>
            </div>

            <!-- Filter Bar -->
            <div class="filter-bar mb-4">
                <div class="search-wrap">
                    <i class="fa-solid fa-magnifying-glass"></i>
                    <input type="text" id="searchInput" class="search-input" placeholder="Search chocolates…">
                </div>

                <select id="catFilter" class="filter-select">
                    <option value="">All Categories</option>
                    <?php $cats->data_seek(0); while ($c = $cats->fetch_assoc()): ?>
                    <option value="<?= htmlspecialchars($c['category']) ?>"><?= htmlspecialchars($c['category']) ?></option>
                    <?php endwhile; ?>
                </select>

                <select id="brandFilter" class="filter-select">
                    <option value="">All Brands</option>
                    <?php $brands->data_seek(0); while ($b = $brands->fetch_assoc()): ?>
                    <option value="<?= htmlspecialchars($b['brand']) ?>"><?= htmlspecialchars($b['brand']) ?></option>
                    <?php endwhile; ?>
                </select>
            </div>

            <!-- Table -->
            <div class="table-responsive">
                <table class="choco-table">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>Image</th>
                            <th class="sortable" data-col="2">Name <span class="sort-icon"></span></th>
                            <th class="sortable" data-col="3">Brand <span class="sort-icon"></span></th>
                            <th class="sortable" data-col="4">Category <span class="sort-icon"></span></th>
                            <th class="sortable" data-col="5">Flavor <span class="sort-icon"></span></th>
                            <th class="sortable" data-col="6">Price <span class="sort-icon"></span></th>
                            <th class="sortable" data-col="7">Stock <span class="sort-icon"></span></th>
                            <th class="sortable" data-col="8">Expires <span class="sort-icon"></span></th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody id="chocoTableBody">
                    <?php
                    $i = 0;
                    while ($ch = $chocolates->fetch_assoc()):
                        $i++;
                        $qtyClass = $ch['quantity'] <= 10 ? 'badge-low' : ($ch['quantity'] <= 20 ? 'badge-warn' : 'badge-ok');
                        $imgSrc   = (!empty($ch['image']) && file_exists(UPLOAD_DIR . $ch['image']))
                            ? UPLOAD_URL . htmlspecialchars($ch['image'])
                            : null;
                    ?>
                    <tr data-row="<?= $i ?>"
                        data-category="<?= htmlspecialchars($ch['category']) ?>"
                        data-brand="<?= htmlspecialchars($ch['brand']) ?>">
                        <td><span style="color:var(--text-muted);font-size:.78rem;"><?= $i ?></span></td>
                        <td>
                            <?php if ($imgSrc): ?>
                                <img src="<?= $imgSrc ?>" alt="<?= htmlspecialchars($ch['name']) ?>" class="choco-img-cell">
                            <?php else: ?>
                                <div class="img-placeholder"><i class="fa-solid fa-cookie-bite"></i></div>
                            <?php endif; ?>
                        </td>
                        <td><strong><?= htmlspecialchars($ch['name']) ?></strong></td>
                        <td><?= htmlspecialchars($ch['brand']) ?></td>
                        <td><span class="badge-choco badge-category"><?= htmlspecialchars($ch['category']) ?></span></td>
                        <td><?= htmlspecialchars($ch['flavor']) ?></td>
                        <td class="price-col">$<?= number_format($ch['price'], 2) ?></td>
                        <td><span class="badge-choco <?= $qtyClass ?>"><?= $ch['quantity'] ?></span></td>
                        <td><?= date('M j, Y', strtotime($ch['expiration_date'])) ?></td>
                        <td>
                            <div class="action-btns">
                                <a href="edit.php?id=<?= $ch['id'] ?>" class="btn-icon btn-edit" title="Edit">
                                    <i class="fa-solid fa-pen"></i>
                                </a>
                                <a href="#" class="btn-icon btn-delete delete-btn"
                                   data-href="delete.php?id=<?= $ch['id'] ?>"
                                   data-name="<?= htmlspecialchars($ch['name']) ?>"
                                   title="Delete">
                                    <i class="fa-solid fa-trash"></i>
                                </a>
                            </div>
                        </td>
                    </tr>
                    <?php endwhile; ?>
                    </tbody>
                </table>
            </div>

            <!-- Empty State -->
            <div id="emptyState" class="empty-state" style="display:none;">
                <i class="fa-solid fa-magnifying-glass"></i>
                <p>No chocolates match your search or filter.</p>
            </div>

            <!-- Pagination -->
            <div class="pagination-wrap">
                <span class="pagination-info" id="paginationInfo"></span>
                <div class="pagination-btns" id="paginationBtns"></div>
            </div>
        </div>

    </div><!-- col -->

</div><!-- row -->

<?php require_once 'components/footer.php'; ?>
