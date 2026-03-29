<?php
//AQUI SE CREA EL CONTROLADOR DE AUTENTICACION PARA EL LOGIN
namespace App\Controllers;

use CodeIgniter\RESTful\ResourceController;

class AuthController extends ResourceController
{
    public function login()
    {
        $origin = $_SERVER['HTTP_ORIGIN'] ?? '';
        $allowed = ['http://localhost:5173', 'https://last-king.vercel.app'];
        if (in_array($origin, $allowed)) {
            header("Access-Control-Allow-Origin: $origin");
        }
        header("Access-Control-Allow-Headers: Content-Type, Authorization, X-Requested-With");
        header("Access-Control-Allow-Methods: GET, POST, OPTIONS, PUT, DELETE");

        if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') { exit; }

        $email = null; $password = null;
        try {
            $json = $this->request->getJSON(true);
            $email = $json['email'] ?? null;
            $password = $json['password'] ?? null;
        } catch (\Throwable $e) {}

        if (!$email) $email = $this->request->getPost('email');
        if (!$password) $password = $this->request->getPost('password');

        if ((!$email || !$password) && ($raw = $this->request->getBody())) {
            $decoded = json_decode($raw, true);
            if (json_last_error() === JSON_ERROR_NONE) {
                $email = $email ?? ($decoded['email'] ?? null);
                $password = $password ?? ($decoded['password'] ?? null);
            }
        }

        $userModel = new \App\Models\UserModel();
        $user = $userModel->where('email', $email)->first();

        if ($user && password_verify($password, $user['password'])) {
            return $this->respond([
                'status'  => true,
                'token'   => 'mi-token-secreto-123',
                'message' => '¡Bienvenido!',
                'user'    => [
                    'id'         => $user['id'],
                    'username'   => $user['username'],
                    'email'      => $user['email'],
                    'avatar'     => $user['avatar'] ?? null,
                    'banner'     => $user['banner'] ?? null,
                    'bio'        => $user['bio'] ?? null,
                    'created_at' => $user['created_at'] ?? null,
                ]
            ]);
        }

        return $this->respond(['status' => false, 'message' => 'Usuario o clave incorrectos'], 401);
    }

    public function register()
    {
        $origin = $_SERVER['HTTP_ORIGIN'] ?? '';
        $allowed = ['http://localhost:5173', 'https://last-king.vercel.app'];
        if (in_array($origin, $allowed)) {
            header("Access-Control-Allow-Origin: $origin");
        }
        header("Access-Control-Allow-Headers: Content-Type, Authorization, X-Requested-With");
        header("Access-Control-Allow-Methods: GET, POST, OPTIONS, PUT, DELETE");

        if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') { exit; }

        try {
            $db = \Config\Database::connect();
            $db->table('users')->select('id')->limit(1)->get();
        } catch (\Exception $e) {
            return $this->response->setStatusCode(500)->setJSON(['error' => $e->getMessage()]);
        }

        $email = null; $password = null; $username = null;
        try {
            $json = $this->request->getJSON(true);
            $email    = $json['email'] ?? null;
            $password = $json['password'] ?? null;
            $username = $json['username'] ?? null;
        } catch (\Throwable $e) {}

        $email    = $email    ?? $this->request->getPost('email');
        $password = $password ?? $this->request->getPost('password');
        $username = $username ?? $this->request->getPost('username');

        if ((!$email || !$password) && ($raw = $this->request->getBody())) {
            $decoded = json_decode($raw, true);
            if (json_last_error() === JSON_ERROR_NONE) {
                $email    = $email    ?? ($decoded['email']    ?? null);
                $password = $password ?? ($decoded['password'] ?? null);
                $username = $username ?? ($decoded['username'] ?? null);
            }
        }

        $db = \Config\Database::connect();
        $existingUser = $db->table('users')->where('email', $email)->get()->getRow();
        if ($existingUser) {
            return $this->response->setStatusCode(400)->setJSON(['error' => 'El correo ya está registrado']);
        }

        try {
            $result = $db->table('users')->insert([
                'username'   => $username,
                'email'      => $email,
                'password'   => password_hash($password, PASSWORD_DEFAULT),
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s')
            ]);

            if (!$result) {
                return $this->response->setStatusCode(500)->setJSON(['error' => 'Error al insertar en la BD', 'debug' => $db->error()]);
            }

            return $this->response->setJSON(['message' => 'Usuario creado con éxito']);
        } catch (\Exception $e) {
            return $this->response->setStatusCode(500)->setJSON(['error' => $e->getMessage()]);
        }
    }

