--
-- PostgreSQL database dump
--

\restrict m4iWg6Lz94qm9ZvQ1ibPP7hDfjIFJ5P2aVKmSUxpAKJck9kx0RoVENsnKgDUgQh

-- Dumped from database version 18.1
-- Dumped by pg_dump version 18.1

SET statement_timeout = 0;
SET lock_timeout = 0;
SET idle_in_transaction_session_timeout = 0;
SET transaction_timeout = 0;
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
-- Name: backup_files; Type: TABLE; Schema: public; Owner: -
--

CREATE TABLE public.backup_files (
    backup_files_id bigint NOT NULL,
    is_auto boolean DEFAULT true NOT NULL,
    file_name character varying(255) NOT NULL,
    file_path character varying(255) NOT NULL,
    is_success boolean DEFAULT true NOT NULL,
    size bigint NOT NULL,
    created_at timestamp(0) without time zone DEFAULT CURRENT_TIMESTAMP NOT NULL
);


--
-- Name: backup_files_backup_files_id_seq; Type: SEQUENCE; Schema: public; Owner: -
--

CREATE SEQUENCE public.backup_files_backup_files_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


--
-- Name: backup_files_backup_files_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: -
--

ALTER SEQUENCE public.backup_files_backup_files_id_seq OWNED BY public.backup_files.backup_files_id;


--
-- Name: backup_settings; Type: TABLE; Schema: public; Owner: -
--

CREATE TABLE public.backup_settings (
    backup_settings_id bigint NOT NULL,
    settings_key character varying(50) DEFAULT 'default'::character varying NOT NULL,
    is_enabled boolean DEFAULT false NOT NULL,
    run_time time(0) without time zone DEFAULT '02:00:00'::time without time zone NOT NULL,
    frequency character varying(20) DEFAULT 'daily'::character varying NOT NULL,
    retention_count smallint DEFAULT '7'::smallint NOT NULL,
    created_at timestamp(0) without time zone,
    updated_at timestamp(0) without time zone
);


--
-- Name: backup_settings_backup_settings_id_seq; Type: SEQUENCE; Schema: public; Owner: -
--

CREATE SEQUENCE public.backup_settings_backup_settings_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


--
-- Name: backup_settings_backup_settings_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: -
--

ALTER SEQUENCE public.backup_settings_backup_settings_id_seq OWNED BY public.backup_settings.backup_settings_id;


--
-- Name: cache; Type: TABLE; Schema: public; Owner: -
--

CREATE TABLE public.cache (
    key character varying(255) NOT NULL,
    value text NOT NULL,
    expiration integer NOT NULL
);


--
-- Name: cache_locks; Type: TABLE; Schema: public; Owner: -
--

CREATE TABLE public.cache_locks (
    key character varying(255) NOT NULL,
    owner character varying(255) NOT NULL,
    expiration integer NOT NULL
);


--
-- Name: failed_jobs; Type: TABLE; Schema: public; Owner: -
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


--
-- Name: failed_jobs_id_seq; Type: SEQUENCE; Schema: public; Owner: -
--

CREATE SEQUENCE public.failed_jobs_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


--
-- Name: failed_jobs_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: -
--

ALTER SEQUENCE public.failed_jobs_id_seq OWNED BY public.failed_jobs.id;


--
-- Name: job_batches; Type: TABLE; Schema: public; Owner: -
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


--
-- Name: jobs; Type: TABLE; Schema: public; Owner: -
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


--
-- Name: jobs_id_seq; Type: SEQUENCE; Schema: public; Owner: -
--

CREATE SEQUENCE public.jobs_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


--
-- Name: jobs_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: -
--

ALTER SEQUENCE public.jobs_id_seq OWNED BY public.jobs.id;


--
-- Name: line_accounts; Type: TABLE; Schema: public; Owner: -
--

CREATE TABLE public.line_accounts (
    line_accounts_id bigint NOT NULL,
    user_id bigint NOT NULL,
    line_user_id character varying(255) NOT NULL,
    line_link_token character varying(255),
    is_linked character varying(255) DEFAULT '0'::character varying NOT NULL,
    created_at timestamp(0) without time zone,
    updated_at timestamp(0) without time zone
);


--
-- Name: line_accounts_line_accounts_id_seq; Type: SEQUENCE; Schema: public; Owner: -
--

CREATE SEQUENCE public.line_accounts_line_accounts_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


--
-- Name: line_accounts_line_accounts_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: -
--

ALTER SEQUENCE public.line_accounts_line_accounts_id_seq OWNED BY public.line_accounts.line_accounts_id;


--
-- Name: migrations; Type: TABLE; Schema: public; Owner: -
--

CREATE TABLE public.migrations (
    id integer NOT NULL,
    migration character varying(255) NOT NULL,
    batch integer NOT NULL
);


--
-- Name: migrations_id_seq; Type: SEQUENCE; Schema: public; Owner: -
--

CREATE SEQUENCE public.migrations_id_seq
    AS integer
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


--
-- Name: migrations_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: -
--

ALTER SEQUENCE public.migrations_id_seq OWNED BY public.migrations.id;


--
-- Name: password_reset_tokens; Type: TABLE; Schema: public; Owner: -
--

CREATE TABLE public.password_reset_tokens (
    email character varying(255) NOT NULL,
    token character varying(255) NOT NULL,
    created_at timestamp(0) without time zone
);


--
-- Name: personal_access_tokens; Type: TABLE; Schema: public; Owner: -
--

CREATE TABLE public.personal_access_tokens (
    id bigint NOT NULL,
    tokenable_type character varying(255) NOT NULL,
    tokenable_id bigint NOT NULL,
    name text NOT NULL,
    token character varying(64) NOT NULL,
    abilities text,
    last_used_at timestamp(0) without time zone,
    expires_at timestamp(0) without time zone,
    created_at timestamp(0) without time zone,
    updated_at timestamp(0) without time zone
);


--
-- Name: personal_access_tokens_id_seq; Type: SEQUENCE; Schema: public; Owner: -
--

CREATE SEQUENCE public.personal_access_tokens_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


--
-- Name: personal_access_tokens_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: -
--

ALTER SEQUENCE public.personal_access_tokens_id_seq OWNED BY public.personal_access_tokens.id;


--
-- Name: qualification; Type: TABLE; Schema: public; Owner: -
--

CREATE TABLE public.qualification (
    qualification_id bigint NOT NULL,
    name character varying(255) NOT NULL,
    is_active boolean DEFAULT true NOT NULL,
    created_at timestamp(0) without time zone,
    updated_at timestamp(0) without time zone
);


--
-- Name: qualification_domains; Type: TABLE; Schema: public; Owner: -
--

CREATE TABLE public.qualification_domains (
    qualification_domains_id bigint NOT NULL,
    qualification_id bigint NOT NULL,
    name character varying(255) NOT NULL,
    is_active boolean DEFAULT true NOT NULL,
    created_at timestamp(0) without time zone,
    updated_at timestamp(0) without time zone
);


--
-- Name: qualification_domains_qualification_domains_id_seq; Type: SEQUENCE; Schema: public; Owner: -
--

CREATE SEQUENCE public.qualification_domains_qualification_domains_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


--
-- Name: qualification_domains_qualification_domains_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: -
--

ALTER SEQUENCE public.qualification_domains_qualification_domains_id_seq OWNED BY public.qualification_domains.qualification_domains_id;


--
-- Name: qualification_qualification_id_seq; Type: SEQUENCE; Schema: public; Owner: -
--

CREATE SEQUENCE public.qualification_qualification_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


--
-- Name: qualification_qualification_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: -
--

ALTER SEQUENCE public.qualification_qualification_id_seq OWNED BY public.qualification.qualification_id;


--
-- Name: qualification_subdomains; Type: TABLE; Schema: public; Owner: -
--

CREATE TABLE public.qualification_subdomains (
    qualification_subdomains_id bigint NOT NULL,
    qualification_domains_id bigint NOT NULL,
    name character varying(255) NOT NULL,
    is_active boolean DEFAULT true NOT NULL,
    created_at timestamp(0) without time zone,
    updated_at timestamp(0) without time zone
);


--
-- Name: qualification_subdomains_qualification_subdomains_id_seq; Type: SEQUENCE; Schema: public; Owner: -
--

CREATE SEQUENCE public.qualification_subdomains_qualification_subdomains_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


