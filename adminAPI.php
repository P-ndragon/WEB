<?php
header('Content-Type: application/json; charset=utf-8');
include("conexion.php");
include("producto.php");

switch($_SERVER['REQUEST_METHOD']) {
    case 'POST':
        _post();
        break;
    case 'GET':
        _get();
        break;
    case 'PUT':
        _put();
        break;
    case 'DELETE':
        _delete();
        break;
    default:
        header('HTTP/1.1 405 Method Not Allowed');
        header('Allow: GET, POST, PUT, DELETE');
        break;
}

function _post() {
    global $mysqli;
    $respuesta = array('status' => 0);

    if (isset($_POST['nombre_producto']) && isset($_POST['precio_producto']) && isset($_POST['descripcion']) && isset($_FILES['archivo'])) {
        $nombre_producto = $_POST['nombre_producto'];
        $precio_producto = $_POST['precio_producto'];
        $descripcion = $_POST['descripcion'];
        $archivo = file_get_contents($_FILES['archivo']['tmp_name']);

        try {
            $stmt = $mysqli->prepare("INSERT INTO productos (nombre_producto, precio_producto, descripcion, archivo) VALUES (?, ?, ?, ?)");
            $stmt->bind_param("ssss", $nombre_producto, $precio_producto, $descripcion, $archivo);
            $stmt->execute();
            $stmt->close();
            $respuesta['status'] = 1;
        } catch (Exception $e) {
            $respuesta['error'] = "Exception: " . $e->getMessage();
        } finally {
            $mysqli->close();
        }
    }
    echo json_encode($respuesta);
}

function _get() {
    global $mysqli;
    $respuesta = array();

    try {
        if (isset($_GET['id'])) {
            $stmt = $mysqli->prepare("SELECT id_producto, nombre_producto, precio_producto, descripcion, archivo FROM productos WHERE id_producto = ?");
            $stmt->bind_param("i", $_GET['id']);
        } else {
            $stmt = $mysqli->prepare("SELECT id_producto, nombre_producto, precio_producto, descripcion, archivo FROM productos");
        }
        $stmt->execute();
        $stmt->bind_result($id_producto, $nombre_producto, $precio_producto, $descripcion, $archivo);

        while ($stmt->fetch()) {
            $producto = new producto($id_producto, $nombre_producto, $precio_producto, $descripcion, base64_encode($archivo));
            array_push($respuesta, $producto);
        }

        $stmt->close();
    } catch (Exception $e) {
        $respuesta['error'] = "Exception: " . $e->getMessage();
    } finally {
        $mysqli->close();
    }
    echo json_encode($respuesta);
}

function _put() {
    global $mysqli;
    $respuesta = array('status' => 0);
    parse_str(file_get_contents("php://input"), $_PUT);

    if (isset($_GET['id']) && isset($_PUT['nombre_producto']) && isset($_PUT['precio_producto']) && isset($_PUT['descripcion'])) {
        $id_producto = $_GET['id'];
        $nombre_producto = $_PUT['nombre_producto'];
        $precio_producto = $_PUT['precio_producto'];
        $descripcion = $_PUT['descripcion'];
        $archivo = isset($_FILES['archivo']) ? file_get_contents($_FILES['archivo']['tmp_name']) : null;

        if ($archivo) {
            $stmt = $mysqli->prepare("UPDATE productos SET nombre_producto = ?, precio_producto = ?, descripcion = ?, archivo = ? WHERE id_producto = ?");
            $stmt->bind_param("ssssi", $nombre_producto, $precio_producto, $descripcion, $archivo, $id_producto);
        } else {
            $stmt = $mysqli->prepare("UPDATE productos SET nombre_producto = ?, precio_producto = ?, descripcion = ? WHERE id_producto = ?");
            $stmt->bind_param("sssi", $nombre_producto, $precio_producto, $descripcion, $id_producto);
        }

        try {
            $stmt->execute();
            $stmt->close();
            $respuesta['status'] = 1;
        } catch (Exception $e) {
            $respuesta['error'] = "Exception: " . $e->getMessage();
        } finally {
            $mysqli->close();
        }
    }
    echo json_encode($respuesta);
}

function _delete() {
    global $mysqli;
    $respuesta = array('status' => 0);

    if (isset($_GET['id'])) {
        try {
            $stmt = $mysqli->prepare("DELETE FROM productos WHERE id_producto = ?");
            $stmt->bind_param("i", $_GET['id']);
            $stmt->execute();
            $stmt->close();
            $respuesta['status'] = 1;
        } catch (Exception $e) {
            $respuesta['error'] = "Exception: " . $e->getMessage();
        } finally {
            $mysqli->close();
        }
    }
    echo json_encode($respuesta);
}
?>
