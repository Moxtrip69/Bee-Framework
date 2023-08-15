<?php

class BeeFormBuilder
{
  /**
   * El código html final del formulario
   *
   * @var string
   */
  private $formHtml     = '';

  /**
   * El nombre del formulario, puede ser usado para seleccionar por javascript
   *
   * @var string
   */
  private $formName     = '';

  /**
   * El identificador único del formulario
   *
   * @var string
   */
  private $id           = '';

  /**
   * La ruta de acción del formulario, dónde enviará la información
   *
   * @var string
   */
  private $action       = '';

  /**
   * Todas las clases del formulario
   *
   * @var array
   */
  private $classes      = [];
  
  /**
   * El método a utilizar, puede ser sólo GET o POST
   *
   * @var string
   */
  private $method       = '';

  /**
   * Método de encriptación o content-type para enviar la información
   *
   * @var string
   */
  private $encType      = '';

  /**
   * Todos los campos personalizados insertados
   *
   * @var array
   */
  private $customFields = [];

  /**
   * Todos los campos agregados al formulario
   *
   * @var array
   */
  private $fields       = [];

  /***
   * Todos los botones registrados para el formulario
   */
  private $buttons      = [];

  /**
   * Todos los campos de tipo file registrados para el formulario
   *
   * @var array
   */
  private $files        = [];

  /**
   * Todos los campos de tipo range o slider registrados para el formulario
   *
   * @var array
   */
  private $sliders      = [];

  // TODO: Agregar los campos de seguridad de forma dinámica: CSRF etc
  // TODO: Agregar textos de ayuda abajo de los campos por si es requerido
  // TODO: Agregar grupos de inputs en los formularios (para que no se vea tan líneal el formulario)

  /**
   * Inicialización del formulario
   *
   * @param string $name
   * @param string $id
   * @param array $classes
   * @param string $action
   * @param boolean $post
   * @param boolean $sendFiles
   */
  function __construct($name, $id = null, $classes = [], $action = null, $post = true, $sendFiles = false)
  {
    $this->formName = $name;
    $this->id       = $id;
    $this->classes  = $classes;
    $this->action   = $action;
    $this->method   = $post === true ? 'POST' : 'GET';
    $this->encType  = $sendFiles === true ? 'multipart/form-data' : '';
  }

  /**
   * Método general para agregar campos repetitivos en estructura
   *
   * @param string $name
   * @param string $type
   * @param string $label
   * @param array $classes
   * @param string $id
   * @param boolean $required
   * @param array $options
   * @param string $defaultValue
   * @return void
   */
  private function addField($name, $type, $label, $classes = [], $id = null, $required = false, $options = [], $defaultValue = null)
  {
    $field = 
    [
      'name'         => $name,
      'type'         => $type,
      'label'        => $label,
      'classes'      => $classes,
      'id'           => $id,
      'options'      => $options,
      'defaultValue' => $defaultValue,
      'required'     => $required === true
    ];

    $this->fields[] = $field;
  }

  /**
   * Agrega un campo personalizado o varios, básicamente inserta código html en el formulario
   *
   * @param string $fields
   * @return void
   */
  function addCustomFields($fields)
  {
    $customField = 
    [
      'name'         => null,
      'type'         => 'custom',
      'label'        => null,
      'classes'      => [],
      'id'           => null,
      'options'      => [],
      'defaultValue' => null,
      'required'     => null,
      'content'      => $fields
    ];

    $this->fields[]       = $customField;

    $this->customFields[] = $fields;
  }

  /**
   * Agrega un campo escondido o hidden
   *
   * @param string $name
   * @param string $label
   * @param array $classes
   * @param string $id
   * @param boolean $required
   * @param string $defaultValue
   * @return void
   */
  public function addHiddenField($name, $label, $classes = [], $id = null, $required = false, $defaultValue = null)
  {
    $this->addField($name, 'hidden', $label, $classes, $id, $required, [], $defaultValue);
  }