    public function updateProfile()
{
    // Configuración de CORS (puedes mover esto a Filters.php para no repetirlo)
    $origin = $_SERVER['HTTP_ORIGIN'] ?? '';
    $allowed = ['http://localhost:5173', 'https://last-king.vercel.app'];
    if (in_array($origin, $allowed)) {
        header("Access-Control-Allow-Origin: $origin");
    }
    header("Access-Control-Allow-Headers: Content-Type, Authorization, X-Requested-With");
    header("Access-Control-Allow-Methods: GET, POST, PUT, OPTIONS, DELETE");

    if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') { exit; }

    // Obtenemos los datos del JSON enviado por Vue
    $json = $this->request->getJSON(true);
    $userId = $json['user_id'] ?? null;

    if (!$userId) {
        return $this->response->setStatusCode(400)->setJSON(['error' => 'User ID requerido']);
    }

    $db = \Config\Database::connect();
    
    // Verificamos si el usuario existe
    $user = $db->table('users')->where('id', $userId)->get()->getRow();
    if (!$user) {
        return $this->response->setStatusCode(404)->setJSON(['error' => 'Usuario no encontrado']);
    }

    // Preparamos los datos a actualizar
    $updateData = ['updated_at' => date('Y-m-d H:i:s')];
    
    // Solo agregamos al update si el campo viene en el JSON
    if (isset($json['username'])) $updateData['username'] = $json['username'];
    if (isset($json['bio']))      $updateData['bio']      = $json['bio'];
    if (isset($json['avatar']))   $updateData['avatar']   = $json['avatar'];
    if (isset($json['banner']))   $updateData['banner']   = $json['banner'];

    try {
        // Ejecutamos la actualización
        $db->table('users')->where('id', $userId)->update($updateData);

        // OBTENEMOS EL USUARIO ACTUALIZADO (Corregido: getRowArray)
        $updated = $db->table('users')->where('id', $userId)->get()->getRowArray();

        return $this->respond([
            'status'  => true,
            'message' => 'Perfil actualizado con éxito',
            'user'    => [
                'id'         => $updated['id'],
                'username'   => $updated['username'],
                'email'      => $updated['email'],
                'avatar'     => $updated['avatar']   ?? null,
                'banner'     => $updated['banner']   ?? null,
                'bio'        => $updated['bio']      ?? null,
                'created_at' => $updated['created_at'] ?? null,
            ]
        ]);
    } catch (\Exception $e) {
        // Si algo falla en la DB (ej. nombre de usuario duplicado), capturamos el error
        return $this->response->setStatusCode(500)->setJSON([
            'error' => 'Error al actualizar la base de datos',
            'debug' => $e->getMessage()
        ]);
    }
}

    public function test()
    {
        return $this->response->setJSON(['status' => 'ok']);
    }

