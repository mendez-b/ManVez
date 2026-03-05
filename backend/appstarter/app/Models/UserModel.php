<?php

namespace App\Models;

//AQUI SE CREA EL MODELO DE USUARIO

use CodeIgniter\Model;

class UserModel extends Model
{
    protected $table            = 'users';
    protected $primaryKey       = 'id';
    protected $allowedFields    = ['username','email', 'password']; // Campos que Vue enviará
    protected $useTimestamps    = true;
}