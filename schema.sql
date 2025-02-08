--
-- PostgreSQL database dump
--

-- Dumped from database version 16.3 (Homebrew)
-- Dumped by pg_dump version 16.3 (Homebrew)

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

SET default_tablespace = '';

SET default_table_access_method = heap;

--
-- Name: bookings; Type: TABLE; Schema: public; Owner: roslansaidi
--

CREATE TABLE public.bookings (
    id bigint NOT NULL,
    room_id bigint NOT NULL,
    user_id bigint NOT NULL,
    meeting_name character varying(255) NOT NULL,
    description character varying(255),
    request character varying(255),
    status character varying(255) NOT NULL,
    booked_at date NOT NULL,
    booked_from time(0) without time zone NOT NULL,
    booked_to time(0) without time zone NOT NULL,
    created_at timestamp(0) without time zone,
    updated_at timestamp(0) without time zone
);


ALTER TABLE public.bookings OWNER TO roslansaidi;

--
-- Name: bookings_id_seq; Type: SEQUENCE; Schema: public; Owner: roslansaidi
--

CREATE SEQUENCE public.bookings_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER SEQUENCE public.bookings_id_seq OWNER TO roslansaidi;

--
-- Name: bookings_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: roslansaidi
--

ALTER SEQUENCE public.bookings_id_seq OWNED BY public.bookings.id;


--
-- Name: cache; Type: TABLE; Schema: public; Owner: roslansaidi
--

CREATE TABLE public.cache (
    key character varying(255) NOT NULL,
    value text NOT NULL,
    expiration integer NOT NULL
);


ALTER TABLE public.cache OWNER TO roslansaidi;

--
-- Name: cache_locks; Type: TABLE; Schema: public; Owner: roslansaidi
--

CREATE TABLE public.cache_locks (
    key character varying(255) NOT NULL,
    owner character varying(255) NOT NULL,
    expiration integer NOT NULL
);


ALTER TABLE public.cache_locks OWNER TO roslansaidi;

--
-- Name: failed_jobs; Type: TABLE; Schema: public; Owner: roslansaidi
--

CREATE TABLE public.failed_jobs (
    id bigint NOT NULL,
    uuid character varying(255) NOT NULL,
    connection text NOT NULL,
    queue text NOT NULL,
    payload text NOT NULL,
    exception text NOT NULL,
    failed_at timestamp(0) without time zone DEFAULT CURRENT_TIMESTAMP NOT NULL
);


ALTER TABLE public.failed_jobs OWNER TO roslansaidi;

--
-- Name: failed_jobs_id_seq; Type: SEQUENCE; Schema: public; Owner: roslansaidi
--

CREATE SEQUENCE public.failed_jobs_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER SEQUENCE public.failed_jobs_id_seq OWNER TO roslansaidi;

--
-- Name: failed_jobs_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: roslansaidi
--

ALTER SEQUENCE public.failed_jobs_id_seq OWNED BY public.failed_jobs.id;


--
-- Name: job_batches; Type: TABLE; Schema: public; Owner: roslansaidi
--

CREATE TABLE public.job_batches (
    id character varying(255) NOT NULL,
    name character varying(255) NOT NULL,
    total_jobs integer NOT NULL,
    pending_jobs integer NOT NULL,
    failed_jobs integer NOT NULL,
    failed_job_ids text NOT NULL,
    options text,
    cancelled_at integer,
    created_at integer NOT NULL,
    finished_at integer
);


ALTER TABLE public.job_batches OWNER TO roslansaidi;

--
-- Name: jobs; Type: TABLE; Schema: public; Owner: roslansaidi
--

CREATE TABLE public.jobs (
    id bigint NOT NULL,
    queue character varying(255) NOT NULL,
    payload text NOT NULL,
    attempts smallint NOT NULL,
    reserved_at integer,
    available_at integer NOT NULL,
    created_at integer NOT NULL
);


ALTER TABLE public.jobs OWNER TO roslansaidi;

--
-- Name: jobs_id_seq; Type: SEQUENCE; Schema: public; Owner: roslansaidi
--

