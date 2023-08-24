<?php require_once INCLUDES . 'header.php'; ?>

<!-- Three js -->
<script src="https://cdn.jsdelivr.net/npm/three@0.128.0/build/three.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/three@0.128.0/examples/js/loaders/GLTFLoader.js"></script>
<script src="https://cdn.jsdelivr.net/npm/three@0.128.0/examples/js/controls/OrbitControls.js"></script>

<div id="threejs-container"></div>
<script>
  // Configuración básica
  const scene = new THREE.Scene();
  scene.background = new THREE.Color(0xD48618); // Fondo rosa pastel

  const camera = new THREE.PerspectiveCamera(75, window.innerWidth / window.innerHeight, 0.1, 1000);
  camera.position.set(0, 2, 7); // Ajusta la posición de la cámara
  camera.lookAt(0, 0, 0); // Asegúrate de que la cámara apunte al centro de la escena
  const renderer = new THREE.WebGLRenderer();
  renderer.setSize(window.innerWidth, window.innerHeight);

  // Configurar el renderizador para sombras suaves (PCFSoftShadowMap)
  renderer.shadowMap.type = THREE.PCSSoftShadowMap; // O podrías usar THREE.PCSSoftShadowMap para sombras PCSS


  const container = document.getElementById('threejs-container');
  container.appendChild(renderer.domElement);

  // Crear una esfera
  // const geometry = new THREE.SphereGeometry(1, 32, 32);
  // const material = new THREE.MeshPhongMaterial({
  //   color: 0x00aaff,
  //   shininess: 35,         // Brillo del material
  //   specular: 0x111111     // Color especular del material
  // });
  // const sphere = new THREE.Mesh(geometry, material);
  // sphere.castShadow = true;
  // sphere.receiveShadow = true;
  // scene.add(sphere);

  // Cargar el modelo glTF
  const loader = new THREE.GLTFLoader();
  loader.load('http://localhost:8848/Bee-Framework/assets/uploads/room.glb', (gltf) => {
    const model = gltf.scene; // El modelo cargado
    model.castShadow = true; // Habilitar sombras para el modelo
    model.receiveShadow = true;

    // Ajustar la posición, escala y rotación del modelo según sea necesario
    model.position.set(0, 10, 0);
    model.rotation.set(0, 180, 0);
    model.scale.set(1, 1, 1);

    // Recorrer cada objeto dentro del modelo
    model.traverse((object) => {
      if (object.isMesh) {
        // Configurar las propiedades de sombreado para cada material
        object.castShadow = true; // El objeto arroja sombras
        object.receiveShadow = true; // El objeto recibe sombras
      }
    });

    scene.add(model); // Agregar el modelo a la escena

    // Animación para hacer que el modelo entre girando suavemente
    const targetPosition = new THREE.Vector3(0, 0, 0);
    const initialPosition = model.position.clone();

    const targetRotation = new THREE.Vector3(0, 0, 0);
    const initialRotation = model.rotation.clone();

    let animationProgress = 0;

    const animateModelEntrance = () => {
      if (animationProgress < 1) {
        animationProgress += 0.005; // Ajusta la velocidad de entrada
        const easedProgress = 1 - Math.pow(1 - animationProgress, 2); // Easing cuadrático para suavizar

        model.position.lerpVectors(initialPosition, targetPosition, easedProgress);
        model.rotation.y = Math.PI * easedProgress + 90; // Gira a medida que entra

        requestAnimationFrame(animateModelEntrance);
      }
    };

    animateModelEntrance();
  });

  // Crear un cubo con sombreado "FlatShading"
  // const geometry = new THREE.BoxGeometry(1, 1, 1);
  // const material = new THREE.MeshPhongMaterial({
  //   color: 0xffccaa,
  //   shading: THREE.FlatShading
  // }); // Material con sombreado "FlatShading"
  // const cube = new THREE.Mesh(geometry, material);
  // cube.castShadow = true;
  // cube.receiveShadow = true;
  // scene.add(cube);

  // Crear un plano para recibir sombras pero no ser visible
  const groundGeometry = new THREE.PlaneGeometry(10, 10);
  const groundMaterial = new THREE.ShadowMaterial({
    opacity: 0.1
  });
  const ground = new THREE.Mesh(groundGeometry, groundMaterial);
  ground.position.y = -3
  ground.rotation.x = -Math.PI / 2;
  ground.receiveShadow = true;
  ground.renderOrder = -1; // Esta línea hace que el plano se renderice antes de otros objetos
  scene.add(ground);

  // Agregar sombras
  renderer.shadowMap.enabled = true;

  // Agregar luces
  const ambientLight = new THREE.AmbientLight(0x404040);
  scene.add(ambientLight);

  const directionalLight = new THREE.DirectionalLight(0xffffff, 1);
  directionalLight.position.set(1, 5, 2);
  directionalLight.castShadow = true;


  // Configurar sombras de luz direccional
  directionalLight.shadow.mapSize.width  = 4096;
  directionalLight.shadow.mapSize.height = 4096;
  directionalLight.shadow.camera.near    = 0.1;
  directionalLight.shadow.camera.far     = 15;
  directionalLight.shadow.bias           = -0.001; // Ajusta el bias
  directionalLight.shadow.radius         = 1; // Ajusta el radio de la muestra

  scene.add(directionalLight);

  // Crear un helper para visualizar la luz direccional
  const directionalLightHelper = new THREE.DirectionalLightHelper(directionalLight, 3); // Segundo parámetro es el tamaño del helper
  scene.add(directionalLightHelper);

  // Posicionar la cámara
  camera.position.z = 5; // nivel de zoom

  // Configura OrbitControls
  const controls = new THREE.OrbitControls(camera, renderer.domElement);

  // Configura la posición de la cámara y el punto focal
  controls.target.set(0, 0, 0);

  // Variable para almacenar el valor de rotación
  let rotationValue = 0;

  // Función para manejar el evento de rueda del ratón
  const handleMouseWheel = (event) => {
    const delta = Math.sign(event.deltaY); // Dirección de la rueda del ratón

    // Ajustar la rotación basada en la dirección de la rueda del ratón
    rotationValue += delta * 0.1; // Incremento más rápido que el scroll

    // Evitar que la rotación se vuelva demasiado grande
    //rotationValue = Math.min(Math.max(rotationValue, -Math.PI), Math.PI);
  };

  // Asignar el manejador de evento al evento de rueda del ratón
  window.addEventListener('wheel', handleMouseWheel);

  // Animación del modelo
  const animate = () => {
    requestAnimationFrame(animate);

    // Actualiza los controles de órbita
    controls.update();

    // Aplicar la rotación al cubo
    //cube.rotation.x = rotationValue;
    //cube.rotation.y = rotationValue;

    renderer.render(scene, camera);
  };

  animate();
</script>

<?php require_once INCLUDES . 'footer.php'; ?>