<?php require_once INCLUDES.'inc_header.php'; ?>

<div id="test_ajax"></div>

<div class="container">
  <div class="row">
    <div class="col-12">
      <?php echo Flasher::flash(); ?>
    </div>
  </div>
  <div class="row">
    <div class="col-8 text-center offset-md-2">
      <a href="<?php echo URL; ?>"><img src="<?php echo get_image('bee_logo_white.png') ?>" alt="Bee framework" class="img-fluid" style="width: 200px;"></a>
      <h2 class="text-white mt-5"><span class="text-warning">Bee</span> framework</h2>
      <span class="d-block text-white mb-3"><?php echo sprintf('Versión %s', get_bee_version()); ?></span>
      <p class="text-center text-white">Un framework hecho en casa, con pasión y mucho cariño. Ligero, rápido y personalizable. Úsalo como gustes, en tus proyectos personales o profesionales.</p>
      
      <ul class="text-white">
        <li>Desarrollado con PHP, Javascript y HTML5</li>
        <li>Bootstrap 5 Beta</li>
        <li>Funciona utilizando el patrón <b>MVC</b></li>
        <li>Sistema de sesiones listo para usarse</li>
        <li>ORM sencillo incluido para manipulación de bases de datos</li>
        <li><b>100%</b> personalizable y escalable <?php echo more_info('¡Hola mundo!'); ?></li>
      </ul>

      <div class="mt-5">
        <a class="btn btn-light btn-lg" href="creator"><i class="fas fa-plus"></i> Creator</a>
        <a class="btn btn-warning btn-lg" href="login">Ingresar</a>
        <a class="btn btn-info btn-lg" href="home/flash">Mi cuenta</a>
        <a class="btn btn-success btn-lg" href="https://github.com/Moxtrip69/Bee-Framework"><i class="fab fa-github"></i> Github</a>
      </div>

      <div class="my-5 bg-light p-3">
        <div id="todo-list-example" >
          <form v-on:submit.prevent="addNewTodo">
            <div class="mb-2">
              <label for="new-todo">Lista de Tareas Vue.js 3</label>
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
          <div class="py-5" v-else>
            No hay tareas en la lista.
          </div>
        </div>
      </div>

      <div class="mt-5">
        <p class="text-muted">Desarrollado con <i class="fas fa-heart text-danger"></i> por <a href="https://www.joystick.com.mx" class="text-white">Joystick</a>.</p>
      </div>
    </div>
  </div>
</div>

<?php require_once INCLUDES.'inc_footer.php'; ?>