    public function forgotPassword()
    {
        $origin = $_SERVER['HTTP_ORIGIN'] ?? '';
        $allowed = ['http://localhost:5173', 'https://last-king.vercel.app'];
        if (in_array($origin, $allowed)) {
            header("Access-Control-Allow-Origin: $origin");
        }
        header("Access-Control-Allow-Headers: Content-Type, Authorization, X-Requested-With");
        header("Access-Control-Allow-Methods: GET, POST, OPTIONS, PUT, DELETE");
        if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') { exit; }

        try { $raw = $this->request->getBody(); } catch (\Throwable $e) { $raw = ''; }

        try {
            $json  = $this->request->getJSON();
            $email = $json->email ?? null;
        } catch (\Throwable $e) {
            $email = $this->request->getPost('email') ?? null;
        }

        if (!$email && !empty($raw)) {
            $decoded = json_decode($raw, true);
            if (json_last_error() === JSON_ERROR_NONE) {
                $email = $decoded['email'] ?? $email;
            }
        }

        if (!$email) {
            return $this->response->setStatusCode(400)->setJSON(['error' => 'Email inválido']);
        }

        $email = strtolower(trim($email));

        try {
            $userModel = new \App\Models\UserModel();
            $user = $userModel->where('email', $email)->first();
        } catch (\Throwable $e) {
            return $this->response->setStatusCode(500)->setJSON(['error' => 'Server DB error']);
        }

        $token = bin2hex(random_bytes(16));
        try {
            $db = \Config\Database::connect();
            $db->table('password_resets')->where('email', $email)->delete();
            $db->table('password_resets')->insert([
                'email'      => $email,
                'token'      => $token,
                'created_at' => date('Y-m-d H:i:s')
            ]);
        } catch (\Throwable $e) {
            return $this->response->setStatusCode(500)->setJSON(['error' => 'Server DB error']);
        }

        $apiKey = getenv('RESEND_API_KEY') ?: env('resend.apiKey');
        if (empty($apiKey)) {
            return $this->response->setStatusCode(500)->setJSON(['error' => 'Email provider not configured']);
        }

        $resetUrl = 'https://last-king.vercel.app/reset-password?token=' . $token;
        $html = "<p>Hola,</p><p>Recibimos una solicitud para reestablecer tu contraseña. Pulsa el enlace:</p><p><a href=\"$resetUrl\">$resetUrl</a></p><p>Si no solicitaste este correo, ignóralo.</p>";

        $payload = json_encode([
            'from'    => 'no-reply@lastking.com',
            'to'      => [$email],
            'subject' => 'Recuperar contraseña - LastKing',
            'html'    => $html
        ]);

        try {
            $ch = curl_init('https://api.resend.com/emails');
            curl_setopt($ch, CURLOPT_POST, true);
            curl_setopt($ch, CURLOPT_POSTFIELDS, $payload);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
            curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
            curl_setopt($ch, CURLOPT_HTTPHEADER, [
                'Content-Type: application/json',
                'Authorization: Bearer ' . $apiKey,
            ]);
            $resp     = curl_exec($ch);
            $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
            curl_close($ch);

            if ($resp === false || $httpCode >= 400) {
                return $this->response->setStatusCode(500)->setJSON(['error' => 'Error enviando correo']);
            }

            return $this->response->setJSON(['message' => 'Correo de recuperación enviado']);
        } catch (\Throwable $e) {
            return $this->response->setStatusCode(500)->setJSON(['error' => 'Error enviando correo']);
        }
    }

    public function resetPassword()
    {
        $origin = $_SERVER['HTTP_ORIGIN'] ?? '';
        $allowed = ['http://localhost:5173', 'https://last-king.vercel.app'];
        if (in_array($origin, $allowed)) {
            header("Access-Control-Allow-Origin: $origin");
        }
        header("Access-Control-Allow-Headers: Content-Type, Authorization, X-Requested-With");
        header("Access-Control-Allow-Methods: GET, POST, OPTIONS, PUT, DELETE");

        if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') { exit; }

        $json = $this->request->getJSON();
        $db   = \Config\Database::connect();

        $resetData = $db->table('password_resets')->where('token', $json->token)->get()->getRow();
        if (!$resetData) {
            return $this->response->setStatusCode(400)->setJSON(['error' => 'El token es inválido o ha expirado.']);
        }

        $userModel = new \App\Models\UserModel();
        $user = $userModel->where('email', $resetData->email)->first();
        $userModel->update($user['id'], ['password' => password_hash($json->password, PASSWORD_DEFAULT)]);
        $db->table('password_resets')->where('email', $resetData->email)->delete();

        return $this->response->setJSON(['message' => 'Contraseña actualizada correctamente.']);
    }
}