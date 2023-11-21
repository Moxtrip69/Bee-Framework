
export const postsListComponent = {
  data() {
    return {
      postId: null,
      postTitle: String,
      postContent: String,
      post: {
        titulo: '',
        contenido: '',
        nombre: ''
      },
      postEdit: {
        id: '',
        titulo: '',
        contenido: '',
        nombre: ''
      },
      isEditing: false
    }
  },
  methods: {
    async addPost() {
      if (this.post.titulo.length < 5) {
        toastr.error('Completa el título del post.');
        return;
      }

      if (this.post.nombre.length < 5) {
        toastr.error('Completa el nombre del Autor.');
        return;
      }

      if (this.post.contenido.length < 10) {
        toastr.error('El contenido es demasiado corto.');
        return;
      }

      // Petición de agregado
      const body = {
        nombre: this.post.nombre,
        titulo: this.post.titulo,
        contenido: this.post.contenido
      };

      const res = await fetch(Bee.url + `api/posts`, {
        headers: { 'Authorization': `Bearer ${Bee.private_key}`, 'Content-Type': 'application/json' },
        method: 'POST',
        body: JSON.stringify(body)
      }).then(res => res.json());

      if (res.status !== 201) {
        toastr.error(res.msg);
        return;
      } 

      this.resetPost();
      toastr.success(res.msg);
      this.$emit('fetch-data');
      return;

    },
    async updatePost() {
      if (this.postEdit.titulo.length < 5) {
        toastr.error('Completa el título del post.');
        return;
      }

      if (this.postEdit.nombre.length < 5) {
        toastr.error('Completa el nombre del Autor.');
        return;
      }

      if (this.postEdit.contenido.length < 10) {
        toastr.error('El contenido es demasiado corto.');
        return;
      }

      // Petición de agregado
      const body = {
        nombre   : this.postEdit.nombre,
        titulo   : this.postEdit.titulo,
        contenido: this.postEdit.contenido
      };

      const res = await fetch(Bee.url + `api/posts/${this.postEdit.id}`, {
        headers: { 'Authorization': `Bearer ${Bee.private_key}`, 'Content-Type': 'application/json' },
        method: 'PUT',
        body: JSON.stringify(body)
      }).then(res => res.json());

      if (res.status === 200) {
        this.isEditing = false;
        this.resetPost();
        toastr.success(res.msg);
        this.$emit('fetch-data');
      } else {
        toastr.error(res.msg);
      }
    },
    async removePost(id) {
      this.postId = id;

      if (!this.postId) {
        toastr.error('El ID del post no es válido.');
      }

      if (!confirm('¿Estás seguro?')) return;

      // Petición de borrado
      const res = await fetch(Bee.url + `api/posts/${this.postId}`, {
        headers: { 'Authorization': `Bearer ${Bee.private_key}` },
        method: 'delete'
      }).then(res => res.json());

      if (res.status === 200) {
        toastr.success(res.msg);
        this.$emit('fetch-data');
      } else {
        toastr.error(res.msg);
      }
      this.postId = null;
    },
    async openEdit(id) {
      this.postEdit.id = id;

      const res = await fetch(Bee.url + `api/posts/${this.postEdit.id}`, {
        headers: { 'Authorization': `Bearer ${Bee.private_key}` },
        method: 'GET'
      }).then(res => res.json());

      if (res.status === 200) {
        this.postEdit.titulo    = res.data.titulo;
        this.postEdit.nombre    = res.data.nombre;
        this.postEdit.contenido = res.data.contenido;
        this.isEditing          = true;
      } else {
        toastr.error(res.msg);
      }
    },
    resetPost() {
      this.post = {
        titulo: '',
        contenido: '',
        nombre: ''
      };

      this.postEdit = {
        titulo: '',
        contenido: '',
        nombre: ''
      };
    },
    cancelUpdate() {
      this.resetPost();
      this.isEditing = false;
    }
  },
  computed: {
    hasPosts() {
      return this.posts.length > 0;
    },
    canAdd() {
      return (this.post.titulo.length > 5 && this.post.contenido.length > 10 && this.post.nombre.length > 5);
    },
    canUpdate() {
      return (this.postEdit.titulo.length > 5 && this.postEdit.contenido.length > 10 && this.postEdit.nombre.length > 5);
    }
  },
  props: ['posts'],
  emits: ['fetch-data'],
  components: {
  },
  template:
  /*html*/
  `
  <form @submit.prevent="addPost" class="mb-3" v-if="!isEditing">
    <div className="mb-3 row">
      <div class="col-12 col-md-6">
        <label htmlFor="titulo" className="form-label">Título del post</label>
        <input type="text" className="form-control" v-model="post.titulo" placeholder="El título del post..."/>
      </div>
      <div class="col-12 col-md-6">
        <label htmlFor="nombre" className="form-label">Nombre del autor</label>
        <input type="text" className="form-control" v-model="post.nombre" placeholder="El nombre del Autor..."/>
      </div>
    </div>
    <div className="mb-3">
      <label htmlFor="contenido" className="form-label">Contenido</label>
      <textarea class="form-control" name="contenido" id="contenido" cols="30" rows="10" v-model="post.contenido"></textarea>
    </div>

    <button className="btn btn-success" :disabled="!canAdd"><i className="fas fa-save fa-fw"></i> Guardar</button>
  </form>

  <form @submit.prevent="updatePost" class="mb-3" v-else>
    <div className="mb-3 row">
      <div class="col-12 col-md-6">
        <label htmlFor="titulo" className="form-label">Título del post</label>
        <input type="text" className="form-control" v-model="postEdit.titulo" placeholder="El título del post..."/>
      </div>
      <div class="col-12 col-md-6">
        <label htmlFor="nombre" className="form-label">Nombre del autor</label>
        <input type="text" className="form-control" v-model="postEdit.nombre" placeholder="El nombre del Autor..."/>
      </div>
    </div>
    <div className="mb-3">
      <label htmlFor="contenido" className="form-label">Contenido</label>
      <textarea class="form-control" name="contenido" id="contenido" cols="30" rows="10" v-model="postEdit.contenido"></textarea>
    </div>

    <button className="btn btn-success me-1" :disabled="!canUpdate" type="submit"><i className="fas fa-save fa-fw"></i> Actualizar</button>
    <button className="btn btn-warning" @click="cancelUpdate" type="button">Cancelar</button>
  </form>

  <div v-if="hasPosts">
    <ul class="list-group">
      <li class="list-group-item" v-for="(post, index) in posts" :key="post.id">
        {{post.titulo}} <span className="text-muted">por</span> {{post.nombre}}
        <div className="btn-group float-end">
          <a :href="'bee/test-component/' + post.id" className="btn btn-sm btn-primary"><span className="fas fa-eye"></span></a>
          <button @click="openEdit(post.id)" class="btn btn-sm btn-success" :disabled="post.id == postEdit.id"><i class="fas fa-edit"></i></button>
          <button @click="removePost(post.id)" class="btn btn-sm btn-danger"><i class="fas fa-trash"></i></button>
        </div>
      </li>
    </ul>
  </div>
  <div class="py-5 text-center text-muted" v-else>
    No hay posts en la lista.
  </div>
  `
}