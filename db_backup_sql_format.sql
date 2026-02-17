--
-- PostgreSQL database dump
--

-- Dumped from database version 16.8
-- Dumped by pg_dump version 16.8

-- Started on 2026-02-17 22:30:18 WIB

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

DROP DATABASE IF EXISTS aicusper;
--
-- TOC entry 3693 (class 1262 OID 16602)
-- Name: aicusper; Type: DATABASE; Schema: -; Owner: postgres
--

CREATE DATABASE aicusper WITH TEMPLATE = template0 ENCODING = 'UTF8' LOCALE_PROVIDER = libc LOCALE = 'C';


ALTER DATABASE aicusper OWNER TO postgres;

\connect aicusper

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
-- TOC entry 221 (class 1259 OID 16637)
-- Name: cache; Type: TABLE; Schema: public; Owner: postgres
--

CREATE TABLE public.cache (
    key character varying(255) NOT NULL,
    value text NOT NULL,
    expiration integer NOT NULL
);


ALTER TABLE public.cache OWNER TO postgres;

--
-- TOC entry 222 (class 1259 OID 16644)
-- Name: cache_locks; Type: TABLE; Schema: public; Owner: postgres
--

CREATE TABLE public.cache_locks (
    key character varying(255) NOT NULL,
    owner character varying(255) NOT NULL,
    expiration integer NOT NULL
);


ALTER TABLE public.cache_locks OWNER TO postgres;

--
-- TOC entry 227 (class 1259 OID 16669)
-- Name: failed_jobs; Type: TABLE; Schema: public; Owner: postgres
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


ALTER TABLE public.failed_jobs OWNER TO postgres;

--
-- TOC entry 226 (class 1259 OID 16668)
-- Name: failed_jobs_id_seq; Type: SEQUENCE; Schema: public; Owner: postgres
--

CREATE SEQUENCE public.failed_jobs_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER SEQUENCE public.failed_jobs_id_seq OWNER TO postgres;

--
-- TOC entry 3694 (class 0 OID 0)
-- Dependencies: 226
-- Name: failed_jobs_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: postgres
--

ALTER SEQUENCE public.failed_jobs_id_seq OWNED BY public.failed_jobs.id;


--
-- TOC entry 225 (class 1259 OID 16661)
-- Name: job_batches; Type: TABLE; Schema: public; Owner: postgres
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


ALTER TABLE public.job_batches OWNER TO postgres;

--
-- TOC entry 224 (class 1259 OID 16652)
-- Name: jobs; Type: TABLE; Schema: public; Owner: postgres
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


ALTER TABLE public.jobs OWNER TO postgres;

--
-- TOC entry 223 (class 1259 OID 16651)
-- Name: jobs_id_seq; Type: SEQUENCE; Schema: public; Owner: postgres
--

CREATE SEQUENCE public.jobs_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER SEQUENCE public.jobs_id_seq OWNER TO postgres;

--
-- TOC entry 3695 (class 0 OID 0)
-- Dependencies: 223
-- Name: jobs_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: postgres
--

ALTER SEQUENCE public.jobs_id_seq OWNED BY public.jobs.id;


--
-- TOC entry 229 (class 1259 OID 16681)
-- Name: master_outlets; Type: TABLE; Schema: public; Owner: postgres
--

CREATE TABLE public.master_outlets (
    id bigint NOT NULL,
    name character varying(255) NOT NULL,
    latitude character varying(255),
    longitude character varying(255),
    address text,
    created_at timestamp without time zone DEFAULT CURRENT_TIMESTAMP,
    updated_at timestamp without time zone DEFAULT CURRENT_TIMESTAMP
);


ALTER TABLE public.master_outlets OWNER TO postgres;

--
-- TOC entry 228 (class 1259 OID 16680)
-- Name: master_outlet_id_seq; Type: SEQUENCE; Schema: public; Owner: postgres
--

CREATE SEQUENCE public.master_outlet_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER SEQUENCE public.master_outlet_id_seq OWNER TO postgres;

--
-- TOC entry 3696 (class 0 OID 0)
-- Dependencies: 228
-- Name: master_outlet_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: postgres
--

ALTER SEQUENCE public.master_outlet_id_seq OWNED BY public.master_outlets.id;


--
-- TOC entry 216 (class 1259 OID 16604)
-- Name: migrations; Type: TABLE; Schema: public; Owner: postgres
--

