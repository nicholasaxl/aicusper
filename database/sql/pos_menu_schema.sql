-- POS menu tables, copied from pos_revamp_h2 (public schema), structure only.
-- Names and columns match the POS database so the real one can replace these later.
-- Foreign keys to tables that are not copied (merchant_users, admin_users, ...) are left out.

CREATE TABLE item_categories (
    id bigint NOT NULL,
    merchant_id bigint NOT NULL,
    name character varying(100),
    is_active boolean DEFAULT true NOT NULL,
    internal_name character varying,
    is_popular boolean DEFAULT false NOT NULL,
    is_deleted boolean DEFAULT false NOT NULL,
    created_at timestamp with time zone,
    created_by bigint,
    updated_at timestamp with time zone,
    updated_by bigint
);

CREATE SEQUENCE item_categories_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;

ALTER SEQUENCE item_categories_id_seq OWNED BY item_categories.id;

CREATE TABLE item_category_map (
    item_id bigint NOT NULL,
    category_id bigint NOT NULL
);

CREATE TABLE item_images (
    id bigint NOT NULL,
    item_id bigint NOT NULL,
    image_path character varying(1024) NOT NULL,
    is_main boolean,
    is_deleted boolean DEFAULT false
);

CREATE SEQUENCE item_images_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;

ALTER SEQUENCE item_images_id_seq OWNED BY item_images.id;

CREATE TABLE item_variant_outlet_data (
    id bigint NOT NULL,
    outlet_id bigint NOT NULL,
    item_variant_id bigint NOT NULL,
    item_no character varying(100),
    custom_id character varying(100),
    is_active boolean DEFAULT true NOT NULL
);

CREATE SEQUENCE item_outlet_data_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;

ALTER SEQUENCE item_outlet_data_id_seq OWNED BY item_variant_outlet_data.id;

CREATE TABLE item_outlet_map (
    item_id bigint NOT NULL,
    outlet_id bigint NOT NULL,
    is_active boolean DEFAULT true NOT NULL
);

CREATE TABLE item_variants (
    id bigint NOT NULL,
    item_id bigint NOT NULL,
    sku character varying(200),
    base_variant_unit_cost numeric,
    base_variant_unit_price numeric,
    ingredient_bom_id bigint,
    is_bundle boolean DEFAULT false NOT NULL,
    is_deleted boolean DEFAULT false NOT NULL,
    custom_id character varying(100),
    merchant_id bigint,
    price_can_be_custom boolean DEFAULT false NOT NULL,
    multiple_price boolean DEFAULT false NOT NULL,
    ingredient_id bigint,
    monitor_stock boolean
);

COMMENT ON COLUMN item_variants.monitor_stock IS 'if null, fallback to check parent';

CREATE SEQUENCE item_variants_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;

ALTER SEQUENCE item_variants_id_seq OWNED BY item_variants.id;

CREATE TABLE items (
    id bigint NOT NULL,
    merchant_id bigint NOT NULL,
    name character varying(100) NOT NULL,
    item_brand_id bigint,
    "desc" character varying(1000),
    created_by bigint NOT NULL,
    created_at timestamp with time zone NOT NULL,
    updated_by bigint,
    updated_at timestamp with time zone,
    deleted_by bigint,
    deleted_at timestamp with time zone,
    unit_id bigint,
    sellable boolean DEFAULT true,
    is_bundle boolean DEFAULT false NOT NULL,
    monitor_stock boolean,
    base_unit_cost numeric,
    base_unit_price numeric,
    is_active boolean DEFAULT true NOT NULL,
    custom_id character varying(100)
);

CREATE SEQUENCE items_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;

ALTER SEQUENCE items_id_seq OWNED BY items.id;

