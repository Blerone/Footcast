<?php
session_start();
require_once __DIR__ . '/db_connection.php';
require_once __DIR__ . '/admin-dashboard/assets/includes/PromotionsRepository.php';

$db = footcast_db();
$promotionsRepository = new PromotionsRepository($db);
$allPromotions = $promotionsRepository->getAll();
$db->close();

$activePromotions = [];
$now = new DateTimeImmutable('now');
foreach ($allPromotions as $promo) {
    if ((int) ($promo['is_active'] ?? 0) !== 1) {
        continue;
    }
    if (!empty($promo['start_date']) && $now < new DateTimeImmutable($promo['start_date'])) {
        continue;
    }
    if (!empty($promo['end_date']) && $now > new DateTimeImmutable($promo['end_date'])) {
        continue;
    }
    $activePromotions[] = $promo;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <title>Promotions</title>
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <link rel="stylesheet" href="./assets/css/promotions.css" />
  <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined"/>
</head>
<body>
  <?php include('./assets/php/header.php'); ?>
  <main>
    <h3 class="page-title">Active Promotions</h3>
    <section class="promotions-grid">
      <?php if (empty($activePromotions)): ?>
        <div class="promo-card">
          <div class="promo-card-inner">
            <div class="promo-body">
              <p class="promo-text">No active promotions right now.</p>
            </div>
          </div>
        </div>
      <?php else: ?>
        <?php foreach ($activePromotions as $promo): ?>
          <?php
            $cardStyle = trim((string) ($promo['card_style'] ?? ''));
            $tagStyle = trim((string) ($promo['tag_style'] ?? ''));
            $iconName = trim((string) ($promo['icon_name'] ?? 'local_offer'));
            $tagLabel = trim((string) ($promo['tag_label'] ?? ''));
            $promoCode = trim((string) ($promo['promo_code'] ?? ''));
            $iconClass = '';
            if (strpos($cardStyle, 'card-') === 0) {
                $iconClass = substr($cardStyle, 5);
            }
          ?>
          <div class="promo-card <?php echo htmlspecialchars($cardStyle, ENT_QUOTES, 'UTF-8'); ?>">
            <div class="promo-card-inner">
              <div class="promo-header">
                <div class="promo-icon <?php echo htmlspecialchars($iconClass, ENT_QUOTES, 'UTF-8'); ?>">
                  <span class="material-symbols-outlined"><?php echo htmlspecialchars($iconName, ENT_QUOTES, 'UTF-8'); ?></span>
                </div>
                <?php if ($tagLabel !== ''): ?>
                  <?php
                    $tagClass = 'promo-main-tag';
                    if ($tagStyle !== '') {
                        if (strpos($tagStyle, 'pill-') === 0) {
                            $tagClass = 'small-pill ' . $tagStyle;
                        } else {
                            $tagClass = 'promo-main-tag ' . $tagStyle;
                        }
                    }
                  ?>
                  <div class="promo-top-right">
                    <span class="<?php echo htmlspecialchars($tagClass, ENT_QUOTES, 'UTF-8'); ?>">
                      <?php echo htmlspecialchars($tagLabel, ENT_QUOTES, 'UTF-8'); ?>
                    </span>
                  </div>
                <?php endif; ?>
              </div>
              <div class="promo-body">
                <div>
                  <h3 class="promo-title"><?php echo htmlspecialchars($promo['title'] ?? '', ENT_QUOTES, 'UTF-8'); ?></h3>
                  <p class="promo-text"><?php echo htmlspecialchars($promo['description'] ?? '', ENT_QUOTES, 'UTF-8'); ?></p>
                </div>
                <?php if ($promoCode !== ''): ?>
                  <div>
                    <p class="promo-label">Promo Code</p>
                    <div class="promo-code-box">
                      <span class="promo-code"><?php echo htmlspecialchars($promoCode, ENT_QUOTES, 'UTF-8'); ?></span>
                    </div>
                  </div>
                <?php endif; ?>
                <div class="promo-footer">
                  <button class="btn-claim" data-code="<?php echo htmlspecialchars($promoCode, ENT_QUOTES, 'UTF-8'); ?>">Claim Offer</button>
                </div>
              </div>
            </div>
          </div>
        <?php endforeach; ?>
      <?php endif; ?>
    </section>
  </main>
  <?php include('./assets/php/footer.php'); ?>
</body>
</html>
