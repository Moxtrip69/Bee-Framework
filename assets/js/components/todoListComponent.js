import { todoItemComponent } from "./todoItemComponent"

export const todoListComponent = {
  data() {
    return {}
  },
  methods: {
    removeTodo(index) {
      this.todos.splice(index, 1);
      toastr.success('Tarea borrada con éxito.');
    }
  },
  computed: {
    hasTodos() {
      return this.todos.length > 0;
    }
  },
  props: ['todos'],
  emits: [],
  components: {
    'todo-item': todoItemComponent
  },
  template:
  /*html*/
  `
  <div v-if="hasTodos">
    <ul class="list-group">
      <todo-item
        v-for="(todo, index) in todos"
        :key="todo.id"
        :todo="todo"
        @remove="removeTodo(index)"
      ></todo-item>
    </ul>
  </div>
  <div class="py-5 text-center text-muted" v-else>
    No hay tareas en la lista.
  </div>
  `
}