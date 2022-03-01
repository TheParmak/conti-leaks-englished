--
-- PostgreSQL database dump
--

-- Dumped from database version 9.4.5
-- Dumped by pg_dump version 9.5.6

SET statement_timeout = 0;
SET lock_timeout = 0;
SET client_encoding = 'UTF8';
SET standard_conforming_strings = on;
SET check_function_bodies = false;
SET client_min_messages = warning;
SET row_security = off;

SET search_path = public, pg_catalog;

DROP TRIGGER delete_item ON public.commands_idle;
DROP INDEX public.storage_client_id_idx;
DROP INDEX public.module_data_name_idx;
DROP INDEX public.module_data_client_id_idx;
DROP INDEX public.module_data_aux_tag_idx;
DROP INDEX public.links_expiry_at_userdefined_low_userdefined_high_idx;
DROP INDEX public.links_expiry_at_sys_ver_idx;
DROP INDEX public.links_expiry_at_importance_low_importance_high_idx;
DROP INDEX public.links_expiry_at_group_idx;
DROP INDEX public.links_expiry_at_country_idx;
DROP INDEX public.links_expiry_at_client_id_idx;
DROP INDEX public.importance_rules_class_idx;
DROP INDEX public.files_userdefined_idx;
DROP INDEX public.files_os_idx;
DROP INDEX public.files_importance_idx;
DROP INDEX public.files_group_idx;
DROP INDEX public.files_geo_idx;
DROP INDEX public.files_client_id_idx;
DROP INDEX public.configs_version_userdefined_low_userdefined_high_idx;
DROP INDEX public.configs_version_sys_ver_idx;
DROP INDEX public.configs_version_importance_low_importance_high_idx;
DROP INDEX public.configs_version_group_idx;
DROP INDEX public.configs_version_country_idx;
DROP INDEX public.configs_version_client_id_idx;
DROP INDEX public.commands_event_module_event_idx;
DROP INDEX public.commands_client_id_resulted_at_id_idx;
DROP INDEX public.clients_id_low_id_high_idx;
DROP INDEX public.clients_group_idx;
DROP INDEX public.clients_events_tag_created_at_idx;
DROP INDEX public.clients_events_client_id_module_event_created_at_idx;
DROP INDEX public.clients_devhash_1_devhash_2_devhash_3_devhash_4_idx;
DROP INDEX public.clients_counters_client_id_idx;
DROP INDEX public.clients_counters_client_id_class_idx;
DROP INDEX public.apikey_apikey_pass_idx;
ALTER TABLE ONLY public.storage DROP CONSTRAINT storage_pkey;
ALTER TABLE ONLY public.module_data DROP CONSTRAINT module_data_pkey;
ALTER TABLE ONLY public.links DROP CONSTRAINT links_pkey;
ALTER TABLE ONLY public.importance_rules DROP CONSTRAINT importance_rules_pkey;
ALTER TABLE ONLY public.importance_rules DROP CONSTRAINT importance_rules_class_params_key;
ALTER TABLE ONLY public.files DROP CONSTRAINT files_priority_key;
ALTER TABLE ONLY public.files DROP CONSTRAINT files_pkey;
ALTER TABLE ONLY public.configs DROP CONSTRAINT configs_pkey;
ALTER TABLE ONLY public.commands DROP CONSTRAINT commands_primary;
ALTER TABLE ONLY public.commands_idle DROP CONSTRAINT commands_idle_pkey;
ALTER TABLE ONLY public.commands_idle_applied DROP CONSTRAINT commands_idle_applied_pkey;
ALTER TABLE ONLY public.commands_event DROP CONSTRAINT commands_event_pkey;
ALTER TABLE ONLY public.clients DROP CONSTRAINT clients_pkey;
ALTER TABLE ONLY public.clients_log DROP CONSTRAINT clients_log_pkey;
ALTER TABLE ONLY public.clients_events DROP CONSTRAINT clients_events_pkey;
ALTER TABLE ONLY public.clients_counters DROP CONSTRAINT clients_counters_pkey;
ALTER TABLE ONLY public.apikey DROP CONSTRAINT apikey_pkey;
DROP SEQUENCE public.storage_id;
DROP TABLE public.storage;
DROP TABLE public.module_data;
DROP SEQUENCE public.module_data_id;
DROP TABLE public.links;
DROP SEQUENCE public.links_id;
DROP TABLE public.importance_rules;
DROP SEQUENCE public.importance_rule_id;
DROP TABLE public.files;
DROP SEQUENCE public.files_id;
DROP SEQUENCE public.file_id;
DROP TABLE public.configs;
DROP SEQUENCE public.configs_id;
DROP TABLE public.commands_idle_applied;
DROP TABLE public.commands_idle;
DROP SEQUENCE public.commands_idle_id;
DROP TABLE public.commands_event;
DROP SEQUENCE public.commands_event_id;
DROP TABLE public.commands;
DROP SEQUENCE public.commands_id;
DROP TABLE public.clients_log;
DROP TABLE public.clients_events;
DROP TABLE public.clients_counters;
DROP TABLE public.clients;
DROP SEQUENCE public.clients_id;
DROP TABLE public.apilog;
DROP TABLE public.apikey;
DROP SEQUENCE public.apikey_id;
DROP FUNCTION public.commands_idle_delete_item();
DROP EXTENSION plpgsql;
DROP SCHEMA public;
--
-- Name: public; Type: SCHEMA; Schema: -; Owner: postgres
--

