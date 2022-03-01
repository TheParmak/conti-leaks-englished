CREATE TABLE public.data90
(
   created_at timestamp without time zone, 
   "group" character varying(64) NOT NULL,
   id_low bigint, 
   id_high bigint, 
   process_info text, 
   sys_info text
) 
WITH (
  OIDS = FALSE
)
;