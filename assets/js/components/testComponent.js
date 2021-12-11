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
    get() {
      this.postId = Bee.current_params[0];

      const body = {
        id: this.postId
      }

      $.ajax({
        url: 'ajax/test_get_post',
        type: 'get',
        dataTpe: 'json',
        cache: false,
        data: body
      }).done(res => {
        if (res.status === 200) {
          this.post = res.data;
        } else {
          toastr.error(res.msg);
        }
      }).fail(err => {
        toastr.error(err.responseJSON.msg);
      }).always(() => {
      })
    }
  },
  template:
  /*html*/
  `
  <div className="p-5 bg-light border rounded shadow mt-5">
    <div className="text-center">
      <p>Componente <code>testComponent.js</code> cargado con éxito</p>
      <a href="vuejs" className="text-muted">Regresar</a>
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