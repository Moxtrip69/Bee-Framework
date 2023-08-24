<?php require_once INCLUDES . 'admin/header.php'; ?>

<body id="page-top">

  <!-- Page Wrapper -->
  <div id="wrapper">

    <?php require_once INCLUDES . 'admin/sidebar.php'; ?>

    <!-- Content Wrapper -->
    <div id="content-wrapper" class="d-flex flex-column">

      <!-- Main Content -->
      <div id="content">

        <?php require_once INCLUDES . 'admin/topbar.php'; ?>

        <!-- Begin Page Content -->
        <div class="container-fluid px-4">

          <!-- Page Heading -->
          <div class="d-sm-flex align-items-center justify-content-between mb-4">
            <h1 class="h3 mb-0 text-gray-800"><?php echo $d->title; ?></h1>
            <?php require_once INCLUDES . 'admin/dashboardButtons.php'; ?>
          </div>

          <?php echo Flasher::flash(); ?>