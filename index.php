<?php
require_once __DIR__ . '/config/DatabaseUtility.php';

$db = DatabaseUtility::getInstance();
$siteTitle       = $db->getSetting('site_title') ?? 'DelphianLogic in Action';
$siteDescription = $db->getSetting('site_description') ?? '';
$sliderDotsCount = $db->getSetting('slider_dots_count') ?? 3;

$categories = $db->getActiveCategories();
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <title><?= htmlspecialchars($siteTitle) ?></title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet" />
  <link rel="stylesheet" href="Assets/css/style.css" />
</head>
<body>
  <!-- Device Toggle Switch -->
  <div class="device-toggle-container">
    <div id="deviceToggle">
      <div class="container text-end my-1">
  <a href="admin/" class="btn btn-sm btn-outline-primary">
    Manage Content
  </a>
</div>
    </div>
  </div>
<!-- Header -->
<div class="section-header">
  <h2><?= htmlspecialchars($siteTitle) ?></h2>
  <p><?= htmlspecialchars($siteDescription) ?></p>
</div>

<div class="main-container">

<!-- MOBILE VIEW -->
<div class="mobile-view">
  <div class="accordion-wrapper">

<?php foreach ($categories as $index => $category): 
    $topics = $db->getTopicsByCategory((int)$category['id']);
    $topic  = $topics[0] ?? null;
?>
    <div class="accordion-item-custom <?= $index === 0 ? 'expanded' : '' ?>">
      <button class="accordion-header-custom">
        <div class="header-content">
          <div class="header-icon">
            <img src="<?= htmlspecialchars($category['icon_url']) ?>" style="width:28px;height:28px;">
          </div>
          <div class="header-text"><?= htmlspecialchars($category['name']) ?></div>
        </div>
        <div class="toggle-icon">
          <img src="Assets/images/<?= $index === 0 ? 'minus-01.svg' : 'plus-01.svg' ?>">
        </div>
      </button>

      <?php if ($topic): ?>
      <div class="accordion-body-custom">
        <div class="body-content">
          <span class="tag"><?= strtoupper($category['description']) ?></span>
          <h3><?= htmlspecialchars($topic['title']) ?></h3>
          <p><?= htmlspecialchars($topic['short_content'] ?? '') ?></p>
          <div class="image-placeholder">
            <img src="<?= htmlspecialchars($topic['image_url'] ?? '') ?>" onerror="this.style.display='none'">
          </div>
          <a href="topic.php?slug=<?= urlencode($topic['slug']) ?>" class="learn-more-link">Learn More →</a>
        </div>
      </div>
      <?php endif; ?>
    </div>
<?php endforeach; ?>

  </div>
</div>

<!-- DESKTOP VIEW -->
<div class="desktop-view">
  <div class="feature-wrapper">
    <div class="desktop-row">

      <div class="feature-tabs">
        <?php foreach ($categories as $i => $category): ?>
        <div class="feature-tab <?= $i === 0 ? 'active' : '' ?>" data-tab="<?= $i ?>">
          <div class="feature-tab-icon">
            <img src="<?= htmlspecialchars($category['icon_url']) ?>" style="width:28px;height:28px;">
          </div>
          <span><?= htmlspecialchars($category['name']) ?></span>
        </div>
        <?php endforeach; ?>
      </div>

      <?php 
        $firstCategory = $categories[0] ?? null;
        $firstTopic = $firstCategory ? ($db->getTopicsByCategory((int)$firstCategory['id'])[0] ?? null) : null;
      ?>

      <div class="feature-slider">
        <?php if ($firstTopic): ?>
        <div class="slider-content">
          <small><?= strtoupper($firstCategory['description']) ?></small>
          <h3><?= htmlspecialchars($firstTopic['title']) ?></h2>
          <p><?= htmlspecialchars($firstTopic['short_content'] ?? '') ?></p>
          <a href="topic.php?slug=<?= urlencode($firstTopic['slug']) ?>" class="learn-more">Learn More →</a>
        </div>
        <?php endif; ?>

        <div class="slider-dots">
          <?php for ($d = 0; $d < $sliderDotsCount; $d++): ?>
            <span class="<?= $d === 0 ? 'active' : '' ?>" data-dot="<?= $d ?>"></span>
          <?php endfor; ?>
        </div>
      </div>

      <div class="feature-image">
        <img src="Assets/images/thumbnail.png" onerror="this.style.display='none'">
  

        <?php if ($insuranceEnabled): ?>
        <div class="insurance-toggle-section">
          <span class="insurance-label">Insurance</span>
          <div class="insurance-toggle">
            <div class="insurance-toggle-slider"></div>
          </div>
        </div>
        <?php endif; ?>
      </div>

    </div>
  </div>
</div>

</div>

<script src="Assets/js/script.js"></script>
</body>
</html>
