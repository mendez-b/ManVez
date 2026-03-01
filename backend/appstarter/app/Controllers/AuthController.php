<?php
//AQUI SE CREA EL CONTROLADOR DE AUTENTICACION PARA EL LOGIN
namespace App\Controllers;

use CodeIgniter\RESTful\ResourceController;

class AuthController extends ResourceController {
    public function login() {
        $email = $this->request->getVar('email');
        $password = $this->request->getVar('password');

        // Aquí se buca al suaurio en la base de datos
        // Por ahora, una validación simple de prueba:
        if($email === 'admin@admin.com' && $password === '1234') {
            return $this->respond([
                'status' => true,
                'token'  => 'mi-token-secreto-123', // En el futuro será un JWT
                'message' => '¡Bienvenido!'
            ]);
        }

        return $this->failUnauthorized('Usuario o clave incorrectos');
    }
}

//esta es la logica para recibir los datos de RegisterView.Vue
public function register()
{
    $json = $this->request->getJSON();
    $userModel = new \App\Models\UserModel();

    // Validar si el usuario ya existe
    if ($userModel->where('email', $json->email)->first()) {
        return $this->response->setStatusCode(400)->setJSON(['error' => 'El correo ya está registrado']);
    }

    // Insertar el nuevo usuario con la clave encriptada
    $userModel->save([
        'email'    => $json->email,
        'password' => password_hash($json->password, PASSWORD_DEFAULT) // Seguridad esencial
    ]);

    return $this->response->setJSON(['message' => 'Usuario creado con éxito']);
}