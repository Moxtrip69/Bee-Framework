    </main>
    <footer class="px-3 px-md-4 px-xl-5 py-4 mt-auto border-top bg-body"><div class="d-flex flex-wrap justify-content-between gap-2 small text-secondary"><span>Bee Framework · Administración</span><span>Bee <?= htmlspecialchars(get_bee_version(), ENT_QUOTES, 'UTF-8') ?></span></div></footer>
  </div>
</div>
<?php require_once INCLUDES . 'scripts.php'; ?>
<script src="<?php echo JS . 'admin.js?v=' . get_asset_version(); ?>"></script>
</body>
</html>