--
-- Name: qualification_subdomains_qualification_subdomains_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: -
--

ALTER SEQUENCE public.qualification_subdomains_qualification_subdomains_id_seq OWNED BY public.qualification_subdomains.qualification_subdomains_id;


--
-- Name: sessions; Type: TABLE; Schema: public; Owner: -
--

CREATE TABLE public.sessions (
    id character varying(255) NOT NULL,
    user_id bigint,
    ip_address character varying(45),
    user_agent text,
    payload text NOT NULL,
    last_activity integer NOT NULL
);


--
-- Name: study_plan_items; Type: TABLE; Schema: public; Owner: -
--

CREATE TABLE public.study_plan_items (
    study_plan_items_id bigint NOT NULL,
    todo_id bigint NOT NULL,
    qualification_domains_id bigint,
    qualification_subdomains_id bigint,
    planned_minutes smallint NOT NULL,
    status boolean DEFAULT false NOT NULL,
    CONSTRAINT ck_spi_domain_or_subdomain CHECK (((qualification_domains_id IS NOT NULL) OR (qualification_subdomains_id IS NOT NULL)))
);


--
-- Name: study_plan_items_study_plan_items_id_seq; Type: SEQUENCE; Schema: public; Owner: -
--

CREATE SEQUENCE public.study_plan_items_study_plan_items_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


--
-- Name: study_plan_items_study_plan_items_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: -
--

ALTER SEQUENCE public.study_plan_items_study_plan_items_id_seq OWNED BY public.study_plan_items.study_plan_items_id;


--
-- Name: study_plans; Type: TABLE; Schema: public; Owner: -
--

CREATE TABLE public.study_plans (
    study_plans_id bigint NOT NULL,
    user_qualification_targets_id bigint NOT NULL,
    version smallint DEFAULT '1'::smallint NOT NULL,
    is_active boolean DEFAULT true NOT NULL,
    created_at timestamp(0) without time zone,
    updated_at timestamp(0) without time zone
);


--
-- Name: study_plans_study_plans_id_seq; Type: SEQUENCE; Schema: public; Owner: -
--

CREATE SEQUENCE public.study_plans_study_plans_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


--
-- Name: study_plans_study_plans_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: -
--

ALTER SEQUENCE public.study_plans_study_plans_id_seq OWNED BY public.study_plans.study_plans_id;


--
-- Name: study_records; Type: TABLE; Schema: public; Owner: -
--

CREATE TABLE public.study_records (
    study_records_id bigint NOT NULL,
    todo_id bigint NOT NULL,
    study_plan_items_id bigint NOT NULL,
    actual_minutes smallint NOT NULL,
    is_completed boolean DEFAULT false NOT NULL,
    created_at timestamp(0) without time zone,
    updated_at timestamp(0) without time zone
);


--
-- Name: study_records_study_records_id_seq; Type: SEQUENCE; Schema: public; Owner: -
--

CREATE SEQUENCE public.study_records_study_records_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


--
-- Name: study_records_study_records_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: -
--

ALTER SEQUENCE public.study_records_study_records_id_seq OWNED BY public.study_records.study_records_id;


--
-- Name: todo; Type: TABLE; Schema: public; Owner: -
--

CREATE TABLE public.todo (
    todo_id bigint NOT NULL,
    study_plans_id bigint NOT NULL,
    date date NOT NULL,
    memo text,
    created_at timestamp(0) without time zone,
    updated_at timestamp(0) without time zone
);


--
-- Name: todo_todo_id_seq; Type: SEQUENCE; Schema: public; Owner: -
--

CREATE SEQUENCE public.todo_todo_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


--
-- Name: todo_todo_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: -
--

ALTER SEQUENCE public.todo_todo_id_seq OWNED BY public.todo.todo_id;


--
-- Name: user_domain_preferences; Type: TABLE; Schema: public; Owner: -
--

CREATE TABLE public.user_domain_preferences (
    user_domain_preferences_id bigint NOT NULL,
    user_qualification_targets_id bigint NOT NULL,
    qualification_domains_id bigint NOT NULL,
    weight smallint NOT NULL
);


--
-- Name: user_domain_preferences_user_domain_preferences_id_seq; Type: SEQUENCE; Schema: public; Owner: -
--

CREATE SEQUENCE public.user_domain_preferences_user_domain_preferences_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


--
-- Name: user_domain_preferences_user_domain_preferences_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: -
--

ALTER SEQUENCE public.user_domain_preferences_user_domain_preferences_id_seq OWNED BY public.user_domain_preferences.user_domain_preferences_id;


--
-- Name: user_no_study_days; Type: TABLE; Schema: public; Owner: -
--

CREATE TABLE public.user_no_study_days (
    user_no_study_days_id bigint NOT NULL,
    user_qualification_targets_id bigint NOT NULL,
    no_study_day date NOT NULL,
    created_at timestamp(0) without time zone,
    updated_at timestamp(0) without time zone
);


--
-- Name: user_no_study_days_user_no_study_days_id_seq; Type: SEQUENCE; Schema: public; Owner: -
--

CREATE SEQUENCE public.user_no_study_days_user_no_study_days_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


--
-- Name: user_no_study_days_user_no_study_days_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: -
--

ALTER SEQUENCE public.user_no_study_days_user_no_study_days_id_seq OWNED BY public.user_no_study_days.user_no_study_days_id;


--
-- Name: user_qualification_targets; Type: TABLE; Schema: public; Owner: -
--

CREATE TABLE public.user_qualification_targets (
    user_qualification_targets_id bigint CONSTRAINT user_qualification_targets_user_qualification_targets__not_null NOT NULL,
    user_id bigint NOT NULL,
    qualification_id bigint NOT NULL,
    start_date date NOT NULL,
    exam_date date NOT NULL,
    daily_study_time smallint NOT NULL,
    buffer_rate smallint NOT NULL,
    created_at timestamp(0) without time zone,
    updated_at timestamp(0) without time zone
);


--
-- Name: user_qualification_targets_user_qualification_targets_id_seq; Type: SEQUENCE; Schema: public; Owner: -
--

CREATE SEQUENCE public.user_qualification_targets_user_qualification_targets_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


--
-- Name: user_qualification_targets_user_qualification_targets_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: -
--

ALTER SEQUENCE public.user_qualification_targets_user_qualification_targets_id_seq OWNED BY public.user_qualification_targets.user_qualification_targets_id;


--
-- Name: user_subdomain_preferences; Type: TABLE; Schema: public; Owner: -
--

CREATE TABLE public.user_subdomain_preferences (
    user_subdomain_preferences_id bigint CONSTRAINT user_subdomain_preferences_user_subdomain_preferences__not_null NOT NULL,
    user_qualification_targets_id bigint CONSTRAINT user_subdomain_preferences_user_qualification_targets__not_null NOT NULL,
    qualification_subdomains_id bigint NOT NULL,
    weight smallint NOT NULL
);


--
-- Name: user_subdomain_preferences_user_subdomain_preferences_id_seq; Type: SEQUENCE; Schema: public; Owner: -
--

CREATE SEQUENCE public.user_subdomain_preferences_user_subdomain_preferences_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


--
-- Name: user_subdomain_preferences_user_subdomain_preferences_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: -
--

ALTER SEQUENCE public.user_subdomain_preferences_user_subdomain_preferences_id_seq OWNED BY public.user_subdomain_preferences.user_subdomain_preferences_id;


--
-- Name: users; Type: TABLE; Schema: public; Owner: -
--

CREATE TABLE public.users (
    id bigint NOT NULL,
    name character varying(255) NOT NULL,
    email character varying(255) NOT NULL,
    email_verified_at timestamp(0) without time zone,
    password character varying(255) NOT NULL,
    remember_token character varying(100),
    current_team_id bigint,
    profile_photo_path character varying(2048),
    created_at timestamp(0) without time zone,
    updated_at timestamp(0) without time zone,
    two_factor_secret text,
    two_factor_recovery_codes text,
    two_factor_confirmed_at timestamp(0) without time zone,
    is_active boolean DEFAULT true NOT NULL,
    is_admin boolean DEFAULT false NOT NULL,
    last_login_at timestamp(0) without time zone,
    email_morning_time time(0) without time zone,
    email_evening_time time(0) without time zone,
    line_morning_time time(0) without time zone,
    line_evening_time time(0) without time zone
);


