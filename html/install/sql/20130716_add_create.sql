CREATE TABLE dtb_shukka_jisseki (
    order_id int NOT NULL,
    customer_id int NOT NULL,
    query_number text,
    send_flag smallint NOT NULL DEFAULT 0,
    create_date timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
    update_date timestamp NOT NULL
) ENGINE=InnoDB;