CREATE TABLE public.migrations (
    id integer NOT NULL,
    migration character varying(255) NOT NULL,
    batch integer NOT NULL
);


ALTER TABLE public.migrations OWNER TO postgres;

--
-- TOC entry 215 (class 1259 OID 16603)
-- Name: migrations_id_seq; Type: SEQUENCE; Schema: public; Owner: postgres
--

CREATE SEQUENCE public.migrations_id_seq
    AS integer
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER SEQUENCE public.migrations_id_seq OWNER TO postgres;

--
-- TOC entry 3697 (class 0 OID 0)
-- Dependencies: 215
-- Name: migrations_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: postgres
--

ALTER SEQUENCE public.migrations_id_seq OWNED BY public.migrations.id;


--
-- TOC entry 219 (class 1259 OID 16621)
-- Name: password_reset_tokens; Type: TABLE; Schema: public; Owner: postgres
--

CREATE TABLE public.password_reset_tokens (
    email character varying(255) NOT NULL,
    token character varying(255) NOT NULL,
    created_at timestamp(0) without time zone
);


ALTER TABLE public.password_reset_tokens OWNER TO postgres;

--
-- TOC entry 220 (class 1259 OID 16628)
-- Name: sessions; Type: TABLE; Schema: public; Owner: postgres
--

CREATE TABLE public.sessions (
    id character varying(255) NOT NULL,
    user_id bigint,
    ip_address character varying(45),
    user_agent text,
    payload text NOT NULL,
    last_activity integer NOT NULL
);


ALTER TABLE public.sessions OWNER TO postgres;

--
-- TOC entry 231 (class 1259 OID 16692)
-- Name: unified_inventory_table; Type: TABLE; Schema: public; Owner: postgres
--

CREATE TABLE public.unified_inventory_table (
    inventory_id bigint NOT NULL,
    outlet_id integer NOT NULL,
    outlet_name character varying(255),
    item_name character varying(255),
    image_path text,
    active character varying(255) DEFAULT 'active'::character varying NOT NULL,
    price numeric(12,2),
    description text,
    CONSTRAINT unified_inventory_table_active_check CHECK (((active)::text = ANY ((ARRAY['active'::character varying, 'inactive'::character varying])::text[])))
);


ALTER TABLE public.unified_inventory_table OWNER TO postgres;

--
-- TOC entry 230 (class 1259 OID 16691)
-- Name: unified_inventory_table_inventory_id_seq; Type: SEQUENCE; Schema: public; Owner: postgres
--

CREATE SEQUENCE public.unified_inventory_table_inventory_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER SEQUENCE public.unified_inventory_table_inventory_id_seq OWNER TO postgres;

--
-- TOC entry 3698 (class 0 OID 0)
-- Dependencies: 230
-- Name: unified_inventory_table_inventory_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: postgres
--

ALTER SEQUENCE public.unified_inventory_table_inventory_id_seq OWNED BY public.unified_inventory_table.inventory_id;


--
-- TOC entry 218 (class 1259 OID 16611)
-- Name: users; Type: TABLE; Schema: public; Owner: postgres
--

CREATE TABLE public.users (
    id bigint NOT NULL,
    name character varying(255) NOT NULL,
    email character varying(255) NOT NULL,
    email_verified_at timestamp(0) without time zone,
    password character varying(255) NOT NULL,
    remember_token character varying(100),
    created_at timestamp(0) without time zone,
    updated_at timestamp(0) without time zone
);


ALTER TABLE public.users OWNER TO postgres;

--
-- TOC entry 217 (class 1259 OID 16610)
-- Name: users_id_seq; Type: SEQUENCE; Schema: public; Owner: postgres
--

CREATE SEQUENCE public.users_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER SEQUENCE public.users_id_seq OWNER TO postgres;

--
-- TOC entry 3699 (class 0 OID 0)
-- Dependencies: 217
-- Name: users_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: postgres
--

ALTER SEQUENCE public.users_id_seq OWNED BY public.users.id;


--
-- TOC entry 3491 (class 2604 OID 16672)
-- Name: failed_jobs id; Type: DEFAULT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.failed_jobs ALTER COLUMN id SET DEFAULT nextval('public.failed_jobs_id_seq'::regclass);