  /**
   * Agrega un campo de texto al formulario
   *
   * @param string $name
   * @param string $label
   * @param array $classes
   * @param string $id
   * @param boolean $required
   * @param string $defaultValue
   * @return void
   */
  public function addTextField($name, $label, $classes = [], $id = null, $required = false, $defaultValue = null)
  {
    $this->addField($name, 'text', $label, $classes, $id, $required, [], $defaultValue);
  }

  /**
   * Agrega un campo de tipo password o contraseña
   *
   * @param string $name
   * @param string $label
   * @param array $classes
   * @param string $id
   * @param boolean $required
   * @param string $defaultValue
   * @return void
   */
  public function addPasswordField($name, $label, $classes = [], $id = null, $required = false, $defaultValue = null)
  {
    $this->addField($name, 'password', $label, $classes, $id, $required, [], $defaultValue);
  }

  /**
   * Agrega un campo de tipo email
   *
   * @param string $name
   * @param string $label
   * @param array $classes
   * @param string $id
   * @param boolean $required
   * @param string $defaultValue
   * @return void
   */
  public function addEmailField($name, $label, $classes = [], $id = null, $required = false, $defaultValue = null)
  {
    $this->addField($name, 'email', $label, $classes, $id, $required, [], $defaultValue);
  }

  /**
   * Agrega un campo de tipo select y sus opciones
   *
   * @param string $name
   * @param string $label
   * @param array $options
   * @param array $classes
   * @param string $id
   * @param boolean $required
   * @param string $defaultValue
   * @return void
   */
  public function addSelectField($name, $label, $options = [], $classes = [], $id = null, $required = false, $defaultValue = null)
  {
    $this->addField($name, 'select', $label, $classes, $id, $required, $options, $defaultValue);
  }

  /**
   * Agrega un campo de tipo checkbox
   *
   * @param string $name
   * @param string $label
   * @param string $value
   * @param array $classes
   * @param string $id
   * @param boolean $checked
   * @param boolean $required
   * @return void
   */
  public function addCheckboxField($name, $label, $value, $classes = [], $id = null, $checked = false, $required = false)
  {
    $field = 
    [
      'name'     => $name,
      'type'     => 'checkbox',
      'label'    => $label,
      'value'    => $value,
      'classes'  => $classes,
      'id'       => $id,
      'checked'  => $checked,
      'required' => $required
    ];

    $this->fields[] = $field;
  }

  /**
   * Agrega un campo tipo radio
   *
   * @param string $name
   * @param string $label
   * @param string $value
   * @param array $classes
   * @param string $id
   * @param boolean $checked
   * @param boolean $required
   * @return void
   */
  public function addRadioField($name, $label, $value, $classes = [], $id = null, $checked = false, $required = false)
  {
    $field = 
    [
      'name'     => $name,
      'type'     => 'radio',
      'label'    => $label,
      'value'    => $value,
      'classes'  => $classes,
      'id'       => $id,
      'checked'  => $checked,
      'required' => $required
    ];

    $this->fields[] = $field;
  }

  /**
   * Agrega un campo textarea al formulario
   *
   * @param string $name
   * @param string $label
   * @param int $rows
   * @param int $cols
   * @param array $classes
   * @param string $id
   * @param boolean $required
   * @param string $defaultValue
   * @return void
   */
  public function addTextareaField($name, $label, $rows, $cols, $classes = [], $id = null, $required = false, $defaultValue = null)
  {
    $field = 
    [
      'name'         => $name,
      'type'         => 'textarea',
      'label'        => $label,
      'rows'         => $rows,
      'cols'         => $cols,
      'classes'      => $classes,
      'id'           => $id,
      'defaultValue' => $defaultValue,
      'required'     => $required === true
    ];

    $this->fields[] = $field;
  }

  /**
   * Agrega un campo de archivo o file
   *
   * @param string $name
   * @param string $label
   * @param array $classes
   * @param string $id
   * @param boolean $required
   * @return void
   */
  public function addFileField($name, $label, $classes = [], $id = null, $required = false)
  {
    $field = 
    [
      'name'     => $name,
      'type'     => 'file',
      'label'    => $label,
      'classes'  => $classes,
      'id'       => $id,
      'required' => $required
    ];

    $this->fields[] = $field;
    $this->files[]  = $name;
  }