--
-- Name: users_id_seq; Type: SEQUENCE; Schema: public; Owner: -
--

CREATE SEQUENCE public.users_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


--
-- Name: users_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: -
--

ALTER SEQUENCE public.users_id_seq OWNED BY public.users.id;


--
-- Name: backup_files backup_files_id; Type: DEFAULT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.backup_files ALTER COLUMN backup_files_id SET DEFAULT nextval('public.backup_files_backup_files_id_seq'::regclass);


--
-- Name: backup_settings backup_settings_id; Type: DEFAULT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.backup_settings ALTER COLUMN backup_settings_id SET DEFAULT nextval('public.backup_settings_backup_settings_id_seq'::regclass);


--
-- Name: failed_jobs id; Type: DEFAULT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.failed_jobs ALTER COLUMN id SET DEFAULT nextval('public.failed_jobs_id_seq'::regclass);


--
-- Name: jobs id; Type: DEFAULT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.jobs ALTER COLUMN id SET DEFAULT nextval('public.jobs_id_seq'::regclass);


--
-- Name: line_accounts line_accounts_id; Type: DEFAULT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.line_accounts ALTER COLUMN line_accounts_id SET DEFAULT nextval('public.line_accounts_line_accounts_id_seq'::regclass);


--
-- Name: migrations id; Type: DEFAULT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.migrations ALTER COLUMN id SET DEFAULT nextval('public.migrations_id_seq'::regclass);


--
-- Name: personal_access_tokens id; Type: DEFAULT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.personal_access_tokens ALTER COLUMN id SET DEFAULT nextval('public.personal_access_tokens_id_seq'::regclass);


--
-- Name: qualification qualification_id; Type: DEFAULT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.qualification ALTER COLUMN qualification_id SET DEFAULT nextval('public.qualification_qualification_id_seq'::regclass);


--
-- Name: qualification_domains qualification_domains_id; Type: DEFAULT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.qualification_domains ALTER COLUMN qualification_domains_id SET DEFAULT nextval('public.qualification_domains_qualification_domains_id_seq'::regclass);


--
-- Name: qualification_subdomains qualification_subdomains_id; Type: DEFAULT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.qualification_subdomains ALTER COLUMN qualification_subdomains_id SET DEFAULT nextval('public.qualification_subdomains_qualification_subdomains_id_seq'::regclass);


--
-- Name: study_plan_items study_plan_items_id; Type: DEFAULT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.study_plan_items ALTER COLUMN study_plan_items_id SET DEFAULT nextval('public.study_plan_items_study_plan_items_id_seq'::regclass);


--
-- Name: study_plans study_plans_id; Type: DEFAULT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.study_plans ALTER COLUMN study_plans_id SET DEFAULT nextval('public.study_plans_study_plans_id_seq'::regclass);


--
-- Name: study_records study_records_id; Type: DEFAULT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.study_records ALTER COLUMN study_records_id SET DEFAULT nextval('public.study_records_study_records_id_seq'::regclass);


--
-- Name: todo todo_id; Type: DEFAULT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.todo ALTER COLUMN todo_id SET DEFAULT nextval('public.todo_todo_id_seq'::regclass);


--
-- Name: user_domain_preferences user_domain_preferences_id; Type: DEFAULT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.user_domain_preferences ALTER COLUMN user_domain_preferences_id SET DEFAULT nextval('public.user_domain_preferences_user_domain_preferences_id_seq'::regclass);


--
-- Name: user_no_study_days user_no_study_days_id; Type: DEFAULT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.user_no_study_days ALTER COLUMN user_no_study_days_id SET DEFAULT nextval('public.user_no_study_days_user_no_study_days_id_seq'::regclass);


--
-- Name: user_qualification_targets user_qualification_targets_id; Type: DEFAULT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.user_qualification_targets ALTER COLUMN user_qualification_targets_id SET DEFAULT nextval('public.user_qualification_targets_user_qualification_targets_id_seq'::regclass);


--
-- Name: user_subdomain_preferences user_subdomain_preferences_id; Type: DEFAULT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.user_subdomain_preferences ALTER COLUMN user_subdomain_preferences_id SET DEFAULT nextval('public.user_subdomain_preferences_user_subdomain_preferences_id_seq'::regclass);


--
-- Name: users id; Type: DEFAULT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.users ALTER COLUMN id SET DEFAULT nextval('public.users_id_seq'::regclass);


--
-- Data for Name: backup_files; Type: TABLE DATA; Schema: public; Owner: -
--

COPY public.backup_files (backup_files_id, is_auto, file_name, file_path, is_success, size, created_at) FROM stdin;
\.


--
-- Data for Name: backup_settings; Type: TABLE DATA; Schema: public; Owner: -
--

COPY public.backup_settings (backup_settings_id, settings_key, is_enabled, run_time, frequency, retention_count, created_at, updated_at) FROM stdin;
\.


--
-- Data for Name: cache; Type: TABLE DATA; Schema: public; Owner: -
--

COPY public.cache (key, value, expiration) FROM stdin;
\.


--
-- Data for Name: cache_locks; Type: TABLE DATA; Schema: public; Owner: -
--

COPY public.cache_locks (key, owner, expiration) FROM stdin;
\.


--
-- Data for Name: failed_jobs; Type: TABLE DATA; Schema: public; Owner: -
--

COPY public.failed_jobs (id, uuid, connection, queue, payload, exception, failed_at) FROM stdin;
\.


--
-- Data for Name: job_batches; Type: TABLE DATA; Schema: public; Owner: -
--

COPY public.job_batches (id, name, total_jobs, pending_jobs, failed_jobs, failed_job_ids, options, cancelled_at, created_at, finished_at) FROM stdin;
\.


--
-- Data for Name: jobs; Type: TABLE DATA; Schema: public; Owner: -
--

COPY public.jobs (id, queue, payload, attempts, reserved_at, available_at, created_at) FROM stdin;
\.


--
-- Data for Name: line_accounts; Type: TABLE DATA; Schema: public; Owner: -
--

COPY public.line_accounts (line_accounts_id, user_id, line_user_id, line_link_token, is_linked, created_at, updated_at) FROM stdin;
\.


--
-- Data for Name: migrations; Type: TABLE DATA; Schema: public; Owner: -
--

COPY public.migrations (id, migration, batch) FROM stdin;
1	0001_01_01_000000_create_users_table	1
2	0001_01_01_000001_create_cache_table	1
3	0001_01_01_000002_create_jobs_table	1
4	2025_12_18_000944_add_two_factor_columns_to_users_table	1
5	2025_12_18_000957_create_personal_access_tokens_table	1
6	2026_01_29_015518_add_stapla_columns_to_users_table	1
7	2026_01_29_021516_create_line_accounts_table	1
8	2026_01_29_060434_create_qualification_table	1
9	2026_01_30_000103_create_qualification_domains_table	1
10	2026_01_30_003619_create_qualification_subdomains_table	1
11	2026_01_30_010439_create_user_qualification_targets_table	1
12	2026_01_30_021607_create_user_no_study_days_table	1
13	2026_01_30_032344_create_user_domain_preferences_table	1
14	2026_01_30_050910_create_user_subdomain_preferences_table	1
15	2026_01_30_051531_create_study_plans_table	1
16	2026_01_30_060643_add_unique_user_qualification_to_user_qualification_targets_table	1
17	2026_01_30_061500_create_todo_table	1
18	2026_01_30_064627_create_study_plan_items_table	1
19	2026_02_01_235132_create_study_records_table	1
20	2026_02_02_022323_create_backup_files_table	1
21	2026_02_02_022748_create_backup_settings_table	1
\.


--
-- Data for Name: password_reset_tokens; Type: TABLE DATA; Schema: public; Owner: -
--

COPY public.password_reset_tokens (email, token, created_at) FROM stdin;
\.


--
-- Data for Name: personal_access_tokens; Type: TABLE DATA; Schema: public; Owner: -
--

COPY public.personal_access_tokens (id, tokenable_type, tokenable_id, name, token, abilities, last_used_at, expires_at, created_at, updated_at) FROM stdin;
\.


--
-- Data for Name: qualification; Type: TABLE DATA; Schema: public; Owner: -
--