CREATE SEQUENCE public.jobs_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER SEQUENCE public.jobs_id_seq OWNER TO roslansaidi;

--
-- Name: jobs_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: roslansaidi
--

ALTER SEQUENCE public.jobs_id_seq OWNED BY public.jobs.id;


--
-- Name: media; Type: TABLE; Schema: public; Owner: roslansaidi
--

CREATE TABLE public.media (
    id bigint NOT NULL,
    model_type character varying(255) NOT NULL,
    model_id bigint NOT NULL,
    uuid uuid,
    collection_name character varying(255) NOT NULL,
    name character varying(255) NOT NULL,
    file_name character varying(255) NOT NULL,
    mime_type character varying(255),
    disk character varying(255) NOT NULL,
    conversions_disk character varying(255),
    size bigint NOT NULL,
    manipulations json NOT NULL,
    custom_properties json NOT NULL,
    generated_conversions json NOT NULL,
    responsive_images json NOT NULL,
    order_column integer,
    created_at timestamp(0) without time zone,
    updated_at timestamp(0) without time zone
);


ALTER TABLE public.media OWNER TO roslansaidi;

--
-- Name: media_id_seq; Type: SEQUENCE; Schema: public; Owner: roslansaidi
--

CREATE SEQUENCE public.media_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER SEQUENCE public.media_id_seq OWNER TO roslansaidi;

--
-- Name: media_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: roslansaidi
--

ALTER SEQUENCE public.media_id_seq OWNED BY public.media.id;


--
-- Name: migrations; Type: TABLE; Schema: public; Owner: roslansaidi
--

CREATE TABLE public.migrations (
    id integer NOT NULL,
    migration character varying(255) NOT NULL,
    batch integer NOT NULL
);


ALTER TABLE public.migrations OWNER TO roslansaidi;

--
-- Name: migrations_id_seq; Type: SEQUENCE; Schema: public; Owner: roslansaidi
--

CREATE SEQUENCE public.migrations_id_seq
    AS integer
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER SEQUENCE public.migrations_id_seq OWNER TO roslansaidi;

--
-- Name: migrations_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: roslansaidi
--

ALTER SEQUENCE public.migrations_id_seq OWNED BY public.migrations.id;


--
-- Name: model_has_permissions; Type: TABLE; Schema: public; Owner: roslansaidi
--

CREATE TABLE public.model_has_permissions (
    permission_id bigint NOT NULL,
    model_type character varying(255) NOT NULL,
    model_id bigint NOT NULL
);


ALTER TABLE public.model_has_permissions OWNER TO roslansaidi;

--
-- Name: model_has_roles; Type: TABLE; Schema: public; Owner: roslansaidi
--

CREATE TABLE public.model_has_roles (
    role_id bigint NOT NULL,
    model_type character varying(255) NOT NULL,
    model_id bigint NOT NULL
);


ALTER TABLE public.model_has_roles OWNER TO roslansaidi;

--
-- Name: password_reset_tokens; Type: TABLE; Schema: public; Owner: roslansaidi
--

CREATE TABLE public.password_reset_tokens (
    email character varying(255) NOT NULL,
    token character varying(255) NOT NULL,
    created_at timestamp(0) without time zone
);


ALTER TABLE public.password_reset_tokens OWNER TO roslansaidi;

--
-- Name: permissions; Type: TABLE; Schema: public; Owner: roslansaidi
--

CREATE TABLE public.permissions (
    id bigint NOT NULL,
    name character varying(255) NOT NULL,
    guard_name character varying(255) NOT NULL,
    created_at timestamp(0) without time zone,
    updated_at timestamp(0) without time zone
);


ALTER TABLE public.permissions OWNER TO roslansaidi;

--
-- Name: permissions_id_seq; Type: SEQUENCE; Schema: public; Owner: roslansaidi
--

CREATE SEQUENCE public.permissions_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER SEQUENCE public.permissions_id_seq OWNER TO roslansaidi;

--
-- Name: permissions_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: roslansaidi
--

ALTER SEQUENCE public.permissions_id_seq OWNED BY public.permissions.id;


