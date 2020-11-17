<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Profesor extends Model{

    protected $primaryKey = 'id';
    public $nombre;
    public $apellido;
    const CREATED_AT = 'creation_date';
    const UPDATED_AT = 'last_update';
    protected $guarded = [];

    public function __get($name)
    {
        return $this->$name;
    }

    public function __set($name, $value)
    {
        $this->$name = $value;
    }
}

?>