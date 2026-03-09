<?php
// =============================================
// CONEXIÓN A MARIADB EN FILESS.IO CON PDO
// Y OPERACIONES CON LA TABLA PLATOS
// =============================================

// Datos de conexión proporcionados por filess.io
$host = "l2vr-m.h.filess.io";
$database = "GUSTO_requiredid";
$port = "3306";
$username = "GUSTO_requiredid";
$password = "46d6e126818c383f98c78dd0c40004f8da46c5bd";

// Variable para almacenar mensajes de resultado
$mensaje = "";
$conn = null;

// Verificar si la tabla existe, si no, crearla
function verificarOCrearTabla($conn) {
    try {
        // Verificar si la tabla platos existe
        $query = "SHOW TABLES LIKE 'platos'";
        $stmt = $conn->prepare($query);
        $stmt->execute();
        
        if ($stmt->rowCount() == 0) {
            // La tabla no existe, crearla
            $createTable = "CREATE TABLE IF NOT EXISTS `platos` (
                `id` int(11) NOT NULL AUTO_INCREMENT COMMENT 'ID único del plato',
                `product_name` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL COMMENT 'Nombre del producto',
                `product_price` decimal(10,2) NOT NULL COMMENT 'Precio del producto',
                `product_desc` varchar(500) COLLATE utf8mb4_unicode_ci NOT NULL COMMENT 'Descripción del producto',
                `categoria` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'Plato Principal',
                `photourl` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT 'URL de la foto del producto',
                `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP COMMENT 'Fecha de creación',
                `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP COMMENT 'Fecha de última actualización',
                PRIMARY KEY (`id`)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci AUTO_INCREMENT=1";
            
            $conn->exec($createTable);
            
            // Insertar datos de ejemplo si la tabla está vacía
            $checkData = "SELECT COUNT(*) as total FROM platos";
            $stmt = $conn->prepare($checkData);
            $stmt->execute();
            $result = $stmt->fetch();
            
            if ($result['total'] == 0) {
                $insertData = "INSERT INTO `platos` (`product_name`, `product_price`, `product_desc`, `categoria`, `photourl`) VALUES
                ('Pulpo a la Parrilla', '11.00', 'Pulpo fresco a la parrilla con pure de papa al oliva, pimentón ahumado y aceite de oliva arbequina.', 'Frutos del Mar', '4.jpg'),
                ('Risotto de Hongos', '18.00', 'Arroz arborio cremoso con variedad de hongos silvestres, trufa negra y parmesano reggiano.', 'Vegetariano', 'http://static.photos/food/640x360/2'),
                ('Smoked Old Fashioned', '14.00', 'Bourbon ahumado en casa, bitter de naranja, azúcar de caña y esencia de cereza amarena.', 'Bebidas', 'http://static.photos/food/640x360/3'),
                ('Tacos de Pescado', '16.00', 'Mahi-mahi empanizado, slaw de col morada, salsa de aguacate y tortillas de maíz hechas a mano.', 'Frutos del Mar', 'http://static.photos/food/640x360/4'),
                ('Parrillada Mixta', '45.00', 'Selección de carnes premium: chorizo, morcilla, entraña y vacío con chimichurri casero y verduras.', 'Carnes', 'http://static.photos/food/640x360/5'),
                ('Volcán de Chocolate', '12.00', 'Bizcocho de chocolate caliente con corazón líquido, helado de vainilla bourbon y frutos rojos.', 'Postres', 'http://static.photos/food/640x360/6')";
                
                $conn->exec($insertData);
                return "✅ Tabla 'platos' creada y datos de ejemplo insertados correctamente.";
            }
            return "✅ Tabla 'platos' creada correctamente.";
        }
        return null;
    } catch (PDOException $e) {
        return "❌ Error al verificar/crear tabla: " . $e->getMessage();
    }
}