--
-- Name: personal_access_tokens; Type: TABLE; Schema: public; Owner: roslansaidi
--

CREATE TABLE public.personal_access_tokens (
    id bigint NOT NULL,
    tokenable_type character varying(255) NOT NULL,
    tokenable_id bigint NOT NULL,
    name character varying(255) NOT NULL,
    token character varying(64) NOT NULL,
    abilities text,
    last_used_at timestamp(0) without time zone,
    expires_at timestamp(0) without time zone,
    created_at timestamp(0) without time zone,
    updated_at timestamp(0) without time zone
);


ALTER TABLE public.personal_access_tokens OWNER TO roslansaidi;

--
-- Name: personal_access_tokens_id_seq; Type: SEQUENCE; Schema: public; Owner: roslansaidi
--

CREATE SEQUENCE public.personal_access_tokens_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER SEQUENCE public.personal_access_tokens_id_seq OWNER TO roslansaidi;

--
-- Name: personal_access_tokens_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: roslansaidi
--

ALTER SEQUENCE public.personal_access_tokens_id_seq OWNED BY public.personal_access_tokens.id;


--
-- Name: role_has_permissions; Type: TABLE; Schema: public; Owner: roslansaidi
--

CREATE TABLE public.role_has_permissions (
    permission_id bigint NOT NULL,
    role_id bigint NOT NULL
);


ALTER TABLE public.role_has_permissions OWNER TO roslansaidi;

--
-- Name: roles; Type: TABLE; Schema: public; Owner: roslansaidi
--

CREATE TABLE public.roles (
    id bigint NOT NULL,
    name character varying(255) NOT NULL,
    guard_name character varying(255) NOT NULL,
    created_at timestamp(0) without time zone,
    updated_at timestamp(0) without time zone
);


ALTER TABLE public.roles OWNER TO roslansaidi;

--
-- Name: roles_id_seq; Type: SEQUENCE; Schema: public; Owner: roslansaidi
--

CREATE SEQUENCE public.roles_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER SEQUENCE public.roles_id_seq OWNER TO roslansaidi;

--
-- Name: roles_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: roslansaidi
--

ALTER SEQUENCE public.roles_id_seq OWNED BY public.roles.id;


--
-- Name: rooms; Type: TABLE; Schema: public; Owner: roslansaidi
--

CREATE TABLE public.rooms (
    id bigint NOT NULL,
    name character varying(255) NOT NULL,
    description text NOT NULL,
    total_pax character varying(255) NOT NULL,
    equipments json NOT NULL,
    status character varying(255) DEFAULT 'Available'::character varying NOT NULL,
    created_at timestamp(0) without time zone,
    updated_at timestamp(0) without time zone
);


ALTER TABLE public.rooms OWNER TO roslansaidi;

--
-- Name: rooms_id_seq; Type: SEQUENCE; Schema: public; Owner: roslansaidi
--

CREATE SEQUENCE public.rooms_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER SEQUENCE public.rooms_id_seq OWNER TO roslansaidi;

--
-- Name: rooms_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: roslansaidi
--

ALTER SEQUENCE public.rooms_id_seq OWNED BY public.rooms.id;


--
-- Name: sessions; Type: TABLE; Schema: public; Owner: roslansaidi
--

CREATE TABLE public.sessions (
    id character varying(255) NOT NULL,
    user_id bigint,
    ip_address character varying(45),
    user_agent text,
    payload text NOT NULL,
    last_activity integer NOT NULL
);


ALTER TABLE public.sessions OWNER TO roslansaidi;

--
-- Name: templates; Type: TABLE; Schema: public; Owner: roslansaidi
--

CREATE TABLE public.templates (
    id bigint NOT NULL,
    type character varying(255) NOT NULL,
    subject character varying(255) NOT NULL,
    name character varying(255) NOT NULL,
    description character varying(255) NOT NULL,
    content text,
    parameter json,
    created_at timestamp(0) without time zone,
    updated_at timestamp(0) without time zone
);


ALTER TABLE public.templates OWNER TO roslansaidi;

--
-- Name: templates_id_seq; Type: SEQUENCE; Schema: public; Owner: roslansaidi
--