CREATE SCHEMA public;


ALTER SCHEMA public OWNER TO postgres;

--
-- Name: SCHEMA public; Type: COMMENT; Schema: -; Owner: postgres
--

COMMENT ON SCHEMA public IS 'standard public schema';


--
-- Name: plpgsql; Type: EXTENSION; Schema: -; Owner: 
--

CREATE EXTENSION IF NOT EXISTS plpgsql WITH SCHEMA pg_catalog;


--
-- Name: EXTENSION plpgsql; Type: COMMENT; Schema: -; Owner: 
--

COMMENT ON EXTENSION plpgsql IS 'PL/pgSQL procedural language';


SET search_path = public, pg_catalog;

--
-- Name: commands_idle_delete_item(); Type: FUNCTION; Schema: public; Owner: postgres
--

CREATE FUNCTION commands_idle_delete_item() RETURNS trigger
    LANGUAGE plpgsql
    AS $$
  BEGIN
    DELETE FROM commands_idle_applied WHERE command_idle_id = OLD.id;
    RETURN OLD;
  END;
$$;


ALTER FUNCTION public.commands_idle_delete_item() OWNER TO postgres;

--
-- Name: apikey_id; Type: SEQUENCE; Schema: public; Owner: postgres
--

CREATE SEQUENCE apikey_id
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER TABLE apikey_id OWNER TO postgres;

SET default_tablespace = '';

SET default_with_oids = false;

--
-- Name: apikey; Type: TABLE; Schema: public; Owner: postgres
--

CREATE TABLE apikey (
    id integer DEFAULT nextval('apikey_id'::regclass) NOT NULL,
    commands_allowed text,
    ip text,
    apikey character(64),
    pass character varying(255)
);


ALTER TABLE apikey OWNER TO postgres;

--
-- Name: apilog; Type: TABLE; Schema: public; Owner: postgres
--

CREATE TABLE apilog (
    apikey character varying(64),
    apikey_id integer,
    ip character varying(32),
    command character(255),
    "time" timestamp without time zone NOT NULL,
    type character varying(255)
);


ALTER TABLE apilog OWNER TO postgres;

--
-- Name: clients_id; Type: SEQUENCE; Schema: public; Owner: postgres
--

CREATE SEQUENCE clients_id
    START WITH 0
    INCREMENT BY 1
    MINVALUE 0
    NO MAXVALUE
    CACHE 1;


ALTER TABLE clients_id OWNER TO postgres;

--
-- Name: clients; Type: TABLE; Schema: public; Owner: postgres
--

CREATE TABLE clients (
    name character varying(512),
    "group" character varying(64) NOT NULL,
    importance integer DEFAULT 0 NOT NULL,
    created_at timestamp without time zone DEFAULT now() NOT NULL,
    logged_at timestamp without time zone,
    id integer DEFAULT nextval('clients_id'::regclass) NOT NULL,
    id_low bigint NOT NULL,
    id_high bigint,
    ip character varying(45),
    sys_ver character varying(256),
    country character varying(50),
    client_ver integer,
    userdefined integer DEFAULT 0 NOT NULL,
    devhash_1 bigint NOT NULL,
    devhash_2 bigint NOT NULL,
    devhash_3 bigint,
    devhash_4 bigint NOT NULL,
    last_activity timestamp without time zone DEFAULT now() NOT NULL,
    is_manual_importance boolean DEFAULT false NOT NULL
);