--
-- TOC entry 3490 (class 2604 OID 16655)
-- Name: jobs id; Type: DEFAULT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.jobs ALTER COLUMN id SET DEFAULT nextval('public.jobs_id_seq'::regclass);


--
-- TOC entry 3493 (class 2604 OID 16684)
-- Name: master_outlets id; Type: DEFAULT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.master_outlets ALTER COLUMN id SET DEFAULT nextval('public.master_outlet_id_seq'::regclass);


--
-- TOC entry 3488 (class 2604 OID 16607)
-- Name: migrations id; Type: DEFAULT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.migrations ALTER COLUMN id SET DEFAULT nextval('public.migrations_id_seq'::regclass);


--
-- TOC entry 3496 (class 2604 OID 16695)
-- Name: unified_inventory_table inventory_id; Type: DEFAULT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.unified_inventory_table ALTER COLUMN inventory_id SET DEFAULT nextval('public.unified_inventory_table_inventory_id_seq'::regclass);


--
-- TOC entry 3489 (class 2604 OID 16614)
-- Name: users id; Type: DEFAULT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.users ALTER COLUMN id SET DEFAULT nextval('public.users_id_seq'::regclass);


--
-- TOC entry 3677 (class 0 OID 16637)
-- Dependencies: 221
-- Data for Name: cache; Type: TABLE DATA; Schema: public; Owner: postgres
--



--
-- TOC entry 3678 (class 0 OID 16644)
-- Dependencies: 222
-- Data for Name: cache_locks; Type: TABLE DATA; Schema: public; Owner: postgres
--



--
-- TOC entry 3683 (class 0 OID 16669)
-- Dependencies: 227
-- Data for Name: failed_jobs; Type: TABLE DATA; Schema: public; Owner: postgres
--



--
-- TOC entry 3681 (class 0 OID 16661)
-- Dependencies: 225
-- Data for Name: job_batches; Type: TABLE DATA; Schema: public; Owner: postgres
--



--
-- TOC entry 3680 (class 0 OID 16652)
-- Dependencies: 224
-- Data for Name: jobs; Type: TABLE DATA; Schema: public; Owner: postgres
--



--
-- TOC entry 3685 (class 0 OID 16681)
-- Dependencies: 229
-- Data for Name: master_outlets; Type: TABLE DATA; Schema: public; Owner: postgres
--

INSERT INTO public.master_outlets VALUES (1, 'JH Kitchen Ashta', '-6.229806707581031', '106.80726244662833', 'Jl. Senopati No.83, Senayan, Kec. Kby. Baru, Kota Jakarta Selatan, Daerah Khusus Ibukota Jakarta 12190', '2026-01-11 17:45:59.427056', '2026-01-11 17:45:59.427056');
INSERT INTO public.master_outlets VALUES (2, 'JH Kitchen SCBD', '-6.229806707581031', '106.80726244662833', 'Jl. Senopati No.83, Senayan, Kec. Kby. Baru, Kota Jakarta Selatan, Daerah Khusus Ibukota Jakarta 12190', '2026-01-11 18:16:04.395626', '2026-01-11 18:16:04.395626');


--
-- TOC entry 3672 (class 0 OID 16604)
-- Dependencies: 216
-- Data for Name: migrations; Type: TABLE DATA; Schema: public; Owner: postgres
--

INSERT INTO public.migrations VALUES (1, '0001_01_01_000000_create_users_table', 1);
INSERT INTO public.migrations VALUES (2, '0001_01_01_000001_create_cache_table', 1);
INSERT INTO public.migrations VALUES (3, '0001_01_01_000002_create_jobs_table', 1);
INSERT INTO public.migrations VALUES (4, '2026_01_11_151905_create_products_table', 2);


--
-- TOC entry 3675 (class 0 OID 16621)
-- Dependencies: 219
-- Data for Name: password_reset_tokens; Type: TABLE DATA; Schema: public; Owner: postgres
--



--
-- TOC entry 3676 (class 0 OID 16628)
-- Dependencies: 220
-- Data for Name: sessions; Type: TABLE DATA; Schema: public; Owner: postgres
--