CREATE SEQUENCE public.templates_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER SEQUENCE public.templates_id_seq OWNER TO roslansaidi;

--
-- Name: templates_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: roslansaidi
--

ALTER SEQUENCE public.templates_id_seq OWNED BY public.templates.id;


--
-- Name: users; Type: TABLE; Schema: public; Owner: roslansaidi
--

CREATE TABLE public.users (
    id bigint NOT NULL,
    uuid uuid NOT NULL,
    name character varying(255) NOT NULL,
    email character varying(255) NOT NULL,
    status character varying(255) NOT NULL,
    verify_code character varying(255),
    email_verified_at timestamp(0) without time zone,
    password character varying(255),
    avatar character varying(255),
    provider character varying(255),
    provider_id character varying(255),
    first_time_login boolean DEFAULT false NOT NULL,
    remember_token character varying(100),
    created_at timestamp(0) without time zone,
    updated_at timestamp(0) without time zone
);


ALTER TABLE public.users OWNER TO roslansaidi;

--
-- Name: users_id_seq; Type: SEQUENCE; Schema: public; Owner: roslansaidi
--

CREATE SEQUENCE public.users_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER SEQUENCE public.users_id_seq OWNER TO roslansaidi;

--
-- Name: users_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: roslansaidi
--

ALTER SEQUENCE public.users_id_seq OWNED BY public.users.id;


--
-- Name: working_days; Type: TABLE; Schema: public; Owner: roslansaidi
--

CREATE TABLE public.working_days (
    id bigint NOT NULL,
    day character varying(255) NOT NULL,
    start_at time(0) without time zone NOT NULL,
    end_at time(0) without time zone NOT NULL,
    created_at timestamp(0) without time zone,
    updated_at timestamp(0) without time zone
);


ALTER TABLE public.working_days OWNER TO roslansaidi;

--
-- Name: working_days_id_seq; Type: SEQUENCE; Schema: public; Owner: roslansaidi
--

CREATE SEQUENCE public.working_days_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER SEQUENCE public.working_days_id_seq OWNER TO roslansaidi;

--
-- Name: working_days_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: roslansaidi
--

ALTER SEQUENCE public.working_days_id_seq OWNED BY public.working_days.id;


--
-- Name: bookings id; Type: DEFAULT; Schema: public; Owner: roslansaidi
--

ALTER TABLE ONLY public.bookings ALTER COLUMN id SET DEFAULT nextval('public.bookings_id_seq'::regclass);


--
-- Name: failed_jobs id; Type: DEFAULT; Schema: public; Owner: roslansaidi
--

ALTER TABLE ONLY public.failed_jobs ALTER COLUMN id SET DEFAULT nextval('public.failed_jobs_id_seq'::regclass);


--
-- Name: jobs id; Type: DEFAULT; Schema: public; Owner: roslansaidi
--

ALTER TABLE ONLY public.jobs ALTER COLUMN id SET DEFAULT nextval('public.jobs_id_seq'::regclass);


--
-- Name: media id; Type: DEFAULT; Schema: public; Owner: roslansaidi
--

ALTER TABLE ONLY public.media ALTER COLUMN id SET DEFAULT nextval('public.media_id_seq'::regclass);


--
-- Name: migrations id; Type: DEFAULT; Schema: public; Owner: roslansaidi
--

ALTER TABLE ONLY public.migrations ALTER COLUMN id SET DEFAULT nextval('public.migrations_id_seq'::regclass);


--
-- Name: permissions id; Type: DEFAULT; Schema: public; Owner: roslansaidi
--

ALTER TABLE ONLY public.permissions ALTER COLUMN id SET DEFAULT nextval('public.permissions_id_seq'::regclass);


--
-- Name: personal_access_tokens id; Type: DEFAULT; Schema: public; Owner: roslansaidi
--

ALTER TABLE ONLY public.personal_access_tokens ALTER COLUMN id SET DEFAULT nextval('public.personal_access_tokens_id_seq'::regclass);


--
-- Name: roles id; Type: DEFAULT; Schema: public; Owner: roslansaidi
--

