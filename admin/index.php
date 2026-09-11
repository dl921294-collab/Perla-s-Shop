<?php
session_start();
require_once '../conexion.php';

// CONTRASEÑA DE ACCESO (Puedes cambiarla aquí)
$PASSWORD_CORRECTA = "admin123";

$mensaje = "";
$tipo_mensaje = "";

// --- 1. PROCESAR LOGIN ---
if (isset($_POST['login'])) {
    if ($_POST['password'] === $PASSWORD_CORRECTA) {
        $_SESSION['admin_logged'] = true;
    } else {
        $mensaje = "Contraseña incorrecta.";
        $tipo_mensaje = "error";
    }
}

// --- 2. PROCESAR LOGOUT ---
if (isset($_GET['logout'])) {
    unset($_SESSION['admin_logged']);
    session_destroy();
    header("Location: index.php");
    exit;
}

// --- 3. ACCIONES DE ADMINISTRADOR ---
if (isset($_SESSION['admin_logged']) && $_SESSION['admin_logged'] === true) {

    // Agregar producto
    if (isset($_POST['agregar_producto'])) {
        $nombre = trim($_POST['nombre']);
        $descripcion = trim($_POST['descripcion']);
        $precio = floatval($_POST['precio']);
        $stock = intval($_POST['stock']);
        $imagen_nombre = 'default.jpg';

        // Subida de imagen
        if (isset($_FILES['imagen']) && $_FILES['imagen']['error'] === UPLOAD_ERR_OK) {
            $ext = pathinfo($_FILES['imagen']['name'], PATHINFO_EXTENSION);
            $imagen_nombre = time() . '_' . uniqid() . '.' . $ext;
            
            $dir_destino = '../assets/img/';
            if (!file_exists($dir_destino)) {
                mkdir($dir_destino, 0777, true);
            }
            move_uploaded_file($_FILES['imagen']['tmp_name'], $dir_destino . $imagen_nombre);
        }

        $sql = "INSERT INTO productos (nombre, descripcion, precio, stock, imagen) VALUES (?, ?, ?, ?, ?)";
        $stmt = $pdo->prepare($sql);
        if ($stmt->execute([$nombre, $descripcion, $precio, $stock, $imagen_nombre])) {
            $mensaje = "Producto publicado con éxito.";
            $tipo_mensaje = "exito";
        }
    }

    // Actualizar Producto (Nombre, Precio y Stock)
    if (isset($_POST['actualizar_producto'])) {
        $id = intval($_POST['id_producto']);
        $nombre = trim($_POST['nombre']);
        $precio = floatval($_POST['precio']);
        $stock = intval($_POST['nuevo_stock']);

        $stmt = $pdo->prepare("UPDATE productos SET nombre = ?, precio = ?, stock = ? WHERE id = ?");
        if ($stmt->execute([$nombre, $precio, $stock, $id])) {
            $mensaje = "Producto actualizado correctamente.";
            $tipo_mensaje = "exito";
        }
    }

    // Eliminar Producto
    if (isset($_POST['eliminar_producto'])) {
        $id = intval($_POST['id_producto']);
        $stmt = $pdo->prepare("DELETE FROM productos WHERE id = ?");
        if ($stmt->execute([$id])) {
            $mensaje = "Producto eliminado correctamente.";
            $tipo_mensaje = "exito";
        }
    }

    // Consultar todos los productos
    $productos = $pdo->query("SELECT * FROM productos ORDER BY id DESC")->fetchAll();
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Panel de Administración | Perla's Shop</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

    <style>
        :root {
            --primary-color: #e84393;
            --dark-color: #2d3436;
            --light-bg: #f4f6f9;
            --white: #ffffff;
            --success: #00b894;
            --danger: #d63031;
        }

        * { margin: 0; padding: 0; box-sizing: border-box; font-family: 'Poppins', sans-serif; }
        body { background-color: var(--light-bg); color: var(--dark-color); }

        /* --- Pantalla de Login --- */
        .login-container {
            max-width: 400px;
            margin: 6rem auto;
            background: var(--white);
            padding: 2.5rem;
            border-radius: 12px;
            box-shadow: 0 4px 20px rgba(0,0,0,0.1);
            text-align: center;
        }
        .login-container h2 { margin-bottom: 1.5rem; color: var(--primary-color); }
        .form-group { margin-bottom: 1.2rem; text-align: left; }
        .form-group label { display: block; margin-bottom: 0.4rem; font-weight: 600; font-size: 0.9rem; }
        .form-control {
            width: 100%;
            padding: 0.75rem;
            border: 1px solid #ddd;
            border-radius: 6px;
            font-size: 1rem;
        }
        .btn-primary {
            width: 100%;
            background: var(--primary-color);
            color: var(--white);
            border: none;
            padding: 0.8rem;
            border-radius: 6px;
            font-weight: 600;
            cursor: pointer;
        }
        .btn-primary:hover { background: #d63031; }

        /* --- Navigation Admin --- */
        .admin-nav {
            background: var(--dark-color);
            color: var(--white);
            padding: 1rem 2rem;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        .admin-nav a { color: var(--white); text-decoration: none; font-weight: 500; }

        /* --- Dashboard Grid --- */
        .admin-container { max-width: 1200px; margin: 2rem auto; padding: 0 1.5rem; }
        .alert {
            padding: 1rem;
            border-radius: 6px;
            margin-bottom: 1.5rem;
            font-weight: 600;
        }
        .alert.exito { background: #e6fffa; color: var(--success); border: 1px solid var(--success); }
        .alert.error { background: #ffe3e3; color: var(--danger); border: 1px solid var(--danger); }

        .dashboard-grid {
            display: grid;
            grid-template-columns: 350px 1fr;
            gap: 2rem;
        }

        @media (max-width: 900px) {
            .dashboard-grid { grid-template-columns: 1fr; }
        }

        .card { background: var(--white); padding: 1.5rem; border-radius: 10px; box-shadow: 0 2px 10px rgba(0,0,0,0.05); }
        .card h3 { margin-bottom: 1rem; color: var(--primary-color); font-size: 1.2rem; }

        /* --- Tabla de Productos --- */
        table { width: 100%; border-collapse: collapse; margin-top: 1rem; }
        th, td { padding: 0.75rem 0.5rem; text-align: left; border-bottom: 1px solid #eee; font-size: 0.9rem; vertical-align: middle; }
        th { background-color: #fafafa; font-weight: 600; }
        .img-thumb { width: 50px; height: 50px; object-fit: cover; border-radius: 6px; }
        
        /* Inputs dentro de la tabla */
        .tbl-input {
            padding: 0.4rem;
            border: 1px solid #ccc;
            border-radius: 4px;
            font-size: 0.88rem;
        }
        .input-nombre { width: 100%; min-width: 120px; font-weight: 600; }
        .input-precio { width: 85px; }
        .input-stock { width: 60px; text-align: center; }

        .action-btns { display: flex; gap: 0.4rem; align-items: center; }
        .btn-small { padding: 0.4rem 0.6rem; border: none; border-radius: 4px; cursor: pointer; color: var(--white); }
        .btn-update { background: var(--success); }
        .btn-delete { background: var(--danger); }
    </style>
</head>
<body>

<?php if (!isset($_SESSION['admin_logged'])): ?>

    <!-- FORMULARIO DE INICIO DE SESIÓN -->
    <div class="login-container">
        <h2><i class="fa-solid fa-lock"></i> Acceso Admin</h2>
        
        <?php if ($mensaje): ?>
            <div class="alert <?php echo $tipo_mensaje; ?>"><?php echo $mensaje; ?></div>
        <?php endif; ?>

        <form method="POST" action="">
            <div class="form-group">
                <label>Contraseña de Acceso:</label>
                <input type="password" name="password" class="form-control" placeholder="Ingresa la contraseña" required>
            </div>
            <button type="submit" name="login" class="btn-primary">Ingresar al Panel</button>
        </form>
        <p style="margin-top: 1rem;"><a href="../index.php" style="color: #666; text-decoration: none;">&larr; Volver a la Tienda</a></p>
    </div>

<?php else: ?>

    <!-- PANEL DE ADMINISTRACIÓN -->
    <nav class="admin-nav">
        <h2>Perla's Shop | Panel de Control</h2>
        <div>
            <a href="../index.php" style="margin-right: 1.5rem;"><i class="fa-solid fa-store"></i> Ver Tienda</a>
            <a href="?logout=1" style="color: #ff7675;"><i class="fa-solid fa-right-from-bracket"></i> Cerrar Sesión</a>
        </div>
    </nav>

    <div class="admin-container">
        
        <?php if ($mensaje): ?>
            <div class="alert <?php echo $tipo_mensaje; ?>"><?php echo $mensaje; ?></div>
        <?php endif; ?>

        <div class="dashboard-grid">
            
            <!-- FORMULARIO PARA SUBIR PRODUCTO -->
            <div class="card">
                <h3><i class="fa-solid fa-plus-circle"></i> Agregar Producto</h3>
                <form method="POST" action="" enctype="multipart/form-data">
                    <div class="form-group">
                        <label>Nombre del Producto:</label>
                        <input type="text" name="nombre" class="form-control" required>
                    </div>
                    <div class="form-group">
                        <label>Descripción:</label>
                        <textarea name="descripcion" class="form-control" rows="3"></textarea>
                    </div>
                    <div class="form-group">
                        <label>Precio (L.):</label>
                        <input type="number" step="0.01" name="precio" class="form-control" required>
                    </div>
                    <div class="form-group">
                        <label>Stock Inicial:</label>
                        <input type="number" name="stock" class="form-control" value="1" min="0" required>
                    </div>
                    <div class="form-group">
                        <label>Imagen del Producto:</label>
                        <input type="file" name="imagen" class="form-control" accept="image/*">
                    </div>
                    <button type="submit" name="agregar_producto" class="btn-primary">Publicar Producto</button>
                </form>
            </div>

            <!-- TABLA DE CONTROL DE PRODUCTOS Y STOCK -->
            <div class="card">
                <h3><i class="fa-solid fa-boxes-stacked"></i> Inventario de Productos</h3>
                
                <?php if (!empty($productos)): ?>
                    <table>
                        <thead>
                            <tr>
                                <th>Imagen</th>
                                <th>Producto</th>
                                <th>Precio (L.)</th>
                                <th>Stock</th>
                                <th>Acción</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($productos as $prod): ?>
                                <?php $form_id = "form-edit-" . $prod['id']; ?>
                                <tr>
                                    <!-- Formulario invisible que conecta los campos de la fila mediante el atributo form -->
                                    <form id="<?php echo $form_id; ?>" method="POST" action=""></form>

                                    <td>
                                        <?php 
                                            $imgSrc = "../assets/img/" . $prod['imagen'];
                                            if (!file_exists($imgSrc) || empty($prod['imagen'])) {
                                                $imgSrc = "https://via.placeholder.com/50";
                                            }
                                        ?>
                                        <img src="<?php echo $imgSrc; ?>" class="img-thumb" alt="Img">
                                    </td>
                                    <td>
                                        <input type="text" form="<?php echo $form_id; ?>" name="nombre" value="<?php echo htmlspecialchars($prod['nombre']); ?>" class="tbl-input input-nombre" required>
                                    </td>
                                    <td>
                                        <input type="number" step="0.01" form="<?php echo $form_id; ?>" name="precio" value="<?php echo $prod['precio']; ?>" class="tbl-input input-precio" required>
                                    </td>
                                    <td>
                                        <input type="number" form="<?php echo $form_id; ?>" name="nuevo_stock" value="<?php echo $prod['stock']; ?>" class="tbl-input input-stock" min="0" required>
                                    </td>
                                    <td>
                                        <div class="action-btns">
                                            <input type="hidden" form="<?php echo $form_id; ?>" name="id_producto" value="<?php echo $prod['id']; ?>">
                                            <button type="submit" form="<?php echo $form_id; ?>" name="actualizar_producto" class="btn-small btn-update" title="Guardar Cambios">
                                                <i class="fa-solid fa-floppy-disk"></i>
                                            </button>
                                            
                                            <!-- Formulario independiente para eliminar -->
                                            <form method="POST" action="" onsubmit="return confirm('¿Eliminar este producto?');">
                                                <input type="hidden" name="id_producto" value="<?php echo $prod['id']; ?>">
                                                <button type="submit" name="eliminar_producto" class="btn-small btn-delete" title="Eliminar">
                                                    <i class="fa-solid fa-trash"></i>
                                                </button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                <?php else: ?>
                    <p>No hay productos registrados en la base de datos.</p>
                <?php endif; ?>
            </div>

        </div>
    </div>

<?php endif; ?>

</body>
</html>