INSERT INTO public.sessions VALUES ('E16z5pw8Ku6LT3JllqoFMo1Q5QAqjarHPbsP1EeC', NULL, '127.0.0.1', 'Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/144.0.0.0 Safari/537.36', 'YTozOntzOjY6Il90b2tlbiI7czo0MDoibzRhajhKd2x1dVQ2TFh5SE1YZVQ3ZXdvSmwxanZHNVZaVTZMbmVnViI7czo2OiJfZmxhc2giO2E6Mjp7czozOiJvbGQiO2E6MDp7fXM6MzoibmV3IjthOjA6e319czo5OiJfcHJldmlvdXMiO2E6MTp7czozOiJ1cmwiO3M6MjE6Imh0dHA6Ly8xMjcuMC4wLjE6Njk2OSI7fX0=', 1771341787);
INSERT INTO public.sessions VALUES ('VburNyLrK8DLwBlkL9vtfD0Dd6Zu6EqEpXBT95Xl', 1, '127.0.0.1', 'Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/144.0.0.0 Safari/537.36', 'YTo0OntzOjY6Il90b2tlbiI7czo0MDoibFZNekVQS1dza296RTB6Sm9jYUI0SnRQQUtkZVhUYXVFbXd5Tm9VbyI7czo5OiJfcHJldmlvdXMiO2E6MTp7czozOiJ1cmwiO3M6MzE6Imh0dHA6Ly8xMjcuMC4wLjE6Njk2OS9kYXNoYm9hcmQiO31zOjY6Il9mbGFzaCI7YToyOntzOjM6Im9sZCI7YTowOnt9czozOiJuZXciO2E6MDp7fX1zOjUwOiJsb2dpbl93ZWJfNTliYTM2YWRkYzJiMmY5NDAxNTgwZjAxNGM3ZjU4ZWE0ZTMwOTg5ZCI7aToxO30=', 1771339283);


--
-- TOC entry 3687 (class 0 OID 16692)
-- Dependencies: 231
-- Data for Name: unified_inventory_table; Type: TABLE DATA; Schema: public; Owner: postgres
--

INSERT INTO public.unified_inventory_table VALUES (4, 2, 'JH Kitchen SCBD', 'Curros Hot', 'inventory/d637cf9a-600e-4a0d-9be6-781cf50f48d9.jpg', 'active', 20000.00, 'Best curros');
INSERT INTO public.unified_inventory_table VALUES (5, 1, 'JH Kitchen Ashta', 'Risotto', 'inventory/f4e6c9e7-74af-4802-b20b-0c84b6691a29.jpg', 'active', 120000.00, 'Hot enjoying');
INSERT INTO public.unified_inventory_table VALUES (6, 1, 'JH Kitchen Ashta', 'sdfdfg', 'inventory/40d5d3cd-68e2-4dee-a32d-2dc66c41e153.jpg', 'active', 2133.00, 'werfgdbv');
INSERT INTO public.unified_inventory_table VALUES (1, 2, NULL, 'in nama', 'inventory/aa7c9018-7684-465f-852a-aa394704fb97.jpg', 'active', 120000.00, 'uni desdf');
INSERT INTO public.unified_inventory_table VALUES (10, 1, 'JH Kitchen Ashta', 'Hidup mu marah marah mulu', 'inventory/897e2d4c-9faa-4b20-8a96-83007afe2458.jpg', 'active', 100000.00, 'marah marah mulu');
INSERT INTO public.unified_inventory_table VALUES (12, 2, 'JH Kitchen SCBD', 'werwerwer', 'inventory/fcfd933a-9242-4e5e-a497-9d598bc93df6.jpg', 'active', 345674.00, 'wteryjhhthrgh');
INSERT INTO public.unified_inventory_table VALUES (14, 2, 'JH Kitchen SCBD', 'Curros Hot', 'inventory/2a6da962-6d7f-4e15-92d1-85309ccfb176.jpg', 'active', 90000.00, 'Kjldshjbfb');
INSERT INTO public.unified_inventory_table VALUES (3, 2, NULL, 'Bantal Hanget', 'inventory/fef25edc-4810-4d53-ae2c-f34a06892823.jpeg', 'active', 120000.00, 'Lorem ipsum dolor sit amet consectetur, adipisicing elit. Praesentium qui ad soluta natus fuga aperiam quaerat deleniti enim iusto facere quo incidunt, nisi eveniet, quia saepe dolorem illo sunt maxime.');
INSERT INTO public.unified_inventory_table VALUES (17, 2, 'JH Kitchen SCBD', 'aeresr', 'inventory/34323bf3-cf68-46de-b1bb-a118a4a8e393.jpg', 'inactive', 12000.00, 'well done amre');
INSERT INTO public.unified_inventory_table VALUES (11, 2, 'JH Kitchen SCBD', 'ewrtyhjg', 'products/10dc01fb-9951-43cb-8359-e1bf36de9cfd.jpeg', 'active', 123123.00, 'dsfsdf');
INSERT INTO public.unified_inventory_table VALUES (13, 1, 'JH Kitchen Ashta', 'qweqwe', 'products/0749c7f7-5a0f-4a51-b2e4-23421d930ae4.jpg', 'inactive', 123123.00, '123123');
INSERT INTO public.unified_inventory_table VALUES (15, 1, 'JH Kitchen Ashta', 'Marah-marah muku', 'inventory/bf794041-8792-4f2b-8c42-9f894a969361.jpg', 'active', 45000.00, 'ini mantab ini mantab ini mantab ini mantab ini mantab ini mantab ini mantab ini mantab ini mantab ini mantab ini mantab ini mantab ini mantab ini mantab ini mantab ini mantab ini mantab ini mantab ini mantab ini mantab ini mantab');
INSERT INTO public.unified_inventory_table VALUES (16, 2, 'JH Kitchen Ashta', 'Paling Baru Ni', 'inventory/78b0e909-e4a5-44da-af06-336cb231b02f.jpeg', 'inactive', 73000.00, '1Lorem ipsum dolor sit amet consectetur, adipisicing elit. Praesentium qui ad soluta natus fuga aperiam quaerat deleniti enim iusto facere quo incidunt, nisi eveniet, quia saepe dolorem illo sunt maxime.');