ALTER TABLE ONLY public.roles ALTER COLUMN id SET DEFAULT nextval('public.roles_id_seq'::regclass);


--
-- Name: rooms id; Type: DEFAULT; Schema: public; Owner: roslansaidi
--

ALTER TABLE ONLY public.rooms ALTER COLUMN id SET DEFAULT nextval('public.rooms_id_seq'::regclass);


--
-- Name: templates id; Type: DEFAULT; Schema: public; Owner: roslansaidi
--

ALTER TABLE ONLY public.templates ALTER COLUMN id SET DEFAULT nextval('public.templates_id_seq'::regclass);


--
-- Name: users id; Type: DEFAULT; Schema: public; Owner: roslansaidi
--

ALTER TABLE ONLY public.users ALTER COLUMN id SET DEFAULT nextval('public.users_id_seq'::regclass);


--
-- Name: working_days id; Type: DEFAULT; Schema: public; Owner: roslansaidi
--

ALTER TABLE ONLY public.working_days ALTER COLUMN id SET DEFAULT nextval('public.working_days_id_seq'::regclass);


--
-- Name: bookings bookings_pkey; Type: CONSTRAINT; Schema: public; Owner: roslansaidi
--

ALTER TABLE ONLY public.bookings
    ADD CONSTRAINT bookings_pkey PRIMARY KEY (id);


--
-- Name: cache_locks cache_locks_pkey; Type: CONSTRAINT; Schema: public; Owner: roslansaidi
--

ALTER TABLE ONLY public.cache_locks
    ADD CONSTRAINT cache_locks_pkey PRIMARY KEY (key);


--
-- Name: cache cache_pkey; Type: CONSTRAINT; Schema: public; Owner: roslansaidi
--

ALTER TABLE ONLY public.cache
    ADD CONSTRAINT cache_pkey PRIMARY KEY (key);


--
-- Name: failed_jobs failed_jobs_pkey; Type: CONSTRAINT; Schema: public; Owner: roslansaidi
--

ALTER TABLE ONLY public.failed_jobs
    ADD CONSTRAINT failed_jobs_pkey PRIMARY KEY (id);


--
-- Name: failed_jobs failed_jobs_uuid_unique; Type: CONSTRAINT; Schema: public; Owner: roslansaidi
--

ALTER TABLE ONLY public.failed_jobs
    ADD CONSTRAINT failed_jobs_uuid_unique UNIQUE (uuid);


--
-- Name: job_batches job_batches_pkey; Type: CONSTRAINT; Schema: public; Owner: roslansaidi
--

ALTER TABLE ONLY public.job_batches
    ADD CONSTRAINT job_batches_pkey PRIMARY KEY (id);


--
-- Name: jobs jobs_pkey; Type: CONSTRAINT; Schema: public; Owner: roslansaidi
--

ALTER TABLE ONLY public.jobs
    ADD CONSTRAINT jobs_pkey PRIMARY KEY (id);


--
-- Name: media media_pkey; Type: CONSTRAINT; Schema: public; Owner: roslansaidi
--

ALTER TABLE ONLY public.media
    ADD CONSTRAINT media_pkey PRIMARY KEY (id);


--
-- Name: media media_uuid_unique; Type: CONSTRAINT; Schema: public; Owner: roslansaidi
--

ALTER TABLE ONLY public.media
    ADD CONSTRAINT media_uuid_unique UNIQUE (uuid);


--
-- Name: migrations migrations_pkey; Type: CONSTRAINT; Schema: public; Owner: roslansaidi
--

ALTER TABLE ONLY public.migrations
    ADD CONSTRAINT migrations_pkey PRIMARY KEY (id);


--
-- Name: model_has_permissions model_has_permissions_pkey; Type: CONSTRAINT; Schema: public; Owner: roslansaidi
--

ALTER TABLE ONLY public.model_has_permissions
    ADD CONSTRAINT model_has_permissions_pkey PRIMARY KEY (permission_id, model_id, model_type);


--
-- Name: model_has_roles model_has_roles_pkey; Type: CONSTRAINT; Schema: public; Owner: roslansaidi
--

