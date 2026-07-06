<?php
session_start();
require_once('db.php');

$title       = "ChairHive - Store";
$currentPage = "store";

$sql    = "SELECT * FROM products WHERE is_active = 1 ORDER BY category, name";
$result = mysqli_query($conn, $sql);

$grouped = array();
while ($row = mysqli_fetch_assoc($result)) {
    $grouped[$row['category']][] = $row;
}

$catIcons = array(
    'Ergonomic Chairs'         => 'bi-person-workspace',
    'Executive Chairs'         => 'bi-briefcase',
    'Gaming Chairs'            => 'bi-controller',
    'Visitor & Guest Chairs'   => 'bi-people',
    'Stools & Drafting Chairs' => 'bi-easel',
);

$justAdded = isset($_GET['added']) ? $_GET['added'] : null;

mysqli_close($conn);

require('include/header.php');
?>

<!-- Page Heading -->
<div class="cv-page-head">
    <div class="cv-page-head-inner">
        <h1><i class="bi bi-shop"></i> Store</h1>
        <p>All chair types, organized by category. Add any chair to your cart.</p>
    </div>
</div>

<div style="max-width:1200px;margin:0 auto;padding:40px 40px 60px;">

    <?php if ($justAdded): ?>
        <div class="cv-alert cv-alert-success mb-4">
            <i class="bi bi-cart-check"></i>
            <strong>"<?= htmlspecialchars($justAdded) ?>"</strong> was added to your cart.
            <a href="cart.php" style="color:#166534;font-weight:600;">View Cart &rarr;</a>
        </div>
    <?php endif; ?>

    <?php foreach ($grouped as $categoryName => $items): ?>
        <div class="mb-5" id="<?= urlencode($categoryName) ?>">
            <div class="cv-cat-heading">
                <?php $icon = isset($catIcons[$categoryName]) ? $catIcons[$categoryName] : 'bi-person-workspace'; ?>
                <div class="cv-category-icon" style="width:36px;height:36px;font-size:1rem;">
                    <i class="bi <?= $icon ?>"></i>
                </div>
                <h4><?= htmlspecialchars($categoryName) ?></h4>
            </div>

            <div class="cv-product-grid">
                <?php foreach ($items as $p):
                    $outOfStock = (int)$p['stock_qty'] <= 0;
                ?>
                    <div class="cv-product-card">
                        <div class="cv-product-thumb">
                            <?php $icon = isset($catIcons[$categoryName]) ? $catIcons[$categoryName] : 'bi-person-workspace'; ?>
                            <i class="bi <?= $icon ?>"></i>
                        </div>
                        <div class="cv-product-body">
                            <div class="cv-product-cat"><?= htmlspecialchars($p['category']) ?></div>
                            <div class="cv-product-name"><?= htmlspecialchars($p['name']) ?></div>
                            <div class="cv-product-desc"><?= htmlspecialchars($p['description']) ?></div>

                            <?php if ($outOfStock): ?>
                                <span class="cv-badge-out">Out of Stock</span>
                            <?php elseif ((int)$p['stock_qty'] <= 5): ?>
                                <span class="cv-badge-low">Only <?= (int)$p['stock_qty'] ?> left</span>
                            <?php endif; ?>

                            <div class="cv-product-footer">
                                <span class="cv-product-price">&#8369;<?= number_format($p['price'], 2) ?></span>
                                <form action="cart_action.php" method="post">
                                    <input type="hidden" name="action" value="add">
                                    <input type="hidden" name="id" value="<?= (int)$p['id'] ?>">
                                    <input type="hidden" name="redirect" value="store.php#<?= urlencode($categoryName) ?>">
                                    <button type="submit" name="submit" class="btn-cv" <?= $outOfStock ? 'disabled' : '' ?>>
                                        <?php if ($outOfStock): ?>
                                            Unavailable
                                        <?php else: ?>
                                            <i class="bi bi-cart-plus"></i> Add to Cart
                                        <?php endif; ?>
                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>
    <?php endforeach; ?>

</div>

<?php require('include/footer.php'); ?>
