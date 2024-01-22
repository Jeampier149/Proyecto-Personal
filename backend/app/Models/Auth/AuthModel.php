<?php

namespace App\Models\Auth;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use PDO;

class AuthModel extends Model
{
    use HasFactory;
    use HasFactory;

    protected $conexion;

    public function __construct()
    {
        parent::__construct();
        $this->conexion = DB::connection('sqlsrv');
    }

    public function validarInicioSesion($username, $password, $passwordHash)
    {
        try {
            return $this->conexion->selectOne('EXEC sp_validar_inicio_sesion ?, ?, ?', [$username,$password, $passwordHash]);
        } catch (\Error $e) {
            return 0;
        }
    }

    public function validarUsuario($usuario, $contrasena)
    {
        try {
            return $this->conexion->selectOne('EXEC dbo_web.sp_validar_usuario ?, ?', [$usuario, $contrasena]);
        } catch (\Error $e) {
            return 0;
        }
    }

    public function obtenerMenu($perfil): array
    {
        try {
            return $this->conexion->select("EXEC dbo_web.sp_Obtener_Accesos2 ?", [$perfil]);
        } catch (\Error $e) {
            return [];
        }
    }

    /**
     * @param $perfil
     * @return array
     */
    public function obtenerAccesos($perfil): array
    {
        $smtp = $this->conexion->getPdo()->prepare(/** @lang SQL */ 'EXEC dbo_web.gral_sp_lst_xg_accion_perfil ?');
        $smtp->bindParam(1, $perfil);
        $smtp->execute();
        $resultados = $smtp->columnCount() > 0 ? $smtp->fetchAll(PDO::FETCH_ASSOC) : [];
        $smtp->closeCursor();
        return $resultados;
    }

    public function cambiarContrasena($usuario, $contrasena, $nuevaContrasena, $nuevaContrasenaHash, $equipo)
    {
        try {
            return $this->conexion->selectOne('EXEC dbo_web.sp_upd_contrasena_usuario ?, ?, ?, ?, ?', [$usuario, $contrasena, $nuevaContrasena, $nuevaContrasenaHash, $equipo]);
        } catch (\Error $e) {
            return 0;
        }
    }
}
