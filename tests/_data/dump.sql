--
-- PostgreSQL database dump
--

SET statement_timeout = 0;
SET lock_timeout = 0;
SET client_encoding = 'UTF8';
SET standard_conforming_strings = on;
SET check_function_bodies = false;
SET client_min_messages = warning;

--
-- Name: plpgsql; Type: EXTENSION; Schema: -; Owner: 
--

CREATE EXTENSION IF NOT EXISTS plpgsql WITH SCHEMA pg_catalog;


--
-- Name: EXTENSION plpgsql; Type: COMMENT; Schema: -; Owner: 
--

COMMENT ON EXTENSION plpgsql IS 'PL/pgSQL procedural language';


SET search_path = public, pg_catalog;

SET default_tablespace = '';

SET default_with_oids = false;

--
-- Name: multispider; Type: TABLE; Schema: public; Owner: multispider; Tablespace: 
--

CREATE TABLE multispider (
    id integer NOT NULL,
    path character varying(4095),
    mask character varying(150)
);


ALTER TABLE multispider OWNER TO multispider;

--
-- Name: multispider_id_seq; Type: SEQUENCE; Schema: public; Owner: multispider
--

CREATE SEQUENCE multispider_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER TABLE multispider_id_seq OWNER TO multispider;

--
-- Name: multispider_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: multispider
--

ALTER SEQUENCE multispider_id_seq OWNED BY multispider.id;


--
-- Name: id; Type: DEFAULT; Schema: public; Owner: multispider
--

ALTER TABLE ONLY multispider ALTER COLUMN id SET DEFAULT nextval('multispider_id_seq'::regclass);


--
-- Name: multispider_id_seq; Type: SEQUENCE SET; Schema: public; Owner: multispider
--

SELECT pg_catalog.setval('multispider_id_seq', 1, false);


--
-- Name: multispider_pkey; Type: CONSTRAINT; Schema: public; Owner: multispider; Tablespace: 
--

ALTER TABLE ONLY multispider
    ADD CONSTRAINT multispider_pkey PRIMARY KEY (id);


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