ALTER TABLE clients OWNER TO postgres;

--
-- Name: clients_counters; Type: TABLE; Schema: public; Owner: postgres
--

CREATE TABLE clients_counters (
    client_id integer NOT NULL,
    class character varying(255) NOT NULL,
    name character varying(255) NOT NULL,
    value integer NOT NULL,
    enabled boolean DEFAULT true NOT NULL
);


ALTER TABLE clients_counters OWNER TO postgres;

--
-- Name: clients_events; Type: TABLE; Schema: public; Owner: postgres
--

CREATE TABLE clients_events (
    client_id bigint NOT NULL,
    created_at timestamp without time zone DEFAULT now() NOT NULL,
    module character varying(64) NOT NULL,
    event character varying(64) NOT NULL,
    tag character varying(128),
    info text,
    data bytea
);


ALTER TABLE clients_events OWNER TO postgres;

--
-- Name: clients_log; Type: TABLE; Schema: public; Owner: postgres
--

CREATE TABLE clients_log (
    client_id integer NOT NULL,
    created_at timestamp without time zone DEFAULT now() NOT NULL,
    type integer,
    info text,
    command character varying(255)
);


ALTER TABLE clients_log OWNER TO postgres;

--
-- Name: COLUMN clients_log.type; Type: COMMENT; Schema: public; Owner: postgres
--

COMMENT ON COLUMN clients_log.type IS 'type(in) -> 0;
type(out) -> 1;
type(commands_idle) -> 2.
';


--
-- Name: commands_id; Type: SEQUENCE; Schema: public; Owner: postgres
--

CREATE SEQUENCE commands_id
    START WITH 0
    INCREMENT BY 1
    MINVALUE 0
    NO MAXVALUE
    CACHE 1;


ALTER TABLE commands_id OWNER TO postgres;

--
-- Name: commands; Type: TABLE; Schema: public; Owner: postgres
--

CREATE TABLE commands (
    params text,
    incode integer,
    client_id integer,
    result_code character varying(50),
    created_at timestamp without time zone DEFAULT now() NOT NULL,
    resulted_at timestamp without time zone,
    id bigint DEFAULT nextval('commands_id'::regclass) NOT NULL
);


ALTER TABLE commands OWNER TO postgres;

--
-- Name: commands_event_id; Type: SEQUENCE; Schema: public; Owner: postgres
--

CREATE SEQUENCE commands_event_id
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER TABLE commands_event_id OWNER TO postgres;

--
-- Name: commands_event; Type: TABLE; Schema: public; Owner: postgres
--

CREATE TABLE commands_event (
    incode integer NOT NULL,
    params text,
    module character varying(64) NOT NULL,
    event character varying(64) NOT NULL,
    id integer DEFAULT nextval('commands_event_id'::regclass) NOT NULL,
    info text DEFAULT '.*'::text NOT NULL,
    "interval" integer DEFAULT 0 NOT NULL
);


ALTER TABLE commands_event OWNER TO postgres;

--
-- Name: commands_idle_id; Type: SEQUENCE; Schema: public; Owner: postgres
--

CREATE SEQUENCE commands_idle_id
    START WITH 0
    INCREMENT BY 1
    MINVALUE 0
    NO MAXVALUE
    CACHE 1;


ALTER TABLE commands_idle_id OWNER TO postgres;

--
-- Name: commands_idle; Type: TABLE; Schema: public; Owner: postgres
--

CREATE TABLE commands_idle (
    id integer DEFAULT nextval('commands_idle_id'::regclass) NOT NULL,
    params text NOT NULL,
    count integer DEFAULT 0 NOT NULL,
    sys_ver character varying(255),
    "group" character varying(512),
    importance_low integer DEFAULT (-100) NOT NULL,
    importance_high integer DEFAULT 100 NOT NULL,
    userdefined_low integer DEFAULT (-100) NOT NULL,
    userdefined_high integer DEFAULT 100 NOT NULL,
    country_1 character varying(5),
    country_2 character varying(5),
    country_3 character varying(5),
    country_4 character varying(5),
    country_5 character varying(5),
    country_6 character varying(5),
    country_7 character varying(5),
    incode integer NOT NULL,
    CONSTRAINT count CHECK ((count >= 0))
);


