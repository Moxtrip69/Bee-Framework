////////////////////////////////////////////////////////////////////////
////////////////////////////////////////////////////////////////////////
///////// VUE JS V3 EJEMPLO
////////////////////////////////////////////////////////////////////////

import { postsListComponent } from "./components/postsListComponent";
import { todoListComponent } from "./components/todoListComponent";

////////////////////////////////////////////////////////////////////////
Vue.createApp({
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
    fetchData: function() {
      var xhr = new XMLHttpRequest(),
      $this   = this;

      xhr.open("get", Bee.url + 'ajax/test_posts');
      xhr.onload = function() {
        var res     = JSON.parse(xhr.responseText);
        $this.posts = res.data;
      };
      xhr.send();

      $.ajax({
        url: 'ajax/test_posts',
        type: 'get',
        dataType: 'json',
        cache: false,
        data: {},
        beforeSend() {
          $('#vueApp').waitMe();
        }
      }).done(res => {
      }).fail(err => {
        var res = err.responseJSON;
        toastr.error(res.msg, '¡Hubo un error!');
      }).always(() => {
        $('#vueApp').waitMe('hide');
      });
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
  }
}).mount('#vueApp');