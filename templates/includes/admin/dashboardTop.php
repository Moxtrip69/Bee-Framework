<?php require_once INCLUDES . 'admin/header.php'; ?>
<div class="bee-admin-shell">
  <?php require_once INCLUDES . 'admin/sidebar.php'; ?>
  <div class="bee-admin-main">
    <?php require_once INCLUDES . 'admin/topbar.php'; ?>
    <main class="container-fluid p-3 p-md-4 p-xl-5">
      <header class="d-flex flex-wrap justify-content-between align-items-end gap-3 mb-4">
        <div><span class="text-uppercase small fw-bold text-secondary">Administración</span><h1 class="h2 mb-0 mt-1"><?= htmlspecialchars((string) $d->title, ENT_QUOTES, 'UTF-8') ?></h1></div>
      </header>
      <?= Flasher::flash(); ?>