--
-- TOC entry 3674 (class 0 OID 16611)
-- Dependencies: 218
-- Data for Name: users; Type: TABLE DATA; Schema: public; Owner: postgres
--

INSERT INTO public.users VALUES (1, 'dondon', 'mail@mail.com', NULL, '$2y$12$/DOSu0MNaodd/Q4UkAbwge6Qgi6FPy/VOZr2g03kPUk7pgpphNkt6', 'MZNlmzyafeFDYx5qM57Pi7d8L1NAcG0EiVoIqRN2ZYvtFlCx2PaNkFOJx0O5', '2026-01-05 15:30:12', '2026-01-05 15:30:12');
INSERT INTO public.users VALUES (2, 'hellaw', 'hellaw@guysmail.com', NULL, '$2y$12$XDCExDo/q3hnBmyRp/jZAOL2UVabnlqm0bIMZPPeHmyG3mK.q4/xi', 'yDQWUztHwvC6MqDe1pBh401LGWp8NMyuH6FCtAUjLMQ7bXpmjuWmzPEh0MhD', '2026-02-17 15:04:32', '2026-02-17 15:22:32');
INSERT INTO public.users VALUES (3, 'adsfdsf', 'sdfsdfz@sdfsdf.sdf', NULL, '$2y$12$Ba8aFZj8nXcMNmu0F7VakeVSxo/282vPnuHjvT.wsXlTk0s3qyQlK', NULL, '2026-02-17 15:22:54', '2026-02-17 15:22:54');


--
-- TOC entry 3700 (class 0 OID 0)
-- Dependencies: 226
-- Name: failed_jobs_id_seq; Type: SEQUENCE SET; Schema: public; Owner: postgres
--

SELECT pg_catalog.setval('public.failed_jobs_id_seq', 1, false);


--
-- TOC entry 3701 (class 0 OID 0)
-- Dependencies: 223
-- Name: jobs_id_seq; Type: SEQUENCE SET; Schema: public; Owner: postgres
--

SELECT pg_catalog.setval('public.jobs_id_seq', 1, false);


--
-- TOC entry 3702 (class 0 OID 0)
-- Dependencies: 228
-- Name: master_outlet_id_seq; Type: SEQUENCE SET; Schema: public; Owner: postgres
--

SELECT pg_catalog.setval('public.master_outlet_id_seq', 2, true);


--
-- TOC entry 3703 (class 0 OID 0)
-- Dependencies: 215
-- Name: migrations_id_seq; Type: SEQUENCE SET; Schema: public; Owner: postgres
--

SELECT pg_catalog.setval('public.migrations_id_seq', 4, true);


