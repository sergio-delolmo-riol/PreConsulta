<?php
/**
 * Clase Database
 * Gestión de conexiones y operaciones con la base de datos
 * Proyecto: PreConsulta - Centro de Triaje Digital
 * Fecha: 18/11/2025
 * 
 * Implementa el patrón Singleton para garantizar una única conexión
 * Proporciona métodos seguros para ejecutar queries con PDO
 */

require_once __DIR__ . '/../config/database.php';

class Database {
    /**
     * Instancia única de la clase (Singleton)
     */
    private static $instance = null;
    
    /**
     * Objeto PDO de conexión
     */
    private $connection = null;
    
    /**
     * Statement de la última query ejecutada
     */
    private $statement = null;
    
    /**
     * Indica si hay una transacción activa
     */
    private $inTransaction = false;

    /**
     * Constructor privado (Singleton)
     * Establece la conexión con la base de datos
     */
    private function __construct() {
        try {
            $dsn = getDSN();
            $this->connection = new PDO($dsn, DB_USER, DB_PASS, PDO_OPTIONS);
            
            if (APP_DEBUG) {
                error_log("✓ Conexión a base de datos establecida: " . DB_NAME);
            }
        } catch (PDOException $e) {
            $this->handleError("Error de conexión a base de datos", $e);
        }
    }

    /**
     * Previene la clonación del objeto (Singleton)
     */
    private function __clone() {}

    /**
     * Previene la deserialización del objeto (Singleton)
     */
    public function __wakeup() {
        throw new Exception("No se puede deserializar un Singleton.");
    }

