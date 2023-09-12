const formularioComponent = {
  data() {
    return {
      nombre: '',
      email: '',
      problema: '',
      imagen: ''
    }
  },
  methods: {
    async levantarReporte() {
      let errores = 0;

      if (this.nombre == '' || this.nombre.length < 5) {
        toastr.error('Completa tu nombre por favor.');
        errores++;
      }

      if (this.email == '' || this.email.length < 5) {
        toastr.error('Completa tu correo electrónico por favor.');
        errores++;
      }

      if (this.problema == '' || this.problema.length < 5) {
        toastr.error('Ingresa una descripción del problema de más de 5 caracteres por favor.');
        errores++;
      }

      if (this.imagen == '') {
        toastr.error('Selecciona una imagen para adjuntar al reporte por favor.');
        errores++;
      }

      if (errores > 0) return;

      const payload = new FormData();
      payload.append('nombre'  , this.nombre);
      payload.append('email'   , this.email);
      payload.append('problema', this.problema);
      payload.append('imagen'  , this.imagen);
      payload.append('csrf'    , Bee.csrf);

      // Enviar información
      const res = await fetch('ajax/levantar-reporte', {
        method: 'POST',
        body: payload
      })
      .then(res => res.json())
      .catch(err => toastr.error(err));

      if (res.status !== 201) {
        toastr.error(res.msg, 'Hubo un error');
        return false;
      }

      toastr.success(res.msg);
      this.reiniciarReporte();
      this.$emit('cargar-reportes');
      return true;
    },
    adjuntarImagen(event) {
      // Obtener el archivo seleccionado del input
      const archivo = event.target.files[0];

      // Actualizar la propiedad del modelo de datos con el nombre del archivo
      this.imagen   = archivo;
    },
    reiniciarReporte() {
      this.nombre   = '';
      this.email    = '';
      this.problema = '';
      
      const inputImagen = document.getElementById('imagen');
      inputImagen.value = '';
      this.imagen       = '';
    }
  },
  computed: {
    listoParaLevantar() {
      if (this.nombre == '' || this.nombre.length < 5) {
        return false;
      }

      if (this.email == '' || this.email.length < 5) {
        return false;
      }

      if (this.problema == '' || this.problema.length < 5) {
        return false;
      }

      if (this.imagen == '') {
        return false;
      }

      return true;
    },
    imagenUrl() {
      return URL.createObjectURL(this.imagen);
    }
  },
  emits: ['cargar-reportes'],
  template:
  /*html */
  `
  <div className="card">
    <div className="card-header">Reportar falla</div>
    <div className="card-body">
      <form v-on:submit.prevent="levantarReporte">
        <div className="mb-2 row">
          <div className="col-12 col-md-6">
            <label htmlFor="nombre" className="form-label">Tu nombre completo <span className="text-danger">*</span></label>
            <input 
            type="text" 
            v-model="nombre" 
            className="form-control" 
            id="nombre"
            placeholder="Pancho Villa" />
          </div>
          <div className="col-12 col-md-6">
            <label htmlFor="email" className="form-label">Tu correo electrónico <span className="text-danger">*</span></label>
            <input 
            type="email" 
            v-model="email" 
            className="form-control" 
            id="email"
            placeholder="pancho@villa.com" />
          </div>
        </div>
        <div class="mb-2">
          <label for="problema" class="form-label">¿Cuál es el problema del equipo? <span className="text-danger">*</span></label>
          <textarea
            v-model="problema"
            class="form-control"
            id="problema"
            placeholder="Parpadea una luz roja..."
          ></textarea>
        </div>
        <div className="mb-2">
          <label htmlFor="imagen" className="form-label">Imagen adicional <span className="text-danger">*</span></label>
          <input type="file" className="form-control" id="imagen" accept="image/png, image/gif, image/jpeg" @change="adjuntarImagen" />
          <img class="img-fluid img-thumbnail img-shadow mt-3" v-if="imagen" :src="imagenUrl" alt="Preview de la imagen" style="width: 200px;">
        </div>
        
        <button class="btn btn-success mt-2" type="submit" :disabled="!listoParaLevantar">Levantar reporte</button>
      </form>
    </div>
  </div>
  `
};

const reporteComponent = {
  data() {
    return {
      uploads: Bee.uploaded
    }
  },
  props: {
    reporte: Object,
    procesando: Boolean
  },
  computed: {
    formatearUrlImagen(reporteImagen) {
      console.log(reporteImagen)
    }
  },
  emits: ['resolver-reporte','pendiente-reporte'],
  template:
  /*html*/
  `
  <!-- Modal -->
  <div class="modal fade" id="reporteModal" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-labelledby="reporteModal" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
      <div class="modal-content">
        <div class="modal-header">
          <h1 class="modal-title fs-5">Viendo reporte</h1>
        </div>
        <div class="modal-body">
          <span v-if="reporte">
            <h3>{{reporte.titulo}}</h3>
            {{reporte.contenido}}
            <img class="img-fluid img-thumbnail shadow" :src="uploads + reporte.mime_type" :alt="reporte.titulo" />
          </span>
          <span v-else>Oh no 😢</span>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button>
          <button 
          type="button" 
          class="btn btn-success" 
          :disabled="procesando" 
          @click="$emit('resolver-reporte', reporte.id)" 
          v-if="reporte.status === 'pendiente'"><i className="fa fa-check"></i> Marcar como resuelto</button>
          <button 
          type="button" 
          class="btn btn-danger" 
          :disabled="procesando" 
          @click="$emit('pendiente-reporte', reporte.id)" 
          v-if="reporte.status === 'resuelto'"><i className="fa fa-undo"></i> Marcar como pendiente</button>
        </div>
      </div>
    </div>
  </div>
  `
};