CREATE TABLE merchants (
    id bigint NOT NULL,
    name character varying(100) NOT NULL,
    created_by bigint NOT NULL,
    created_at timestamp with time zone NOT NULL,
    updated_by bigint,
    updated_at timestamp with time zone,
    deleted_by bigint,
    deleted_at timestamp with time zone,
    address character varying(1024),
    logo_path character varying(1024),
    phone_no character varying(100),
    email character varying(100),
    tier_id bigint,
    status_id character varying(10) DEFAULT 'inactive'::character varying NOT NULL,
    location_contact_id bigint,
    location_city_id bigint,
    business_type_id bigint,
    merchant_code character varying(100),
    custom_id character varying(100),
    base_currency bigint DEFAULT 1,
    trial_expired_at timestamp with time zone,
    to_non_trial_at timestamp with time zone,
    to_non_trial_by bigint,
    to_trial_at timestamp with time zone,
    to_trial_by bigint,
    postal_code character varying(10),
    ktp_image_url uuid,
    npwp_image_url uuid,
    merchant_photo_url uuid,
    is_suspected boolean DEFAULT false NOT NULL,
    merchant_type_id character varying(20) DEFAULT 'webmin'::character varying,
    update_by_merchant_user bigint,
    update_by_merchant_user_at timestamp with time zone,
    is_approved_terms_and_privacy boolean DEFAULT false NOT NULL
);

CREATE SEQUENCE merchants_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;

ALTER SEQUENCE merchants_id_seq OWNED BY merchants.id;

CREATE TABLE outlets (
    id bigint NOT NULL,
    merchant_id bigint NOT NULL,
    name character varying(100),
    is_open boolean NOT NULL,
    created_by bigint NOT NULL,
    created_at timestamp with time zone NOT NULL,
    updated_by bigint,
    updated_at timestamp with time zone,
    deleted_by bigint,
    deleted_at timestamp with time zone,
    address character varying(1024),
    phone_no character varying(100),
    email character varying(100),
    kode_pos character varying(10),
    outlet_type_id bigint,
    status_id character varying(10) DEFAULT 'inactive'::character varying NOT NULL,
    tier_id bigint,
    location_city_id bigint,
    location_contact_id bigint,
    logo_path character varying(1024),
    is_suspected boolean DEFAULT false NOT NULL,
    timezone character varying,
    is_sync_kitchen_display boolean DEFAULT false NOT NULL,
    kitchen_display_order_type_id smallint,
    is_enable_self_payment boolean DEFAULT false NOT NULL
);

COMMENT ON COLUMN outlets.timezone IS 'timezone info. ''Asia/Jakarta''. Not offset ''+07:00''';

CREATE SEQUENCE outlets_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;

ALTER SEQUENCE outlets_id_seq OWNED BY outlets.id;

ALTER TABLE ONLY item_categories ALTER COLUMN id SET DEFAULT nextval('item_categories_id_seq'::regclass);

ALTER TABLE ONLY item_images ALTER COLUMN id SET DEFAULT nextval('item_images_id_seq'::regclass);

ALTER TABLE ONLY item_variant_outlet_data ALTER COLUMN id SET DEFAULT nextval('item_outlet_data_id_seq'::regclass);

ALTER TABLE ONLY item_variants ALTER COLUMN id SET DEFAULT nextval('item_variants_id_seq'::regclass);

ALTER TABLE ONLY items ALTER COLUMN id SET DEFAULT nextval('items_id_seq'::regclass);

ALTER TABLE ONLY merchants ALTER COLUMN id SET DEFAULT nextval('merchants_id_seq'::regclass);

ALTER TABLE ONLY outlets ALTER COLUMN id SET DEFAULT nextval('outlets_id_seq'::regclass);

ALTER TABLE ONLY item_categories
    ADD CONSTRAINT item_categories_internal_name_unique UNIQUE (merchant_id, internal_name);

ALTER TABLE ONLY item_categories
    ADD CONSTRAINT item_categories_pkey PRIMARY KEY (id);

ALTER TABLE ONLY item_category_map
    ADD CONSTRAINT item_category_map_pkey PRIMARY KEY (item_id, category_id);

ALTER TABLE ONLY item_images
    ADD CONSTRAINT item_images_pkey PRIMARY KEY (id);

ALTER TABLE ONLY item_variant_outlet_data
    ADD CONSTRAINT item_outlet_data_pkey PRIMARY KEY (id);

