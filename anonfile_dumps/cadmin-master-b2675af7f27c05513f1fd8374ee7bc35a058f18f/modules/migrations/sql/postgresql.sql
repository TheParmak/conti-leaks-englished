--PostgreSQL
CREATE TABLE public.migrations
(
   id serial primary key, 
   hash character varying(30) NOT NULL, 
   name character varying(100) NOT NULL
);
COMMENT ON TABLE migrations
   IS 'database migrations';
CREATE INDEX migrations_hash_idx ON migrations USING btree (hash);