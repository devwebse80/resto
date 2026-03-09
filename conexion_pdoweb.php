<?php
/**
 * Conexión a Base de Datos MySQL con PDO
 * Restaurante Gusto & Sabor
 * Método seguro y moderno
 */


 /*
$host = "l2vr-m.h.filess.io";
$database = "GUSTO_requiredid";
$port = "3306";
$username = "GUSTO_requiredid";
$password = "46d6e126818c383f98c78dd0c40004f8da46c5bd";

// Variable para almacenar mensajes de resultado
$mensaje = "";
$conn = null;

// Crear la conexión PDO con MariaDB/MySQL
    $dsn = "mysql:host=$host;port=$port;dbname=$database;charset=utf8mb4";
    $options = [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        PDO::ATTR_EMULATE_PREPARES => false,
        PDO::ATTR_TIMEOUT => 30,
        PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8mb4 COLLATE utf8mb4_unicode_ci"
    ];
*/








// =====================================================
// CONFIGURACIÓN DE CONEXIÓN
// =====================================================

/* LOCAL
define('DB_HOST', 'cqftrl.h.filess.io');      // Host del servidor MySQL
define('DB_USER', 'gustosabor_tengasinto');           // Usuario MySQL
define('DB_PASS', '6942dd921f23a8bc2255458d81c3163c8e663a40');               // Contraseña MySQL (vacía por defecto en localhost)
define('DB_NAME', 'gustosabor_tengasinto');  // Nombre de la base de datos
define('DB_CHARSET', 'utf8mb4');     // Conjunto de caracteres
*/



/*  CONEXION SERVIDOR WEB     */
define('DB_HOST', 'l2vr-m.h.filess.io');      // Host del servidor MySQL
define('DB_USER', 'GUSTO_requiredid');           // Usuario MySQL
define('DB_PASS', '46d6e126818c383f98c78dd0c40004f8da46c5bd');               // Contraseña MySQL (vacía por defecto en localhost)
define('DB_NAME', 'GUSTO_requiredid');  // Nombre de la base de datos
define('DB_CHARSET', 'utf8mb4');     // Conjunto de caracteres










// =====================================================
// CREAR CONEXIÓN CON PDO
// =====================================================

class ConexionBD {
    private $conexion;
    private static $instancia = null;
    
    /**
     * Constructor privado para patrón Singleton
     */
    private function __construct() {
        try {
            // DSN (Data Source Name)
            $dsn = 'mysql:host=' . DB_HOST . ';dbname=' . DB_NAME . ';charset=' . DB_CHARSET;
            
            // Opciones de PDO
            $opciones = [
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                PDO::ATTR_EMULATE_PREPARES => false,
            ];
            
            // Crear conexión
            $this->conexion = new PDO($dsn, DB_USER, DB_PASS, $opciones);
            
            // Log de conexión exitosa (comentar en producción)
            // echo "✓ Conexión exitosa a la base de datos\n";
            
        } catch (PDOException $e) {
            die("Error de conexión: " . $e->getMessage());
        }
    }
    
    /**
     * Obtener instancia de conexión (Singleton)
     */
    public static function obtener() {
        if (self::$instancia === null) {
            self::$instancia = new self();
        }
        return self::$instancia;
    }
    
    /**
     * Obtener objeto PDO
     */
    public function getConexion() {
        return $this->conexion;
    }
    
    /**
     * Evitar clonación
     */
    private function __clone() {}
    
    /**
     * Evitar deserialización
     */
    private function __wakeup() {}
}

// =====================================================
// FUNCIONES DE CONSULTA CON PDO
// =====================================================

/**
 * Obtener todos los platos
 */
function obtenerTodosLosPlatos() {
    try {
        $pdo = ConexionBD::obtener()->getConexion();
        $sql = "SELECT id, product_name, product_price, product_desc, categoria ,photourl FROM platos ORDER BY id ASC";
        
        $stmt = $pdo->prepare($sql);
        $stmt->execute();
        
        return $stmt->fetchAll();
    } catch (PDOException $e) {
        return array('error' => $e->getMessage());
    }
}

