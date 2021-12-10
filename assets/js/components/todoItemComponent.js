export const todoItemComponent = {
  data() {
    return {
    }
  },
  methods: {
  },
  computed: {
  },
  props: ['todo'],
  emits: ['remove'],
  template:
  /*html*/
  `
  <li class="list-group-item">
    {{ todo.title }}
    <button @click="$emit('remove')" class="btn btn-sm btn-danger float-end"><i class="fas fa-trash"></i></button>
  </li>
  `
}