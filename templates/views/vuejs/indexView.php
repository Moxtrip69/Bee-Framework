<?php require_once INCLUDES.'inc_bee_header.php'; ?>
<?php require_once INCLUDES.'inc_bee_navbar.php'; ?>

<div class="container">
  <div class="py-5 text-center">
    <a href="<?php echo URL; ?>"><img src="<?php echo get_logo() ?>" alt="<?php echo get_sitename(); ?>" class="img-fluid" style="width: 150px;"></a>
    <h2><?php echo $d->title; ?></h2>
    <p class="lead">Lorem ipsum dolor sit amet consectetur, adipisicing elit. Nam, ullam.</p>
  </div>

  <div class="row">
    <div class="col-12">
      <?php echo Flasher::flash(); ?>
    </div>

    <!-- Formulario -->
    <div id="vueApp">
      <div class="col-12 col-md-6 offset-md-3 mb-3">
        <div class="card">
          <div class="card-header">
            <h4>Lista de tareas Vue.js 3</h4>
          </div>
          <div class="card-body">
            <form v-on:submit.prevent="addNewTodo">
              <div class="mb-2">
                <label for="new-todo" class="form-label">Escribe una tarea...</label>
                <input
                  v-model="newTodo"
                  placeholder="Alimentar al gato asesino..."
                  class="form-control"
                />
                <button class="btn btn-success mt-2" type="submit" :disabled="!notEmpty">Agregar tarea</button>
              </div>
            </form>
            
            <todo-list :todos="todos"></todo-list>
          </div>
        </div>
      </div>

      <div class="col-12 col-md-6 offset-md-3 mb-3">
        <div class="card">
          <div class="card-header">
            <button class="btn btn-sm float-end" @click="fetchData"><i class="fas fa-sync fa-fw"></i></button>
            <h4>Lista de posts</h4>
          </div>
          <div class="card-body">
            <posts-list :posts="posts"></posts-list>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>

<?php require_once INCLUDES.'inc_bee_footer.php'; ?>