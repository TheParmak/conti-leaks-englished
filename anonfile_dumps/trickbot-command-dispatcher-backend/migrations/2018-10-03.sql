CREATE INDEX
   ON commands (incode ASC NULLS LAST, created_at DESC NULLS LAST);

-- Index: commands_created_at_idx

-- DROP INDEX commands_created_at_idx;

CREATE INDEX commands_created_at_idx
  ON commands
  USING btree
  (created_at DESC NULLS LAST);

