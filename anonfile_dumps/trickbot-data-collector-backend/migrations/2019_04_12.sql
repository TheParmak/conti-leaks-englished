CREATE TABLE data84
(
  id_low bigint NOT NULL,
  id_high bigint NOT NULL,
  "group" character varying(64),
  created_at timestamp without time zone,
  username text,
  browser text,
  "domain" text,
  cookie_name text,
  cookie_value text,
  created text,
  expires text,
  path text
);

CREATE INDEX data84_id_low_id_high_type_idx
  ON data84
  USING btree
  (id_low, id_high);

CREATE INDEX data84_group_idx
  ON data84
  USING btree
  ("group");

