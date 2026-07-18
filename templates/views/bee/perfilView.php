<?php require_once INCLUDES . 'header.php'; ?>
<?php require_once INCLUDES . 'bee_navbar.php'; ?>

<?php
$username = isset($d->user->username) ? (string) $d->user->username : 'Usuario';
$initial = function_exists('mb_substr')
  ? mb_strtoupper(mb_substr($username, 0, 1, 'UTF-8'), 'UTF-8')
  : strtoupper(substr($username, 0, 1));
?>

<main class="container bee-section main-wrapper">
  <header class="bee-page-header">
    <span class="bee-eyebrow">Cuenta local</span>
    <h1>Hola, <?= htmlspecialchars($username, ENT_QUOTES, 'UTF-8') ?></h1>
    <p>Consulta la información disponible para tu sesión actual.</p>
  </header>
  <?= Flasher::flash(); ?>
  <section class="bee-card bee-profile" aria-labelledby="profile-title">
    <div class="bee-card-heading">
      <div class="bee-avatar" aria-hidden="true"><?= htmlspecialchars($initial, ENT_QUOTES, 'UTF-8') ?></div>
      <div><span class="bee-eyebrow">Perfil</span><h2 id="profile-title"><?= htmlspecialchars($username, ENT_QUOTES, 'UTF-8') ?></h2></div>
    </div>
    <dl class="bee-data-list">
      <?php foreach ($d->user as $key => $value) : ?>
        <?php $displayValue = is_scalar($value) || $value === null ? (string) $value : (string) json_encode($value, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES); ?>
        <div><dt><?= htmlspecialchars((string) $key, ENT_QUOTES, 'UTF-8') ?></dt><dd><?= htmlspecialchars($displayValue, ENT_QUOTES, 'UTF-8') ?></dd></div>
      <?php endforeach; ?>
    </dl>
    <footer class="bee-card-actions">
      <a href="bee" class="btn btn-outline-secondary">Volver al inicio</a>
      <a href="logout" class="btn btn-danger confirmar">Cerrar sesión</a>
    </footer>
  </section>
</main>

<?php require_once INCLUDES . 'footer.php'; ?>
