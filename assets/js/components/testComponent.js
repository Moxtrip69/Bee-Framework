export const testComponent = {
  data() {
    return {
      postId: null,
      post: false,
    }
  },
  created() {
    this.get();
  },
  methods: {
    async get() {
      this.postId = Bee.current_params[0];

      const res = await fetch(Bee.url + `api/posts/${this.postId}`, {
        headers: { 'Authorization': `Bearer ${Bee.private_key}` },
        method: 'GET'
      }).then(res => res.json());

      if (res.status !== 200) {
        toastr.error(res.msg);
      }
      
      this.post = res.data;
    }
  },
  template:
  /*html*/
  `
  <div className="p-5 bg-light border rounded shadow mt-5">
    <div className="text-center">
      <p>Componente <code>testComponent.js</code> cargado con éxito</p>
      <a href="bee/vuejs" className="text-muted">Regresar</a>
    </div>

    <div className="row mt-3" v-if="post">
      <div className="col-12">
        <div className="card">
          <div className="card-header"><h4>{{post.titulo}}</h4></div>
          <div className="card-body nl2br">
            {{post.contenido}}
          </div>
          <div className="card-footer">
            Por <span className="text-muted">{{post.nombre}}</span>
          </div>
        </div>
      </div>
    </div>
  </div>
  `
}