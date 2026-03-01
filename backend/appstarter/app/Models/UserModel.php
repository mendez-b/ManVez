//AQUI SE CREA EL MODELO DE USUARIO

<?php

namespace App\Models;

use CodeIgniter\Model;

class UserModel extends Model
{
    protected $table            = 'users';
    protected $primaryKey       = 'id';
    protected $allowedFields    = ['email', 'password']; // Campos que Vue enviará
    protected $useTimestamps    = true;
}