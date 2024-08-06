<?php
// Definir que la respuesta a la petición es un JSON y no HTML o texto plano
header('Content-Type: application/json; charset=utf-8');

// Incluir la clase Producto
include("producto.php");

// Obtener el contenido de la petición
$postBody = file_get_contents("php://input");
$data = json_decode($postBody);

// Procesar la solicitud según el método HTTP
switch($_SERVER['REQUEST_METHOD']) {
    case 'POST':  // Create
        _post($data);
        break;
    case 'GET':   // Read
        _get();
        break;
    case 'PUT':   // Update
        _puts($data);
        break;
    case 'DELETE':   // Delete
        _delete($data);
        break;
    default:
        header('HTTP/1.1 405 Method Not Allowed');
        header('Allow: GET, PUT, DELETE, POST');
        break;  
}

// Rutina para Create
function _post($datos) {
    $respuesta = 0;
    include("./conn/conexion.php");

    try {
        $stmt = $mysqli->prepare("INSERT INTO productos (nombre_producto, precio_producto, descripcion, archivo) VALUES (?, ?, ?, './lentes1.jpg');");
        if ($stmt) {
            $stmt->bind_param("sss", $datos->nombre_producto, $datos->precio_producto, $datos->descripcion);
            $stmt->execute();
            $stmt->close();
            $respuesta = 1;
        } else {
            echo json_encode(["error" => "No disponible"]);
        }
    } catch (Exception $e) {
        echo json_encode(["error" => "Exception: " . $e->getMessage()]);
    } finally {
        $mysqli->close();
    }
    echo json_encode($respuesta);
}

// Rutina para Read
function _get() {
    include("conexion.php");
    $respuesta = [];
    $stmt = null;

    try {
        if (isset($_GET["producto"])) {
            $stmt = $mysqli->prepare("SELECT id_producto, nombre_producto, precio_producto, descripcion, archivo FROM productos WHERE id_producto = ?;");
            $stmt->bind_param("i", $_GET['producto']);
        } else {
            $stmt = $mysqli->prepare("SELECT id_producto, nombre_producto, precio_producto, descripcion, archivo FROM productos;");
        }

        if ($stmt) {
            $stmt->execute();
            $stmt->bind_result($id_producto, $nombre_producto, $precio_producto, $descripcion, $archivo);
            while ($stmt->fetch()) {
                $registro_producto = new Producto($id_producto, $nombre_producto, $precio_producto, $descripcion, $archivo);
                array_push($respuesta, $registro_producto);
            }
            $stmt->close();
        } else {
            echo json_encode(["error" => "No disponible"]);
        }
    } catch (Exception $e) {
        echo json_encode(["error" => "Exception: " . $e->getMessage()]);
    } finally {
        $mysqli->close();
    }
    echo json_encode($respuesta);
}

// Rutina para Update
function _puts($datos) {
    $respuesta = 0;

    if (isset($datos->producto) && isset($datos->nombre_producto) && isset($datos->precio_producto) && isset($datos->descripcion)) {
        include("./conn/conexion.php");
        try {
            $stmt = $mysqli->prepare("UPDATE productos SET nombre_producto = ?, precio_producto = ?, descripcion = ? WHERE id_producto = ?;");
            if ($stmt) {
                $stmt->bind_param("sssi", $datos->nombre_producto, $datos->precio_producto, $datos->descripcion, $datos->producto);
                $stmt->execute();
                $stmt->close();
                $respuesta = 1;
            } else {
                echo json_encode(["error" => "No disponible"]);
            }
        } catch (Exception $e) {
            echo json_encode(["error" => "Exception: " . $e->getMessage()]);
        } finally {
            $mysqli->close();
        }
    } else {
        echo json_encode(["error" => "Sin datos"]);
        die();
    }
    echo json_encode($respuesta);
}

// Rutina para Delete
function _delete($datos) {
    $respuesta = 0;

    if (isset($datos->producto)) {
        include("./conn/conexion.php");
        try {
            $stmt = $mysqli->prepare("DELETE FROM productos WHERE id_producto = ?;");
            if ($stmt) {
                $stmt->bind_param("i", $datos->producto);
                $stmt->execute();
                $stmt->close();
                $respuesta = 1;
            } else {
                echo json_encode(["error" => "No disponible"]);
            }
        } catch (Exception $e) {
            echo json_encode(["error" => "Exception: " . $e->getMessage()]);
        } finally {
            $mysqli->close();
        }
    } else {
        echo json_encode(["error" => "Sin datos"]);
        die();
    }
    echo json_encode($respuesta);
}

?>
