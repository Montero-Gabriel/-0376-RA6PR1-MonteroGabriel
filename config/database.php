<?php
/**
 * Configuració de connexió a la Base de Dades MySQL
 * Aplicació Tracking d'Hores
 */

// Configuracions generals
define('DB_HOST', 'localhost');
define('DB_NAME', 'tracking_hores');
define('DB_USER', 'root');
define('DB_PASS', '');
define('DB_CHARSET', 'utf8mb4');

/**
 * Funció per obtenir connexió PDO a la base de dades
 * @return PDO
 */
function getDBConnection() {
    static $conn = null;
    
    if ($conn === null) {
        try {
            $dsn = "mysql:host=" . DB_HOST . ";dbname=" . DB_NAME . ";charset=" . DB_CHARSET;
            
            $options = [
                PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                PDO::ATTR_EMULATE_PREPARES   => false,
                PDO::ATTR_PERSISTENT         => true
            ];
            
            $conn = new PDO($dsn, DB_USER, DB_PASS, $options);
            
        } catch (PDOException $e) {
            error_log("Error connexió BD: " . $e->getMessage());
            die("Error de connexió al sistema. Si us plau, intenta-ho més tard.");
        }
    }
    
    return $conn;
}

/**
 * Funció helper per executar consultes preparades de forma segura
 * @param string $sql Consulta SQL
 * @param array $params Paràmetres per la consulta
 * @return PDOStatement
 */
function dbQuery($sql, $params = []) {
    $db = getDBConnection();
    $stmt = $db->prepare($sql);
    $stmt->execute($params);
    return $stmt;
}

/**
 * Funció per obtenir un sol registre
 * @param string $sql
 * @param array $params
 * @return array|false
 */
function dbGetOne($sql, $params = []) {
    return dbQuery($sql, $params)->fetch();
}

/**
 * Funció per obtenir tots els registres
 * @param string $sql
 * @param array $params
 * @return array
 */
function dbGetAll($sql, $params = []) {
    return dbQuery($sql, $params)->fetchAll();
}

/**
 * Funció per obtenir l'últim ID insertat
 * @return string
 */
function dbLastInsertId() {
    return getDBConnection()->lastInsertId();
}
?>