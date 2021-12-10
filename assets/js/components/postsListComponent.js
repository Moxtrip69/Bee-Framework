
export const postsListComponent = {
  data() {
    return {}
  },
  methods: {
  },
  computed: {
    hasPosts() {
      return this.posts.length > 0;
    }
  },
  props: ['posts'],
  emits: [],
  components: {
  },
  template:
  /*html*/
  `
  <div v-if="hasPosts">
    <ul class="list-group">
      <li class="list-group-item" v-for="(post, index) in posts" :key="post.id">
        {{ post.titulo }}
      </li>
    </ul>
  </div>
  <div class="py-5 text-center text-muted" v-else>
    No hay posts en la lista.
  </div>
  `
}