    /**
     * Obtiene la instancia única de la clase
     * 
     * @return Database Instancia única
     */
    public static function getInstance() {
        if (self::$instance === null) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    /**
     * Obtiene el objeto PDO de conexión
     * 
     * @return PDO Objeto de conexión
     */
    public function getConnection() {
        return $this->connection;
    }

    /**
     * Prepara y ejecuta una query SQL
     * 
     * @param string $sql Query SQL a ejecutar
     * @param array $params Parámetros para la query preparada
     * @return bool True si la ejecución fue exitosa
     */
    public function query($sql, $params = []) {
        try {
            $this->statement = $this->connection->prepare($sql);
            $result = $this->statement->execute($params);
            
            if (APP_DEBUG) {
                error_log("✓ Query ejecutada: " . $sql);
            }
            
            return $result;
        } catch (PDOException $e) {
            $this->handleError("Error ejecutando query", $e, $sql);
            return false;
        }
    }

    /**
     * Obtiene todos los resultados de la última query
     * 
     * @return array Array de resultados
     */
    public function fetchAll() {
        if ($this->statement) {
            return $this->statement->fetchAll();
        }
        return [];
    }

    /**
     * Obtiene un único resultado de la última query
     * 
     * @return mixed Resultado único o false
     */
    public function fetch() {
        if ($this->statement) {
            return $this->statement->fetch();
        }
        return false;
    }

    /**
     * Obtiene una columna específica de la última query
     * 
     * @param int $column Índice de la columna (0 por defecto)
     * @return mixed Valor de la columna
     */
    public function fetchColumn($column = 0) {
        if ($this->statement) {
            return $this->statement->fetchColumn($column);
        }
        return false;
    }

    /**
     * Obtiene el número de filas afectadas por la última query
     * 
     * @return int Número de filas afectadas
     */
    public function rowCount() {
        if ($this->statement) {
            return $this->statement->rowCount();
        }
        return 0;
    }

    /**
     * Obtiene el ID del último registro insertado
     * 
     * @return string ID del último registro insertado
     */
    public function lastInsertId() {
        return $this->connection->lastInsertId();
    }

    /**
     * Inicia una transacción
     * 
     * @return bool True si la transacción se inició correctamente
     */
    public function beginTransaction() {
        if (!$this->inTransaction) {
            $this->inTransaction = $this->connection->beginTransaction();
            if (APP_DEBUG && $this->inTransaction) {
                error_log("✓ Transacción iniciada");
            }
        }
        return $this->inTransaction;
    }

    /**
     * Confirma una transacción
     * 
     * @return bool True si la transacción se confirmó correctamente
     */
    public function commit() {
        if ($this->inTransaction) {
            $result = $this->connection->commit();
            $this->inTransaction = false;
            if (APP_DEBUG && $result) {
                error_log("✓ Transacción confirmada");
            }
            return $result;
        }
        return false;
    }

    /**
     * Revierte una transacción
     * 
     * @return bool True si la transacción se revirtió correctamente
     */
    public function rollback() {
        if ($this->inTransaction) {
            $result = $this->connection->rollback();
            $this->inTransaction = false;
            if (APP_DEBUG && $result) {
                error_log("⚠ Transacción revertida");
            }
            return $result;
        }
        return false;
    }

    /**
     * Verifica si hay una transacción activa
     * 
     * @return bool True si hay una transacción activa
     */
    public function inTransaction() {
        return $this->inTransaction;
    }

    /**
     * Ejecuta un SELECT y devuelve todos los resultados
     * 
     * @param string $sql Query SELECT
     * @param array $params Parámetros
     * @return array Array de resultados
     */
    public function select($sql, $params = []) {
        $this->query($sql, $params);
        return $this->fetchAll();
    }

    /**
     * Ejecuta un SELECT y devuelve un único resultado
     * 
     * @param string $sql Query SELECT
     * @param array $params Parámetros
     * @return mixed Resultado único o false
     */
    public function selectOne($sql, $params = []) {
        $this->query($sql, $params);
        return $this->fetch();
    }

    /**
     * Ejecuta un INSERT
     * 
     * @param string $table Nombre de la tabla
     * @param array $data Array asociativo [columna => valor]
     * @return int|bool ID del registro insertado o false
     */
    public function insert($table, $data) {
        $columns = implode(', ', array_keys($data));
        $placeholders = ':' . implode(', :', array_keys($data));
        
        $sql = "INSERT INTO {$table} ({$columns}) VALUES ({$placeholders})";
        
        if ($this->query($sql, $data)) {
            return $this->lastInsertId();
        }
        return false;
    }

    /**
     * Ejecuta un UPDATE
     * 
     * @param string $table Nombre de la tabla
     * @param array $data Array asociativo [columna => valor]
     * @param string $where Condición WHERE (ej: "id = :id")
     * @param array $whereParams Parámetros para la condición WHERE
     * @return int Número de filas afectadas
     */
    public function update($table, $data, $where, $whereParams = []) {
        $setClause = [];
        foreach (array_keys($data) as $column) {
            $setClause[] = "{$column} = :{$column}";
        }
        $setClause = implode(', ', $setClause);
        
        $sql = "UPDATE {$table} SET {$setClause} WHERE {$where}";
        
        $params = array_merge($data, $whereParams);
        
        if ($this->query($sql, $params)) {
            return $this->rowCount();
        }
        return 0;
    }

    /**
     * Ejecuta un DELETE
     * 
     * @param string $table Nombre de la tabla
     * @param string $where Condición WHERE
     * @param array $params Parámetros
     * @return int Número de filas afectadas
     */
    public function delete($table, $where, $params = []) {
        $sql = "DELETE FROM {$table} WHERE {$where}";
        
        if ($this->query($sql, $params)) {
            return $this->rowCount();
        }
        return 0;
    }

    /**
     * Escapa un valor para uso en queries (usar con precaución)
     * 
     * @param string $value Valor a escapar
     * @return string Valor escapado
     */
    public function quote($value) {
        return $this->connection->quote($value);
    }

    /**
     * Maneja los errores de base de datos
     * 
     * @param string $message Mensaje de error
     * @param PDOException $e Excepción capturada
     * @param string $sql Query que causó el error (opcional)
     */
    private function handleError($message, $e, $sql = '') {
        $errorMsg = $message . ": " . $e->getMessage();
        
        if ($sql && APP_DEBUG) {
            $errorMsg .= "\nSQL: " . $sql;
        }
        
        error_log("✗ " . $errorMsg);
        
        if (APP_DEBUG) {
            throw new Exception($errorMsg);
        } else {
            // En producción, no revelar detalles técnicos
            throw new Exception("Error en la base de datos. Por favor, contacte al administrador.");
        }
    }

    /**
     * Cierra la conexión a la base de datos
     */
    public function close() {
        $this->connection = null;
        $this->statement = null;
        if (APP_DEBUG) {
            error_log("✓ Conexión a base de datos cerrada");
        }
    }

    /**
     * Destructor - cierra la conexión automáticamente
     */
    public function __destruct() {
        if ($this->inTransaction) {
            $this->rollback();
        }
    }
}