--
-- TOC entry 3704 (class 0 OID 0)
-- Dependencies: 230
-- Name: unified_inventory_table_inventory_id_seq; Type: SEQUENCE SET; Schema: public; Owner: postgres
--

SELECT pg_catalog.setval('public.unified_inventory_table_inventory_id_seq', 17, true);


--
-- TOC entry 3705 (class 0 OID 0)
-- Dependencies: 217
-- Name: users_id_seq; Type: SEQUENCE SET; Schema: public; Owner: postgres
--

SELECT pg_catalog.setval('public.users_id_seq', 3, true);


--
-- TOC entry 3514 (class 2606 OID 16650)
-- Name: cache_locks cache_locks_pkey; Type: CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.cache_locks
    ADD CONSTRAINT cache_locks_pkey PRIMARY KEY (key);


--
-- TOC entry 3512 (class 2606 OID 16643)
-- Name: cache cache_pkey; Type: CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.cache
    ADD CONSTRAINT cache_pkey PRIMARY KEY (key);


--
-- TOC entry 3521 (class 2606 OID 16677)
-- Name: failed_jobs failed_jobs_pkey; Type: CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.failed_jobs
    ADD CONSTRAINT failed_jobs_pkey PRIMARY KEY (id);


--
-- TOC entry 3523 (class 2606 OID 16679)
-- Name: failed_jobs failed_jobs_uuid_unique; Type: CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.failed_jobs
    ADD CONSTRAINT failed_jobs_uuid_unique UNIQUE (uuid);


--
-- TOC entry 3519 (class 2606 OID 16667)
-- Name: job_batches job_batches_pkey; Type: CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.job_batches
    ADD CONSTRAINT job_batches_pkey PRIMARY KEY (id);


--
-- TOC entry 3516 (class 2606 OID 16659)
-- Name: jobs jobs_pkey; Type: CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.jobs
    ADD CONSTRAINT jobs_pkey PRIMARY KEY (id);


--
-- TOC entry 3525 (class 2606 OID 16690)
-- Name: master_outlets master_outlet_pkey; Type: CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.master_outlets
    ADD CONSTRAINT master_outlet_pkey PRIMARY KEY (id);


--
-- TOC entry 3500 (class 2606 OID 16609)
-- Name: migrations migrations_pkey; Type: CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.migrations
    ADD CONSTRAINT migrations_pkey PRIMARY KEY (id);


--
-- TOC entry 3506 (class 2606 OID 16627)
-- Name: password_reset_tokens password_reset_tokens_pkey; Type: CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.password_reset_tokens
    ADD CONSTRAINT password_reset_tokens_pkey PRIMARY KEY (email);


--
-- TOC entry 3509 (class 2606 OID 16634)
-- Name: sessions sessions_pkey; Type: CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.sessions
    ADD CONSTRAINT sessions_pkey PRIMARY KEY (id);


--
-- TOC entry 3527 (class 2606 OID 16701)
-- Name: unified_inventory_table unified_inventory_table_pkey; Type: CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.unified_inventory_table
    ADD CONSTRAINT unified_inventory_table_pkey PRIMARY KEY (inventory_id);


--
-- TOC entry 3502 (class 2606 OID 16620)
-- Name: users users_email_unique; Type: CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.users
    ADD CONSTRAINT users_email_unique UNIQUE (email);


--
-- TOC entry 3504 (class 2606 OID 16618)
-- Name: users users_pkey; Type: CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.users
    ADD CONSTRAINT users_pkey PRIMARY KEY (id);


--
-- TOC entry 3517 (class 1259 OID 16660)
-- Name: jobs_queue_index; Type: INDEX; Schema: public; Owner: postgres
--

CREATE INDEX jobs_queue_index ON public.jobs USING btree (queue);


--
-- TOC entry 3507 (class 1259 OID 16636)
-- Name: sessions_last_activity_index; Type: INDEX; Schema: public; Owner: postgres
--

CREATE INDEX sessions_last_activity_index ON public.sessions USING btree (last_activity);


--
-- TOC entry 3510 (class 1259 OID 16635)
-- Name: sessions_user_id_index; Type: INDEX; Schema: public; Owner: postgres
--

CREATE INDEX sessions_user_id_index ON public.sessions USING btree (user_id);


-- Completed on 2026-02-17 22:30:18 WIB

--
-- PostgreSQL database dump complete
--

