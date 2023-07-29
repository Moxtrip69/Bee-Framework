import { todoListComponent } from './todoListComponent'
import { postsListComponent } from './postsListComponent';

export const mainComponent = {
  data() {
    return {
      posts: [],
      nextTodoId: 4,
      newTodo: '',
      todos: [
        {
          id: 1,
          title: 'Crear un framework cool'
        },
        {
          id: 2,
          title: 'Tomar cursos gratuitos de la Academia'
        },
        {
          id: 3,
          title: 'Suscribirse a nuestro Youtube'
        }
      ]
    }
  },
  created() {
    this.fetchData();
  },
  methods: {
    async fetchData() {
      const res = await fetch(Bee.url + 'api/posts', {
        headers: { 'Authorization': `Bearer ${Bee.private_key}` },
        method: 'GET'
      }).then(res => res.json());

      if (res.status !== 200) {
        toastr.error(res.msg, '¡Hubo un error!');
        return;
      }

      this.posts = res.data;
      $('#vueApp').waitMe('hide');
    },
    addNewTodo() {
      if (this.newTodo.length < 5) {
        toastr.error('La tarea es demasiado corta.');
        return false;
      }

      this.todos.push({
        id: this.nextTodoId++,
        title: this.newTodo
      });

      toastr.success('Nueva tarea agregada con éxito.');
      this.newTodo = ''
    }
  },
  computed: {
    notEmpty() {
      return this.newTodo.length > 0;
    }
  },
  components: {
    'todo-list': todoListComponent,
    'posts-list': postsListComponent
  },
  template:
  /*html*/
  `
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
        <posts-list :posts="posts" @fetch-data="fetchData"></posts-list>
      </div>
    </div>
  </div>
  `
};