  /**
   * Agrega un botón al formulario
   *
   * @param string $name
   * @param string $type
   * @param string $value
   * @param array $classes
   * @param string $id
   * @return void
   */
  public function addButton($name, $type, $value, $classes = [], $id = null)
  {
    $button = 
    [
      'name'    => $name,
      'type'    => $type,
      'value'   => $value,
      'id'      => $id,
      'classes' => $classes
    ];

    $this->buttons[] = $button;
  }

  /**
   * Agrega un campo de tipo slider o range
   *
   * @param strin $name
   * @param string $label
   * @param float $min
   * @param float $max
   * @param float $step
   * @param array $classes
   * @param string $id
   * @param boolean $required
   * @param mixed $defaultValue
   * @return void
   */
  public function addSliderField($name, $label, $min, $max, $step, $classes = [], $id = null, $required = false, $defaultValue = null)
  {
    $field = 
    [
      'name'         => $name,
      'type'         => 'slider',
      'label'        => $label,
      'min'          => $min,
      'max'          => $max,
      'step'         => $step,
      'classes'      => $classes,
      'id'           => $id,
      'defaultValue' => $defaultValue,
      'required'     => $required
    ];

    $this->fields[]  = $field;
    $this->sliders[] = $name;
  }

  /**
   * Agrega un campo de tipo color
   *
   * @param string $name
   * @param string $label
   * @param array $classes
   * @param string $id
   * @param boolean $required
   * @param string $defaultValue
   * @return void
   */
  public function addColorField($name, $label, $classes = [], $id = null, $required = false, $defaultValue = null)
  {
    $field = 
    [
      'name'         => $name,
      'type'         => 'color',
      'label'        => $label,
      'classes'      => $classes,
      'id'           => $id,
      'required'     => $required,
      'defaultValue' => $defaultValue
    ];

    $this->fields[] = $field;
  }

  /**
   * Agrega un campo de tipo número
   *
   * @param string $name
   * @param string $label
   * @param string $min
   * @param float $max
   * @param float $step
   * @param float $defaultValue
   * @param array $classes
   * @param string $id
   * @param boolean $required
   * @return void
   */
  public function addNumberField($name, $label, $min = null, $max = null, $step = null, $defaultValue = null, $classes = [], $id = null, $required = false, )
  {
    $field = 
    [
      'name'         => $name,
      'type'         => 'number',
      'label'        => $label,
      'min'          => $min,
      'max'          => $max,
      'step'         => $step,
      'defaultValue' => $defaultValue,
      'classes'      => $classes,
      'id'           => $id,
      'required'     => $required
    ];

    $this->fields[] = $field;
  }

  /**
   * Agregar un campo de tipo fecha o date
   *
   * @param string $name
   * @param string $label
   * @param string $defaultValue
   * @param array $classes
   * @param string $id
   * @param boolean $required
   * @return void
   */
  public function addDateField($name, $label, $defaultValue = null, $classes = [], $id = null, $required = false)
  {
    $field = 
    [
      'name'         => $name,
      'type'         => 'date',
      'label'        => $label,
      'defaultValue' => $defaultValue,
      'classes'      => $classes,
      'id'           => $id,
      'required'     => $required
    ];

    $this->fields[] = $field;
  }

