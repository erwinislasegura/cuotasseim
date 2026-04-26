-- 025_add_flow_checkout_config
ALTER TABLE configuracion
    ADD COLUMN flow_checkout_activo TINYINT(1) NOT NULL DEFAULT 0 AFTER sitio_web,
    ADD COLUMN flow_api_key VARCHAR(120) NULL AFTER flow_checkout_activo,
    ADD COLUMN flow_secret_key VARCHAR(140) NULL AFTER flow_api_key,
    ADD COLUMN flow_modo_sandbox TINYINT(1) NOT NULL DEFAULT 1 AFTER flow_secret_key,
    ADD COLUMN flow_url_confirmacion VARCHAR(255) NULL AFTER flow_modo_sandbox,
    ADD COLUMN flow_url_retorno VARCHAR(255) NULL AFTER flow_url_confirmacion;