COPY public.qualification (qualification_id, name, is_active, created_at, updated_at) FROM stdin;
1	ITパスポート	t	2026-02-05 00:01:48	2026-02-05 00:01:48
2	基本情報技術者	t	2026-02-05 00:01:48	2026-02-05 00:01:48
3	応用情報技術者	t	2026-02-05 00:01:48	2026-02-05 00:01:48
4	ITストラテジスト	t	2026-02-05 00:01:48	2026-02-05 00:01:48
5	システムアーキテクト	t	2026-02-05 00:01:48	2026-02-05 00:01:48
6	プロジェクトマネージャ	t	2026-02-05 00:01:48	2026-02-05 00:01:48
7	ネットワークスペシャリスト	t	2026-02-05 00:01:48	2026-02-05 00:01:48
8	データベーススペシャリスト	t	2026-02-05 00:01:48	2026-02-05 00:01:48
9	情報処理安全確保支援士	t	2026-02-05 00:01:48	2026-02-05 00:01:48
10	ITサービスマネージャ	t	2026-02-05 00:01:48	2026-02-05 00:01:48
11	エンベデッドシステムスペシャリスト	t	2026-02-05 00:01:48	2026-02-05 00:01:48
\.


--
-- Data for Name: qualification_domains; Type: TABLE DATA; Schema: public; Owner: -
--

COPY public.qualification_domains (qualification_domains_id, qualification_id, name, is_active, created_at, updated_at) FROM stdin;
1	1	ストラテジ系	t	2026-02-05 00:01:48	2026-02-05 00:01:48
2	1	マネジメント系	t	2026-02-05 00:01:48	2026-02-05 00:01:48
3	1	テクノロジ系	t	2026-02-05 00:01:48	2026-02-05 00:01:48
4	2	テクノロジ系	t	2026-02-05 00:01:48	2026-02-05 00:01:48
5	2	マネジメント系	t	2026-02-05 00:01:48	2026-02-05 00:01:48
6	2	ストラテジ系	t	2026-02-05 00:01:48	2026-02-05 00:01:48
7	3	テクノロジ系	t	2026-02-05 00:01:48	2026-02-05 00:01:48
8	3	マネジメント系	t	2026-02-05 00:01:48	2026-02-05 00:01:48
9	3	ストラテジ系	t	2026-02-05 00:01:48	2026-02-05 00:01:48
10	4	ストラテジ	t	2026-02-05 00:01:48	2026-02-05 00:01:48
11	4	マネジメント	t	2026-02-05 00:01:48	2026-02-05 00:01:48
12	5	システム設計	t	2026-02-05 00:01:48	2026-02-05 00:01:48
13	5	テクノロジ	t	2026-02-05 00:01:48	2026-02-05 00:01:48
14	6	プロジェクト管理	t	2026-02-05 00:01:48	2026-02-05 00:01:48
15	6	組織・契約	t	2026-02-05 00:01:48	2026-02-05 00:01:48
16	7	ネットワーク技術	t	2026-02-05 00:01:48	2026-02-05 00:01:48
17	7	設計・運用	t	2026-02-05 00:01:48	2026-02-05 00:01:48
18	7	セキュリティ	t	2026-02-05 00:01:48	2026-02-05 00:01:48
19	8	データベース技術	t	2026-02-05 00:01:48	2026-02-05 00:01:48
20	8	設計・運用	t	2026-02-05 00:01:48	2026-02-05 00:01:48
21	9	セキュリティ技術	t	2026-02-05 00:01:48	2026-02-05 00:01:48
22	9	管理・運用	t	2026-02-05 00:01:48	2026-02-05 00:01:48
23	10	ITサービス管理	t	2026-02-05 00:01:48	2026-02-05 00:01:48
24	10	運用管理	t	2026-02-05 00:01:48	2026-02-05 00:01:48
25	11	組込み技術	t	2026-02-05 00:01:48	2026-02-05 00:01:48
26	11	開発管理	t	2026-02-05 00:01:48	2026-02-05 00:01:48
\.


--
-- Data for Name: qualification_subdomains; Type: TABLE DATA; Schema: public; Owner: -
--

