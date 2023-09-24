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

--
-- Name: tree; Type: SCHEMA; Schema: -; Owner: postgres
--

CREATE SCHEMA tree;

ALTER SCHEMA tree OWNER TO postgres;

SET default_tablespace = '';

SET default_table_access_method = heap;

--
-- Name: tree; Type: TABLE; Schema: tree; Owner: postgres
--

CREATE TABLE tree.tree (
    id integer NOT NULL,
    pid bigint DEFAULT 0 NOT NULL,
    name character varying DEFAULT ''::character varying NOT NULL
);


ALTER TABLE tree.tree OWNER TO postgres;

--
-- Name: tree_id_seq; Type: SEQUENCE; Schema: tree; Owner: postgres
--

CREATE SEQUENCE tree.tree_id_seq
    AS integer
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER TABLE tree.tree_id_seq OWNER TO postgres;

--
-- Name: tree_id_seq; Type: SEQUENCE OWNED BY; Schema: tree; Owner: postgres
--

ALTER SEQUENCE tree.tree_id_seq OWNED BY tree.tree.id;


--
-- Name: tree id; Type: DEFAULT; Schema: tree; Owner: postgres
--

ALTER TABLE ONLY tree.tree ALTER COLUMN id SET DEFAULT nextval('tree.tree_id_seq'::regclass);


--
-- Name: tree_id_seq; Type: SEQUENCE SET; Schema: tree; Owner: postgres
--

SELECT pg_catalog.setval('tree.tree_id_seq', 71, true);


--
-- Name: tree tree_un; Type: CONSTRAINT; Schema: tree; Owner: postgres
--

ALTER TABLE ONLY tree.tree
    ADD CONSTRAINT tree_un UNIQUE (id);


--
-- Name: tree_pid_idx; Type: INDEX; Schema: tree; Owner: postgres
--

CREATE INDEX tree_pid_idx ON tree.tree USING btree (pid);


--
-- PostgreSQL database dump complete
--