try {
    // Crear la conexión PDO con MariaDB/MySQL
    $dsn = "mysql:host=$host;port=$port;dbname=$database;charset=utf8mb4";
    $options = [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        PDO::ATTR_EMULATE_PREPARES => false,
        PDO::ATTR_TIMEOUT => 30,
        PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8mb4 COLLATE utf8mb4_unicode_ci"
    ];
    
    $conn = new PDO($dsn, $username, $password, $options);
    
    // Verificar/Crear tabla
    $tablaResult = verificarOCrearTabla($conn);
    
    $mensaje = '<div style="color: green; padding: 10px; background: #e8f5e8; border-radius: 5px; margin: 10px 0;">✅ Conexión exitosa a la base de datos "' . $database . '"</div>';
    
    if ($tablaResult) {
        $mensaje .= '<div style="color: blue; padding: 10px; background: #e8f0fe; border-radius: 5px; margin: 10px 0;">' . $tablaResult . '</div>';
    }
    
} catch (PDOException $e) {
    $mensaje = '<div style="color: red; padding: 10px; background: #ffe8e8; border-radius: 5px; margin: 10px 0;">❌ Error de conexión: ' . $e->getMessage() . '</div>';
    
    // Intentar conexión sin seleccionar base de datos para crearla si no existe
    try {
        $dsn = "mysql:host=$host;port=$port;charset=utf8mb4";
        $conn = new PDO($dsn, $username, $password, $options);
        
        // Crear base de datos si no existe
        $conn->exec("CREATE DATABASE IF NOT EXISTS `$database` CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci");
        
        // Reconectar con la base de datos
        $dsn = "mysql:host=$host;port=$port;dbname=$database;charset=utf8mb4";
        $conn = new PDO($dsn, $username, $password, $options);
        
        // Verificar/Crear tabla
        $tablaResult = verificarOCrearTabla($conn);
        
        $mensaje = '<div style="color: green; padding: 10px; background: #e8f5e8; border-radius: 5px; margin: 10px 0;">✅ Base de datos creada y conexión exitosa</div>';
        
        if ($tablaResult) {
            $mensaje .= '<div style="color: blue; padding: 10px; background: #e8f0fe; border-radius: 5px; margin: 10px 0;">' . $tablaResult . '</div>';
        }
        
    } catch (PDOException $e2) {
        $mensaje .= '<div style="color: red; padding: 10px; background: #ffe8e8; border-radius: 5px; margin: 10px 0;">❌ Error al crear base de datos: ' . $e2->getMessage() . '</div>';
        $conn = null;
    }
}

// Procesar acciones POST (insertar/eliminar)
if ($_SERVER['REQUEST_METHOD'] === 'POST' && $conn) {
    
    if (isset($_POST['action'])) {
        
        // INSERTAR NUEVO PLATO
        if ($_POST['action'] === 'insertar') {
            try {
                $query = "INSERT INTO platos (product_name, product_price, product_desc, categoria, photourl) 
                          VALUES (:nombre, :precio, :descripcion, :categoria, :foto)";
                
                $stmt = $conn->prepare($query);
                
                $stmt->bindParam(":nombre", $_POST['nombre']);
                $stmt->bindParam(":precio", $_POST['precio']);
                $stmt->bindParam(":descripcion", $_POST['descripcion']);
                $stmt->bindParam(":categoria", $_POST['categoria']);
                $stmt->bindParam(":foto", $_POST['foto']);
                
                if ($stmt->execute()) {
                    $ultimoId = $conn->lastInsertId();
                    $mensaje .= '<div style="color: green; padding: 10px; background: #e8f5e8; border-radius: 5px; margin: 10px 0;">✅ Plato insertado correctamente con ID: ' . $ultimoId . '</div>';
                }
                
            } catch (PDOException $e) {
                $mensaje .= '<div style="color: red; padding: 10px; background: #ffe8e8; border-radius: 5px; margin: 10px 0;">❌ Error al insertar: ' . $e->getMessage() . '</div>';
            }
        }
        
        // ELIMINAR PLATO
        if ($_POST['action'] === 'eliminar' && isset($_POST['id'])) {
            try {
                $query = "DELETE FROM platos WHERE id = :id";
                $stmt = $conn->prepare($query);
                $stmt->bindParam(":id", $_POST['id']);
                
                if ($stmt->execute()) {
                    $mensaje .= '<div style="color: green; padding: 10px; background: #e8f5e8; border-radius: 5px; margin: 10px 0;">✅ Plato con ID ' . $_POST['id'] . ' eliminado correctamente</div>';
                }
                
            } catch (PDOException $e) {
                $mensaje .= '<div style="color: red; padding: 10px; background: #ffe8e8; border-radius: 5px; margin: 10px 0;">❌ Error al eliminar: ' . $e->getMessage() . '</div>';
            }
        }
        
        // ACTUALIZAR PLATO
        if ($_POST['action'] === 'actualizar') {
            try {
                $query = "UPDATE platos SET 
                          product_name = :nombre,
                          product_price = :precio,
                          product_desc = :descripcion,
                          categoria = :categoria,
                          photourl = :foto,
                          updated_at = CURRENT_TIMESTAMP
                          WHERE id = :id";
                
                $stmt = $conn->prepare($query);
                
                $stmt->bindParam(":id", $_POST['id']);
                $stmt->bindParam(":nombre", $_POST['nombre']);
                $stmt->bindParam(":precio", $_POST['precio']);
                $stmt->bindParam(":descripcion", $_POST['descripcion']);
                $stmt->bindParam(":categoria", $_POST['categoria']);
                $stmt->bindParam(":foto", $_POST['foto']);
                
                if ($stmt->execute()) {
                    $mensaje .= '<div style="color: green; padding: 10px; background: #e8f5e8; border-radius: 5px; margin: 10px 0;">✅ Plato con ID ' . $_POST['id'] . ' actualizado correctamente</div>';
                }
                
            } catch (PDOException $e) {
                $mensaje .= '<div style="color: red; padding: 10px; background: #ffe8e8; border-radius: 5px; margin: 10px 0;">❌ Error al actualizar: ' . $e->getMessage() . '</div>';
            }
        }
    }
}