COPY public.qualification_subdomains (qualification_subdomains_id, qualification_domains_id, name, is_active, created_at, updated_at) FROM stdin;
1	1	企業活動	t	2026-02-05 00:01:48	2026-02-05 00:01:48
2	1	法務	t	2026-02-05 00:01:48	2026-02-05 00:01:48
3	1	経営戦略	t	2026-02-05 00:01:48	2026-02-05 00:01:48
4	1	システム戦略	t	2026-02-05 00:01:48	2026-02-05 00:01:48
5	2	システム開発技術（概要）	t	2026-02-05 00:01:48	2026-02-05 00:01:48
6	2	プロジェクトマネジメント	t	2026-02-05 00:01:48	2026-02-05 00:01:48
7	2	サービスマネジメント	t	2026-02-05 00:01:48	2026-02-05 00:01:48
8	3	基礎理論	t	2026-02-05 00:01:48	2026-02-05 00:01:48
9	3	アルゴリズムとプログラミング	t	2026-02-05 00:01:48	2026-02-05 00:01:48
10	3	コンピュータ構成要素	t	2026-02-05 00:01:48	2026-02-05 00:01:48
11	3	システム構成要素	t	2026-02-05 00:01:48	2026-02-05 00:01:48
12	3	ソフトウェア	t	2026-02-05 00:01:48	2026-02-05 00:01:48
13	3	ハードウェア	t	2026-02-05 00:01:48	2026-02-05 00:01:48
14	3	ネットワーク	t	2026-02-05 00:01:48	2026-02-05 00:01:48
15	3	セキュリティ	t	2026-02-05 00:01:48	2026-02-05 00:01:48
16	4	基礎理論	t	2026-02-05 00:01:48	2026-02-05 00:01:48
17	4	アルゴリズムとデータ構造	t	2026-02-05 00:01:48	2026-02-05 00:01:48
18	4	プログラミング	t	2026-02-05 00:01:48	2026-02-05 00:01:48
19	4	コンピュータ構成要素	t	2026-02-05 00:01:48	2026-02-05 00:01:48
20	4	オペレーティングシステム	t	2026-02-05 00:01:48	2026-02-05 00:01:48
21	4	ソフトウェア	t	2026-02-05 00:01:48	2026-02-05 00:01:48
22	4	データベース	t	2026-02-05 00:01:48	2026-02-05 00:01:48
23	4	ネットワーク	t	2026-02-05 00:01:48	2026-02-05 00:01:48
24	4	セキュリティ	t	2026-02-05 00:01:48	2026-02-05 00:01:48
25	5	プロジェクトマネジメント	t	2026-02-05 00:01:48	2026-02-05 00:01:48
26	5	サービスマネジメント	t	2026-02-05 00:01:48	2026-02-05 00:01:48
27	5	システム監査	t	2026-02-05 00:01:48	2026-02-05 00:01:48
28	6	システム戦略	t	2026-02-05 00:01:48	2026-02-05 00:01:48
29	6	経営戦略	t	2026-02-05 00:01:48	2026-02-05 00:01:48
30	6	企業活動	t	2026-02-05 00:01:48	2026-02-05 00:01:48
31	6	法務	t	2026-02-05 00:01:48	2026-02-05 00:01:48
32	7	基礎理論	t	2026-02-05 00:01:48	2026-02-05 00:01:48
33	7	アルゴリズム	t	2026-02-05 00:01:48	2026-02-05 00:01:48
34	7	データ構造	t	2026-02-05 00:01:48	2026-02-05 00:01:48
35	7	コンピュータアーキテクチャ	t	2026-02-05 00:01:48	2026-02-05 00:01:48
36	7	オペレーティングシステム	t	2026-02-05 00:01:48	2026-02-05 00:01:48
37	7	ソフトウェア工学	t	2026-02-05 00:01:48	2026-02-05 00:01:48
38	7	データベース	t	2026-02-05 00:01:48	2026-02-05 00:01:48
39	7	ネットワーク	t	2026-02-05 00:01:48	2026-02-05 00:01:48
40	7	情報セキュリティ	t	2026-02-05 00:01:48	2026-02-05 00:01:48
41	7	組込みシステム	t	2026-02-05 00:01:48	2026-02-05 00:01:48
42	7	システム開発技術	t	2026-02-05 00:01:48	2026-02-05 00:01:48
43	8	プロジェクトマネジメント	t	2026-02-05 00:01:48	2026-02-05 00:01:48
44	8	サービスマネジメント	t	2026-02-05 00:01:48	2026-02-05 00:01:48
45	8	システム監査	t	2026-02-05 00:01:48	2026-02-05 00:01:48
46	9	システム戦略	t	2026-02-05 00:01:48	2026-02-05 00:01:48
47	9	経営戦略	t	2026-02-05 00:01:48	2026-02-05 00:01:48
48	9	企業活動	t	2026-02-05 00:01:48	2026-02-05 00:01:48
49	9	法務	t	2026-02-05 00:01:48	2026-02-05 00:01:48
50	9	標準化	t	2026-02-05 00:01:48	2026-02-05 00:01:48
51	10	経営戦略立案	t	2026-02-05 00:01:48	2026-02-05 00:01:48
52	10	IT投資戦略	t	2026-02-05 00:01:48	2026-02-05 00:01:48
53	10	業務改革（BPR）	t	2026-02-05 00:01:48	2026-02-05 00:01:48
54	10	ITガバナンス	t	2026-02-05 00:01:48	2026-02-05 00:01:48
55	10	法務・コンプライアンス	t	2026-02-05 00:01:48	2026-02-05 00:01:48
56	11	プロジェクト統制	t	2026-02-05 00:01:48	2026-02-05 00:01:48
57	11	ITサービス戦略	t	2026-02-05 00:01:48	2026-02-05 00:01:48
58	12	要件定義	t	2026-02-05 00:01:48	2026-02-05 00:01:48
59	12	システム方式設計	t	2026-02-05 00:01:48	2026-02-05 00:01:48
60	12	アプリケーション設計	t	2026-02-05 00:01:48	2026-02-05 00:01:48
61	12	非機能要件設計	t	2026-02-05 00:01:48	2026-02-05 00:01:48
62	13	ソフトウェア設計	t	2026-02-05 00:01:48	2026-02-05 00:01:48
63	13	データベース設計	t	2026-02-05 00:01:48	2026-02-05 00:01:48
64	13	ネットワーク設計	t	2026-02-05 00:01:48	2026-02-05 00:01:48
65	13	セキュリティ設計	t	2026-02-05 00:01:48	2026-02-05 00:01:48
66	14	スコープ管理	t	2026-02-05 00:01:48	2026-02-05 00:01:48
67	14	スケジュール管理	t	2026-02-05 00:01:48	2026-02-05 00:01:48
68	14	コスト管理	t	2026-02-05 00:01:48	2026-02-05 00:01:48
69	14	リスク管理	t	2026-02-05 00:01:48	2026-02-05 00:01:48
70	14	品質管理	t	2026-02-05 00:01:48	2026-02-05 00:01:48
71	14	調達管理	t	2026-02-05 00:01:48	2026-02-05 00:01:48
72	15	組織マネジメント	t	2026-02-05 00:01:48	2026-02-05 00:01:48
73	15	契約・法務	t	2026-02-05 00:01:48	2026-02-05 00:01:48
74	16	TCP/IP	t	2026-02-05 00:01:48	2026-02-05 00:01:48
75	16	ルーティング	t	2026-02-05 00:01:48	2026-02-05 00:01:48
76	16	スイッチング	t	2026-02-05 00:01:48	2026-02-05 00:01:48
77	16	WAN/LAN	t	2026-02-05 00:01:48	2026-02-05 00:01:48
78	16	無線ネットワーク	t	2026-02-05 00:01:48	2026-02-05 00:01:48
79	17	ネットワーク設計	t	2026-02-05 00:01:48	2026-02-05 00:01:48
80	17	パフォーマンス設計	t	2026-02-05 00:01:48	2026-02-05 00:01:48
81	17	障害対応	t	2026-02-05 00:01:48	2026-02-05 00:01:48
82	17	運用管理	t	2026-02-05 00:01:48	2026-02-05 00:01:48
83	18	ネットワークセキュリティ	t	2026-02-05 00:01:48	2026-02-05 00:01:48
84	18	暗号技術	t	2026-02-05 00:01:48	2026-02-05 00:01:48
85	18	認証・認可	t	2026-02-05 00:01:48	2026-02-05 00:01:48
86	19	データモデル	t	2026-02-05 00:01:48	2026-02-05 00:01:48
87	19	正規化	t	2026-02-05 00:01:48	2026-02-05 00:01:48
88	19	SQL	t	2026-02-05 00:01:48	2026-02-05 00:01:48
89	19	トランザクション管理	t	2026-02-05 00:01:48	2026-02-05 00:01:48
90	19	排他制御	t	2026-02-05 00:01:48	2026-02-05 00:01:48
91	20	論理設計	t	2026-02-05 00:01:48	2026-02-05 00:01:48
92	20	物理設計	t	2026-02-05 00:01:48	2026-02-05 00:01:48
93	20	パフォーマンスチューニング	t	2026-02-05 00:01:48	2026-02-05 00:01:48
94	20	バックアップ・リカバリ	t	2026-02-05 00:01:48	2026-02-05 00:01:48
95	21	暗号	t	2026-02-05 00:01:48	2026-02-05 00:01:48
96	21	認証技術	t	2026-02-05 00:01:48	2026-02-05 00:01:48
97	21	ネットワークセキュリティ	t	2026-02-05 00:01:48	2026-02-05 00:01:48
98	21	Webセキュリティ	t	2026-02-05 00:01:48	2026-02-05 00:01:48
99	21	マルウェア対策	t	2026-02-05 00:01:48	2026-02-05 00:01:48
100	22	セキュリティポリシー	t	2026-02-05 00:01:48	2026-02-05 00:01:48
101	22	インシデント対応	t	2026-02-05 00:01:48	2026-02-05 00:01:48
102	22	リスクマネジメント	t	2026-02-05 00:01:48	2026-02-05 00:01:48
103	22	法務・標準	t	2026-02-05 00:01:48	2026-02-05 00:01:48
104	23	ITIL	t	2026-02-05 00:01:48	2026-02-05 00:01:48
105	23	サービス設計	t	2026-02-05 00:01:48	2026-02-05 00:01:48
106	23	サービス運用	t	2026-02-05 00:01:48	2026-02-05 00:01:48
107	23	SLA管理	t	2026-02-05 00:01:48	2026-02-05 00:01:48
108	24	障害管理	t	2026-02-05 00:01:48	2026-02-05 00:01:48
109	24	構成管理	t	2026-02-05 00:01:48	2026-02-05 00:01:48
110	24	継続的改善	t	2026-02-05 00:01:48	2026-02-05 00:01:48
111	25	ハードウェア制御	t	2026-02-05 00:01:48	2026-02-05 00:01:48
112	25	リアルタイムOS	t	2026-02-05 00:01:48	2026-02-05 00:01:48
113	25	組込みソフトウェア設計	t	2026-02-05 00:01:48	2026-02-05 00:01:48
114	26	要件定義	t	2026-02-05 00:01:48	2026-02-05 00:01:48
115	26	品質管理	t	2026-02-05 00:01:48	2026-02-05 00:01:48
116	26	安全設計	t	2026-02-05 00:01:48	2026-02-05 00:01:48
\.


--
-- Data for Name: sessions; Type: TABLE DATA; Schema: public; Owner: -
--

COPY public.sessions (id, user_id, ip_address, user_agent, payload, last_activity) FROM stdin;
lBcHUAeFCIKSZRdN8DOzzOWyWV7Qn2GQYHYe76g7	\N	192.168.65.1	Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/144.0.0.0 Safari/537.36	YTo0OntzOjY6Il90b2tlbiI7czo0MDoiZXdoYVVxVlVXQ1A4cFRTYndMQVV2czg1c0dORVJyWnFVdTQzSDF3ViI7czozOiJ1cmwiO2E6MTp7czo4OiJpbnRlbmRlZCI7czoyMToiaHR0cDovL2xvY2FsaG9zdC9ob21lIjt9czo5OiJfcHJldmlvdXMiO2E6Mjp7czozOiJ1cmwiO3M6MjI6Imh0dHA6Ly9sb2NhbGhvc3QvbG9naW4iO3M6NToicm91dGUiO3M6NToibG9naW4iO31zOjY6Il9mbGFzaCI7YToyOntzOjM6Im9sZCI7YTowOnt9czozOiJuZXciO2E6MDp7fX19	1770249729
\.


--
-- Data for Name: study_plan_items; Type: TABLE DATA; Schema: public; Owner: -
--