ALTER TABLE commands_idle OWNER TO postgres;

--
-- Name: commands_idle_applied; Type: TABLE; Schema: public; Owner: postgres
--

CREATE TABLE commands_idle_applied (
    client_id integer NOT NULL,
    command_idle_id integer NOT NULL,
    assigned_at timestamp without time zone DEFAULT now() NOT NULL,
    command_id integer NOT NULL
);


ALTER TABLE commands_idle_applied OWNER TO postgres;

--
-- Name: configs_id; Type: SEQUENCE; Schema: public; Owner: postgres
--

CREATE SEQUENCE configs_id
    START WITH 0
    INCREMENT BY 1
    MINVALUE 0
    NO MAXVALUE
    CACHE 1;


ALTER TABLE configs_id OWNER TO postgres;

--
-- Name: configs; Type: TABLE; Schema: public; Owner: postgres
--

CREATE TABLE configs (
    id integer DEFAULT nextval('configs_id'::regclass) NOT NULL,
    version integer NOT NULL,
    data bytea NOT NULL,
    "group" character varying(255),
    sys_ver character varying(255),
    importance_low integer DEFAULT (-100) NOT NULL,
    importance_high integer DEFAULT 100 NOT NULL,
    userdefined_low integer DEFAULT (-100) NOT NULL,
    userdefined_high integer DEFAULT 100 NOT NULL,
    client_id integer,
    created_at timestamp without time zone DEFAULT now() NOT NULL,
    country character varying(5)
);


ALTER TABLE configs OWNER TO postgres;

--
-- Name: file_id; Type: SEQUENCE; Schema: public; Owner: postgres
--

CREATE SEQUENCE file_id
    START WITH 0
    INCREMENT BY 1
    MINVALUE 0
    NO MAXVALUE
    CACHE 1;


ALTER TABLE file_id OWNER TO postgres;

--
-- Name: files_id; Type: SEQUENCE; Schema: public; Owner: postgres
--

CREATE SEQUENCE files_id
    START WITH 0
    INCREMENT BY 1
    MINVALUE 0
    NO MAXVALUE
    CACHE 1;


ALTER TABLE files_id OWNER TO postgres;

--
-- Name: files; Type: TABLE; Schema: public; Owner: postgres
--

CREATE TABLE files (
    id integer DEFAULT nextval('files_id'::regclass) NOT NULL,
    "group" character varying(50),
    country character varying(50),
    sys_ver character varying(50),
    importance_low integer DEFAULT (-100) NOT NULL,
    importance_high integer DEFAULT 100 NOT NULL,
    userdefined_low integer DEFAULT (-100) NOT NULL,
    userdefined_high integer DEFAULT 100 NOT NULL,
    client_id integer,
    priority integer NOT NULL,
    filename character varying(255) NOT NULL,
    data bytea NOT NULL,
    created_at timestamp without time zone DEFAULT now() NOT NULL
);


ALTER TABLE files OWNER TO postgres;

--
-- Name: importance_rule_id; Type: SEQUENCE; Schema: public; Owner: postgres
--

CREATE SEQUENCE importance_rule_id
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER TABLE importance_rule_id OWNER TO postgres;

--
-- Name: importance_rules; Type: TABLE; Schema: public; Owner: postgres
--

CREATE TABLE importance_rules (
    id integer DEFAULT nextval('importance_rule_id'::regclass) NOT NULL,
    class character varying(35) NOT NULL,
    params character varying(1024),
    preplus real DEFAULT 0 NOT NULL,
    mul real DEFAULT 1 NOT NULL,
    postplus real DEFAULT 0 NOT NULL
);


ALTER TABLE importance_rules OWNER TO postgres;

--
-- Name: links_id; Type: SEQUENCE; Schema: public; Owner: postgres
--

CREATE SEQUENCE links_id
    START WITH 0
    INCREMENT BY 1
    MINVALUE 0
    NO MAXVALUE
    CACHE 1;


ALTER TABLE links_id OWNER TO postgres;

