CREATE TABLE public.client_blacklist_ip
(
    ip character(50) COLLATE pg_catalog."default" NOT NULL
)

CREATE UNIQUE INDEX "unique"
    ON public.client_blacklist_ip USING btree
    (ip COLLATE pg_catalog."default")
    TABLESPACE pg_default;

