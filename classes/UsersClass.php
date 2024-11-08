<?php
use Firebase\JWT\JWT;

class UsersClass
{
    protected $db;
    public function __construct()
    {
        $this->db = new Database();
    }
    public function loginUser()
    {
        $usuario = $this->db->consultarRegistro("SELECT id, nombres, apellidos, identificacion, email, celular, tipoUsuario, cargo, area, genero, fechaUltimoAcceso, eliminado, password FROM usuario WHERE email = :email ", ["email" => Flight::request()->data->email]);
        if ($usuario) {
            $password = Flight::request()->data->password;
            if (password_verify($password, $usuario['password'])) {
                if ($usuario['eliminado'] == 'S') {
                    $salida = respuesta('99', 'Usuario eliminado.');
                } else {
                    unset($usuario['password']);
                    $key = KEY_TOKEN;
                    $now = strtotime("now");
                    $payload = [
                        'exp' => $now + 3600,
                        'data' => 1,
                    ];
                    $usuario['jwt'] = JWT::encode($payload, $key, 'HS256');
                    $salida = respuesta('00', 'Success', $usuario);
                }

            } else {
                $salida = respuesta('99', 'Constraseña incorrecta.');
            }
        } else {
            $salida = respuesta('88', 'El usuario no esta registrado.');
        }
        Flight::json($salida);
    }

}
