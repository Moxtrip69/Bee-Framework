<?php if (isset($d->buttons) && !empty($d->buttons)): ?>
  <div class="btn-group">
    <?php foreach ($d->buttons as $button): ?>
      <?php echo sprintf(
        '<a id="%s" href="%s" class="d-none d-sm-inline-block btn btn-sm shadow-sm %s"><i class="%s fa-sm text-white-50"></i> %s</a>',
        isset($button->id) ? $button->id : '',
        $button->url,
        isset($button->class) ? $button->class : 'btn-primary',
        isset($button->icon) ? $button->icon : 'fas fa-download',
        $button->text
      ); ?>
    <?php endforeach; ?>
  </div>
<?php endif; ?>