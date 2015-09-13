--
-- PostgreSQL database dump
--

-- Dumped from database version 9.5alpha1
-- Dumped by pg_dump version 9.5alpha1

SET statement_timeout = 0;
SET lock_timeout = 0;
SET client_encoding = 'UTF8';
SET standard_conforming_strings = on;
SET check_function_bodies = false;
SET client_min_messages = warning;

--
-- Name: omer-mdata; Type: SCHEMA; Schema: -; Owner: omer-mdata
--

CREATE SCHEMA "omer-mdata";


ALTER SCHEMA "omer-mdata" OWNER TO "omer-mdata";

--
-- Name: plpgsql; Type: EXTENSION; Schema: -; Owner: 
--

CREATE EXTENSION IF NOT EXISTS plpgsql WITH SCHEMA pg_catalog;


--
-- Name: EXTENSION plpgsql; Type: COMMENT; Schema: -; Owner: 
--

COMMENT ON EXTENSION plpgsql IS 'PL/pgSQL procedural language';


SET search_path = "omer-mdata", pg_catalog;

--
-- Name: update_lastedited_column(); Type: FUNCTION; Schema: omer-mdata; Owner: omer-mdata
--

CREATE FUNCTION update_lastedited_column() RETURNS trigger
    LANGUAGE plpgsql
    AS $$
BEGIN
   NEW."lastEdited" = now(); 
   NEW."version"    = OLD."version" + 1; 
   RETURN NEW;
END;
$$;


ALTER FUNCTION "omer-mdata".update_lastedited_column() OWNER TO "omer-mdata";

SET default_tablespace = '';

SET default_with_oids = false;

--
-- Name: people; Type: TABLE; Schema: omer-mdata; Owner: omer-mdata
--

CREATE TABLE people (
    data json NOT NULL,
    deleted boolean DEFAULT false NOT NULL,
    version integer DEFAULT 1 NOT NULL,
    "lastEdited" timestamp with time zone DEFAULT now() NOT NULL,
    "lastEditor" text DEFAULT 'n/a'::text NOT NULL,
    CONSTRAINT validate_name CHECK (((length((data ->> 'name'::text)) > 0) AND ((data ->> 'name'::text) IS NOT NULL))),
    CONSTRAINT validate_vorname CHECK (((length((data ->> 'vorname'::text)) > 0) AND ((data ->> 'vorname'::text) IS NOT NULL)))
);


ALTER TABLE people OWNER TO "omer-mdata";

--
-- Name: TABLE people; Type: COMMENT; Schema: omer-mdata; Owner: omer-mdata
--

COMMENT ON TABLE people IS 'Stammdaten aller Personen';


--
-- Name: COLUMN people.data; Type: COMMENT; Schema: omer-mdata; Owner: omer-mdata
--

COMMENT ON COLUMN people.data IS 'JSON-Dokument mit den eigentlichen Daten';


--
-- Name: COLUMN people.deleted; Type: COMMENT; Schema: omer-mdata; Owner: omer-mdata
--

COMMENT ON COLUMN people.deleted IS 'Datansatz gelöscht?';


--
-- Name: COLUMN people.version; Type: COMMENT; Schema: omer-mdata; Owner: omer-mdata
--

COMMENT ON COLUMN people.version IS 'Version für Optimistic Locking';


--
-- Name: COLUMN people."lastEdited"; Type: COMMENT; Schema: omer-mdata; Owner: omer-mdata
--

COMMENT ON COLUMN people."lastEdited" IS 'Zeitstempel letzte Bearbeitung';


--
-- Name: COLUMN people."lastEditor"; Type: COMMENT; Schema: omer-mdata; Owner: omer-mdata
--

COMMENT ON COLUMN people."lastEditor" IS 'letzter Bearbeiter';


--
-- Name: idxginp; Type: INDEX; Schema: omer-mdata; Owner: omer-mdata
--

-- CREATE INDEX idxginp ON people USING gin (data json_path_ops);


--
-- Name: ui_people_id; Type: INDEX; Schema: omer-mdata; Owner: omer-mdata
--

CREATE UNIQUE INDEX ui_people_id ON people USING btree (((data ->> 'id'::text)));


--
-- Name: update_people_lastedited; Type: TRIGGER; Schema: omer-mdata; Owner: omer-mdata
--

CREATE TRIGGER update_people_lastedited BEFORE UPDATE ON people FOR EACH ROW EXECUTE PROCEDURE update_lastedited_column();


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

