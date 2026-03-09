<?php
/**
 * API REST - Restaurante Gusto & Sabor
 * Obtener platos en formato JSON
 * 
 * Usos:
 * GET  /api.php?accion=todos              - Obtener todos los platos
 * GET  /api.php?accion=id&id=1            - Obtener plato por ID
 * GET  /api.php?accion=buscar&nombre=Pulpo - Buscar platos
 * GET  /api.php?accion=precio&min=10&max=30 - Platos por rango
 * GET  /api.php?accion=estadisticas       - Estadísticas generales
 */

// Permitir CORS (si es necesario)
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST, PUT, DELETE');
header('Content-Type: application/json; charset=utf-8');

// Incluir conexión PDO
require_once 'conexion_pdoweb.php';

// Obtener acción del cliente
$accion = isset($_GET['accion']) ? $_GET['accion'] : 'todos';

// Respuesta por defecto
$respuesta = array('exito' => false, 'mensaje' => 'Acción no válida');

try {
    switch ($accion) {
        
        // ===== OBTENER TODOS LOS PLATOS CON PAGINACIÓN =====
        case 'todos':
            $pagina = isset($_GET['pagina']) ? max(1, intval($_GET['pagina'])) : 1;
            $porPagina = 4; // 3 platos por página
            
            $platos = obtenerTodosLosPlatos();
            if (is_array($platos) && !isset($platos['error'])) {
                $totalPlatos = count($platos);
                $totalPaginas = ceil($totalPlatos / $porPagina);
                $pagina = min($pagina, $totalPaginas);
                
                $inicio = ($pagina - 1) * $porPagina;
                $platosPagina = array_slice($platos, $inicio, $porPagina);
                
                $respuesta = array(
                    'exito' => true,
                    'mensaje' => 'Platos obtenidos correctamente',
                    'total' => $totalPlatos,
                    'pagina' => $pagina,
                    'totalPaginas' => $totalPaginas,
                    'porPagina' => $porPagina,
                    'data' => $platosPagina
                );
            } else {
                $respuesta = array('exito' => false, 'mensaje' => 'Error al obtener platos');
            }



//echo json_encode($respuesta, JSON_PRETTY_PRINT);




            break;
        



        // ===== OBTENER PLATO POR ID =====
        case 'id':
            if (!isset($_GET['id']) || empty($_GET['id'])) {
                $respuesta = array('exito' => false, 'mensaje' => 'ID no proporcionado');
                break;
            }
            
            $plato = obtenerPlatoPorId($_GET['id']);
            if ($plato && !isset($plato['error'])) {
                $respuesta = array(
                    'exito' => true,
                    'mensaje' => 'Plato obtenido correctamente',
                    'data' => $plato
                );
            } else {
                $respuesta = array('exito' => false, 'mensaje' => 'Plato no encontrado');
            }
            break;
        
        // ===== BUSCAR PLATOS CON PAGINACIÓN =====
        case 'buscar':
            if (!isset($_GET['nombre']) || empty($_GET['nombre'])) {
                $respuesta = array('exito' => false, 'mensaje' => 'Nombre de búsqueda no proporcionado');
                break;
            }
            
            $pagina = isset($_GET['pagina']) ? max(1, intval($_GET['pagina'])) : 1;
            $porPagina = 4; // 3 platos por página
            
            $resultados = buscarPlatosPorNombre($_GET['nombre']);
            if (is_array($resultados) && !isset($resultados['error'])) {
                $totalResultados = count($resultados);
                $totalPaginas = ceil($totalResultados / $porPagina);
                $pagina = min($pagina, $totalPaginas);
                
                $inicio = ($pagina - 1) * $porPagina;
                $resultosPagina = array_slice($resultados, $inicio, $porPagina);
                
                $respuesta = array(
                    'exito' => true,
                    'mensaje' => 'Búsqueda completada',
                    'termino' => $_GET['nombre'],
                    'total' => $totalResultados,
                    'pagina' => $pagina,
                    'totalPaginas' => $totalPaginas,
                    'porPagina' => $porPagina,
                    'data' => $resultosPagina
                );
            } else {
                $respuesta = array('exito' => false, 'mensaje' => 'Error en la búsqueda');
            }
            break;
        
        // ===== OBTENER POR RANGO DE PRECIO =====
        case 'precio':
            if (!isset($_GET['min']) || !isset($_GET['max'])) {
                $respuesta = array('exito' => false, 'mensaje' => 'Rango de precio no proporcionado');
                break;
            }
            
            $resultados = obtenerPlatosPorPrecio($_GET['min'], $_GET['max']);
            if (is_array($resultados) && !isset($resultados['error'])) {
                $respuesta = array(
                    'exito' => true,
                    'mensaje' => 'Platos obtenidos por rango de precio',
                    'rango' => array('minimo' => $_GET['min'], 'maximo' => $_GET['max']),
                    'total' => count($resultados),
                    'data' => $resultados
                );
            } else {
                $respuesta = array('exito' => false, 'mensaje' => 'Error al obtener platos');
            }
            break;
        
        // ===== OBTENER ESTADÍSTICAS =====
        case 'estadisticas':
            $stats = obtenerEstadisticas();
            if (!isset($stats['error'])) {
                $respuesta = array(
                    'exito' => true,
                    'mensaje' => 'Estadísticas obtenidas',
                    'data' => $stats
                );
            } else {
                $respuesta = array('exito' => false, 'mensaje' => 'Error al obtener estadísticas');
            }
            break;
        
        // ===== PLATO MÁS CARO =====
        case 'masca ro':
            $plato = obtenerPlatoMasCaro();
            if ($plato && !isset($plato['error'])) {
                $respuesta = array(
                    'exito' => true,
                    'mensaje' => 'Plato más caro obtenido',
                    'data' => $plato
                );
            } else {
                $respuesta = array('exito' => false, 'mensaje' => 'Error al obtener plato');
            }
            break;
        
        // ===== PLATO MÁS BARATO =====
        case 'masbarato':
            $plato = obtenerPlatoMasBarato();
            if ($plato && !isset($plato['error'])) {
                $respuesta = array(
                    'exito' => true,
                    'mensaje' => 'Plato más barato obtenido',
                    'data' => $plato
                );
            } else {
                $respuesta = array('exito' => false, 'mensaje' => 'Error al obtener plato');
            }
            break;
        
        // ===== BÚSQUEDA CON FILTROS (nombre + categoría) =====
        case 'filtro':
            $nombre = isset($_GET['nombre']) ? $_GET['nombre'] : '';
            $categoria = isset($_GET['categoria']) ? $_GET['categoria'] : '';
            $pagina = isset($_GET['pagina']) ? max(1, intval($_GET['pagina'])) : 1;
            $porPagina = 3; // 3 platos por página
            
            // Usar búsqueda avanzada con nombre y categoría
            $resultados = buscarPlatos($nombre, 0, 999999, !empty($categoria) ? $categoria : null);
            
            if (is_array($resultados) && !isset($resultados['error'])) {
                $totalResultados = count($resultados);
                $totalPaginas = ceil($totalResultados / $porPagina);
                $pagina = min($pagina, $totalPaginas);
                
                $inicio = ($pagina - 1) * $porPagina;
                $resultosPagina = array_slice($resultados, $inicio, $porPagina);
                
                $respuesta = array(
                    'exito' => true,
                    'mensaje' => 'Búsqueda completada',
                    'nombre' => $nombre,
                    'categoria' => $categoria,
                    'total' => $totalResultados,
                    'pagina' => $pagina,
                    'totalPaginas' => $totalPaginas,
                    'porPagina' => $porPagina,
                    'data' => $resultosPagina
                );
            } else {
                $respuesta = array('exito' => false, 'mensaje' => 'Error en la búsqueda con filtros');
            }
            break;
        

        case 'categorias':
            $categorias = obtenerCategorias();
            if (is_array($categorias) && !isset($categorias['error'])) {
                $respuesta = array(
                    'exito' => true,
                    'mensaje' => 'Categorías obtenidas correctamente',
                    'total' => count($categorias),
                    'data' => $categorias
                );
            } else {
                $respuesta = array('exito' => false, 'mensaje' => 'Error al obtener categorías');
            }
            break;
        
        // ===== OBTENER PLATOS POR CATEGORÍA =====
        case 'categoria':
            if (!isset($_GET['nombre']) || empty($_GET['nombre'])) {
                $respuesta = array('exito' => false, 'mensaje' => 'Nombre de categoría no proporcionado');
                break;
            }
            
            $pagina = isset($_GET['pagina']) ? max(1, intval($_GET['pagina'])) : 1;
            $porPagina = 3; // 3 platos por página
            
            $resultados = obtenerPlatosPorCategoria($_GET['nombre']);
            if (is_array($resultados) && !isset($resultados['error'])) {
                $totalResultados = count($resultados);
                $totalPaginas = ceil($totalResultados / $porPagina);
                $pagina = min($pagina, $totalPaginas);
                
                $inicio = ($pagina - 1) * $porPagina;
                $resultosPagina = array_slice($resultados, $inicio, $porPagina);
                
                $respuesta = array(
                    'exito' => true,
                    'mensaje' => 'Platos de categoría obtenidos correctamente',
                    'categoria' => $_GET['nombre'],
                    'total' => $totalResultados,
                    'pagina' => $pagina,
                    'totalPaginas' => $totalPaginas,
                    'porPagina' => $porPagina,
                    'data' => $resultosPagina
                );
            } else {
                $respuesta = array('exito' => false, 'mensaje' => 'Error al obtener platos de categoría');
            }
            break;
        

            $termino = isset($_GET['termino']) ? $_GET['termino'] : '';
            $precioMin = isset($_GET['min']) ? $_GET['min'] : 0;
            $precioMax = isset($_GET['max']) ? $_GET['max'] : 999999;
            
            $resultados = buscarPlatos($termino, $precioMin, $precioMax);
            if (is_array($resultados) && !isset($resultados['error'])) {
                $respuesta = array(
                    'exito' => true,
                    'mensaje' => 'Búsqueda avanzada completada',
                    'filtros' => array(
                        'termino' => $termino,
                        'precio_minimo' => $precioMin,
                        'precio_maximo' => $precioMax
                    ),
                    'total' => count($resultados),
                    'data' => $resultados
                );
            } else {
                $respuesta = array('exito' => false, 'mensaje' => 'Error en la búsqueda avanzada');
            }
            break;
        
        // ===== AGREGAR PLATO (POST) =====
        case 'agregar':
            if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
                $respuesta = array('exito' => false, 'mensaje' => 'Use POST para agregar platos');
                break;
            }
            
            $datos = json_decode(file_get_contents('php://input'), true);
            
            if (!isset($datos['nombre']) || !isset($datos['precio']) || !isset($datos['descripcion'])) {
                $respuesta = array('exito' => false, 'mensaje' => 'Faltan datos requeridos');
                break;
            }
            
            $photourl = isset($datos['photourl']) ? $datos['photourl'] : '';
            $resultado = agregarPlato($datos['nombre'], $datos['precio'], $datos['descripcion'], $photourl);
            $respuesta = $resultado;
            break;
        
        // ===== ACTUALIZAR PLATO (PUT) =====
        case 'actualizar':
            if ($_SERVER['REQUEST_METHOD'] !== 'PUT') {
                $respuesta = array('exito' => false, 'mensaje' => 'Use PUT para actualizar platos');
                break;
            }
            
            $datos = json_decode(file_get_contents('php://input'), true);
            
            if (!isset($datos['id']) || !isset($datos['nombre']) || !isset($datos['precio'])) {
                $respuesta = array('exito' => false, 'mensaje' => 'Faltan datos requeridos');
                break;
            }
            
            $photourl = isset($datos['photourl']) ? $datos['photourl'] : '';
            $resultado = actualizarPlato($datos['id'], $datos['nombre'], $datos['precio'], $datos['descripcion'], $photourl);
            $respuesta = $resultado;
            break;
        
        // ===== ELIMINAR PLATO (DELETE) =====
        case 'eliminar':
            if ($_SERVER['REQUEST_METHOD'] !== 'DELETE') {
                $respuesta = array('exito' => false, 'mensaje' => 'Use DELETE para eliminar platos');
                break;
            }
            
            if (!isset($_GET['id']) || empty($_GET['id'])) {
                $respuesta = array('exito' => false, 'mensaje' => 'ID no proporcionado');
                break;
            }
            
            $resultado = eliminarPlato($_GET['id']);
            $respuesta = $resultado;
            break;
        
        // ===== ACCIÓN NO VÁLIDA =====
        default:
            $respuesta = array(
                'exito' => false,
                'mensaje' => 'Acción no válida',
                'acciones_disponibles' => array(
                    'todos' => 'GET /api.php?accion=todos&pagina=1',
                    'id' => 'GET /api.php?accion=id&id=1',
                    'buscar' => 'GET /api.php?accion=buscar&nombre=Pulpo&pagina=1',
                    'filtro' => 'GET /api.php?accion=filtro&nombre=Pulpo&categoria=Frutos del Mar&pagina=1',
                    'categorias' => 'GET /api.php?accion=categorias',
                    'categoria' => 'GET /api.php?accion=categoria&nombre=Carnes&pagina=1',
                    'precio' => 'GET /api.php?accion=precio&min=10&max=30',
                    'estadisticas' => 'GET /api.php?accion=estadisticas',
                    'mascaro' => 'GET /api.php?accion=mascaro',
                    'masbarato' => 'GET /api.php?accion=masbarato',
                    'avanzada' => 'GET /api.php?accion=avanzada&termino=Pulpo&min=10&max=30&categoria=Frutos del Mar'
                )
            );
    }
    
} catch (Exception $e) {
    $respuesta = array(
        'exito' => false,
        'mensaje' => 'Error: ' . $e->getMessage()
    );
}

// Enviar respuesta en JSON
echo json_encode($respuesta, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);

?>
