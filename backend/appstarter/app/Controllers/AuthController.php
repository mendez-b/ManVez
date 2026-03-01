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

    //esta es la logica para recuperar la contraseña del usuario
    public function forgotPassword()
    {
        $json = $this->request->getJSON();
        $email = $json->email;

        $userModel = new \App\Models\UserModel();
        $user = $userModel->where('email', $email)->first();

        if (!$user) {
            return $this->response->setStatusCode(404)->setJSON(['error' => 'Correo no encontrado']);
        }

        // 1. Generar un token único
        $token = bin2hex(random_bytes(16));
        $db = \Config\Database::connect();
        $db->table('password_resets')->where('email', $email)->delete(); // Eliminar tokens anteriores
    
        // 2. Aquí deberías guardar este token en una tabla 'password_resets' con su fecha
        $db->table('password_resets')->insert([
            'email' => $email,
            'token' => $token,
            'created_at' => date('Y-m-d H:i:s')
        ]);

        // 3. Enviar el correo con Resend
        $resend = Resend::client(env('resend.apiKey'));

        try {
            $resend->emails->send([
                'from' => 'MangaReads <onboarding@resend.dev>', // Usa este email si no tienes dominio propio
                'to' => [$email],
                'subject' => 'Recuperar Contraseña - LastKingscans',
                'html' => "<strong>Hola!</strong><br>Haz clic en el siguiente enlace para restablecer tu contraseña: <br>
                        <a href='http://localhost:5173/reset-password?token=$token'>Restablecer Contraseña</a>",
            ]);

            return $this->response->setJSON(['message' => 'Correo de recuperación enviado con éxito']);
        } catch (\Exception $e) {
            return $this->response->setStatusCode(500)->setJSON(['error' => $e->getMessage()]);
        }
    }

    //este metodo actualizara la contraseña y si todo es valido actualizara al usuario en la base de datos
    public function resetPassword()
    {
        $json = $this->request->getJSON();
        $db = \Config\Database::connect();

        // 1. Buscar el token en la base de datos
        $resetData = $db->table('password_resets')->where('token', $json->token)->get()->getRow();

        if (!$resetData) {
            return $this->response->setStatusCode(400)->setJSON(['error' => 'El token es inválido o ha expirado.']);
        }

        // 2. Actualizar la contraseña del usuario
        $userModel = new \App\Models\UserModel();
        $user = $userModel->where('email', $resetData->email)->first();

        $userModel->update($user['id'], [
            'password' => password_hash($json->password, PASSWORD_DEFAULT)
        ]);

        // 3. Borrar el token para que no se use de nuevo
        $db->table('password_resets')->where('email', $resetData->email)->delete();

        return $this->response->setJSON(['message' => 'Contraseña actualizada correctamente.']);
    }
}