<!-- Sidebar -->
<ul class="navbar-nav bg-gradient-primary sidebar sidebar-dark accordion" id="accordionSidebar">

  <!-- Sidebar - Brand -->
  <a class="sidebar-brand d-flex align-items-center justify-content-center" href="<?php echo get_base_url(); ?>">
    <img src="<?php echo get_bee_logo(); ?>" alt="<?php echo get_bee_name(); ?>" width="100px">
  </a>

  <!-- Divider -->
  <hr class="sidebar-divider my-0">

  <!-- Nav Item - Dashboard -->
  <li class="nav-item active">
    <a class="nav-link" href="admin">
      <i class="fas fa-fw fa-tachometer"></i>
      <span>Dashboard</span>
    </a>
  </li>

  <!-- Divider -->
  <hr class="sidebar-divider">

  <!-- Heading -->
  <div class="sidebar-heading">
    Bee framework
  </div>

  <li class="nav-item">
    <a class="nav-link" href="creator">
      <i class="fas fa-fw fa-pen"></i>
      <span>Creator</span>
    </a>
  </li>

  <!-- Nav Item - Pages Collapse Menu -->
  <li class="nav-item">
    <a class="nav-link collapsed" href="#" data-toggle="collapse" data-target="#collapseTwo" aria-expanded="true" aria-controls="collapseTwo">
      <i class="fas fa-fw fa-cog"></i>
      <span>Componentes</span>
    </a>
    <div id="collapseTwo" class="collapse" aria-labelledby="headingTwo" data-parent="#accordionSidebar">
      <div class="bg-white py-2 collapse-inner rounded">
        <h6 class="collapse-header">SB Admin 2</h6>
        <a class="collapse-item" href="https://startbootstrap.com/theme/sb-admin-2" target="_blank">Template original</a>
        <a class="collapse-item" href="admin/botones">Botones</a>
        <a class="collapse-item" href="admin/cartas">Cartas</a>
      </div>
    </div>
  </li>

  <!-- Divider -->
  <hr class="sidebar-divider">

  <!-- Heading -->
  <div class="sidebar-heading">
    Gestión
  </div>

  <li class="nav-item">
    <a class="nav-link" href="admin/usuarios">
      <i class="fas fa-fw fa-users"></i>
      <span>Usuarios</span>
    </a>
  </li>

  <li class="nav-item">
    <a class="nav-link" href="admin/productos">
      <i class="fas fa-fw fa-tag"></i>
      <span>Productos</span>
    </a>
  </li>

  <!-- Divider -->
  <hr class="sidebar-divider">


  <!-- Heading -->
  <div class="sidebar-heading">
    Addons
  </div>

  <!-- Nav Item - Pages Collapse Menu -->
  <li class="nav-item">
    <a class="nav-link collapsed" href="#" data-toggle="collapse" data-target="#collapsePages" aria-expanded="true" aria-controls="collapsePages">
      <i class="fas fa-fw fa-folder"></i>
      <span>Páginas</span>
    </a>
    <div id="collapsePages" class="collapse" aria-labelledby="headingPages" data-parent="#accordionSidebar">
      <div class="bg-white py-2 collapse-inner rounded">
        <a class="collapse-item" href="login">Login</a>
        <a class="collapse-item" href="registro">Registro</a>
        <a class="collapse-item" href="admin/perfil">Perfil</a>
        <a class="collapse-item" href="vuejs">Vue3</a>
      </div>
    </div>
  </li>

  <!-- Divider -->
  <hr class="sidebar-divider d-none d-md-block">

  <!-- Sidebar Toggler (Sidebar) -->
  <div class="text-center d-none d-md-inline">
    <button class="rounded-circle border-0" id="sidebarToggle"></button>
  </div>

  <!-- Sidebar Message 
  <div class="sidebar-card d-none d-lg-flex">
    <img class="sidebar-card-illustration mb-2" src="img/undraw_rocket.svg" alt="...">
    <p class="text-center mb-2"><strong>SB Admin Pro</strong> is packed with premium features, components,
      and more!</p>
    <a class="btn btn-success btn-sm" href="https://startbootstrap.com/theme/sb-admin-pro">Upgrade to
      Pro!</a>
  </div>-->

</ul>
<!-- End of Sidebar -->