ALTER TABLE ONLY public.model_has_roles
    ADD CONSTRAINT model_has_roles_pkey PRIMARY KEY (role_id, model_id, model_type);


--
-- Name: password_reset_tokens password_reset_tokens_pkey; Type: CONSTRAINT; Schema: public; Owner: roslansaidi
--

ALTER TABLE ONLY public.password_reset_tokens
    ADD CONSTRAINT password_reset_tokens_pkey PRIMARY KEY (email);


--
-- Name: permissions permissions_name_guard_name_unique; Type: CONSTRAINT; Schema: public; Owner: roslansaidi
--

ALTER TABLE ONLY public.permissions
    ADD CONSTRAINT permissions_name_guard_name_unique UNIQUE (name, guard_name);


--
-- Name: permissions permissions_pkey; Type: CONSTRAINT; Schema: public; Owner: roslansaidi
--

ALTER TABLE ONLY public.permissions
    ADD CONSTRAINT permissions_pkey PRIMARY KEY (id);


--
-- Name: personal_access_tokens personal_access_tokens_pkey; Type: CONSTRAINT; Schema: public; Owner: roslansaidi
--

ALTER TABLE ONLY public.personal_access_tokens
    ADD CONSTRAINT personal_access_tokens_pkey PRIMARY KEY (id);


--
-- Name: personal_access_tokens personal_access_tokens_token_unique; Type: CONSTRAINT; Schema: public; Owner: roslansaidi
--

ALTER TABLE ONLY public.personal_access_tokens
    ADD CONSTRAINT personal_access_tokens_token_unique UNIQUE (token);


--
-- Name: role_has_permissions role_has_permissions_pkey; Type: CONSTRAINT; Schema: public; Owner: roslansaidi
--

ALTER TABLE ONLY public.role_has_permissions
    ADD CONSTRAINT role_has_permissions_pkey PRIMARY KEY (permission_id, role_id);


--
-- Name: roles roles_name_guard_name_unique; Type: CONSTRAINT; Schema: public; Owner: roslansaidi
--

ALTER TABLE ONLY public.roles
    ADD CONSTRAINT roles_name_guard_name_unique UNIQUE (name, guard_name);


--
-- Name: roles roles_pkey; Type: CONSTRAINT; Schema: public; Owner: roslansaidi
--

ALTER TABLE ONLY public.roles
    ADD CONSTRAINT roles_pkey PRIMARY KEY (id);


--
-- Name: rooms rooms_pkey; Type: CONSTRAINT; Schema: public; Owner: roslansaidi
--

ALTER TABLE ONLY public.rooms
    ADD CONSTRAINT rooms_pkey PRIMARY KEY (id);


--
-- Name: sessions sessions_pkey; Type: CONSTRAINT; Schema: public; Owner: roslansaidi
--

ALTER TABLE ONLY public.sessions
    ADD CONSTRAINT sessions_pkey PRIMARY KEY (id);


--
-- Name: templates templates_pkey; Type: CONSTRAINT; Schema: public; Owner: roslansaidi
--

ALTER TABLE ONLY public.templates
    ADD CONSTRAINT templates_pkey PRIMARY KEY (id);


--
-- Name: users users_email_unique; Type: CONSTRAINT; Schema: public; Owner: roslansaidi
--

ALTER TABLE ONLY public.users
    ADD CONSTRAINT users_email_unique UNIQUE (email);


--
-- Name: users users_pkey; Type: CONSTRAINT; Schema: public; Owner: roslansaidi
--

ALTER TABLE ONLY public.users
    ADD CONSTRAINT users_pkey PRIMARY KEY (id);


--
-- Name: users users_uuid_unique; Type: CONSTRAINT; Schema: public; Owner: roslansaidi
--

ALTER TABLE ONLY public.users
    ADD CONSTRAINT users_uuid_unique UNIQUE (uuid);


--
-- Name: working_days working_days_pkey; Type: CONSTRAINT; Schema: public; Owner: roslansaidi
--

ALTER TABLE ONLY public.working_days
    ADD CONSTRAINT working_days_pkey PRIMARY KEY (id);


