<?php

class Backup extends Controllers
{
    public function __construct()
    {
        sessionStart();
        parent::__construct();

        if (empty($_SESSION['login'])) {
            header('Location: ' . base_url() . 'login');
            die();
        }

        // Bloquea acceso si no es Administrador (idrol = 1)
        if (empty($_SESSION['userData']['id_rol']) || intval($_SESSION['userData']['id_rol']) !== 1) {
            header('Location: ' . base_url() . 'dashboard');
            die();
        }
    }

    public function respaldar()
    {
        set_time_limit(0);

        $pgDump = rtrim(PG_BIN_PATH, '\\/') . DIRECTORY_SEPARATOR . 'pg_dump.exe';
        if (!file_exists($pgDump)) {
            die('Error: No se encontró pg_dump.exe en ' . $pgDump);
        }

        $fecha = date('Y-m-d_H-i-s');
        $nombreArchivo = 'Respaldo_' . DB_NAME . '_' . $fecha . '.sql';
        $rutaTemporal = sys_get_temp_dir() . DIRECTORY_SEPARATOR . $nombreArchivo;

        putenv('PGPASSWORD=' . DB_PASS);

        $comando = sprintf(
            '"%s" -h %s -p 5432 -U %s --clean --if-exists --no-owner --no-privileges -F p -f "%s" %s 2>&1',
            $pgDump,
            escapeshellarg(DB_HOST),
            escapeshellarg(DB_USER),
            $rutaTemporal,
            escapeshellarg(DB_NAME)
        );

        $salida = [];
        $codigoRetorno = 0;
        exec($comando, $salida, $codigoRetorno);

        // Limpia la contraseña de las variables de entorno
        putenv('PGPASSWORD');

        if ($codigoRetorno !== 0 || !file_exists($rutaTemporal) || filesize($rutaTemporal) === 0) {
            if (file_exists($rutaTemporal)) {
                unlink($rutaTemporal);
            }
            $detalleError = implode("\n", $salida);
            die('Error al generar el respaldo de PostgreSQL: ' . htmlspecialchars($detalleError));
        }

        // Limpia el búfer de salida para evitar que el archivo .sql se corrompa
        if (ob_get_level()) {
            ob_end_clean();
        }

        header('Content-Description: File Transfer');
        header('Content-Type: application/sql; charset=utf-8');
        header('Content-Disposition: attachment; filename="' . $nombreArchivo . '"');
        header('Expires: 0');
        header('Cache-Control: must-revalidate');
        header('Pragma: public');
        header('Content-Length: ' . filesize($rutaTemporal));

        readfile($rutaTemporal);
        unlink($rutaTemporal);
        exit;
    }

   public function restaurar()
    {
        header('Content-Type: application/json; charset=utf-8');

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            echo json_encode(['status' => false, 'msg' => 'Método no permitido.'], JSON_UNESCAPED_UNICODE);
            die();
        }

        if (empty($_FILES['archivo_sql']) || $_FILES['archivo_sql']['error'] !== UPLOAD_ERR_OK) {
            echo json_encode(['status' => false, 'msg' => 'Selecciona un archivo .sql válido.'], JSON_UNESCAPED_UNICODE);
            die();
        }

        $archivo = $_FILES['archivo_sql'];
        $extension = strtolower(pathinfo($archivo['name'], PATHINFO_EXTENSION));

        if ($extension !== 'sql') {
            echo json_encode(['status' => false, 'msg' => 'Formato no válido. Solo se permiten archivos .sql'], JSON_UNESCAPED_UNICODE);
            die();
        }

        $psql = rtrim(PG_BIN_PATH, '\\/') . DIRECTORY_SEPARATOR . 'psql.exe';
        if (!file_exists($psql)) {
            echo json_encode(['status' => false, 'msg' => 'No se encontró psql.exe en el servidor.'], JSON_UNESCAPED_UNICODE);
            die();
        }

        set_time_limit(0);

        $rutaTemporal = sys_get_temp_dir() . DIRECTORY_SEPARATOR . 'restore_' . uniqid() . '.sql';
        if (!move_uploaded_file($archivo['tmp_name'], $rutaTemporal)) {
            echo json_encode(['status' => false, 'msg' => 'No se pudo procesar el archivo subido.'], JSON_UNESCAPED_UNICODE);
            die();
        }

        // Limpia BOM UTF-8 y comandos a nivel de servidor si vinieran en archivos externos
        $contenidoSql = file_get_contents($rutaTemporal);
        $contenidoSql = preg_replace('/^\xEF\xBB\xBF/', '', $contenidoSql);
        $contenidoSql = preg_replace('/^\s*(DROP|CREATE|ALTER)\s+DATABASE\b.*?;/ims', '', $contenidoSql);
        $contenidoSql = preg_replace('/^\s*\\\\(connect|c)\b.*$/im', '', $contenidoSql);
        file_put_contents($rutaTemporal, $contenidoSql);

        putenv('PGPASSWORD=' . DB_PASS);

        // 1. Verifica si la base de datos existe; si fue eliminada, la crea primero
        $cmdCheck = sprintf(
            '"%s" -h %s -p 5432 -U %s -d postgres -tAc "SELECT 1 FROM pg_database WHERE datname = \'%s\'"',
            $psql,
            escapeshellarg(DB_HOST),
            escapeshellarg(DB_USER),
            str_replace("'", "''", DB_NAME)
        );
        $existeDb = trim((string) shell_exec($cmdCheck));

        if ($existeDb !== '1') {
            $cmdCreate = sprintf(
                '"%s" -h %s -p 5432 -U %s -d postgres -c "CREATE DATABASE \\"%s\\";" 2>&1',
                $psql,
                escapeshellarg(DB_HOST),
                escapeshellarg(DB_USER),
                DB_NAME
            );
            $salidaCreate = [];
            $retCreate = 0;
            exec($cmdCreate, $salidaCreate, $retCreate);

            if ($retCreate !== 0) {
                putenv('PGPASSWORD');
                if (file_exists($rutaTemporal)) {
                    unlink($rutaTemporal);
                }
                echo json_encode([
                    'status' => false,
                    'msg'    => 'No se pudo recrear la base de datos: ' . implode(' | ', $salidaCreate)
                ], JSON_UNESCAPED_UNICODE);
                die();
            }
        }

        // 2. Restaura todas las tablas y registros dentro de la base de datos
        $comando = sprintf(
            '"%s" -h %s -p 5432 -U %s -d %s -v ON_ERROR_STOP=1 -f "%s" 2>&1',
            $psql,
            escapeshellarg(DB_HOST),
            escapeshellarg(DB_USER),
            escapeshellarg(DB_NAME),
            $rutaTemporal
        );

        $salida = [];
        $codigoRetorno = 0;
        exec($comando, $salida, $codigoRetorno);

        putenv('PGPASSWORD');
        if (file_exists($rutaTemporal)) {
            unlink($rutaTemporal);
        }

        if ($codigoRetorno !== 0) {
            $detalleError = implode(' | ', array_slice($salida, -4));
            echo json_encode([
                'status' => false,
                'msg'    => 'Error al restaurar la base de datos: ' . $detalleError
            ], JSON_UNESCAPED_UNICODE);
            die();
        }

        echo json_encode([
            'status' => true,
            'msg'    => 'Base de datos restaurada correctamente.'
        ], JSON_UNESCAPED_UNICODE);
        die();
    }

  
}