<?php require_once INCLUDES.'inc_header.php'; ?>
<?php require_once INCLUDES.'inc_bee_navbar.php'; ?>

<div class="container">
  <div class="py-5 text-center">
    <a href="<?php echo URL; ?>"><img src="<?php echo get_image('bee_logo.png') ?>" alt="Bee framework" class="img-fluid" style="width: 150px;"></a>
    <h2><?php echo $d->title; ?></h2>
    <p class="lead">Lorem ipsum dolor sit amet consectetur, adipisicing elit. Nam, ullam.</p>
  </div>

  <div class="row">
    <div class="col-12">
      <?php echo Flasher::flash(); ?>
    </div>

    <!-- formulario -->
    <div class="col-xl-12">
      <div class="card">
        <div class="card-body">
          <div id="todo-list-example" >
            <form v-on:submit.prevent="addNewTodo">
              <div class="mb-2">
                <label for="new-todo" class="form-label">Lista de Tareas Vue.js 3</label>
                <input
                  v-model="newTodoText"
                  id="new-todo"
                  placeholder="Alimentar al gato asesino..."
                  class="form-control"
                />
                <button class="btn btn-success mt-2" type="submit">Agregar tarea</button>
              </div>
            </form>
            <div v-if="todos.length > 0">
              <ul class="list-group">
                <todo-item
                  v-for="(todo, index) in todos"
                  :key="todo.id"
                  :title="todo.title"
                  @remove="todos.splice(index, 1)"
                ></todo-item>
              </ul>
            </div>
            <div class="py-5 text-center text-muted" v-else>
              No hay tareas en la lista.
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>

<?php require_once INCLUDES.'inc_bee_footer.php'; ?>