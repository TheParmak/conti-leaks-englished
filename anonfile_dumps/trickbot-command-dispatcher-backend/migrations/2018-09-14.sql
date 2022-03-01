-- Table: storage_last

-- DROP TABLE storage_last;

CREATE TABLE storage_last
(
  client_id bigint NOT NULL,
  key text NOT NULL,
  value text,
  updated_at timestamp without time zone NOT NULL,
  CONSTRAINT storage_last_pkey PRIMARY KEY (client_id, key)
)
WITH (
  OIDS=FALSE
);
ALTER TABLE storage_last
  OWNER TO postgres;

-- Index: storage_last_client_id_idx

-- DROP INDEX storage_last_client_id_idx;

CREATE INDEX storage_last_client_id_idx
  ON storage_last
  USING btree
  (client_id DESC NULLS LAST);



  