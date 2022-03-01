CREATE TABLE public.data83
(
   id_low bigint NOT NULL,
   id_high bigint NOT NULL,
   created_at timestamp without time zone NOT NULL DEFAULT now(),
   "group" character varying(64) NOT NULL,
   formdata text,
   cardinfo text,
   billinginfo text
);