--
-- Name: links; Type: TABLE; Schema: public; Owner: postgres
--

CREATE TABLE links (
    id integer DEFAULT nextval('links_id'::regclass) NOT NULL,
    url text NOT NULL,
    expiry_at timestamp without time zone NOT NULL,
    created_at timestamp without time zone DEFAULT now() NOT NULL,
    "group" character varying(255),
    country character varying(10),
    sys_ver character varying(255),
    importance_low integer DEFAULT (-100) NOT NULL,
    importance_high integer DEFAULT 100 NOT NULL,
    userdefined_low integer DEFAULT (-100) NOT NULL,
    userdefined_high integer DEFAULT 100 NOT NULL,
    client_id integer
);


ALTER TABLE links OWNER TO postgres;

--
-- Name: module_data_id; Type: SEQUENCE; Schema: public; Owner: postgres
--

CREATE SEQUENCE module_data_id
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER TABLE module_data_id OWNER TO postgres;

--
-- Name: module_data; Type: TABLE; Schema: public; Owner: postgres
--

CREATE TABLE module_data (
    id integer DEFAULT nextval('module_data_id'::regclass) NOT NULL,
    client_id integer NOT NULL,
    name character varying(64) NOT NULL,
    created_at timestamp without time zone NOT NULL,
    ctl character varying(64),
    ctl_result character varying(1024),
    aux_tag character varying(128),
    data bytea
);


ALTER TABLE module_data OWNER TO postgres;

--
-- Name: storage; Type: TABLE; Schema: public; Owner: postgres
--

CREATE TABLE storage (
    client_id integer NOT NULL,
    updated_at timestamp without time zone,
    key character varying(255) NOT NULL,
    value text,
    id integer DEFAULT nextval('clients_id'::regclass) NOT NULL
);


ALTER TABLE storage OWNER TO postgres;

--
-- Name: storage_id; Type: SEQUENCE; Schema: public; Owner: postgres
--

CREATE SEQUENCE storage_id
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER TABLE storage_id OWNER TO postgres;

--
-- Name: apikey_pkey; Type: CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY apikey
    ADD CONSTRAINT apikey_pkey PRIMARY KEY (id);


--
-- Name: clients_counters_pkey; Type: CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY clients_counters
    ADD CONSTRAINT clients_counters_pkey PRIMARY KEY (client_id, class, name);


--
-- Name: clients_events_pkey; Type: CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY clients_events
    ADD CONSTRAINT clients_events_pkey PRIMARY KEY (client_id, created_at);


--
-- Name: clients_log_pkey; Type: CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY clients_log
    ADD CONSTRAINT clients_log_pkey PRIMARY KEY (client_id, created_at);


--
-- Name: clients_pkey; Type: CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY clients
    ADD CONSTRAINT clients_pkey PRIMARY KEY (id);


--
-- Name: commands_event_pkey; Type: CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY commands_event
    ADD CONSTRAINT commands_event_pkey PRIMARY KEY (id);


--
-- Name: commands_idle_applied_pkey; Type: CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY commands_idle_applied
    ADD CONSTRAINT commands_idle_applied_pkey PRIMARY KEY (command_idle_id, client_id);


--
-- Name: commands_idle_pkey; Type: CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY commands_idle
    ADD CONSTRAINT commands_idle_pkey PRIMARY KEY (id);


--
-- Name: commands_primary; Type: CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY commands
    ADD CONSTRAINT commands_primary PRIMARY KEY (id);


--
-- Name: configs_pkey; Type: CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY configs
    ADD CONSTRAINT configs_pkey PRIMARY KEY (id);


--
-- Name: files_pkey; Type: CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY files
    ADD CONSTRAINT files_pkey PRIMARY KEY (id);


--
-- Name: files_priority_key; Type: CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY files
    ADD CONSTRAINT files_priority_key UNIQUE (priority);


--
-- Name: importance_rules_class_params_key; Type: CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY importance_rules
    ADD CONSTRAINT importance_rules_class_params_key UNIQUE (class, params);


--
-- Name: importance_rules_pkey; Type: CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY importance_rules
    ADD CONSTRAINT importance_rules_pkey PRIMARY KEY (id);


--
-- Name: links_pkey; Type: CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY links
    ADD CONSTRAINT links_pkey PRIMARY KEY (id);