COPY public.study_plan_items (study_plan_items_id, todo_id, qualification_domains_id, qualification_subdomains_id, planned_minutes, status) FROM stdin;
\.


--
-- Data for Name: study_plans; Type: TABLE DATA; Schema: public; Owner: -
--

COPY public.study_plans (study_plans_id, user_qualification_targets_id, version, is_active, created_at, updated_at) FROM stdin;
\.


--
-- Data for Name: study_records; Type: TABLE DATA; Schema: public; Owner: -
--

COPY public.study_records (study_records_id, todo_id, study_plan_items_id, actual_minutes, is_completed, created_at, updated_at) FROM stdin;
\.


--
-- Data for Name: todo; Type: TABLE DATA; Schema: public; Owner: -
--

COPY public.todo (todo_id, study_plans_id, date, memo, created_at, updated_at) FROM stdin;
\.


--
-- Data for Name: user_domain_preferences; Type: TABLE DATA; Schema: public; Owner: -
--

COPY public.user_domain_preferences (user_domain_preferences_id, user_qualification_targets_id, qualification_domains_id, weight) FROM stdin;
\.


--
-- Data for Name: user_no_study_days; Type: TABLE DATA; Schema: public; Owner: -
--

COPY public.user_no_study_days (user_no_study_days_id, user_qualification_targets_id, no_study_day, created_at, updated_at) FROM stdin;
\.


--
-- Data for Name: user_qualification_targets; Type: TABLE DATA; Schema: public; Owner: -
--

COPY public.user_qualification_targets (user_qualification_targets_id, user_id, qualification_id, start_date, exam_date, daily_study_time, buffer_rate, created_at, updated_at) FROM stdin;
\.


--
-- Data for Name: user_subdomain_preferences; Type: TABLE DATA; Schema: public; Owner: -
--

COPY public.user_subdomain_preferences (user_subdomain_preferences_id, user_qualification_targets_id, qualification_subdomains_id, weight) FROM stdin;
\.


--
-- Data for Name: users; Type: TABLE DATA; Schema: public; Owner: -
--

COPY public.users (id, name, email, email_verified_at, password, remember_token, current_team_id, profile_photo_path, created_at, updated_at, two_factor_secret, two_factor_recovery_codes, two_factor_confirmed_at, is_active, is_admin, last_login_at, email_morning_time, email_evening_time, line_morning_time, line_evening_time) FROM stdin;
1	Test User	test@example.com	2026-02-05 00:01:48	$2y$12$MNOZg2/T0TtYCRJkzOUQ2u/3D1WkLOLid4Af1dQENWV0RFRmG2bGC	08H2dGkje3	\N	\N	2026-02-05 00:01:48	2026-02-05 00:01:48	\N	\N	\N	t	f	\N	\N	\N	\N	\N
\.


--
-- Name: backup_files_backup_files_id_seq; Type: SEQUENCE SET; Schema: public; Owner: -
--

SELECT pg_catalog.setval('public.backup_files_backup_files_id_seq', 1, false);


--
-- Name: backup_settings_backup_settings_id_seq; Type: SEQUENCE SET; Schema: public; Owner: -
--

SELECT pg_catalog.setval('public.backup_settings_backup_settings_id_seq', 1, false);


--
-- Name: failed_jobs_id_seq; Type: SEQUENCE SET; Schema: public; Owner: -
--

SELECT pg_catalog.setval('public.failed_jobs_id_seq', 1, false);


--
-- Name: jobs_id_seq; Type: SEQUENCE SET; Schema: public; Owner: -
--

SELECT pg_catalog.setval('public.jobs_id_seq', 1, false);


--
-- Name: line_accounts_line_accounts_id_seq; Type: SEQUENCE SET; Schema: public; Owner: -
--

SELECT pg_catalog.setval('public.line_accounts_line_accounts_id_seq', 1, false);


--
-- Name: migrations_id_seq; Type: SEQUENCE SET; Schema: public; Owner: -
--

SELECT pg_catalog.setval('public.migrations_id_seq', 21, true);


--
-- Name: personal_access_tokens_id_seq; Type: SEQUENCE SET; Schema: public; Owner: -
--

SELECT pg_catalog.setval('public.personal_access_tokens_id_seq', 1, false);


--
-- Name: qualification_domains_qualification_domains_id_seq; Type: SEQUENCE SET; Schema: public; Owner: -
--

SELECT pg_catalog.setval('public.qualification_domains_qualification_domains_id_seq', 26, true);


--
-- Name: qualification_qualification_id_seq; Type: SEQUENCE SET; Schema: public; Owner: -
--

SELECT pg_catalog.setval('public.qualification_qualification_id_seq', 11, true);


--
-- Name: qualification_subdomains_qualification_subdomains_id_seq; Type: SEQUENCE SET; Schema: public; Owner: -
--

SELECT pg_catalog.setval('public.qualification_subdomains_qualification_subdomains_id_seq', 116, true);


--
-- Name: study_plan_items_study_plan_items_id_seq; Type: SEQUENCE SET; Schema: public; Owner: -
--

SELECT pg_catalog.setval('public.study_plan_items_study_plan_items_id_seq', 1, false);


--
-- Name: study_plans_study_plans_id_seq; Type: SEQUENCE SET; Schema: public; Owner: -
--

SELECT pg_catalog.setval('public.study_plans_study_plans_id_seq', 1, false);


--
-- Name: study_records_study_records_id_seq; Type: SEQUENCE SET; Schema: public; Owner: -
--

SELECT pg_catalog.setval('public.study_records_study_records_id_seq', 1, false);


--
-- Name: todo_todo_id_seq; Type: SEQUENCE SET; Schema: public; Owner: -
--

SELECT pg_catalog.setval('public.todo_todo_id_seq', 1, false);


--
-- Name: user_domain_preferences_user_domain_preferences_id_seq; Type: SEQUENCE SET; Schema: public; Owner: -
--

SELECT pg_catalog.setval('public.user_domain_preferences_user_domain_preferences_id_seq', 1, false);


--
-- Name: user_no_study_days_user_no_study_days_id_seq; Type: SEQUENCE SET; Schema: public; Owner: -
--

SELECT pg_catalog.setval('public.user_no_study_days_user_no_study_days_id_seq', 1, false);


--
-- Name: user_qualification_targets_user_qualification_targets_id_seq; Type: SEQUENCE SET; Schema: public; Owner: -
--

SELECT pg_catalog.setval('public.user_qualification_targets_user_qualification_targets_id_seq', 1, false);


--
-- Name: user_subdomain_preferences_user_subdomain_preferences_id_seq; Type: SEQUENCE SET; Schema: public; Owner: -
--

SELECT pg_catalog.setval('public.user_subdomain_preferences_user_subdomain_preferences_id_seq', 1, false);


--
-- Name: users_id_seq; Type: SEQUENCE SET; Schema: public; Owner: -
--

SELECT pg_catalog.setval('public.users_id_seq', 1, true);


--
-- Name: backup_files backup_files_pkey; Type: CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.backup_files
    ADD CONSTRAINT backup_files_pkey PRIMARY KEY (backup_files_id);


--
-- Name: backup_settings backup_settings_pkey; Type: CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.backup_settings
    ADD CONSTRAINT backup_settings_pkey PRIMARY KEY (backup_settings_id);


--
-- Name: backup_settings backup_settings_settings_key_unique; Type: CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.backup_settings
    ADD CONSTRAINT backup_settings_settings_key_unique UNIQUE (settings_key);


--
-- Name: cache_locks cache_locks_pkey; Type: CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.cache_locks
    ADD CONSTRAINT cache_locks_pkey PRIMARY KEY (key);


--
-- Name: cache cache_pkey; Type: CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.cache
    ADD CONSTRAINT cache_pkey PRIMARY KEY (key);


--
-- Name: failed_jobs failed_jobs_pkey; Type: CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.failed_jobs
    ADD CONSTRAINT failed_jobs_pkey PRIMARY KEY (id);


--
-- Name: failed_jobs failed_jobs_uuid_unique; Type: CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.failed_jobs
    ADD CONSTRAINT failed_jobs_uuid_unique UNIQUE (uuid);


--
-- Name: job_batches job_batches_pkey; Type: CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.job_batches
    ADD CONSTRAINT job_batches_pkey PRIMARY KEY (id);


--
-- Name: jobs jobs_pkey; Type: CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.jobs
    ADD CONSTRAINT jobs_pkey PRIMARY KEY (id);


