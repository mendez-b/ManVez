<?php
//AQUI SE CREA EL CONTROLADOR DE AUTENTICACION PARA EL LOGIN
namespace App\Controllers;

use CodeIgniter\RESTful\ResourceController;

class AuthController extends ResourceController {
    public function login() {
    
        header("Access-Control-Allow-Origin: http://localhost:5173");
        header("Access-Control-Allow-Headers: Content-Type, Authorization, X-Requested-With");
        header("Access-Control-Allow-Methods: GET, POST, OPTIONS, PUT, DELETE");
    
        if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
            exit;
        }

        // Buscar credenciales enviadas (soportar JSON malformado y form data)
        $email = null;
        $password = null;
        try {
            $json = $this->request->getJSON(true);
            $email = $json['email'] ?? null;
            $password = $json['password'] ?? null;
        } catch (\Throwable $e) {
            // Ignorar, intentaremos otros métodos
        }

        if (!$email) {
            $email = $this->request->getPost('email');
        }
        if (!$password) {
            $password = $this->request->getPost('password');
        }

        // Si sigue vacío, intentar decodificar el body crudo y extraer con regex
        if ((!$email || !$password) && ($raw = $this->request->getBody())) {
            $decoded = json_decode($raw, true);
            if (json_last_error() === JSON_ERROR_NONE) {
                $email = $email ?? ($decoded['email'] ?? null);
                $password = $password ?? ($decoded['password'] ?? null);
            } else {
                if (!$email && preg_match('/email\s*[:=]\s*"?([^"\}\s]+)"?/i', $raw, $m)) {
                    $email = $m[1];
                }
                if (!$password && preg_match('/password\s*[:=]\s*"?([^"\}\s]+)"?/i', $raw, $m2)) {
                    $password = $m2[1];
                }
            }
        }

        // Buscar el usuario por email
        $userModel = new \App\Models\UserModel();
        $user = $userModel->where('email', $email)->first();

        // Verificar si el usuario existe y la contraseña es correcta
        if ($user && password_verify($password, $user['password'])) {
            return $this->respond([
                'status' => true,
                'token'  => 'mi-token-secreto-123', // En el futuro será un JWT
                'message' => '¡Bienvenido!',
                'user' => [
                    'id' => $user['id'],
                    'username' => $user['username'],
                    'email' => $user['email']
                ]
            ]);
        }