/**
 * Obtener un plato por ID
 */
function obtenerPlatoPorId($id) {
    try {
        $pdo = ConexionBD::obtener()->getConexion();
        $sql = "SELECT id, product_name, product_price, product_desc, categoria, photourl FROM platos WHERE id = :id";
        
        $stmt = $pdo->prepare($sql);
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);
        $stmt->execute();
        
        return $stmt->fetch();
    } catch (PDOException $e) {
        return array('error' => $e->getMessage());
    }
}

/**
 * Buscar platos por nombre
 */
function buscarPlatosPorNombre($nombre) {
    try {
        $pdo = ConexionBD::obtener()->getConexion();
        $sql = "SELECT id, product_name, product_price, product_desc, categoria, photourl FROM platos WHERE product_name LIKE :nombre ORDER BY product_name ASC";
        
        $stmt = $pdo->prepare($sql);
        $stmt->bindValue(':nombre', '%' . $nombre . '%', PDO::PARAM_STR);
        $stmt->execute();
        
        return $stmt->fetchAll();
    } catch (PDOException $e) {
        return array('error' => $e->getMessage());
    }
}

/**
 * Obtener platos por rango de precio
 */
function obtenerPlatosPorPrecio($precioMin, $precioMax) {
    try {
        $pdo = ConexionBD::obtener()->getConexion();
        $sql = "SELECT id, product_name, product_price, product_desc, categoria, photourl FROM platos WHERE product_price BETWEEN :precioMin AND :precioMax ORDER BY product_price ASC";
        
        $stmt = $pdo->prepare($sql);
        $stmt->bindParam(':precioMin', $precioMin, PDO::PARAM_STR);
        $stmt->bindParam(':precioMax', $precioMax, PDO::PARAM_STR);
        $stmt->execute();
        
        return $stmt->fetchAll();
    } catch (PDOException $e) {
        return array('error' => $e->getMessage());
    }
}

/**
 * Agregar nuevo plato
 */
function agregarPlato($nombre, $precio, $descripcion, $photourl) {
    try {
        $pdo = ConexionBD::obtener()->getConexion();
        $sql = "INSERT INTO platos (product_name, product_price, product_desc, photourl) VALUES (:nombre, :precio, :descripcion, :photourl)";
        
        $stmt = $pdo->prepare($sql);
        $stmt->bindParam(':nombre', $nombre, PDO::PARAM_STR);
        $stmt->bindParam(':precio', $precio, PDO::PARAM_STR);
        $stmt->bindParam(':descripcion', $descripcion, PDO::PARAM_STR);
        $stmt->bindParam(':photourl', $photourl, PDO::PARAM_STR);
        $stmt->execute();
        
        return array(
            'exito' => true,
            'id' => $pdo->lastInsertId(),
            'mensaje' => 'Plato agregado correctamente'
        );
    } catch (PDOException $e) {
        return array(
            'exito' => false,
            'mensaje' => 'Error al agregar plato: ' . $e->getMessage()
        );
    }
}

/**
 * Actualizar plato
 */
function actualizarPlato($id, $nombre, $precio, $descripcion, $photourl) {
    try {
        $pdo = ConexionBD::obtener()->getConexion();
        $sql = "UPDATE platos SET product_name = :nombre, product_price = :precio, product_desc = :descripcion, photourl = :photourl WHERE id = :id";
        
        $stmt = $pdo->prepare($sql);
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);
        $stmt->bindParam(':nombre', $nombre, PDO::PARAM_STR);
        $stmt->bindParam(':precio', $precio, PDO::PARAM_STR);
        $stmt->bindParam(':descripcion', $descripcion, PDO::PARAM_STR);
        $stmt->bindParam(':photourl', $photourl, PDO::PARAM_STR);
        $stmt->execute();
        
        return array(
            'exito' => true,
            'mensaje' => 'Plato actualizado correctamente'
        );
    } catch (PDOException $e) {
        return array(
            'exito' => false,
            'mensaje' => 'Error al actualizar plato: ' . $e->getMessage()
        );
    }
}