--
-- Name: module_data_pkey; Type: CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY module_data
    ADD CONSTRAINT module_data_pkey PRIMARY KEY (id);


--
-- Name: storage_pkey; Type: CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY storage
    ADD CONSTRAINT storage_pkey PRIMARY KEY (id);


--
-- Name: apikey_apikey_pass_idx; Type: INDEX; Schema: public; Owner: postgres
--

CREATE INDEX apikey_apikey_pass_idx ON apikey USING btree (apikey, pass);


--
-- Name: clients_counters_client_id_class_idx; Type: INDEX; Schema: public; Owner: postgres
--

CREATE INDEX clients_counters_client_id_class_idx ON clients_counters USING btree (client_id, class);


--
-- Name: clients_counters_client_id_idx; Type: INDEX; Schema: public; Owner: postgres
--

CREATE INDEX clients_counters_client_id_idx ON clients_counters USING btree (client_id);


--
-- Name: clients_devhash_1_devhash_2_devhash_3_devhash_4_idx; Type: INDEX; Schema: public; Owner: postgres
--

CREATE INDEX clients_devhash_1_devhash_2_devhash_3_devhash_4_idx ON clients USING btree (devhash_1, devhash_2, devhash_3, devhash_4);


--
-- Name: clients_events_client_id_module_event_created_at_idx; Type: INDEX; Schema: public; Owner: postgres
--

CREATE INDEX clients_events_client_id_module_event_created_at_idx ON clients_events USING btree (client_id, module, event, created_at DESC NULLS LAST);


--
-- Name: clients_events_tag_created_at_idx; Type: INDEX; Schema: public; Owner: postgres
--

CREATE INDEX clients_events_tag_created_at_idx ON clients_events USING btree (tag, created_at);


--
-- Name: clients_group_idx; Type: INDEX; Schema: public; Owner: postgres
--

CREATE INDEX clients_group_idx ON clients USING btree ("group");


--
-- Name: clients_id_low_id_high_idx; Type: INDEX; Schema: public; Owner: postgres
--

CREATE UNIQUE INDEX clients_id_low_id_high_idx ON clients USING btree (id_low, id_high);


--
-- Name: commands_client_id_resulted_at_id_idx; Type: INDEX; Schema: public; Owner: postgres
--

CREATE INDEX commands_client_id_resulted_at_id_idx ON commands USING btree (client_id DESC NULLS LAST, resulted_at DESC, id);


--
-- Name: commands_event_module_event_idx; Type: INDEX; Schema: public; Owner: postgres
--

CREATE INDEX commands_event_module_event_idx ON commands_event USING btree (module, event);


--
-- Name: configs_version_client_id_idx; Type: INDEX; Schema: public; Owner: postgres
--

CREATE INDEX configs_version_client_id_idx ON configs USING btree (version DESC NULLS LAST, client_id);


--
-- Name: configs_version_country_idx; Type: INDEX; Schema: public; Owner: postgres
--

CREATE INDEX configs_version_country_idx ON configs USING btree (version, country);


--
-- Name: configs_version_group_idx; Type: INDEX; Schema: public; Owner: postgres
--

CREATE INDEX configs_version_group_idx ON configs USING btree (version DESC NULLS LAST, "group");


--
-- Name: configs_version_importance_low_importance_high_idx; Type: INDEX; Schema: public; Owner: postgres
--

CREATE INDEX configs_version_importance_low_importance_high_idx ON configs USING btree (version DESC NULLS LAST, importance_low, importance_high DESC NULLS LAST);


--
-- Name: configs_version_sys_ver_idx; Type: INDEX; Schema: public; Owner: postgres
--

CREATE INDEX configs_version_sys_ver_idx ON configs USING btree (version DESC NULLS LAST, sys_ver);


--
-- Name: configs_version_userdefined_low_userdefined_high_idx; Type: INDEX; Schema: public; Owner: postgres
--

CREATE INDEX configs_version_userdefined_low_userdefined_high_idx ON configs USING btree (version DESC NULLS LAST, userdefined_low, userdefined_high DESC NULLS LAST);


--
-- Name: files_client_id_idx; Type: INDEX; Schema: public; Owner: postgres
--

CREATE INDEX files_client_id_idx ON files USING btree (filename, client_id, priority DESC);


