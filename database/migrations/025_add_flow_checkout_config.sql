-- 025_add_flow_checkout_config
ALTER TABLE configuracion
    ADD COLUMN flow_api_key VARCHAR(120) NULL AFTER sitio_web,
    ADD COLUMN flow_secret_key VARCHAR(140) NULL AFTER flow_api_key,
    ADD COLUMN flow_modo_sandbox TINYINT(1) NOT NULL DEFAULT 1 AFTER flow_secret_key;