  /**
   * Procesa todos los campos y elementos agregados del formulario
   *
   * @return void
   */
  private function buildForm()
  {
    $this->formHtml = sprintf('<form id="%s" data-form-name="%s" class="%s" action="%s" method="%s" %s>', 
      $this->id, 
      $this->formName, 
      implode(' ', $this->classes),
      $this->action, 
      $this->method, 
      !empty($this->encType) ? sprintf('enctype="%s"', $this->encType) : ''
    );

    // Procesamiento de todos los inputs y elementos del formulario
    foreach ($this->fields as $field) {
      $fieldName    = $field['name'];
      $fieldType    = $field['type'];
      $fieldLabel   = $field['label'];
      $fieldClasses = empty($field['classes']) ? 'form-label' : implode(' ', $field['classes']);
      $fieldId      = $field['id'];
      $fieldOptions = isset($field['options']) ? $field['options'] : [];
      $fieldValue   = isset($field['value']) ? $field['value'] : '';
      $defaultValue = isset($field['defaultValue']) ? $field['defaultValue'] : '';
      $required     = $field['required'];
      
      $specialTypes = ['hidden','checkbox','radio','custom'];

      // Label y grupo del input, no mostrar si es hidden para evitar espaciados no deseados
      if (!in_array($fieldType, $specialTypes)) {
        $this->formHtml .= '<div class="mb-3">';
        $this->formHtml .= sprintf('<label for="%s">%s%s</label>', 
          $fieldId, 
          $fieldLabel,
          $required === true ? ' <span class="text-danger">*</span>' : ''
        );
      }

      switch ($fieldType) {
        case 'select':
          $this->formHtml .= sprintf('<select name="%s" id="%s" class="%s" %s>',
            $fieldName,
            $fieldId,
            $fieldClasses,
            $required === true ? 'required' : ''
          );
    
          foreach ($fieldOptions as $optionValue => $optionLabel) {
            $selected = ($optionValue === $defaultValue) ? 'selected' : '';
            
            $this->formHtml .= sprintf('<option value="%s" %s>%s</option>',
              $optionValue,
              $selected,
              $optionLabel
            );
          }
    
          $this->formHtml .= '</select>';
          break;
        
        case 'textarea':
          $this->formHtml .= sprintf(
            '<textarea name="%s" id="%s" rows="%s" cols="%s" class="%s" %s>%s</textarea>',
            $fieldName,
            $fieldId,
            $field['rows'],
            $field['cols'],
            $fieldClasses,
            $required === true ? 'required' : '',
            $defaultValue
          );
          break;

        // TODO: Mejorar la UI con base a los componentes de Bootstrap 5
        case 'checkbox':
        case 'radio':
          $checked = $field['checked'] ? 'checked' : '';
          $this->formHtml .= '<div class="form-check mb-3">';
          $this->formHtml .= sprintf(
            '<input type="%s" name="%s" id="%s" value="%s" %s class="%s">',
            $fieldType,
            $fieldName,
            $fieldId,
            $fieldValue,
            $checked,
            $fieldClasses,
            $required === true ? 'required' : ''
          );
          $this->formHtml .= sprintf(
            '<label class="form-check-label" for="%s">%s</label>',
            $fieldId,
            $fieldLabel
          );
          $this->formHtml .= '</div>';
          break;

        case 'hidden':
        case 'email':
        case 'password':
        case 'url':
        case 'phone':
        case 'text':
          $this->formHtml .= sprintf('<input type="%s" name="%s" id="%s" class="%s" value="%s" %s>',
            $fieldType,
            $fieldName,
            $fieldId,
            $fieldClasses,
            $defaultValue,
            $required === true ? 'required' : ''
          );
          break;

        // TODO: Aceptar sólo algunos formatos, definirlos al crear el input
        case 'file':
          $this->formHtml .= sprintf('<input type="file" name="%s" id="%s" class="%s" %s>',
            $fieldName,
            $fieldId,
            $fieldClasses,
            $required === true ? 'required' : ''
          );
          break;
        
        case 'slider':
          $sliderValue     = $field['defaultValue'] ?? $field['min'];
          $this->formHtml .= sprintf(
            '<input type="range" name="%s" id="%s" min="%s" max="%s" step="%s" value="%s" class="%s" %s>',
            $fieldName,
            $fieldId,
            $field['min'],
            $field['max'],
            $field['step'],
            $sliderValue,
            $fieldClasses,
            $required
          );
          break;

        case 'custom':
          $this->formHtml .= $field['content'];
          break;

        case 'color':
          $this->formHtml .= sprintf(
            '<input type="color" name="%s" id="%s" value="%s" class="%s" %s>',
            $fieldName,
            $fieldId,
            $defaultValue,
            $fieldClasses,
            $required === true ? 'required' : ''
          );
          break;

        case 'number':
          $this->formHtml .= sprintf(
            '<input type="number" name="%s" id="%s" value="%s" min="%s" max="%s" step="%s" class="%s">',
            $fieldName,
            $fieldId,
            $defaultValue,
            $field['min'],
            $field['max'],
            $field['step'],
            $fieldClasses,
            $required === true ? 'required' : ''
          );
          break;

        case 'date':
          $this->formHtml .= sprintf(
            '<input type="date" name="%s" id="%s" value="%s" class="%s" %s>',
            $fieldName,
            $fieldId,
            $defaultValue,
            $fieldClasses,
            $required === true ? 'required' : ''
          );
          break;
      }

      // Cerrar el div abierto para campos que no son ocultos
      if (!in_array($fieldType, $specialTypes)) {
        $this->formHtml .= '</div>';
      }
    }

    // Procesamiento de todos los botones anexados
    foreach ($this->buttons as $button) {
      $buttonName    = $button['name'];
      $buttonType    = $button['type'];
      $buttonValue   = $button['value'];
      $buttonClasses = implode(' ', $button['classes']);
      $buttonId      = $button['id'];

      $this->formHtml .= sprintf(
        '<button type="%s" name="%s" id="%s" class="%s">%s</button>',
        $buttonType,
        $buttonName,
        $buttonId,
        $buttonClasses,
        $buttonValue
      );
    }

    $this->formHtml .= '</form>';

    return $this->formHtml;
  }