const listadoComponent = {
  props: {
    reportes: Array, // agregar procensado
    procesando: Boolean
  },
  emits: ['mostrar-reporte'],
  template:
  /*html*/
  `
  <div className="card">
    <div className="card-header d-flex justify-content-between align-items-center">
      <span>Listado de reportes</span>
      <div className="btn-group">
        <button className="btn btn-sm btn-success" @click="$emit('cargar-reportes')"><i className="fas fa-sync"></i></button>
      </div>
    </div>
    <div className="card-body">
      <span v-if="procesando">🚀 Cargando información ...</span>
      <ul class="list-group" v-if="reportes">
        <li class="list-group-item d-flex justify-content-between align-items-center" v-for="(reporte, index) in reportes" :key="reporte.id">
          <span>{{reporte.titulo}}</span>
          <span>
            <span className="badge rounded-pill text-bg-danger" v-if="reporte.status == 'pendiente'"><i className="fas fa-clock fa-fw"></i> Pendiente</span>
            <span className="badge rounded-pill text-bg-success" v-if="reporte.status == 'resuelto'"><i className="fa fa-check fa-fw"></i> Resuelto</span>
          </span>
          <button className="btn btn-sm btn-primary" @click="$emit('mostrar-reporte', reporte)" data-bs-toggle="modal" data-bs-target="#reporteModal"><i className="fa fa-eye"></i></button>
        </li>
      </ul>
      <div className="text-muted py-5 text-center" v-else>
        No hay reportes registrados.
      </div>
    </div>
  </div>
  `
};

const mainComponent = {
  data() {
    return {
      reportes: [],
      reporte: '',
      procesando: false
    }
  },
  created() {
    this.cargarReportes();
  },
  methods: {
    async cargarReportes() {
      this.procesando = true;
      const res = await fetch('ajax/cargar-reportes', {
        method: 'GET'
      }).then(res => res.json());

      if (res.status !== 200) {
        toastr.error(res.msg, 'Hubo un error');
        return;
      }
      
      this.procesando = false;
      this.reportes   = res.data;
    },
    mostrarReporte(reporte) {
      this.reporte = reporte;
    },
    async resolverReporte(id) {
      // Definir que algo se está procesando
      this.procesando = true;

      // Actualizar el estado del reporte
      const payload = {
        csrf: Bee.csrf,
        id: id
      };

      const res = await fetch('ajax/resolver-reporte', {
        method: 'POST',
        body: JSON.stringify(payload)
      })
      .then(res => res.json())
      .catch(err => toastr.error(err, 'Hubo un error'));

      if (res.status !== 200) {
        toastr.error(res.msg);
        this.procesando = false;
        return;
      }

      toastr.success(res.msg);
      this.reporte    = res.data;
      this.procesando = false;
      this.cargarReportes();
    },
    async pendienteReporte(id) {
      // Definir que algo se está procesando
      this.procesando = true;

      // Actualizar el estado del reporte
      const payload = {
        csrf: Bee.csrf,
        id: id
      };

      const res = await fetch('ajax/pendiente-reporte', {
        method: 'POST',
        body: JSON.stringify(payload)
      })
      .then(res => res.json())
      .catch(err => toastr.error(err, 'Hubo un error'));

      if (res.status !== 200) {
        toastr.error(res.msg);
        this.procesando = false;
        return;
      }

      toastr.success(res.msg);
      this.reporte    = res.data;
      this.procesando = false;
      this.cargarReportes();
    }
  },
  computed: {
  },
  components: {
    'formulario-component': formularioComponent,
    'listado-component': listadoComponent,
    'reporte-component': reporteComponent
  },
  template:
  /*html*/
  `
  <div className="container-fluid">
    <div className="row">
      <div className="col-12 col-md-4">
        <formulario-component @cargar-reportes="cargarReportes"></formulario-component>
      </div>

      <div className="col-12 col-md-8">
        <listado-component 
        :reportes="reportes" 
        @mostrar-reporte="mostrarReporte"
        @cargar-reportes="cargarReportes"></listado-component>
      </div>
    </div>
  </div>

  <reporte-component 
  :reporte="reporte" 
  :procesando="procesando" 
  @resolver-reporte="resolverReporte"
  @pendiente-reporte="pendienteReporte"></reporte-component>
  `
};

Vue.createApp(mainComponent).mount('#appContainer');