/**
 * Eliminar plato
 */
function eliminarPlato($id) {
    try {
        $pdo = ConexionBD::obtener()->getConexion();
        $sql = "DELETE FROM platos WHERE id = :id";
        
        $stmt = $pdo->prepare($sql);
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);
        $stmt->execute();
        
        return array(
            'exito' => true,
            'mensaje' => 'Plato eliminado correctamente'
        );
    } catch (PDOException $e) {
        return array(
            'exito' => false,
            'mensaje' => 'Error al eliminar plato: ' . $e->getMessage()
        );
    }
}

/**
 * Contar total de platos
 */
function contarPlatos() {
    try {
        $pdo = ConexionBD::obtener()->getConexion();
        $sql = "SELECT COUNT(*) as total FROM platos";
        
        $stmt = $pdo->prepare($sql);
        $stmt->execute();
        $resultado = $stmt->fetch();
        
        return $resultado['total'];
    } catch (PDOException $e) {
        return array('error' => $e->getMessage());
    }
}

/**
 * Obtener precio promedio
 */
function obtenerPrecioPromedio() {
    try {
        $pdo = ConexionBD::obtener()->getConexion();
        $sql = "SELECT AVG(product_price) as promedio FROM platos";
        
        $stmt = $pdo->prepare($sql);
        $stmt->execute();
        $resultado = $stmt->fetch();
        
        return round($resultado['promedio'], 2);
    } catch (PDOException $e) {
        return array('error' => $e->getMessage());
    }
}

/**
 * Obtener plato más caro
 */
function obtenerPlatoMasCaro() {
    try {
        $pdo = ConexionBD::obtener()->getConexion();
        $sql = "SELECT id, product_name, product_price, product_desc, categoria, photourl FROM platos ORDER BY product_price DESC LIMIT 1";
        
        $stmt = $pdo->prepare($sql);
        $stmt->execute();
        
        return $stmt->fetch();
    } catch (PDOException $e) {
        return array('error' => $e->getMessage());
    }
}

/**
 * Obtener plato más barato
 */
function obtenerPlatoMasBarato() {
    try {
        $pdo = ConexionBD::obtener()->getConexion();
        $sql = "SELECT id, product_name, product_price, product_desc, categoria, photourl FROM platos ORDER BY product_price ASC LIMIT 1";
        
        $stmt = $pdo->prepare($sql);
        $stmt->execute();
        
        return $stmt->fetch();
    } catch (PDOException $e) {
        return array('error' => $e->getMessage());
    }
}

/**
 * Obtener estadísticas generales
 */
function obtenerEstadisticas() {
    try {
        $pdo = ConexionBD::obtener()->getConexion();
        $sql = "SELECT 
                    COUNT(*) as total_platos,
                    AVG(product_price) as precio_promedio,
                    MIN(product_price) as precio_minimo,
                    MAX(product_price) as precio_maximo,
                    SUM(product_price) as precio_total
                FROM platos";
        
        $stmt = $pdo->prepare($sql);
        $stmt->execute();
        $resultado = $stmt->fetch();
        
        return array(
            'total_platos' => $resultado['total_platos'],
            'precio_promedio' => round($resultado['precio_promedio'], 2),
            'precio_minimo' => round($resultado['precio_minimo'], 2),
            'precio_maximo' => round($resultado['precio_maximo'], 2),
            'precio_total' => round($resultado['precio_total'], 2)
        );
    } catch (PDOException $e) {
        return array('error' => $e->getMessage());
    }
}

/**
 * Obtener platos por categoría
 */
