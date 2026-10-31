-- Ejecutar una sola vez sobre bases de datos existentes.
ALTER TABLE turnos
    ADD COLUMN aviso_admin TEXT NULL;

ALTER TABLE historial_medico
    ADD COLUMN turno_id INT NULL UNIQUE,
    ADD CONSTRAINT fk_historial_medico_turno
        FOREIGN KEY (turno_id) REFERENCES turnos(id) ON DELETE SET NULL;
