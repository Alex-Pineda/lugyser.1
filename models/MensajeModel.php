<?php

class MensajeModel {
    private $db;

    public function __construct($db) {
        $this->db = $db;
    }

    public function eliminarMensaje($id) {
        $stmt = $this->db->prepare("DELETE FROM notificacion WHERE idnotificacion = ?");
        $stmt->bind_param("i", $id);
        return $stmt->execute();
    }

    public function responderMensaje($id, $respuesta) {
    $stmt = $this->db->prepare("UPDATE notificacion SET respuesta = ?, respondido = 1 WHERE idnotificacion = ?");
    $stmt->bind_param("si", $respuesta, $id);
    return $stmt->execute();
    }
    // Puedes agregar otros métodos aquí según lo necesites
}
?>