--
-- Name: line_accounts line_accounts_line_user_id_unique; Type: CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.line_accounts
    ADD CONSTRAINT line_accounts_line_user_id_unique UNIQUE (line_user_id);


--
-- Name: line_accounts line_accounts_pkey; Type: CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.line_accounts
    ADD CONSTRAINT line_accounts_pkey PRIMARY KEY (line_accounts_id);


--
-- Name: line_accounts line_accounts_user_id_unique; Type: CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.line_accounts
    ADD CONSTRAINT line_accounts_user_id_unique UNIQUE (user_id);


--
-- Name: migrations migrations_pkey; Type: CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.migrations
    ADD CONSTRAINT migrations_pkey PRIMARY KEY (id);


--
-- Name: password_reset_tokens password_reset_tokens_pkey; Type: CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.password_reset_tokens
    ADD CONSTRAINT password_reset_tokens_pkey PRIMARY KEY (email);


--
-- Name: personal_access_tokens personal_access_tokens_pkey; Type: CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.personal_access_tokens
    ADD CONSTRAINT personal_access_tokens_pkey PRIMARY KEY (id);


--
-- Name: personal_access_tokens personal_access_tokens_token_unique; Type: CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.personal_access_tokens
    ADD CONSTRAINT personal_access_tokens_token_unique UNIQUE (token);


--
-- Name: qualification_domains qualification_domains_pkey; Type: CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.qualification_domains
    ADD CONSTRAINT qualification_domains_pkey PRIMARY KEY (qualification_domains_id);


--
-- Name: qualification qualification_name_unique; Type: CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.qualification
    ADD CONSTRAINT qualification_name_unique UNIQUE (name);


--
-- Name: qualification qualification_pkey; Type: CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.qualification
    ADD CONSTRAINT qualification_pkey PRIMARY KEY (qualification_id);


--
-- Name: qualification_subdomains qualification_subdomains_pkey; Type: CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.qualification_subdomains
    ADD CONSTRAINT qualification_subdomains_pkey PRIMARY KEY (qualification_subdomains_id);


--
-- Name: sessions sessions_pkey; Type: CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.sessions
    ADD CONSTRAINT sessions_pkey PRIMARY KEY (id);


--
-- Name: study_plan_items study_plan_items_pkey; Type: CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.study_plan_items
    ADD CONSTRAINT study_plan_items_pkey PRIMARY KEY (study_plan_items_id);


--
-- Name: study_plans study_plans_pkey; Type: CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.study_plans
    ADD CONSTRAINT study_plans_pkey PRIMARY KEY (study_plans_id);


--
-- Name: study_records study_records_pkey; Type: CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.study_records
    ADD CONSTRAINT study_records_pkey PRIMARY KEY (study_records_id);


--
-- Name: todo todo_pkey; Type: CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.todo
    ADD CONSTRAINT todo_pkey PRIMARY KEY (todo_id);


--
-- Name: qualification_domains uq_qualification_domains_qualification_id_name; Type: CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.qualification_domains
    ADD CONSTRAINT uq_qualification_domains_qualification_id_name UNIQUE (qualification_id, name);


--
-- Name: qualification_subdomains uq_qualification_subdomains_domain_id_name; Type: CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.qualification_subdomains
    ADD CONSTRAINT uq_qualification_subdomains_domain_id_name UNIQUE (qualification_domains_id, name);


--
-- Name: study_plans uq_sp_target_version; Type: CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.study_plans
    ADD CONSTRAINT uq_sp_target_version UNIQUE (user_qualification_targets_id, version);


--
-- Name: todo uq_todo_plan_date; Type: CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.todo
    ADD CONSTRAINT uq_todo_plan_date UNIQUE (study_plans_id, date);


--
-- Name: user_domain_preferences uq_udp_target_domain; Type: CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.user_domain_preferences
    ADD CONSTRAINT uq_udp_target_domain UNIQUE (user_qualification_targets_id, qualification_domains_id);


--
-- Name: user_no_study_days uq_unstd_taeget_day; Type: CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.user_no_study_days
    ADD CONSTRAINT uq_unstd_taeget_day UNIQUE (user_qualification_targets_id, no_study_day);


--
-- Name: user_qualification_targets uq_uqt_user_qualification; Type: CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.user_qualification_targets
    ADD CONSTRAINT uq_uqt_user_qualification UNIQUE (user_id, qualification_id);


--
-- Name: user_subdomain_preferences uq_usdp_target_subdomain; Type: CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.user_subdomain_preferences
    ADD CONSTRAINT uq_usdp_target_subdomain UNIQUE (user_qualification_targets_id, qualification_subdomains_id);


--
-- Name: user_domain_preferences user_domain_preferences_pkey; Type: CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.user_domain_preferences
    ADD CONSTRAINT user_domain_preferences_pkey PRIMARY KEY (user_domain_preferences_id);


--
-- Name: user_no_study_days user_no_study_days_pkey; Type: CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.user_no_study_days
    ADD CONSTRAINT user_no_study_days_pkey PRIMARY KEY (user_no_study_days_id);


--
-- Name: user_qualification_targets user_qualification_targets_pkey; Type: CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.user_qualification_targets
    ADD CONSTRAINT user_qualification_targets_pkey PRIMARY KEY (user_qualification_targets_id);


--
-- Name: user_subdomain_preferences user_subdomain_preferences_pkey; Type: CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.user_subdomain_preferences
    ADD CONSTRAINT user_subdomain_preferences_pkey PRIMARY KEY (user_subdomain_preferences_id);


--
-- Name: users users_email_unique; Type: CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.users
    ADD CONSTRAINT users_email_unique UNIQUE (email);


--
-- Name: users users_pkey; Type: CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.users
    ADD CONSTRAINT users_pkey PRIMARY KEY (id);


--
-- Name: idx_backup_files_created_at; Type: INDEX; Schema: public; Owner: -
--

CREATE INDEX idx_backup_files_created_at ON public.backup_files USING btree (created_at);


--
-- Name: idx_backup_files_is_auto; Type: INDEX; Schema: public; Owner: -
--

CREATE INDEX idx_backup_files_is_auto ON public.backup_files USING btree (is_auto);


--
-- Name: idx_backup_files_is_success; Type: INDEX; Schema: public; Owner: -
--

CREATE INDEX idx_backup_files_is_success ON public.backup_files USING btree (is_success);


--
-- Name: idx_backup_settings_enabled; Type: INDEX; Schema: public; Owner: -
--

CREATE INDEX idx_backup_settings_enabled ON public.backup_settings USING btree (is_enabled);


--
-- Name: idx_sp_target; Type: INDEX; Schema: public; Owner: -
--

CREATE INDEX idx_sp_target ON public.study_plans USING btree (user_qualification_targets_id);


--
-- Name: idx_sp_target_active; Type: INDEX; Schema: public; Owner: -
--

CREATE INDEX idx_sp_target_active ON public.study_plans USING btree (user_qualification_targets_id, is_active);


--
-- Name: idx_spi_domain; Type: INDEX; Schema: public; Owner: -
--

CREATE INDEX idx_spi_domain ON public.study_plan_items USING btree (qualification_domains_id);


--
-- Name: idx_spi_subdomain; Type: INDEX; Schema: public; Owner: -
--

CREATE INDEX idx_spi_subdomain ON public.study_plan_items USING btree (qualification_subdomains_id);


--
-- Name: idx_spi_todo; Type: INDEX; Schema: public; Owner: -
--

CREATE INDEX idx_spi_todo ON public.study_plan_items USING btree (todo_id);


--
-- Name: idx_sr_spi; Type: INDEX; Schema: public; Owner: -
--

CREATE INDEX idx_sr_spi ON public.study_records USING btree (study_plan_items_id);


--
-- Name: idx_sr_todo; Type: INDEX; Schema: public; Owner: -
--

CREATE INDEX idx_sr_todo ON public.study_records USING btree (todo_id);


--
-- Name: idx_todo_date; Type: INDEX; Schema: public; Owner: -
--

CREATE INDEX idx_todo_date ON public.todo USING btree (date);


--
-- Name: idx_todo_plan; Type: INDEX; Schema: public; Owner: -
--

CREATE INDEX idx_todo_plan ON public.todo USING btree (study_plans_id);