        return $this->respond([
            'status' => false,
            'message' => 'Usuario o clave incorrectos'
        ], 401);
    }

    //esta es la logica para recibir los datos de RegisterView.Vue
    public function register()
    {
        header("Access-Control-Allow-Origin: http://localhost:5173");
        header("Access-Control-Allow-Headers: Content-Type, Authorization, X-Requested-With");
        header("Access-Control-Allow-Methods: GET, POST, OPTIONS, PUT, DELETE");
    
        if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
            exit;
        }


        // Parsear entrada (JSON o form)
        $email = null;
        $password = null;
        $username = null;
        try {
            $json = $this->request->getJSON(true);
            $email = $json['email'] ?? null;
            $password = $json['password'] ?? null;
            $username = $json['username'] ?? null;
        } catch (\Throwable $e) {
            // fallback
        }

        $email = $email ?? $this->request->getPost('email');
        $password = $password ?? $this->request->getPost('password');
        if ((!$email || !$password) && ($raw = $this->request->getBody())) {
            $decoded = json_decode($raw, true);
            if (json_last_error() === JSON_ERROR_NONE) {
                $email = $email ?? ($decoded['email'] ?? null);
                $password = $password ?? ($decoded['password'] ?? null);
            } else {
                if (!$email && preg_match('/email\s*[:=]\s*"?([^"\}\s]+)"?/i', $raw, $m)) {
                    $email = $m[1];
                }
                if (!$password && preg_match('/password\s*[:=]\s*"?([^"\}\s]+)"?/i', $raw, $m2)) {
                    $password = $m2[1];
                }
            }
        }

        $userModel = new \App\Models\UserModel();

        // Validar si el usuario ya existe
        if ($userModel->where('email', $email)->first()) {
            return $this->response->setStatusCode(400)->setJSON(['error' => 'El correo ya está registrado']);
        }

        // Insertar el nuevo usuario con la clave encriptada
        $userModel->save([
            'username' => $username,
            'email'    => $email,
            'password' => password_hash($password, PASSWORD_DEFAULT)
        ]);

        return $this->response->setJSON(['message' => 'Usuario creado con éxito']);
    }

    //esta es la logica para recuperar la contraseña del usuario
    public function forgotPassword()
    {
        header("Access-Control-Allow-Origin: http://localhost:5173");
        header("Access-Control-Allow-Headers: Content-Type, Authorization, X-Requested-With");
        header("Access-Control-Allow-Methods: GET, POST, OPTIONS, PUT, DELETE");
        if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
            exit;
        }

        // Soportar tanto JSON como form-urlencoded
        // Log raw request for debugging
        try {
            $raw = $this->request->getBody();
        } catch (\Throwable $e) {
            $raw = '';
        }
        // Ensure we capture raw body even if logger level prevents info messages
        @file_put_contents(WRITEPATH . 'logs/forgot_raw.log', date('c') . " - Body: " . $raw . PHP_EOL, FILE_APPEND);
        @file_put_contents(WRITEPATH . 'logs/forgot_raw.log', date('c') . " - Content-Type: " . $this->request->getHeaderLine('Content-Type') . PHP_EOL, FILE_APPEND);
        service('logger')->info('forgotPassword raw body logged');

        try {
            $json = $this->request->getJSON();
            $email = $json->email ?? null;
        } catch (\Throwable $e) {
            service('logger')->warning('forgotPassword getJSON failed: ' . $e->getMessage());
            $email = $this->request->getPost('email') ?? null;
        }

        // If still no email, try to parse raw body (handle non-strict JSON like {email:foo})
        if (!$email && !empty($raw)) {
            $decoded = json_decode($raw, true);
            if (json_last_error() === JSON_ERROR_NONE) {
                $email = $decoded['email'] ?? $email;
            }

            if (!$email) {
                if (preg_match('/email\s*[:=]\s*"?([^"}\s]+)"?/i', $raw, $m)) {
                    $email = $m[1];
                }
            }
        }

        if (!$email) {
            return $this->response->setStatusCode(400)->setJSON(['error' => 'Email inválido']);
        }

        // Normalize email
        $email = trim($email);
        $email = strtolower($email);
        @file_put_contents(WRITEPATH . 'logs/forgot_raw.log', date('c') . " - normalized email: " . $email . PHP_EOL, FILE_APPEND);

        try {
            $userModel = new \App\Models\UserModel();
            $user = $userModel->where('email', $email)->first();
        } catch (\Throwable $e) {
            service('logger')->error('forgotPassword DB error: ' . $e->getMessage());
            return $this->response->setStatusCode(500)->setJSON(['error' => 'Server DB error']);
        }

        if (!$user) {
            // No revelar existencia de la cuenta: registrar el intento y continuar
            service('logger')->warning('forgotPassword: requested email not found: ' . $email);
            // Nota: continuamos y enviaremos igualmente el correo de recuperación para no filtrar cuentas
        }

        // 1. Generar un token único
        $token = bin2hex(random_bytes(16));
        try {
            $db = \Config\Database::connect();
            $db->table('password_resets')->where('email', $email)->delete(); // Eliminar tokens anteriores
        } catch (\Throwable $e) {
            service('logger')->error('forgotPassword DB error on password_resets: ' . $e->getMessage());
            return $this->response->setStatusCode(500)->setJSON(['error' => 'Server DB error']);
        }
    
        // 2. Aquí deberías guardar este token en una tabla 'password_resets' con su fecha
        $db->table('password_resets')->insert([
            'email' => $email,
            'token' => $token,
            'created_at' => date('Y-m-d H:i:s')
        ]);

        // 3. Enviar el correo via Resend API
        $apiKey = getenv('RESEND_API_KEY') ?: env('resend.apiKey');
        if (empty($apiKey)) {
            service('logger')->error('forgotPassword: Resend API key not configured');
            return $this->response->setStatusCode(500)->setJSON(['error' => 'Email provider not configured']);
        }

        $resetUrl = 'https://man-vez.vercel.app/reset-password?token=' . $token;
        $html = "<p>Hola,</p><p>Recibimos una solicitud para reestablecer tu contraseña. Pulsa el enlace para continuar:</p><p><a href=\"$resetUrl\">$resetUrl</a></p><p>Si no solicitaste este correo, ignóralo.</p>";

        $payload = json_encode([
            'from' => 'no-reply@man-vez.vercel.app',
            'to' => [$email],
            'subject' => 'Recuperar contraseña - ManVez',
            'html' => $html
        ]);

        try {
            $ch = curl_init('https://api.resend.com/emails');
            curl_setopt($ch, CURLOPT_POST, true);
            curl_setopt($ch, CURLOPT_POSTFIELDS, $payload);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            // In some local environments cURL may fail SSL verification due to missing CA bundle.
            // For development allow disabling verification (not recommended for production).
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
            curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
            curl_setopt($ch, CURLOPT_HTTPHEADER, [
                'Content-Type: application/json',
                'Authorization: Bearer ' . $apiKey,
            ]);

            $resp = curl_exec($ch);
            $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
            $curlErr = curl_error($ch);
            curl_close($ch);

            if ($resp === false || $httpCode >= 400) {
                service('logger')->error('forgotPassword: Resend send failed: ' . $curlErr . ' resp=' . $resp);
                return $this->response->setStatusCode(500)->setJSON(['error' => 'Error enviando correo']);
            }

            return $this->response->setJSON(['message' => 'Correo de recuperación enviado']);
        } catch (\Throwable $e) {
            service('logger')->error('forgotPassword exception sending email: ' . $e->getMessage());
            return $this->response->setStatusCode(500)->setJSON(['error' => 'Error enviando correo']);
        }
    }

    //este metodo actualizara la contraseña y si todo es valido actualizara al usuario en la base de datos
    public function resetPassword()
    {   

        header("Access-Control-Allow-Origin: http://localhost:5173");
        header("Access-Control-Allow-Headers: Content-Type, Authorization, X-Requested-With");
        header("Access-Control-Allow-Methods: GET, POST, OPTIONS, PUT, DELETE");
    
        if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
            exit;
        }

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