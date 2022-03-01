-- Table: data80

-- DROP TABLE data80;

CREATE TABLE data80
(
  id_low bigint NOT NULL,
  id_high bigint NOT NULL,
  "group" character varying(64),
  os character varying(64),
  os_ver character varying(25),
  data bytea,
  source character varying(1024),
  created_at timestamp without time zone,
  type integer NOT NULL
)
WITH (
  OIDS=FALSE
);

CREATE INDEX data80_id_low_id_high_type_idx
  ON data80
  USING btree
  (id_low, id_high, type);