--
-- Name: jobs_queue_index; Type: INDEX; Schema: public; Owner: roslansaidi
--

CREATE INDEX jobs_queue_index ON public.jobs USING btree (queue);


--
-- Name: media_model_type_model_id_index; Type: INDEX; Schema: public; Owner: roslansaidi
--

CREATE INDEX media_model_type_model_id_index ON public.media USING btree (model_type, model_id);


--
-- Name: media_order_column_index; Type: INDEX; Schema: public; Owner: roslansaidi
--

CREATE INDEX media_order_column_index ON public.media USING btree (order_column);


--
-- Name: model_has_permissions_model_id_model_type_index; Type: INDEX; Schema: public; Owner: roslansaidi
--

CREATE INDEX model_has_permissions_model_id_model_type_index ON public.model_has_permissions USING btree (model_id, model_type);


--
-- Name: model_has_roles_model_id_model_type_index; Type: INDEX; Schema: public; Owner: roslansaidi
--

CREATE INDEX model_has_roles_model_id_model_type_index ON public.model_has_roles USING btree (model_id, model_type);


--
-- Name: personal_access_tokens_tokenable_type_tokenable_id_index; Type: INDEX; Schema: public; Owner: roslansaidi
--

CREATE INDEX personal_access_tokens_tokenable_type_tokenable_id_index ON public.personal_access_tokens USING btree (tokenable_type, tokenable_id);


--
-- Name: sessions_last_activity_index; Type: INDEX; Schema: public; Owner: roslansaidi
--

CREATE INDEX sessions_last_activity_index ON public.sessions USING btree (last_activity);


--
-- Name: sessions_user_id_index; Type: INDEX; Schema: public; Owner: roslansaidi
--

CREATE INDEX sessions_user_id_index ON public.sessions USING btree (user_id);


--
-- Name: bookings bookings_room_id_foreign; Type: FK CONSTRAINT; Schema: public; Owner: roslansaidi
--

ALTER TABLE ONLY public.bookings
    ADD CONSTRAINT bookings_room_id_foreign FOREIGN KEY (room_id) REFERENCES public.rooms(id) ON DELETE CASCADE;


--
-- Name: bookings bookings_user_id_foreign; Type: FK CONSTRAINT; Schema: public; Owner: roslansaidi
--

ALTER TABLE ONLY public.bookings
    ADD CONSTRAINT bookings_user_id_foreign FOREIGN KEY (user_id) REFERENCES public.users(id);


--
-- Name: model_has_permissions model_has_permissions_permission_id_foreign; Type: FK CONSTRAINT; Schema: public; Owner: roslansaidi
--

ALTER TABLE ONLY public.model_has_permissions
    ADD CONSTRAINT model_has_permissions_permission_id_foreign FOREIGN KEY (permission_id) REFERENCES public.permissions(id) ON DELETE CASCADE;


--
-- Name: model_has_roles model_has_roles_role_id_foreign; Type: FK CONSTRAINT; Schema: public; Owner: roslansaidi
--

ALTER TABLE ONLY public.model_has_roles
    ADD CONSTRAINT model_has_roles_role_id_foreign FOREIGN KEY (role_id) REFERENCES public.roles(id) ON DELETE CASCADE;


--
-- Name: role_has_permissions role_has_permissions_permission_id_foreign; Type: FK CONSTRAINT; Schema: public; Owner: roslansaidi
--

ALTER TABLE ONLY public.role_has_permissions
    ADD CONSTRAINT role_has_permissions_permission_id_foreign FOREIGN KEY (permission_id) REFERENCES public.permissions(id) ON DELETE CASCADE;


--
-- Name: role_has_permissions role_has_permissions_role_id_foreign; Type: FK CONSTRAINT; Schema: public; Owner: roslansaidi
--

ALTER TABLE ONLY public.role_has_permissions
    ADD CONSTRAINT role_has_permissions_role_id_foreign FOREIGN KEY (role_id) REFERENCES public.roles(id) ON DELETE CASCADE;


--
-- PostgreSQL database dump complete
--