  /**
   * Regresa todo el código html del formulario listo para ser utilizado
   *
   * @return string
   */
  function getFormHtml()
  {
    $this->buildForm();

    return $this->formHtml;
  }

  /**
   * Renderiza en pantalla el formulario
   *
   * @return void
   */
  function renderForm()
  {
    echo $this->getFormHtml();  
  }

  /**
   * Regresa el nombre de nuestro formulario formateado para usar
   * en Javascript sin problema alguno
   *
   * @return string
   */
  private function formatFormName()
  {
    // Remover espacios, guiones "-" y otros caracteres no permitidos en nombres de funciones
    $formattedId = preg_replace('/[^a-zA-Z0-9_]/', '_', $this->formName);

    // Asegurarse de que el nombre de la función comience con una letra o guión bajo "_"
    $formattedId = preg_replace('/^[^a-zA-Z_]/', '_', $formattedId);

    return $formattedId;
  }

  /**
   * Genera de forma dinámica un bloque de código javascript con una función encargada de envia la información
   * a una ruta específica
   *
   * @param string $url
   * @param string $accessToken
   * @param boolean $addEventListener
   * @return string
   */
  public function generateFetchScript($url, $accessToken = null, $addEventListener = true)
  {
    $functionName = sprintf('submitForm_%s', $this->formatFormName());
    $script  = "<script>";
    $script .= $addEventListener ? "document.getElementById('%s').addEventListener('submit', %s);" : "";
    $script .= 
    "async function %s(e) {
      e.preventDefault();
      const form     = document.getElementById('%s');
      const formData = new FormData(form);
      const res      = await fetch('%s', {
        %s
        method     : 'POST',
        body       : formData
      })
      .then(res => res.json())
      .catch(err => console.log(err));
       
      if (res.status === 200) {
        toastr.success(res.msg, '¡Excelente!');
        form.reset();
      } else {
        toastr.error(res.msg, '¡Hubo un error!');
      }
    }";
    $script .= "</script>";

    return sprintf(
      $script,
      $this->id,
      $functionName, 
      $functionName, 
      $this->id, 
      $url, 
      $accessToken !== null ? sprintf(
          'headers: { "Authorization": "Bearer %s"},',
          $accessToken
        ) : ''
    );
  }
}