function obtenerPlatosPorCategoria($categoria) {
    try {
        $pdo = ConexionBD::obtener()->getConexion();
        $sql = "SELECT id, product_name, product_price, product_desc, categoria, photourl FROM platos WHERE categoria = :categoria ORDER BY product_name ASC";
        
        $stmt = $pdo->prepare($sql);
        $stmt->bindParam(':categoria', $categoria, PDO::PARAM_STR);
        $stmt->execute();
        
        return $stmt->fetchAll();
    } catch (PDOException $e) {
        return array('error' => $e->getMessage());
    }
}

/**
 * Obtener todas las categorías únicas
 */
function obtenerCategorias() {
    try {
        $pdo = ConexionBD::obtener()->getConexion();
     //   $sql = "SELECT DISTINCT categoria FROM platos ORDER BY categoria ASC"; cambie aca la tabla y campos
        $sql = "SELECT nombre FROM categorias ORDER BY nombre ASC";
       
        $stmt = $pdo->prepare($sql);
        $stmt->execute();
        
        $resultados = $stmt->fetchAll();
        // return array_column($resultados, 'categoria'); cambie aca campo
        return array_column($resultados, 'nombre');

    } catch (PDOException $e) {
        return array('error' => $e->getMessage());
    }
}

/**
 * Obtener platos por búsqueda avanzada con categoría
 */
function buscarPlatos($termino = '', $precioMin = 0, $precioMax = 999999, $categoria = null) {
    try {
        $pdo = ConexionBD::obtener()->getConexion();
        
        $sql = "SELECT id, product_name, product_price, product_desc, categoria, photourl FROM platos WHERE 1=1";
        
        if (!empty($termino)) {
            $sql .= " AND product_name LIKE :termino";
        }
        
        if (!empty($categoria)) {
            $sql .= " AND categoria = :categoria";
        }
        
        $sql .= " AND product_price BETWEEN :precioMin AND :precioMax ORDER BY product_name ASC";
        
        $stmt = $pdo->prepare($sql);
        
        if (!empty($termino)) {
            $stmt->bindValue(':termino', '%' . $termino . '%', PDO::PARAM_STR);
        }
        
        if (!empty($categoria)) {
            $stmt->bindParam(':categoria', $categoria, PDO::PARAM_STR);
        }
        
        $stmt->bindParam(':precioMin', $precioMin, PDO::PARAM_STR);
        $stmt->bindParam(':precioMax', $precioMax, PDO::PARAM_STR);
        $stmt->execute();
        
        return $stmt->fetchAll();
    } catch (PDOException $e) {
        return array('error' => $e->getMessage());
    }
}







// =====================================================
// EJEMPLOS DE USO
// =====================================================

/*
// Obtener todos los platos
$platos = obtenerTodosLosPlatos();
echo json_encode($platos, JSON_PRETTY_PRINT);

// Obtener plato por ID
$plato = obtenerPlatoPorId(1);
echo json_encode($plato, JSON_PRETTY_PRINT);

// Buscar platos
$resultados = buscarPlatosPorNombre('Pulpo');
echo json_encode($resultados, JSON_PRETTY_PRINT);

// Platos por rango de precio
$resultados = obtenerPlatosPorPrecio(10, 30);
echo json_encode($resultados, JSON_PRETTY_PRINT);

// Agregar nuevo plato
$resultado = agregarPlato('Mi Nuevo Plato', 29.99, 'Descripción del plato', 'http://url-foto.jpg');
echo json_encode($resultado, JSON_PRETTY_PRINT);

// Actualizar plato
$resultado = actualizarPlato(1, 'Nombre Actualizado', 25.00, 'Nueva descripción', 'http://nueva-foto.jpg');
echo json_encode($resultado, JSON_PRETTY_PRINT);

// Eliminar plato
$resultado = eliminarPlato(1);
echo json_encode($resultado, JSON_PRETTY_PRINT);

// Estadísticas
$stats = obtenerEstadisticas();
echo json_encode($stats, JSON_PRETTY_PRINT);

// Búsqueda avanzada
$resultados = buscarPlatos('Pulpo', 15, 30);
echo json_encode($resultados, JSON_PRETTY_PRINT);
*/

?>
