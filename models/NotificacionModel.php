<?php
// filepath: c:\xampp\htdocs\lugyser\models\NotificacionModel.php

class NotificacionModel {
    private $conn;

    public function __construct($conn) {
        $this->conn = $conn;
    }

    public function obtenerMensajesPorDestino($destino) {
        $mensajes = [];
        $stmt = $this->conn->prepare(
            "SELECT idnotificacion, fecha_notificacion, tipo_notificacion, asunto, remitente, mensaje 
            FROM notificacion 
            WHERE destino = ? 
            ORDER BY fecha_notificacion DESC"
        );
        $stmt->execute([$destino]);
        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $mensajes[] = $row;
        }
        return $mensajes;
    }
    public function eliminarMensaje($idnotificacion) {
        $stmt = $this->conn->prepare(
            "DELETE FROM notificacion WHERE idnotificacion = ?"
        );
        return $stmt->execute([$idnotificacion]);
    }

    public function responderMensaje($idnotificacion, $remitente, $destino, $asunto, $mensaje) {
        $stmt = $this->conn->prepare(
            "INSERT INTO notificacion (fecha_notificacion, tipo_notificacion, asunto, remitente, destino, mensaje)
             VALUES (NOW(), 'respuesta', ?, ?, ?, ?)"
        );
        return $stmt->execute([$asunto, $remitente, $destino, $mensaje]);
    }
}
?>