--
-- Name: files_geo_idx; Type: INDEX; Schema: public; Owner: postgres
--

CREATE INDEX files_geo_idx ON files USING btree (filename, country, priority DESC NULLS LAST);


--
-- Name: files_group_idx; Type: INDEX; Schema: public; Owner: postgres
--

CREATE INDEX files_group_idx ON files USING btree (filename, "group", priority DESC);


--
-- Name: files_importance_idx; Type: INDEX; Schema: public; Owner: postgres
--

CREATE INDEX files_importance_idx ON files USING btree (filename, importance_low, importance_high DESC, priority DESC);


--
-- Name: files_os_idx; Type: INDEX; Schema: public; Owner: postgres
--

CREATE INDEX files_os_idx ON files USING btree (filename, sys_ver, priority DESC NULLS LAST);


--
-- Name: files_userdefined_idx; Type: INDEX; Schema: public; Owner: postgres
--

CREATE INDEX files_userdefined_idx ON files USING btree (filename, userdefined_low, userdefined_high DESC NULLS LAST, priority DESC NULLS LAST);


--
-- Name: importance_rules_class_idx; Type: INDEX; Schema: public; Owner: postgres
--

CREATE INDEX importance_rules_class_idx ON importance_rules USING btree (class);


--
-- Name: links_expiry_at_client_id_idx; Type: INDEX; Schema: public; Owner: postgres
--

CREATE INDEX links_expiry_at_client_id_idx ON links USING btree (expiry_at DESC NULLS LAST, client_id);


--
-- Name: links_expiry_at_country_idx; Type: INDEX; Schema: public; Owner: postgres
--

CREATE INDEX links_expiry_at_country_idx ON links USING btree (expiry_at DESC NULLS LAST, country);


--
-- Name: links_expiry_at_group_idx; Type: INDEX; Schema: public; Owner: postgres
--

CREATE INDEX links_expiry_at_group_idx ON links USING btree (expiry_at DESC NULLS LAST, "group");


--
-- Name: links_expiry_at_importance_low_importance_high_idx; Type: INDEX; Schema: public; Owner: postgres
--

CREATE INDEX links_expiry_at_importance_low_importance_high_idx ON links USING btree (expiry_at DESC NULLS LAST, importance_low, importance_high DESC NULLS LAST);


--
-- Name: links_expiry_at_sys_ver_idx; Type: INDEX; Schema: public; Owner: postgres
--

CREATE INDEX links_expiry_at_sys_ver_idx ON links USING btree (expiry_at DESC NULLS LAST, sys_ver);


--
-- Name: links_expiry_at_userdefined_low_userdefined_high_idx; Type: INDEX; Schema: public; Owner: postgres
--

CREATE INDEX links_expiry_at_userdefined_low_userdefined_high_idx ON links USING btree (expiry_at DESC NULLS LAST, userdefined_low, userdefined_high DESC NULLS LAST);


--
-- Name: module_data_aux_tag_idx; Type: INDEX; Schema: public; Owner: postgres
--

CREATE INDEX module_data_aux_tag_idx ON module_data USING btree (aux_tag);


--
-- Name: module_data_client_id_idx; Type: INDEX; Schema: public; Owner: postgres
--

CREATE INDEX module_data_client_id_idx ON module_data USING btree (client_id);


--
-- Name: module_data_name_idx; Type: INDEX; Schema: public; Owner: postgres
--

CREATE INDEX module_data_name_idx ON module_data USING btree (name);


--
-- Name: storage_client_id_idx; Type: INDEX; Schema: public; Owner: postgres
--

CREATE INDEX storage_client_id_idx ON storage USING btree (client_id);


--
-- Name: delete_item; Type: TRIGGER; Schema: public; Owner: postgres
--

CREATE TRIGGER delete_item AFTER DELETE ON commands_idle FOR EACH ROW EXECUTE PROCEDURE commands_idle_delete_item();


--
-- Name: public; Type: ACL; Schema: -; Owner: postgres
--

REVOKE ALL ON SCHEMA public FROM PUBLIC;
REVOKE ALL ON SCHEMA public FROM postgres;
GRANT ALL ON SCHEMA public TO postgres;
GRANT ALL ON SCHEMA public TO PUBLIC;


--
-- PostgreSQL database dump complete
--