ALTER TABLE ONLY item_outlet_map
    ADD CONSTRAINT item_outlet_map_pkey PRIMARY KEY (item_id, outlet_id);

ALTER TABLE ONLY item_variant_outlet_data
    ADD CONSTRAINT item_variant_outlet_data_unique UNIQUE (outlet_id, item_variant_id);

ALTER TABLE ONLY item_variants
    ADD CONSTRAINT item_variants_pkey PRIMARY KEY (id);

ALTER TABLE ONLY item_variants
    ADD CONSTRAINT "item_variants_unique_customId" UNIQUE (merchant_id, custom_id);

ALTER TABLE ONLY items
    ADD CONSTRAINT items_custom_id_unique_per_merchant UNIQUE (merchant_id, custom_id);

ALTER TABLE ONLY items
    ADD CONSTRAINT items_pkey PRIMARY KEY (id);

ALTER TABLE ONLY items
    ADD CONSTRAINT items_unique_id_mer UNIQUE (id, merchant_id);

ALTER TABLE ONLY merchants
    ADD CONSTRAINT merchants_pkey PRIMARY KEY (id);

ALTER TABLE ONLY outlets
    ADD CONSTRAINT outlets_pkey PRIMARY KEY (id);

CREATE INDEX item_variants_ingredient_id_idx ON item_variants USING btree (ingredient_id);

CREATE INDEX item_variants_item_id_idx ON item_variants USING btree (item_id);

CREATE INDEX outlets_merchant_id_idx ON outlets USING btree (merchant_id);

ALTER TABLE ONLY item_categories
    ADD CONSTRAINT item_categories_merchant_fk FOREIGN KEY (merchant_id) REFERENCES merchants(id);

ALTER TABLE ONLY item_category_map
    ADD CONSTRAINT item_category_map_category_id_fkey FOREIGN KEY (category_id) REFERENCES item_categories(id);

ALTER TABLE ONLY item_category_map
    ADD CONSTRAINT item_category_map_item_id_fkey FOREIGN KEY (item_id) REFERENCES items(id);

ALTER TABLE ONLY item_images
    ADD CONSTRAINT item_images_item_id_fkey FOREIGN KEY (item_id) REFERENCES items(id);

ALTER TABLE ONLY item_variant_outlet_data
    ADD CONSTRAINT item_outlet_data_item_variant_id_fkey FOREIGN KEY (item_variant_id) REFERENCES item_variants(id);

ALTER TABLE ONLY item_variant_outlet_data
    ADD CONSTRAINT item_outlet_data_outlet_id_fkey FOREIGN KEY (outlet_id) REFERENCES outlets(id);

ALTER TABLE ONLY item_outlet_map
    ADD CONSTRAINT item_outlet_map_item_id_fkey FOREIGN KEY (item_id) REFERENCES items(id) ON UPDATE CASCADE ON DELETE CASCADE;

ALTER TABLE ONLY item_outlet_map
    ADD CONSTRAINT item_outlet_map_outlet_id_fkey FOREIGN KEY (outlet_id) REFERENCES outlets(id) ON UPDATE CASCADE ON DELETE CASCADE;

ALTER TABLE ONLY item_variants
    ADD CONSTRAINT item_variants_item_id_fkey FOREIGN KEY (item_id) REFERENCES items(id);

ALTER TABLE ONLY item_variants
    ADD CONSTRAINT item_variants_items_idm_fk FOREIGN KEY (item_id, merchant_id) REFERENCES items(id, merchant_id) ON UPDATE CASCADE ON DELETE CASCADE;

ALTER TABLE ONLY item_variants
    ADD CONSTRAINT item_variants_merchants_fk FOREIGN KEY (merchant_id) REFERENCES merchants(id) ON UPDATE RESTRICT ON DELETE RESTRICT;

ALTER TABLE ONLY items
    ADD CONSTRAINT items_merchant_id_fkey FOREIGN KEY (merchant_id) REFERENCES merchants(id);

ALTER TABLE ONLY outlets
    ADD CONSTRAINT outlets_merchants_fk FOREIGN KEY (merchant_id) REFERENCES merchants(id);