--
-- Name: idx_udp_domain; Type: INDEX; Schema: public; Owner: -
--

CREATE INDEX idx_udp_domain ON public.user_domain_preferences USING btree (qualification_domains_id);


--
-- Name: idx_udp_target; Type: INDEX; Schema: public; Owner: -
--

CREATE INDEX idx_udp_target ON public.user_domain_preferences USING btree (user_qualification_targets_id);


--
-- Name: idx_unstd_day; Type: INDEX; Schema: public; Owner: -
--

CREATE INDEX idx_unstd_day ON public.user_no_study_days USING btree (no_study_day);


--
-- Name: idx_unstd_target; Type: INDEX; Schema: public; Owner: -
--

CREATE INDEX idx_unstd_target ON public.user_no_study_days USING btree (user_qualification_targets_id);


--
-- Name: idx_uqt_exam_date; Type: INDEX; Schema: public; Owner: -
--

CREATE INDEX idx_uqt_exam_date ON public.user_qualification_targets USING btree (exam_date);


--
-- Name: idx_uqt_user_qualification; Type: INDEX; Schema: public; Owner: -
--

CREATE INDEX idx_uqt_user_qualification ON public.user_qualification_targets USING btree (user_id, qualification_id);


--
-- Name: idx_usdp_subdomain; Type: INDEX; Schema: public; Owner: -
--

CREATE INDEX idx_usdp_subdomain ON public.user_subdomain_preferences USING btree (qualification_subdomains_id);


--
-- Name: idx_usdp_target; Type: INDEX; Schema: public; Owner: -
--

CREATE INDEX idx_usdp_target ON public.user_subdomain_preferences USING btree (user_qualification_targets_id);


--
-- Name: jobs_queue_index; Type: INDEX; Schema: public; Owner: -
--

CREATE INDEX jobs_queue_index ON public.jobs USING btree (queue);


--
-- Name: personal_access_tokens_expires_at_index; Type: INDEX; Schema: public; Owner: -
--

CREATE INDEX personal_access_tokens_expires_at_index ON public.personal_access_tokens USING btree (expires_at);


--
-- Name: personal_access_tokens_tokenable_type_tokenable_id_index; Type: INDEX; Schema: public; Owner: -
--

CREATE INDEX personal_access_tokens_tokenable_type_tokenable_id_index ON public.personal_access_tokens USING btree (tokenable_type, tokenable_id);


--
-- Name: qualification_domains_qualification_id_index; Type: INDEX; Schema: public; Owner: -
--

CREATE INDEX qualification_domains_qualification_id_index ON public.qualification_domains USING btree (qualification_id);


--
-- Name: qualification_subdomains_qualification_domains_id_index; Type: INDEX; Schema: public; Owner: -
--

CREATE INDEX qualification_subdomains_qualification_domains_id_index ON public.qualification_subdomains USING btree (qualification_domains_id);


--
-- Name: sessions_last_activity_index; Type: INDEX; Schema: public; Owner: -
--

CREATE INDEX sessions_last_activity_index ON public.sessions USING btree (last_activity);


--
-- Name: sessions_user_id_index; Type: INDEX; Schema: public; Owner: -
--

CREATE INDEX sessions_user_id_index ON public.sessions USING btree (user_id);


--
-- Name: study_plans fk_sp_uqt; Type: FK CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.study_plans
    ADD CONSTRAINT fk_sp_uqt FOREIGN KEY (user_qualification_targets_id) REFERENCES public.user_qualification_targets(user_qualification_targets_id) ON DELETE CASCADE;


--
-- Name: study_plan_items fk_spi_qd; Type: FK CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.study_plan_items
    ADD CONSTRAINT fk_spi_qd FOREIGN KEY (qualification_domains_id) REFERENCES public.qualification_domains(qualification_domains_id) ON DELETE RESTRICT;


--
-- Name: study_plan_items fk_spi_qsd; Type: FK CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.study_plan_items
    ADD CONSTRAINT fk_spi_qsd FOREIGN KEY (qualification_subdomains_id) REFERENCES public.qualification_subdomains(qualification_subdomains_id) ON DELETE RESTRICT;


--
-- Name: study_plan_items fk_sqi_todo; Type: FK CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.study_plan_items
    ADD CONSTRAINT fk_sqi_todo FOREIGN KEY (todo_id) REFERENCES public.todo(todo_id) ON DELETE CASCADE;


--
-- Name: study_records fk_sr_spi; Type: FK CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.study_records
    ADD CONSTRAINT fk_sr_spi FOREIGN KEY (study_plan_items_id) REFERENCES public.study_plan_items(study_plan_items_id) ON DELETE CASCADE;


--
-- Name: study_records fk_sr_todo; Type: FK CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.study_records
    ADD CONSTRAINT fk_sr_todo FOREIGN KEY (todo_id) REFERENCES public.todo(todo_id) ON DELETE CASCADE;


--
-- Name: todo fk_todo_sp; Type: FK CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.todo
    ADD CONSTRAINT fk_todo_sp FOREIGN KEY (study_plans_id) REFERENCES public.study_plans(study_plans_id) ON DELETE CASCADE;


--
-- Name: user_domain_preferences fk_udp_qd; Type: FK CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.user_domain_preferences
    ADD CONSTRAINT fk_udp_qd FOREIGN KEY (qualification_domains_id) REFERENCES public.qualification_domains(qualification_domains_id) ON DELETE RESTRICT;


--
-- Name: user_domain_preferences fk_udp_uqt; Type: FK CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.user_domain_preferences
    ADD CONSTRAINT fk_udp_uqt FOREIGN KEY (user_qualification_targets_id) REFERENCES public.user_qualification_targets(user_qualification_targets_id) ON DELETE CASCADE;


--
-- Name: user_no_study_days fk_unstd_uqt; Type: FK CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.user_no_study_days
    ADD CONSTRAINT fk_unstd_uqt FOREIGN KEY (user_qualification_targets_id) REFERENCES public.user_qualification_targets(user_qualification_targets_id) ON DELETE CASCADE;


--
-- Name: user_subdomain_preferences fk_usdp_qsd; Type: FK CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.user_subdomain_preferences
    ADD CONSTRAINT fk_usdp_qsd FOREIGN KEY (qualification_subdomains_id) REFERENCES public.qualification_subdomains(qualification_subdomains_id) ON DELETE RESTRICT;


--
-- Name: user_subdomain_preferences fk_usdp_uqt; Type: FK CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.user_subdomain_preferences
    ADD CONSTRAINT fk_usdp_uqt FOREIGN KEY (user_qualification_targets_id) REFERENCES public.user_qualification_targets(user_qualification_targets_id) ON DELETE CASCADE;


--
-- Name: line_accounts line_accounts_user_id_foreign; Type: FK CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.line_accounts
    ADD CONSTRAINT line_accounts_user_id_foreign FOREIGN KEY (user_id) REFERENCES public.users(id) ON DELETE CASCADE;


--
-- Name: qualification_domains qualification_domains_qualification_id_foreign; Type: FK CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.qualification_domains
    ADD CONSTRAINT qualification_domains_qualification_id_foreign FOREIGN KEY (qualification_id) REFERENCES public.qualification(qualification_id) ON DELETE RESTRICT;


--
-- Name: qualification_subdomains qualification_subdomains_qualification_domains_id_foreign; Type: FK CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.qualification_subdomains
    ADD CONSTRAINT qualification_subdomains_qualification_domains_id_foreign FOREIGN KEY (qualification_domains_id) REFERENCES public.qualification_domains(qualification_domains_id) ON DELETE CASCADE;


--
-- Name: user_qualification_targets user_qualification_targets_qualification_id_foreign; Type: FK CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.user_qualification_targets
    ADD CONSTRAINT user_qualification_targets_qualification_id_foreign FOREIGN KEY (qualification_id) REFERENCES public.qualification(qualification_id) ON DELETE RESTRICT;


--
-- Name: user_qualification_targets user_qualification_targets_user_id_foreign; Type: FK CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.user_qualification_targets
    ADD CONSTRAINT user_qualification_targets_user_id_foreign FOREIGN KEY (user_id) REFERENCES public.users(id) ON DELETE CASCADE;


--
-- PostgreSQL database dump complete
--

\unrestrict m4iWg6Lz94qm9ZvQ1ibPP7hDfjIFJ5P2aVKmSUxpAKJck9kx0RoVENsnKgDUgQh

