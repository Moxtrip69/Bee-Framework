<?php 

class optionModel extends Model
{

  public $id;
  public $option;
  public $val;
  public $created_at;
  public $updated_at;

  /**
   * Método para agregar un nuevo usuario
   *
   * @return integer
   */
  public function add_option()
  {
    $sql = 'INSERT INTO options (option, val, created_at) VALUES (:option, :val, :created_at)';
    $data = 
    [
      'option'       => $this->option,
      'val'          => $this->val,
      'created_at'   => now()
    ];

    try {
      return ($this->id = parent::query($sql, $data)) ? $this->id : false;
    } catch (Exception $e) {
      throw $e;
    }
  }

  /**
   * Método para cargar todas las opciones de la base de datos
   *
   * @return void
   */
  public function all()
  {
    $sql = 'SELECT * FROM options ORDER BY id DESC';
    try {
      return ($rows = parent::query($sql)) ? $rows : false;
    } catch (Exception $e) {
      throw $e;
    }
  }

  /**
   * Método para cargar un registro de la base de datos usando su id
   *
   * @return void
   */
  public function one()
  {
    $sql = 'SELECT * FROM options WHERE option=:option LIMIT 1';
    try {
      return ($rows = parent::query($sql, ['option' => $this->option])) ? $rows[0] : false;
    } catch (Exception $e) {
      throw $e;
    }
  }

  /**
   * Método para actualizar un registor en la db
   *
   * @return bool
   */
  public function update_option()
  {
    $sql = 'UPDATE options SET val=:val WHERE option=:option';
    $data = 
    [
      'option' => $this->option,
      'val'    => $this->val,
    ];

    try {
      return (parent::query($sql, $data)) ? true : false;
    } catch (Exception $e) {
      throw $e;
    }
  }

  /**
   * Método para borrar un movimiento de la base de datos usando el id
   *
   * @return void
   */
  public function delete()
  {
    $sql = 'DELETE FROM options WHERE option = :option LIMIT 1';
    try {
      return (parent::query($sql, ['option' => $this->option])) ? true : false;
    } catch (Exception $e) {
      throw $e;
    }
  }

  public static function save($option, $val)
  {
    // Verificar si existe la opción
    $self         = new self();
    $self->option = $option;
    $self->val    = $val;

    // Si no existe, guardar
    if(!$option = $self->one()) {
      return ($self->id = $self->add_option()) ? $self->id : false;
    }

    // Si existe, actualizar
    return $self->update_option();
  }

  /**
   * Método para buscar el valor de una opción determinada de forma estática
   *
   * @param string $option
   * @return void
   */
  public static function search($option)
  {
    // color
    // #ebebeb
    // optionModel::search('color') -> #ebebeb;
    // optionModel::search('sidebar_alignment') -> right;
    $self = new self();
    $self->option = $option;
    return ($res = $self->one()) ? $res['val'] : false;
  }
}