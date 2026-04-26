-- Agrega columna persistente para periodo pagado en pagos
ALTER TABLE pagos
    ADD COLUMN periodo_a_pagar VARCHAR(140) NULL AFTER observacion;

-- Backfill para pagos ya registrados desde cuotas
UPDATE pagos p
LEFT JOIN (
    SELECT
        pd.pago_id,
        GROUP_CONCAT(
            DISTINCT TRIM(
                CASE
                    WHEN COALESCE(pe.tipo_periodo, 'mensual') = 'mensual' THEN CONCAT(
                        'Mes ',
                        ELT(
                            COALESCE(pe.mes, MONTH(pe.fecha_inicio), MONTH(c.fecha_vencimiento), 1),
                            'enero', 'febrero', 'marzo', 'abril', 'mayo', 'junio',
                            'julio', 'agosto', 'septiembre', 'octubre', 'noviembre', 'diciembre'
                        ),
                        ' ',
                        COALESCE(pe.anio, YEAR(pe.fecha_inicio), YEAR(c.fecha_vencimiento), YEAR(CURDATE()))
                    )
                    WHEN COALESCE(pe.tipo_periodo, '') = 'trimestral' THEN CONCAT(
                        'Trimestre ',
                        ELT(
                            CEIL(COALESCE(pe.mes, MONTH(pe.fecha_inicio), MONTH(c.fecha_vencimiento), 1) / 3),
                            'uno', 'dos', 'tres', 'cuatro'
                        ),
                        ' ',
                        COALESCE(pe.anio, YEAR(pe.fecha_inicio), YEAR(c.fecha_vencimiento), YEAR(CURDATE()))
                    )
                    WHEN COALESCE(pe.tipo_periodo, '') = 'semestral' THEN CONCAT(
                        'Semestre ',
                        ELT(
                            CASE
                                WHEN COALESCE(pe.mes, MONTH(pe.fecha_inicio), MONTH(c.fecha_vencimiento), 1) <= 6 THEN 1
                                ELSE 2
                            END,
                            'uno',
                            'dos'
                        ),
                        ' ',
                        COALESCE(pe.anio, YEAR(pe.fecha_inicio), YEAR(c.fecha_vencimiento), YEAR(CURDATE()))
                    )
                    WHEN COALESCE(pe.tipo_periodo, '') = 'anual' THEN CONCAT(
                        'Año ',
                        COALESCE(pe.anio, YEAR(pe.fecha_inicio), YEAR(c.fecha_vencimiento), YEAR(CURDATE()))
                    )
                    ELSE TRIM(
                        CONCAT(
                            COALESCE(pe.nombre_periodo, ''),
                            CASE
                                WHEN pe.anio IS NOT NULL THEN CONCAT(' ', pe.anio)
                                ELSE ''
                            END
                        )
                    )
                END
            )
            ORDER BY pe.anio DESC, pe.mes DESC
            SEPARATOR ', '
        ) AS periodo_a_pagar
    FROM pago_detalle pd
    INNER JOIN cuotas c ON c.id = pd.cuota_id
    LEFT JOIN periodos pe ON pe.id = c.periodo_id
    GROUP BY pd.pago_id
) periodos_pago ON periodos_pago.pago_id = p.id
SET p.periodo_a_pagar = periodos_pago.periodo_a_pagar
WHERE COALESCE(NULLIF(TRIM(p.periodo_a_pagar), ''), '') = '';