// Obtener todos los platos para mostrar
$platos = [];
if ($conn) {
    try {
        $query = "SELECT * FROM platos ORDER BY categoria, product_name";
        $stmt = $conn->prepare($query);
        $stmt->execute();
        $platos = $stmt->fetchAll();
    } catch (PDOException $e) {
        $mensaje .= '<div style="color: red; padding: 10px; background: #ffe8e8; border-radius: 5px; margin: 10px 0;">❌ Error al obtener platos: ' . $e->getMessage() . '</div>';
    }
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestión de Platos - filess.io</title>
    <style>
        * {
            box-sizing: border-box;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }
        body {
            background: #f5f5f5;
            margin: 0;
            padding: 20px;
        }
        .container {
            max-width: 1400px;
            margin: 0 auto;
        }
        h1 {
            color: #333;
            text-align: center;
            margin-bottom: 30px;
        }
        h2 {
            color: #444;
            border-bottom: 2px solid #ddd;
            padding-bottom: 10px;
        }
        .grid {
            display: grid;
            grid-template-columns: 1fr 2fr;
            gap: 30px;
        }
        .form-container {
            background: white;
            padding: 20px;
            border-radius: 10px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        }
        .table-container {
            background: white;
            padding: 20px;
            border-radius: 10px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
            overflow-x: auto;
        }
        .form-group {
            margin-bottom: 15px;
        }
        label {
            display: block;
            margin-bottom: 5px;
            font-weight: 600;
            color: #555;
        }
        input, select, textarea {
            width: 100%;
            padding: 10px;
            border: 1px solid #ddd;
            border-radius: 5px;
            font-size: 14px;
        }
        textarea {
            height: 100px;
            resize: vertical;
        }
        button {
            background: #4CAF50;
            color: white;
            padding: 12px 20px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            font-size: 16px;
            width: 100%;
        }
        button:hover {
            background: #45a049;
        }
        table {
            width: 100%;
            border-collapse: collapse;
        }
        th {
            background: #4CAF50;
            color: white;
            padding: 12px;
            text-align: left;
        }
        td {
            padding: 10px;
            border-bottom: 1px solid #ddd;
        }
        tr:hover {
            background: #f5f5f5;
        }
        .btn-delete {
            background: #f44336;
            color: white;
            padding: 5px 10px;
            border: none;
            border-radius: 3px;
            cursor: pointer;
            font-size: 12px;
        }
        .btn-delete:hover {
            background: #da190b;
        }
        .btn-edit {
            background: #2196F3;
            color: white;
            padding: 5px 10px;
            border: none;
            border-radius: 3px;
            cursor: pointer;
            margin-right: 5px;
            font-size: 12px;
        }
        .btn-edit:hover {
            background: #0b7dda;
        }
        .mensaje {
            margin: 20px 0;
        }
        .info-conexion {
            background: #e7f3ff;
            padding: 15px;
            border-radius: 5px;
            margin: 20px 0;
            font-size: 14px;
            border-left: 4px solid #2196F3;
        }
        .foto-preview {
            width: 60px;
            height: 60px;
            object-fit: cover;
            border-radius: 5px;
            border: 1px solid #ddd;
        }
        .acciones {
            display: flex;
            gap: 5px;
            flex-wrap: wrap;
        }
        .badge {
            display: inline-block;
            padding: 3px 8px;
            border-radius: 3px;
            font-size: 12px;
            font-weight: bold;
        }
        .badge-mysql {
            background: #00758f;
            color: white;
        }
        .contador {
            background: #e0e0e0;
            padding: 5px 10px;
            border-radius: 20px;
            font-size: 14px;
            display: inline-block;
        }
        @media (max-width: 768px) {
            .grid {
                grid-template-columns: 1fr;
            }
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>🍽️ Gestión de Platos - Restaurante Gourmet</h1>
        
        <div class="info-conexion">
            <strong>📊 Información de conexión filess.io:</strong><br>
            <span class="badge badge-mysql">MariaDB</span>
            <ul style="margin-top: 10px; list-style: none; padding-left: 0;">
                <li><strong>Host:</strong> <?php echo $host; ?></li>
                <li><strong>Base de datos:</strong> <?php echo $database; ?></li>
                <li><strong>Puerto:</strong> <?php echo $port; ?></li>
                <li><strong>Usuario:</strong> <?php echo $username; ?></li>
                <li><strong>Estado:</strong> <?php echo $conn ? '🟢 Conectado' : '🔴 Desconectado'; ?></li>
            </ul>
        </div>
        
        <div class="mensaje">
            <?php echo $mensaje; ?>
        </div>
        
        <div class="grid">
            <!-- FORMULARIO PARA INSERTAR -->
            <div class="form-container">
                <h2>➕ Agregar Nuevo Plato</h2>
                <form method="POST">
                    <input type="hidden" name="action" value="insertar">
                    
                    <div class="form-group">
                        <label>Nombre del plato:</label>
                        <input type="text" name="nombre" required placeholder="Ej: Pulpo a la Parrilla">
                    </div>
                    
                    <div class="form-group">
                        <label>Precio ($):</label>
                        <input type="number" step="0.01" name="precio" required placeholder="0.00">
                    </div>
                    
                    <div class="form-group">
                        <label>Categoría:</label>
                        <select name="categoria" required>
                            <option value="Plato Principal">Plato Principal</option>
                            <option value="Frutos del Mar">Frutos del Mar</option>
                            <option value="Carnes">Carnes</option>
                            <option value="Vegetariano">Vegetariano</option>
                            <option value="Bebidas">Bebidas</option>
                            <option value="Postres">Postres</option>
                        </select>
                    </div>
                    
                    <div class="form-group">
                        <label>Descripción:</label>
                        <textarea name="descripcion" required placeholder="Descripción detallada del plato..."></textarea>
                    </div>
                    
                    <div class="form-group">
                        <label>URL de la foto:</label>
                        <input type="text" name="foto" placeholder="ej: 4.jpg o URL completa">
                    </div>
                    
                    <button type="submit">Guardar Plato</button>
                </form>
            </div>
            
            <!-- TABLA DE PLATOS -->
            <div class="table-container">
                <h2>📋 Lista de Platos 
                    <?php if (count($platos) > 0): ?>
                        <span class="contador">Total: <?php echo count($platos); ?></span>
                    <?php endif; ?>
                </h2>
                
                <?php if (count($platos) > 0): ?>
                    <table>
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Foto</th>
                                <th>Nombre</th>
                                <th>Precio</th>
                                <th>Categoría</th>
                                <th>Descripción</th>
                                <th>Acciones</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($platos as $plato): ?>
                            <tr>
                                <td><?php echo $plato['id']; ?></td>
                                <td>
                                    <?php if ($plato['photourl']): ?>
                                        <img src="<?php echo htmlspecialchars($plato['photourl']); ?>" 
                                             alt="Foto" class="foto-preview" 
                                             onerror="this.src='https://via.placeholder.com/60?text=No+Image'">
                                    <?php else: ?>
                                        <img src="https://via.placeholder.com/60?text=No+Image" alt="Sin foto" class="foto-preview">
                                    <?php endif; ?>
                                </td>
                                <td><strong><?php echo htmlspecialchars($plato['product_name']); ?></strong></td>
                                <td><strong>$<?php echo number_format($plato['product_price'], 2); ?></strong></td>
                                <td>
                                    <span style="background: <?php 
                                        $colores = [
                                            'Plato Principal' => '#4CAF50',
                                            'Frutos del Mar' => '#00BCD4',
                                            'Carnes' => '#F44336',
                                            'Vegetariano' => '#8BC34A',
                                            'Bebidas' => '#9C27B0',
                                            'Postres' => '#FF9800'
                                        ];
                                        echo $colores[$plato['categoria']] ?: '#999';
                                    ?>; color: white; padding: 3px 8px; border-radius: 3px; font-size: 12px;">
                                        <?php echo htmlspecialchars($plato['categoria']); ?>
                                    </span>
                                </td>
                                <td><?php echo htmlspecialchars(substr($plato['product_desc'], 0, 50)) . '...'; ?></td>
                                <td class="acciones">
                                    <!-- Botón Editar -->
                                    <button class="btn-edit" onclick="editarPlato(
                                        <?php echo $plato['id']; ?>,
                                        '<?php echo addslashes($plato['product_name']); ?>',
                                        <?php echo $plato['product_price']; ?>,
                                        '<?php echo addslashes($plato['categoria']); ?>',
                                        '<?php echo addslashes($plato['product_desc']); ?>',
                                        '<?php echo addslashes($plato['photourl']); ?>'
                                    )">✏️ Editar</button>
                                    
                                    <!-- Formulario para eliminar -->
                                    <form method="POST" style="display: inline;" onsubmit="return confirm('¿Estás seguro de eliminar este plato?');">
                                        <input type="hidden" name="action" value="eliminar">
                                        <input type="hidden" name="id" value="<?php echo $plato['id']; ?>">
                                        <button type="submit" class="btn-delete">🗑️ Eliminar</button>
                                    </form>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                <?php else: ?>
                    <div style="text-align: center; padding: 50px; color: #666;">
                        <img src="https://via.placeholder.com/150?text=No+Datos" alt="Sin datos" style="opacity: 0.5;">
                        <p>No hay platos registrados aún.</p>
                        <p>Usa el formulario de la izquierda para agregar tu primer plato.</p>
                    </div>
                <?php endif; ?>
            </div>
        </div>
        
        <!-- Modal de edición -->
        <div id="modalEditar" style="display: none; position: fixed; top: 0; left: 0; width: 100%; height: 100%; background: rgba(0,0,0,0.5); z-index: 1000;">
            <div style="background: white; width: 90%; max-width: 500px; margin: 50px auto; padding: 30px; border-radius: 10px; position: relative; max-height: 90vh; overflow-y: auto;">
                <h2 style="margin-top: 0;">✏️ Editar Plato</h2>
                <form method="POST" id="formEditar">
                    <input type="hidden" name="action" value="actualizar">
                    <input type="hidden" name="id" id="edit_id">
                    
                    <div class="form-group">
                        <label>Nombre del plato:</label>
                        <input type="text" name="nombre" id="edit_nombre" required>
                    </div>
                    
                    <div class="form-group">
                        <label>Precio ($):</label>
                        <input type="number" step="0.01" name="precio" id="edit_precio" required>
                    </div>
                    
                    <div class="form-group">
                        <label>Categoría:</label>
                        <select name="categoria" id="edit_categoria" required>
                            <option value="Plato Principal">Plato Principal</option>
                            <option value="Frutos del Mar">Frutos del Mar</option>
                            <option value="Carnes">Carnes</option>
                            <option value="Vegetariano">Vegetariano</option>
                            <option value="Bebidas">Bebidas</option>
                            <option value="Postres">Postres</option>
                        </select>
                    </div>
                    
                    <div class="form-group">
                        <label>Descripción:</label>
                        <textarea name="descripcion" id="edit_descripcion" required></textarea>
                    </div>
                    
                    <div class="form-group">
                        <label>URL de la foto:</label>
                        <input type="text" name="foto" id="edit_foto">
                    </div>
                    
                    <div style="display: flex; gap: 10px;">
                        <button type="submit" style="background: #2196F3; flex: 2;">Actualizar</button>
                        <button type="button" onclick="cerrarModal()" style="background: #666; flex: 1;">Cancelar</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    
    <script>
        function editarPlato(id, nombre, precio, categoria, descripcion, foto) {
            document.getElementById('edit_id').value = id;
            document.getElementById('edit_nombre').value = nombre;
            document.getElementById('edit_precio').value = precio;
            document.getElementById('edit_categoria').value = categoria;
            document.getElementById('edit_descripcion').value = descripcion;
            document.getElementById('edit_foto').value = foto || '';
            
            document.getElementById('modalEditar').style.display = 'block';
            document.body.style.overflow = 'hidden';
        }
        
        function cerrarModal() {
            document.getElementById('modalEditar').style.display = 'none';
            document.body.style.overflow = 'auto';
        }
        
        // Cerrar modal al hacer clic fuera
        window.onclick = function(event) {
            var modal = document.getElementById('modalEditar');
            if (event.target == modal) {
                modal.style.display = 'none';
                document.body.style.overflow = 'auto';
            }
        }
        
        // Cerrar modal con tecla ESC
        document.addEventListener('keydown', function(event) {
            if (event.key === 'Escape') {
                cerrarModal();
            }
        });
    </script>
</body>
</html>