PGDMP     5    ,                z            app    13.7    14.3 A               0    0    ENCODING    ENCODING        SET client_encoding = 'UTF8';
                      false                       0    0 
   STDSTRINGS 
   STDSTRINGS     (   SET standard_conforming_strings = 'on';
                      false                       0    0 
   SEARCHPATH 
   SEARCHPATH     8   SELECT pg_catalog.set_config('search_path', '', false);
                      false                       1262    16384    app    DATABASE     W   CREATE DATABASE app WITH TEMPLATE = template0 ENCODING = 'UTF8' LOCALE = 'en_US.utf8';
    DROP DATABASE app;
                symfony    false            �            1255    16497    notify_messenger_messages()    FUNCTION     �   CREATE FUNCTION public.notify_messenger_messages() RETURNS trigger
    LANGUAGE plpgsql
    AS $$
    BEGIN
        PERFORM pg_notify('messenger_messages', NEW.queue_name::text);
        RETURN NEW;
    END;
$$;
 2   DROP FUNCTION public.notify_messenger_messages();
       public          symfony    false            �            1259    16438    cart    TABLE     6   CREATE TABLE public.cart (
    id integer NOT NULL
);
    DROP TABLE public.cart;
       public         heap    symfony    false            �            1259    16432    cart_id_seq    SEQUENCE     t   CREATE SEQUENCE public.cart_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;
 "   DROP SEQUENCE public.cart_id_seq;
       public          symfony    false            �            1259    16443 	   cart_item    TABLE     �   CREATE TABLE public.cart_item (
    id integer NOT NULL,
    product_id integer NOT NULL,
    cart_id integer NOT NULL,
    quantity integer NOT NULL
);
    DROP TABLE public.cart_item;
       public         heap    symfony    false            �            1259    16434    cart_item_id_seq    SEQUENCE     y   CREATE SEQUENCE public.cart_item_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;
 '   DROP SEQUENCE public.cart_item_id_seq;
       public          symfony    false            �            1259    16415    customer    TABLE     p  CREATE TABLE public.customer (
    id integer NOT NULL,
    street_id integer NOT NULL,
    last_name character varying(255) NOT NULL,
    first_name character varying(255) NOT NULL,
    middle_name character varying(255) NOT NULL,
    document_filename character varying(255) DEFAULT NULL::character varying,
    apartment character varying(255),
    building_number character varying(255) NOT NULL,
    info character varying(255) DEFAULT NULL::character varying,
    status character varying(255) NOT NULL,
    created_at timestamp(0) without time zone NOT NULL,
    updated_at timestamp(0) without time zone NOT NULL
);
    DROP TABLE public.customer;
       public         heap    symfony    false            �            1259    16413    customer_id_seq    SEQUENCE     x   CREATE SEQUENCE public.customer_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;
 &   DROP SEQUENCE public.customer_id_seq;
       public          symfony    false            �            1259    16513    doctrine_migration_versions    TABLE     �   CREATE TABLE public.doctrine_migration_versions (
    version character varying(191) NOT NULL,
    executed_at timestamp(0) without time zone DEFAULT NULL::timestamp without time zone,
    execution_time integer
);
 /   DROP TABLE public.doctrine_migration_versions;
       public         heap    symfony    false            �            1259    16484    messenger_messages    TABLE     s  CREATE TABLE public.messenger_messages (
    id bigint NOT NULL,
    body text NOT NULL,
    headers text NOT NULL,
    queue_name character varying(190) NOT NULL,
    created_at timestamp(0) without time zone NOT NULL,
    available_at timestamp(0) without time zone NOT NULL,
    delivered_at timestamp(0) without time zone DEFAULT NULL::timestamp without time zone
);
 &   DROP TABLE public.messenger_messages;
       public         heap    symfony    false            �            1259    16482    messenger_messages_id_seq    SEQUENCE     �   CREATE SEQUENCE public.messenger_messages_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;
 0   DROP SEQUENCE public.messenger_messages_id_seq;
       public          symfony    false    213                       0    0    messenger_messages_id_seq    SEQUENCE OWNED BY     W   ALTER SEQUENCE public.messenger_messages_id_seq OWNED BY public.messenger_messages.id;
          public          symfony    false    212            �            1259    16450    order    TABLE     �  CREATE TABLE public."order" (
    id integer NOT NULL,
    customer_id integer NOT NULL,
    cart_id integer,
    status character varying(255) NOT NULL,
    spreadsheet_filename character varying(255) DEFAULT NULL::character varying,
    total double precision NOT NULL,
    info character varying(255) DEFAULT NULL::character varying,
    created_at timestamp(0) without time zone NOT NULL,
    updated_at timestamp(0) without time zone NOT NULL,
    version integer DEFAULT 1 NOT NULL
);
    DROP TABLE public."order";
       public         heap    symfony    false            �            1259    16436    order_id_seq    SEQUENCE     u   CREATE SEQUENCE public.order_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;
 #   DROP SEQUENCE public.order_id_seq;
       public          symfony    false            �            1259    16403    product    TABLE     �  CREATE TABLE public.product (
    id integer NOT NULL,
    name character varying(255) NOT NULL,
    quantity_in_stock integer NOT NULL,
    status character varying(255) NOT NULL,
    description character varying(255) DEFAULT NULL::character varying,
    price double precision NOT NULL,
    image_filename character varying(255) DEFAULT NULL::character varying,
    created_at timestamp(0) without time zone NOT NULL,
    updated_at timestamp(0) without time zone NOT NULL
);
    DROP TABLE public.product;
       public         heap    symfony    false            �            1259    16401    product_id_seq    SEQUENCE     w   CREATE SEQUENCE public.product_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;
 %   DROP SEQUENCE public.product_id_seq;
       public          symfony    false            �            1259    16393    street    TABLE     b   CREATE TABLE public.street (
    id integer NOT NULL,
    name character varying(255) NOT NULL
);
    DROP TABLE public.street;
       public         heap    symfony    false            �            1259    16391    street_id_seq    SEQUENCE     v   CREATE SEQUENCE public.street_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;
 $   DROP SEQUENCE public.street_id_seq;
       public          symfony    false            �            1259    16502    user    TABLE     �   CREATE TABLE public."user" (
    id integer NOT NULL,
    username character varying(180) NOT NULL,
    roles json NOT NULL,
    password character varying(255) NOT NULL
);
    DROP TABLE public."user";
       public         heap    symfony    false            �            1259    16500    user_id_seq    SEQUENCE     t   CREATE SEQUENCE public.user_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;
 "   DROP SEQUENCE public.user_id_seq;
       public          symfony    false            _           2604    16487    messenger_messages id    DEFAULT     ~   ALTER TABLE ONLY public.messenger_messages ALTER COLUMN id SET DEFAULT nextval('public.messenger_messages_id_seq'::regclass);
 D   ALTER TABLE public.messenger_messages ALTER COLUMN id DROP DEFAULT;
       public          symfony    false    213    212    213                      0    16438    cart 
   TABLE DATA           "   COPY public.cart (id) FROM stdin;
    public          symfony    false    209   �L                 0    16443 	   cart_item 
   TABLE DATA           F   COPY public.cart_item (id, product_id, cart_id, quantity) FROM stdin;
    public          symfony    false    210   �M       
          0    16415    customer 
   TABLE DATA           �   COPY public.customer (id, street_id, last_name, first_name, middle_name, document_filename, apartment, building_number, info, status, created_at, updated_at) FROM stdin;
    public          symfony    false    205   jQ                 0    16513    doctrine_migration_versions 
   TABLE DATA           [   COPY public.doctrine_migration_versions (version, executed_at, execution_time) FROM stdin;
    public          symfony    false    216   �p                 0    16484    messenger_messages 
   TABLE DATA           s   COPY public.messenger_messages (id, body, headers, queue_name, created_at, available_at, delivered_at) FROM stdin;
    public          symfony    false    213   q                 0    16450    order 
   TABLE DATA           �   COPY public."order" (id, customer_id, cart_id, status, spreadsheet_filename, total, info, created_at, updated_at, version) FROM stdin;
    public          symfony    false    211   q                 0    16403    product 
   TABLE DATA           �   COPY public.product (id, name, quantity_in_stock, status, description, price, image_filename, created_at, updated_at) FROM stdin;
    public          symfony    false    203   �z                 0    16393    street 
   TABLE DATA           *   COPY public.street (id, name) FROM stdin;
    public          symfony    false    201   �                 0    16502    user 
   TABLE DATA           ?   COPY public."user" (id, username, roles, password) FROM stdin;
    public          symfony    false    215   4�                  0    0    cart_id_seq    SEQUENCE SET     ;   SELECT pg_catalog.setval('public.cart_id_seq', 146, true);
          public          symfony    false    206                       0    0    cart_item_id_seq    SEQUENCE SET     @   SELECT pg_catalog.setval('public.cart_item_id_seq', 229, true);
          public          symfony    false    207                       0    0    customer_id_seq    SEQUENCE SET     ?   SELECT pg_catalog.setval('public.customer_id_seq', 302, true);
          public          symfony    false    204                        0    0    messenger_messages_id_seq    SEQUENCE SET     H   SELECT pg_catalog.setval('public.messenger_messages_id_seq', 1, false);
          public          symfony    false    212            !           0    0    order_id_seq    SEQUENCE SET     <   SELECT pg_catalog.setval('public.order_id_seq', 146, true);
          public          symfony    false    208            "           0    0    product_id_seq    SEQUENCE SET     >   SELECT pg_catalog.setval('public.product_id_seq', 409, true);
          public          symfony    false    202            #           0    0    street_id_seq    SEQUENCE SET     >   SELECT pg_catalog.setval('public.street_id_seq', 7696, true);
          public          symfony    false    200            $           0    0    user_id_seq    SEQUENCE SET     9   SELECT pg_catalog.setval('public.user_id_seq', 1, true);
          public          symfony    false    214            l           2606    16447    cart_item cart_item_pkey 
   CONSTRAINT     V   ALTER TABLE ONLY public.cart_item
    ADD CONSTRAINT cart_item_pkey PRIMARY KEY (id);
 B   ALTER TABLE ONLY public.cart_item DROP CONSTRAINT cart_item_pkey;
       public            symfony    false    210            j           2606    16442    cart cart_pkey 
   CONSTRAINT     L   ALTER TABLE ONLY public.cart
    ADD CONSTRAINT cart_pkey PRIMARY KEY (id);
 8   ALTER TABLE ONLY public.cart DROP CONSTRAINT cart_pkey;
       public            symfony    false    209            g           2606    16425    customer customer_pkey 
   CONSTRAINT     T   ALTER TABLE ONLY public.customer
    ADD CONSTRAINT customer_pkey PRIMARY KEY (id);
 @   ALTER TABLE ONLY public.customer DROP CONSTRAINT customer_pkey;
       public            symfony    false    205            |           2606    16518 <   doctrine_migration_versions doctrine_migration_versions_pkey 
   CONSTRAINT        ALTER TABLE ONLY public.doctrine_migration_versions
    ADD CONSTRAINT doctrine_migration_versions_pkey PRIMARY KEY (version);
 f   ALTER TABLE ONLY public.doctrine_migration_versions DROP CONSTRAINT doctrine_migration_versions_pkey;
       public            symfony    false    216            w           2606    16493 *   messenger_messages messenger_messages_pkey 
   CONSTRAINT     h   ALTER TABLE ONLY public.messenger_messages
    ADD CONSTRAINT messenger_messages_pkey PRIMARY KEY (id);
 T   ALTER TABLE ONLY public.messenger_messages DROP CONSTRAINT messenger_messages_pkey;
       public            symfony    false    213            r           2606    16459    order order_pkey 
   CONSTRAINT     P   ALTER TABLE ONLY public."order"
    ADD CONSTRAINT order_pkey PRIMARY KEY (id);
 <   ALTER TABLE ONLY public."order" DROP CONSTRAINT order_pkey;
       public            symfony    false    211            e           2606    16412    product product_pkey 
   CONSTRAINT     R   ALTER TABLE ONLY public.product
    ADD CONSTRAINT product_pkey PRIMARY KEY (id);
 >   ALTER TABLE ONLY public.product DROP CONSTRAINT product_pkey;
       public            symfony    false    203            c           2606    16397    street street_pkey 
   CONSTRAINT     P   ALTER TABLE ONLY public.street
    ADD CONSTRAINT street_pkey PRIMARY KEY (id);
 <   ALTER TABLE ONLY public.street DROP CONSTRAINT street_pkey;
       public            symfony    false    201            z           2606    16509    user user_pkey 
   CONSTRAINT     N   ALTER TABLE ONLY public."user"
    ADD CONSTRAINT user_pkey PRIMARY KEY (id);
 :   ALTER TABLE ONLY public."user" DROP CONSTRAINT user_pkey;
       public            symfony    false    215            s           1259    16496    idx_75ea56e016ba31db    INDEX     [   CREATE INDEX idx_75ea56e016ba31db ON public.messenger_messages USING btree (delivered_at);
 (   DROP INDEX public.idx_75ea56e016ba31db;
       public            symfony    false    213            t           1259    16495    idx_75ea56e0e3bd61ce    INDEX     [   CREATE INDEX idx_75ea56e0e3bd61ce ON public.messenger_messages USING btree (available_at);
 (   DROP INDEX public.idx_75ea56e0e3bd61ce;
       public            symfony    false    213            u           1259    16494    idx_75ea56e0fb7336f0    INDEX     Y   CREATE INDEX idx_75ea56e0fb7336f0 ON public.messenger_messages USING btree (queue_name);
 (   DROP INDEX public.idx_75ea56e0fb7336f0;
       public            symfony    false    213            h           1259    16426    idx_81398e0987cf8eb    INDEX     M   CREATE INDEX idx_81398e0987cf8eb ON public.customer USING btree (street_id);
 '   DROP INDEX public.idx_81398e0987cf8eb;
       public            symfony    false    205            m           1259    16449    idx_f0fe25271ad5cdbf    INDEX     M   CREATE INDEX idx_f0fe25271ad5cdbf ON public.cart_item USING btree (cart_id);
 (   DROP INDEX public.idx_f0fe25271ad5cdbf;
       public            symfony    false    210            n           1259    16448    idx_f0fe25274584665a    INDEX     P   CREATE INDEX idx_f0fe25274584665a ON public.cart_item USING btree (product_id);
 (   DROP INDEX public.idx_f0fe25274584665a;
       public            symfony    false    210            o           1259    16461    idx_f52993981ad5cdbf    INDEX     K   CREATE INDEX idx_f52993981ad5cdbf ON public."order" USING btree (cart_id);
 (   DROP INDEX public.idx_f52993981ad5cdbf;
       public            symfony    false    211            p           1259    16460    idx_f52993989395c3f3    INDEX     O   CREATE INDEX idx_f52993989395c3f3 ON public."order" USING btree (customer_id);
 (   DROP INDEX public.idx_f52993989395c3f3;
       public            symfony    false    211            x           1259    16510    uniq_8d93d649f85e0677    INDEX     S   CREATE UNIQUE INDEX uniq_8d93d649f85e0677 ON public."user" USING btree (username);
 )   DROP INDEX public.uniq_8d93d649f85e0677;
       public            symfony    false    215            �           2620    16499 !   messenger_messages notify_trigger    TRIGGER     �   CREATE TRIGGER notify_trigger AFTER INSERT OR UPDATE ON public.messenger_messages FOR EACH ROW EXECUTE FUNCTION public.notify_messenger_messages();
 :   DROP TRIGGER notify_trigger ON public.messenger_messages;
       public          symfony    false    217    213            }           2606    16427    customer fk_81398e0987cf8eb    FK CONSTRAINT     }   ALTER TABLE ONLY public.customer
    ADD CONSTRAINT fk_81398e0987cf8eb FOREIGN KEY (street_id) REFERENCES public.street(id);
 E   ALTER TABLE ONLY public.customer DROP CONSTRAINT fk_81398e0987cf8eb;
       public          symfony    false    201    2915    205                       2606    16467    cart_item fk_f0fe25271ad5cdbf    FK CONSTRAINT     {   ALTER TABLE ONLY public.cart_item
    ADD CONSTRAINT fk_f0fe25271ad5cdbf FOREIGN KEY (cart_id) REFERENCES public.cart(id);
 G   ALTER TABLE ONLY public.cart_item DROP CONSTRAINT fk_f0fe25271ad5cdbf;
       public          symfony    false    2922    210    209            ~           2606    16462    cart_item fk_f0fe25274584665a    FK CONSTRAINT     �   ALTER TABLE ONLY public.cart_item
    ADD CONSTRAINT fk_f0fe25274584665a FOREIGN KEY (product_id) REFERENCES public.product(id);
 G   ALTER TABLE ONLY public.cart_item DROP CONSTRAINT fk_f0fe25274584665a;
       public          symfony    false    203    210    2917            �           2606    16477    order fk_f52993981ad5cdbf    FK CONSTRAINT     y   ALTER TABLE ONLY public."order"
    ADD CONSTRAINT fk_f52993981ad5cdbf FOREIGN KEY (cart_id) REFERENCES public.cart(id);
 E   ALTER TABLE ONLY public."order" DROP CONSTRAINT fk_f52993981ad5cdbf;
       public          symfony    false    209    211    2922            �           2606    16472    order fk_f52993989395c3f3    FK CONSTRAINT     �   ALTER TABLE ONLY public."order"
    ADD CONSTRAINT fk_f52993989395c3f3 FOREIGN KEY (customer_id) REFERENCES public.customer(id);
 E   ALTER TABLE ONLY public."order" DROP CONSTRAINT fk_f52993989395c3f3;
       public          symfony    false    205    211    2919               �   x���m�0���H��zI�u䜇�n,��pi��@B�
tQ�AK)2�"�(P�_/��PK=Np�I�r7��&�����6w��}tТ�.�оN�C/��`�$S�a.����c��l���l����O����.�y���o�׆�����'�7�G��q����s�`>����p�#�3���sg%�I�.ݥ�t��l'�>gw��eA�P6�eE�Qv��~����4�?��vD         �  x�EVIv�0[Ǉ�Ow���Q	��uQ+L�����?������x���r|��|���q\���x����`��ig0\P���r��oPp�m�=mO}d�>��~�lЁ����ͳ���y��>�ܟ����=�v��?{��!�h^nc��Y�g6/�&�@�5�2)p��*�Z�ZD�0l��{��(�ųg�Å�!;o^i�jl--�wDqG�R�Iq��m��beDH"��6��$J����L�*o0*��J5�#�@��_~~�2���M*�g5���q�m��L%�+ �3%����RpJ+��ւ�.q�JN�$u��F���,�v��fUb�NT\{���T|8%8�sԛ�A�W��	��W���l �i4/`r�W��N>uV�7/�zi\.��`��ڞ����1��UW
`U��V�j	@SSq8��Y��+����J�2o6�O!#Q�5�P�Qcc�SSsC/aBA��B�s�!څ�$�
��}�_�1Ƨu�.ǕևP��wڝA6.��a�a��U_f[�[�{�zs�)� ���6�C�u���Xv�߆Ұ���!íj���S����¸���$�1E��>R���٦D�e�E�9ūv��������g��PI#[�ԫ��\�R�9$))��6es��tAW��Љ�f��W�YJ'G�X	���&NY�dH~)A�qT�:}p�n�٠K{N7ÔC�<�%*�f�2\��d��������'_P��.ԭ�!���'y�^�@b
)~`Q��k�K�!|�s�C�oμ�+~>J�0�9��5�~�C����s5OX9��D5�o��ߩn��f�u3	�m7]��޴v	G2���,X=MV?#�j��72%oY� ���*�b��&���|�bT�]/�WBu[x��Q��ϕ�\S@�K��dV���DH�U����Z�~A�      
      x��]��q�=z
�48������O��@��2  � �q�jI]�P�h��h�Wˆ-y��#.��W8�R_u��N�ٝZ Fȥ�]�]]�U�Wն�M����w�����p}P�?_��W�s����������?���Ƙ���x�7�~�Ư���_���~��������/���*li����g��0�M޴���7�"�M,ŭ�������L�<����ǛO����'���ǛCIleg$Il�����_�,��])�$�w@[qH�G9�'���{,'�8�|�;�\a�$GU������?��_]Y��e�@�C����'q���}l����C:�����K������j|*Ϫ�~�mlc{M�a���[YA�-l0�v�:�IT:����qEJ��8�j%;�G�}��4`�.�)\c��ŧ�>��E�u����|F�{����M��B|A���D��ʢ%,�:�����Ṭ��~Jr�ѲX�U<��7r�뉷t�y7�.m���ȟG_�O���-(֏��O�r48��M(L�$�Q.���%e'9ΰ0.J��2k�ݤ�C��<����M*�4dwQ�l�h�oX���,�M>��F��-\�����U�m<�c>��h)>e�[��?������;�&ːͲ6r�/h�W�X-C
eȞ9�Dd�� H�v�u��-GJ������u1ޛʩ.*�ڴ*�����Go�9��r�N%�C�
W뵡�o������Fэ�+� z������,��r��<j�2��-Bm�ڼ�Ao��5����M���q:��a�F6���{�.�c��K�MR}�ۘ���Nɬ+�Z��R��Dj�b�'Z�9��|�x��f�-�S�N�ZҌ6�e_�ͻEʸ��~����46{S��-Z�k�oM�y�~I)���B�WEZ	��Żs	�%�\�$��i��a�c�@6!xI�j�d-]ܘ	�F!��D\Y닪��U�I�e��U���s4>���=,|��ҷ-�!бx�N�ݽ$���Lf$3B:Yrc�:%�Y���������N�h~
E�C���CZ�&�)nEP�3Z�j@#�M�w�<��k�5����r!��"�$����"^2$1 ?z�6h��mQ�GZ���E��ŏ�;�R�m�lZV��b|Ih��`)�3� ;)��t8��Y���N��9V��6�G��[�s�ze)*r��֜��h��t;��O.� uN�f�;����.|]���d�����^:�]8�V�:��[A���5���о�W~�>_�]V��)��DY�Ų i6&�r3��b}/F!��!\B��_�x���ظ1Y��^�O�IވY�j誺��(&�ʪ�q�.eV��'���K)�CA�+%�Y)�&M���t	賏&.$%oF� )$�A����jb��n���174�#`K��H����6�,�'��s=���|�M>������B%(�eY��߽]�p��U]
5�=ρ(�@����w ��iV$ Y�:��9[�K���	#�VB����f��;9���.�`]]�)z �Oْd�7;~��"��-� ��yы�Ӻ�B6g�+���o�Etq�20~��Δ
��yԀ���b!V}�5-%�bV1���~�.�"0���YF2���!;��>�{��N�N�%�d��g����F����4�9�z���{��G�ܮƥ�ǰ�$��%�����gc8�0&{ak������dht���Ҫ�5IO��O�ƌ��$��%<^kj#(�P�K��^�y��d a���W��7�X���C'9�x�N����՜Dg��,�}d�s3�~+n�;R!�H*��>u@��b_b�|0*�}�6>o	���x�4(�X�%�.�/���tZ���B��Y_g�>��������T]���ϥ"�Oo�b����O�ٹ��'�u��mҚǜ����q�
�b���W]_ô�:D�����%Z����U(��v����M6Y����x���=�9l>�$j�|*���j�=��a��	���%y���5N 5!�g�8�x�6�� �Um��\%�:��l.�����G��b9�f�>Q�C���8�26]��QY�G��P�!� ţX7�ǲ#S@ �V�N-�"X�c�E�*����"��$'j�R$=iG| �f���2��x[��c�=�V�%dh��[r9�Q�8t蹊��K����)t�5Lbt\��\>�V�.b�jL)��"��g����+�g(����U�EB�NǘÛ视6z��x�/u{��V�����*��Ҳ��w�R�]j�([~�f�' M�Ysy�@G"RL�a~��.PPZ��ae�+J�/OS���4$y�F�<�:��=��B���1�q��0�$K��b��y͎񳵴�R�p�yMn�v,�d�h���x�J�����Sd#֩��D�,�e��|S͇ħ��w�]��i��7��:��*aǴ�߳���A��l�/�2t��kR��g�$C�K�ա��:�@�_�>�+� �*5�V�cq�Ȣy#�/���t�bM�C����*Y��|�0d���	��ťp
�|���9�F[
��$�#Z9�i�V�<�xFAØx1adl��x�Z��eo�ơD�:(l��Oag�Y=�k���(j7MCB����%��92L+6��	���F�tAlP���J��Y�Sݣ�����P�}���K٩d6�����2�eԓ�Z��h!���҆��>%V�Ze³�)~���/��{�d�h�Qh��u�`�
~�l�Y�C��6{Q�3�I9��R�:��?`��>���ň���9��R�*�!�U��erA�l�����j���(��HOs�%_8�Ǵ9�@�]���2Fn��������q ����,X���&H�0�-xB�IL^�C�L�.N#d�H׈�(�w��D��Ya S���djL���w�o��1cσ���������FWB�ܻ5�MӫLb\Qtr%�`�N����k �)-�h"b�t�cfbC��$��o�Q|	zyw��?����Ig��M�IiWtp�����c ��Kr�Pq�_���wfj�N�|�J
�k[F��bŊ�ځ@�~���X��gC�Z�+�d+�@�sđb)b�ZC�mE�>U�G�� q.b&x��0�vbЈ4��)ɠVUE��������_��vMM�XX��-���iҖ�ononu�e�V��k4�:�����r_FS�N���c�Y�n����Y,�O�x�7-���A(8�mɮZ�`��\~a������ �}�I�#����4S���j�4���v�姑q��3T?��M![$�\���
��e'�k�؊i(r��,��p� �9K��;�ڪ�O#��H[e��j�!�L��MV��#�LN�񤻰-���K�?|�Ϊ��u���K�H�5�)P���޷l`Ȟ��T���hZ�|�/*��Kty0C��G�hǖ��-R���[.�E^�z,��Y�T����,�����L� dp��'�y/�7����xG��s��Xe�o,�A���;NL�I�C��\�M�����<���<�B8�i�L	�䠼E�M/چ�2�"n��dB��I;�MM�F�M�%:�@TeI���ե���R�=�fURڠ;ȱӡ�>�}`��L����:ǌE�7L��d$���e� Ȫ�2]A�
[ˏ�D�l��1����2�Q:�����Lڐ�� f��v�i�V�R��q3b��Qϡ�J���L��>��vZ�R�{������3:@8���T�����!�(y��i\R-7���*u�9)0�S*&]�A�P�dgh3��r���ჹ`FIl"�k);bHW�{i�{��:��"�­ RL�XrZ���4������>dt,7�<29�5:��dk^٭�R�d��b���L��4�k:�L�t�&G��1fS��o� p��n�Iȗ�/�����0|*�@�G^�!��YSB�z�^U��T�.���#;Jo��G�_�:ƻ;h��^ qu_�fI������X�a�r3�w��}s�C�
Wz����-����G�8W���q�����I��y����ui����` )Z�"�a�~�%Sk� 9  d'�t;�`ܶ��[L��c�װ nxң�/Fc�D�K�@����(t���A�3���O+���*>o�4�)�QΟu�q��h�� rz�)+CmC&_��AY(�b�f���v�!fd�4��V!�K���y��U�$A�7����댅O2wo2yf�4H���:!�I�w�B����Ufv��)(�U�MA-ڴF�n���bS�*��55!4��?���θm��I ��T�w��6�~�O^�1}�2��5x�B�n�4��`$�,��U��: ��{�8�r9�`F��&&}���5U�V>���/-/Y��p̒<
~���3�|��yk��u�s\��k�p[Nh�<�qһ����ފd��1י���4|U??�l��1���ʪ�&��l�U�M�$�=x�H��|d�|�`�rt����iǠkǛf+<�*ߦ4�h��L��+�R4�Ե��$7���\��_��/������(�<{.�(�Δ�t�����z|��g��S�3B�ӕ���oT����DI8d��79_J~3�T� zz�������{1����ւ�(�������W��iL"3���k5.0��Χ,��ȱ�S,<����s~y9=6��^������'�ch��N����?�8<b�����E�++/�=�o������ ��C��؝��jAW�Zjׇn���<�4��/�~d���~�	Dк�dLř����C�a.��6�t��(�?�9�|��9����DG����dHE=���ڕ^���]/pP���$j��$ʊ3�Ia���y�J�L	Z�J��{Pէ�sz�f�:�V�:�4�[b#����YU'hd�A��ޢ��z�z�O8��Y0���6 ���`RxP�u��5�S����5;���;�zǇ�A�ο�az�>���D��r`�V��.�|���|<�|�g���ݨM�Dr�*�D�xTe�h���xg�=Ye��T�k2q�y�$�ֲ��ȁp�s��6�/bo�'MD�:=Aj��k�!�ZJ�(J3���U{�j]�$���nH����ȼ_cVv�b�j�����\z�!>�"ŰdB������Z�e��I�b�,G�-�($?]�A?b�dV����p�> t�/�P���9�"�Bj�AѮU��a?�ڳ �|�(I$S��T�ʖ
����z�\;���v
ȥ���^$�/G��TQ�:=�5F	'�8�δ�[䑬2�$��srN(�S�P
e�F��.��[}�2�l>���D�Z�ޢ̛����x&�k��ő��ކ�3��Ane�# �S4���) U� n������9�P�������"晴�q�u��}xˇ>s����sw����'��N���Ǡlu��|K��t�]F�zBJ��VL{� º�	�?#%�s���9����_Ǉ�p�\�d
1����ls����y��L+P�l>n����v_E��שc%�\k(�n��s�5�D��m됤{[��#�����P�bh&����$W1�'� \���ΖTp���M�2Z�%ХQ*���9|���Z�^jF����#�������>V1���pV��0�<'q;���| ;��A�˳�֠��ȳqY'�.�W<o���)y}<A���08=ޟW�P`B��@��*%���W�)w���;�S��~�H���C��?s1p$WS<��)"l�oм����@�z��f����F�(#m�q�W`�4��>r�x>��C=m�X��2V��hBXA�ޙ��H�Ҷ�+���qJJ��
��kэ�M� ��}څ?��YM^����-�!����s�"��.:`��sR�c�o�2=춟��.-2���j`���t'��b�#g���1Yk���v$|�bI,��Q�I�05A��wY<�Kt�V1�"�'�EzM��N�9�A�)��a�x6��6Ѡ!�x'�0��ݝ�0F�����<�̣}w=h@�f�}�����w(/�O�i�ׇ��x���T��<B�T�.-�|x �91[/���bT��h�\Z~2$i�w���F
��̋{vyC��mr������]��KRsCvM!Χ��.�}B�wY"�x���с�G�x������ws���8���u������i����9�Ï����M�D�-m���
�ᛴ�n�0R��k��t��ْ�Ƨ��MG`s� Ucp�x3��z��1ß��Fi�H�!��"ɢ��+�'�==��\�$��e��F�+.��]�H������`��,&���+��o�B���]r���F��/6P���<�� ��,��H���F"��_p�rS�@��� m�4�� �=�Ͻy��L��M��<fN�.�w�������	ܝ)p���T��G)��	Ԏ���৥!�*&��pÓ	\���Q�K�OEY�qc�oebų�/�s�֔��UP�n�5��i��k�.R8p)/��9�,m���0� ��X�� �'����Vx���v�
֫�ѝ&�#�3wҝbH`�L�ܟҰ���5M�3+��!�f�x�pa����6�)��̛��R��{��*�-�V��8��Pa0�Wl�N3�ݢJl���`0T�U��<���=�:�H��f��ȸ�f[��wrk��gL��ő8?����b�k�%�>�=��HwG#\�!]B#��[/7�h1B|�����3I�3[1��y��%B�)P�C����t�G�"�ax)�)5�vp��ɵ�U�P[,��jx�2�M��>�4Q�S�wR���:�,���H��gl�3:�5�U���*��΄�@������i_q�LJ*��}E��ɠ�V�0�Ʒ|77硔j�Ή�e��Ԙ�+\_����T�1�1���:xO.AW�@�P��:��u���H�����w�@�qL�n�$Z��R/�R4������U'l��k�
u�χ���y:a_H���������.T��O�oؾqZ~5�L$炧nM5��o6q��i$��9�	&�^��zϰI�.^��z�,��i5��Xnۉ���x1��-����vj9�꺧h��%g��K���ӄ���v�_r�(�?���`^EW|ix^�����la���j�$/���\Ul����jp�-�a�yv7ֿ����?���scmy���n� >��/C�"�K�x�,@��)|�bi@o$��?��
_��Efc�Z���{�Q����y��q�S��%��e��T���9�z!��\�k����#�r���\�+��x��5� !j�c��"��;�2ȴ�wg����|KS9�м��F��Hë~Z�(I�i��h:E��s�,�j���>N;�Ľ�,G�`�\L�i�F�׸�������<��W���,n��7H@�3���8���
�R��*.oï�ٸ��1�n�	|fq���pk��=`a<y�!�@Q��g������� �Ŋ;�����}[�s��d�ʾ5�p��N�-3,B��7i��_9��
�Ň�/����!�Ƿ@)$�ͳ>�>�4�j�~�-����b��,��s��4�a���+�x�Q��Lt�N�o���׃Q�W�h.��m���0�<�d�����y�M�9#IDM��6<��{�dtsE�A�]�S5��]R��X�ѫ)rO��[}�i����8]K��H�?���~Ц�w�F�&�7|3�.?�S��I ٵ�i�`�O	H͓@������;��a[�q*M����{/FON�g]"_��KT�.�j0ro��Z�7:��\r �j�k�4�MSMV�Y�7�S�&oi��d��ъ6Z���/��n�X�Xܠ%��Qݨ+{c�w������O~�ӟ�4-]�S�y�������}Ӵo��_�x���VU         :   x�s�O.)��K��L/J,���+��	K-*������,�-M8c���+F��� �m�            x������ � �         �	  x��Z[n7��=�.��o�!r� A;���	�|���"G;$�+m�34Jf���7e7����ן�~�����߾m?��~���~��E�!r�0`�0�0v�����>���O�(���R��1����%&7����,1��M��!	Z%�<ni��m��tx���7�[~��:����V�P��G�KLnP�-T��_~�G8�{��M��@`�TO�m�s���h9�}c;��#�%��9����|n)�)�夁H8��wҤ��NHU.�6y��O�j^=���Fx"|?��&6���lU1~ ��UW3���@���c�3x���G�KLn��~�Ë�F��b�a��l�,w����b4v0|�1�=kg���|�Ѯ���B
��ϝBʂ�k�hⳡ˸N�I��G��0�w���k*n�� �L��u�+-x��|����!��N�vq��a6솁��?\�ǅe�9����4���"���A�D��� �?�Yb�HB���S�>/10dz��J��6�d��Q�Iʇ��Ί!�	�wf����,1�Y2,�}�����?��%�-*Dlz�ξ�Xi��ͫD+~�=Q�4Z�C���wi�z��������~��$��8��+I^$δ�ʖ�Xci�*�b������_`LSX�.�ﯿ|���M_���=�5�d���.���2��`av.��SS�`���ӵ��|��5+�Ig�wB�����l�!��^�j�̎ʪ���(�P�Ծ�I] 4T��Q,�.�$�����Xq�.M�18!G�w��"ၮ�|#�^f�ٰ�L	����j� �X>�~J����Okui��ԖL�B%nu�ߖl����g�X���f%��Y
xQbɛm�E��r�tM0 P��!�y�(�I�cGc�yb����[������W��RDa�PT��H��J�8\�� ckނ�(a�Q,\25粴�Z�d��Մm����ݺ�@�Z���F���
}e��*AS�<
=���Jքt�L!������*�Q.10���N�>HIw��W�4b`��_R))���v�s/�������l����E���7,��i�{l�uZ�՘��KO���1(,j���Ǌ��F��٢227��}jm��@U�ZT������,1P��E�pJ���Y��\�Ⱥ"��I����A���U�Tj��?�Rk���(MyȿX����e�<� �6�2���1\3���2ۆ��$[��7�d���]؏���=-֤	C#U��X�	(�i>�d��0�Zs�I�&�{���V���,�f4���-�n ��ؠ#0v���<�ڬdݞU\��p���?{�⪲���9e�]b�
�����7�W��Bg�ڀm���j�(�T�]'����C�W�`��Fi�8���a�i)�*NV��9��<��Ng�����ڀ�]p�2V�+}��ݱԬ��O��sW��W����=`
��t�j�,*Vr��5����a�JLE��Z(*a>^���7ؒ*VDu�h�QiW�L܅=�WE+�sb$͍ߙ��I�*Ց�]b�j6��I�p��F�l��%�S�q�=\Yb$s�h*Ĩ�^9�5�1���Y�s��-I��X*��	���7I`߄.uj��SC~?>-Cpf���x�P++��i��킭\��#[�&��iju�����펑+U.�N���H���'ܸ��V{sM���HƼ�qy"fp�ڔ�`�4��r�P��C�1L�㎑L8K�N�H�x��F�d��Y�vB���ʂ����y�藐�i��O��,�W�:���D	�Yn�VP23�#U��|���e�HV�Vt�%n2�sc�F@#F�\ɔ�.����{�\�r�Z�W���#��>S�ou�5�t�#F���e��b!�b8��ŉ���D�9��DC?��F�o~���Ú�꫊�Q�V���$��c[?ܞPO�����5�U���4��Fv7}�c|g5�Z�gtel񱋝��U؉�3�2��ZD�b��`E;���C?w�1R�z�N�0L���.���B#����!��E���d��,��R#��c2��H�k"r:�= �׳숑�T�u�TD��� ;�x��WI.s�^����ا�;��ԃK��ʥ7�(S1��p�m���y3d0A^&!�2�\�Im�M"}�񈮺��U�|Ķ/_��#F.��)�9�N��s_���c��EC�!�$i�J��c��cd�7����QFV'�2�E���p��c7^��Ji�є-n�1�.�\���ƒ�aM�9✰�FScL�2Uꀖ�-�F>{u�%�J5��Pv��V=� :_MP�i��u����<�w>�9��c��v2?u2vcړ+��/;�U��<y�/��?�?���1Y�h^�,�5��&��]��q�<*-_�y�?����_f�CF         D	  x��Y�n�H]���vYI �ŗv����$v<��h4��2U�������F�����~lN�z��њ� ABT���s�9���2]����u�S��t&E�Ud���B��,��T����c���F�?bu�	'�y���s�q���\�k�L����~�\�:Y/�s�,�{�]F�JQ�+� ��K%��,�:[�R���U���2<':<�2�G�Q$�D�9�}*3Q����T2��Ҝ��G�\���z�Uc��[E��F����)J�7n�%�D�����u��&�����a�2�O�U�P�Kz��a�����84۾#���]�q$��efʎ�؜���뇆�҃�crл'�Ɩ�.���D+,t��$0�Ù��U�+�u.M�ǖ�3r+�r�$Lz�8��>�]��[��{)j��t�Љ�E��*iT��|WK�A�Z�J&����d�Jz��B���E��H��ݰ��&�Po��va�a�Y��������ƃ��=��yD������|K�r^n��9~(R�~0����)�����m_��}�Y��;x� Al~����7�U�J 2�$<�7ND��į榙
z�l~��&�hZʕ$a�˕�D�>'�EU��p�H uh��[i4c��ߒ��?|���37�᎙g�#�d��LQ��RwH>П��`�[�������t��e�,E�G|�q��ٶ<�؎}��b�G		��q�z^��KSV5B	3�BנE+�Tt.3�|X`4*}�j�@�(��4�M(r�``�8���+]f�����{��B�c]?p?�m�\�*�$�&�+�+����^�t;��c���Q�lX��Y�����D��F�ɂ��%�u��Y��`�5!XjVK������Ο�ٝ���"YJg*�یɶ���ϸvp	8�n�	L���xAb�=TB�&!g�g���Tdb��1�W�\*؆��	^Kiz{��\�r�"���Z/�T?<��;�� �ʰ�W�F3�K�P��h�����;ofՍm�H�;�I�RyJ�ciR q�Lv��l-3ٞ�6?���=� �J��ri�h�w�~nM��a�ų�����HO왝_���(�VƨV�2�'cGg'��	lmp��ţZ5��p�v�n�DE`(z���Io�K�w��^����nހ��J�M�V����Kc��ʸ��v��ɣ)k��w)��������|���_��w�,�k
%돑��γ�.����#w���L���n���'?��t]�|�e:+0����5�փY��	��kp�R�$��&�ަ�[�-�n�����菱�*�9����M,�MTD�E��m��-��"L���f�G�)9������c�Ʋ�����!?��J���`&�|Y`���Y
�ck+����k��nT�D!襒�����r������P^��a�ر��zM��Z%`�O���x<l�\;^�=�Q4Z<�1���Q$K�Q������_�"yդs��R�W�rO�]�xv����Yi:�Յy�=7!#�S'�0w��d��X�W�[hY�L�l�DJs�L��H&��,D�&��_89 �W]����O�	�����;s�1?_��|沉^p����q�2���Kw%w�\Ǹ���h��M��>�ym��z���B1���ˎ��C��-{�&91E�}S��>���EY��	ɼWhP؁���@Ũ�N87Y�Xd@e������[����9Z  �Mٖ�MN�-Ixvk"9ܷ�K���>���BHu�PÄ���6n�R'�x��	��\:�Ӡ���.@y-@M|b<�(܁���8��͜0f�~�c ��+x8f���m�*	:����bv<��Gk�3v|SJ7B� �!�x�MEo��C�Q�H�4+�J������MC��vI�����\�OM=?P��8c��;,��?$�����eG��u����ĀA���������Z�<nbq4��H�z��]��ᰱ'��.����f����r�E�se2φ�w�>=d>����˥��r�}󽄔�G����E�q%�a�½K]o��~+u�'��9�tw������4�َO7@�1��^聟;�n���[��Ec'6ܙ�.�>��N��bH<�@�"��g�@ݏMRi���{�c;���D�O�ɻ֍�8���#��\������c��L�)KD(�o�7|'?��S�����Ɯ� YUj��a~.8,��]�g���a �x�S�2U��^�p.0 C��r�G�IAo~�fQzؾm�e7� _���WQ�<F���~E��V��܈?�/㋋��W��            x���[�����۫�G2b�(܁%x�6)��XMQ#+HI)ɒl5�}؇}��<��� �:dfu��ء����S��%�ǥ���A|0~<_��������b��>���V��9��ww���.]$1�������j<���:�$7<��䢷9y|?^��N�/�/w�s�����&c׻/���t�%dv���w��f�:c��+�o��Ή����F�CY'�����K(��i|כ�^��?"w���{��#x��Pb�[���%�R�#�����J�U�����S��6���s=��f�rh)��r)��rX)���᯹Q����C�!OjJz�4��\����b5'��*�</U�U<�$^�xjB��gk����{^�v������l����ak���CH�r_��v\�����9���r� t�y0�r.�Է��w�O8�wzW�6��6� ��\�h�+��2>����m~��� ge�˵	ϑ���Q�톒�k9��5�����~N�<W0\ΏN�^��
ʹ�}�(���3k�G�{�J�Z���P8@���������j����i�%m��n��.r�ԋ]�tn՗}3�CI|���})�c���lSMN~��i��S@o��O���Dl�r�p�$W��<�%'>��e������U�=�y2�-Z���g;l�x>I9$�A�!�AI9��8�}���灂y�`������EB��slW巹�u�@�Ȧ�<����_��B���� ��W���'ƒ��u�lH%��&�a�4�@O�k�QM��H����p��<(R�L��=2�V�$VJĪ���pz�{��Ʒ��2�`u�+��E�ƹ��8�/p�Z$��5�rb����a�|�����璪Hnyg�%�!��;��zv�-���8Z���s��Z��ђc�"G W��x
Zh��v.>n@�'�8&�9�nPR��ݠ�BM����2ʰ��l�o�ֺ� �kXn�x���W�k(�Xu�C��DGu���K7ĕ�N2�<�
�1�{�K�}���$�s���vTɀ�J��Sq:pJ�h���9_�h�a���֩=\:���<C����|����|T"���a�e��)�M�!����<��+�9���2�M��wXi���P��+#� �W9=��y�a9>8�&���'(����i�q$nv�,��M<��>��tZ�������$�l�/C�!���� �
u1In!���@Wy���#H�ư�i) �d g���H��v@"y�����&Y�r�d ���N@�4O6Wbc�c��Y����~L Ѥ-����������Ք��q��+�|�I|����׶D/�s΢姠r�vVU���؍�����z�5<*����4g[��Ҍm=�ޑ���������l�yAzrn��(��ċ�m���f}C�΍�+����lJ �|�[��!�d���1�����@Y�	��$_d���&����7�4�5F�_I.H"_c�\��@�����Q0;��HxSIֲH�ԑ�|R?�n=��߼_�'�;���^��&�l�#���Lb��|Z�ɳ��8s���x�x-ʫ����N�=/��ɸ�4����Q�����1.�cI����n����xs�@{��)�TvhBmEg����e��j�݇DY��ǁ�R����R��Z|�fp�F|֖��s������%_۱Z�N�6������J3�501Ӏ"Zc�É+p/�/&JQ/3%
^.��(iLHX���������}ι�<a���{�dKr��<!|�"� �Чk8K!_��*5<$;�B�r�"yUI�'����'���ՓB����I����I�y�C���|�C?8�%��^b�ڪ���3��D3��W�L���(&��)+�eG�����X��l'������pe��O��B���Q��q�r�����q�r��̃��u���Ua�k4�o���g��s��1�	����SLj7 e��Rq� e�@�|:{�2 �X��>�H�A�K^� f�(%�n�1�EQz�R�-�
���3�7�G�6���-`0=o�.`�y�+��Tտ������=zoy	j�
v���b�7�� �>?�fN2�<�x�W��������'8�o'W�ˁ�Ա�T7X���=K% ��	�f��iM�y��3ew87����}wkkVl��E[�jY��l��X�CmaN��u�����^W!�3�#<�~�-��TI,��䷝^&o������1�l�.W�x��9��\��_ B)���XσT�h���V������3���~���B��g?g�[2��$'a�K�%yڈ#�=�(FEZ���
x�7Ѡ�����xP^L�<V�(H�}�X�B�$�ԍ��RW"(�]	_}�RiF�!yp� |3��2B��ح�y������Kt��!?��b}�$J9�:IR�N@
�sHq|)�Y��!�= �9���b[ޣ�h�\[y�n��)J�CS����zb,�0ŕ������C֞)��6 "��ܗ�� g��C�7�L�I�p[*�`�"2l�g䬟��-�	W�ɕDP$�`�ڞ�w*d�e���ڠ�K�0�>��J�M��A�h��a��,32��-{�1u0��8�>���&�,�Bgő��#���!��_�İOܮ�!�}�}���J]u\�\mr;�g�n*��8:@��M|������}�i�m�����a��c-��$IWՃC�wYu�o �_ś��x�;Yix �������-1���[��"�k�bx_S����9��.@��-z�O�ir�� �b,t�c��Q ��P�� nāq�K̭�����fiv��C��@�����Tl�@ �;R@�����#�`������#� ��^�t���{�y��P�z���֑q"q�`E� |IBx]�7��=�+��Z�&3�dM���zk9Z��u-�v�N&����@[�'g�L�6R�-%5�� ��J��e��qZ|���Έ��rv�ц��'�Q��Y����s�����O�Mr���1���C� g��X�/A��E�&�Bgo�8��[���p�wK�f���r&D���-�'�e2�Ko[�5�wj�+��w0��!D���w=@ �]ݠG�'͉Ҹ��w���Z��"��G������y]��ī���B)�W� ᳔G���y���G�rP��?���=��+�x��5�����@����x��|l&@4�<�n��G�_ⴌ{Kd�'@�̑tV�p�#�d�3G�'@����y�>Ĵ;���6)f�?C�� |Ä�e�B�8�f .�@�!t~����U.�]�-XLxB�X�T������ގ+f �b�b�e+�"�?	�hu�L��.��K�-E���ZJ</�Dqx^P��x^Ј���U�8�ᶬV;%�Sh\���+��c�!p�`�8�B��b
3�x��Q<Iƌ�O�1#*͓d�9*CIa_AT�g�-*�Y�=󮉹��=*/e���Î*ܭ@@�A�-�!#�� ��O��Z�V��ڞ֔���e��A[�v��Ř;��f�~��^7�'Ś�B��%���2�"��p�Wr�Q�w�0@�џڡ^}4Z*��mv�c�4J�6V8B�7v_�FxS�'(A�'!�c��$���'�0f"O�a̤B��ܦ�h��|�buU�U(W��j-��D�'p|���}�њ��|zƖh��<�!O�X�x��fr����ME�����=���mX"\Vc\��SH��m�PS�&����e|��ã\�g���r�����fA!ʋά�M�t�E�5�y*uWr����i��Е /���K*]\�%M'�����ˊ��0��>/�����vݵ�$^~r8�M|��9�U^ק>���/&ğ�w�F��U�)��=�VI��ł G�-��D���8*��ak��I�!Y>R��AI9$W"���A6s����%1�	���#nB���G&A��#�Q4�l������q�\��㒂Ғ��w�^������    �X�c�O*GP%��1b�&��
�/b���P�񵖇��jN�����Sj��ߖSj�1ܖSruc�-��t[N):N�m9�#x1��rvZZ��2��^��=�}�6�T�˦�$�ƱT��V\&�����j�r��ڏ�mHP�+�!�"#K�����O�$THv���o���o[��v�YB�d.��1�$KD�a�%;8��56:�&TM�_��0i�K��to���	����caE �V�B�e�	�N�z�9�A�qWy���t�3��QÔ�����s�b�fR�&�ϗ��@��m��H#	p%�C^Ջ[��]	��%aW����E@�x�n>�7�&P9^�Z�t��Ӌ�$h��x�ݕ��@��Q�!A�xU]p��4����Ӷ%h�i�$FMڷ�M�-A���cK��I�����:��om�=IW�H�v���	"DM�F����($����$��E����/?윒Lh��%�HY��Rao��C�y6Y5!�(f�o�½��B��hs��'i���'i󶞐�v���Z���g0�������㗜����Θ�Z\�ܣ)�Fsg&P���j4�zn��;�ђ\�h��.δtF< �
r^�10O~(`�G�l�j�`�mL^3_�G0�d{�OfO��k!�a_�ۮH�̐�̡ ��x����i�� H��Zv��
nK��e@,x����ד��m�H ���p|��Q�3���� �������[��`�l�������|=�#k],���X�-u��8Z�����Gmm�{�U�����J�T7 1ȹ�:yA�%�Utk��V j�����������o���YK��	$@�Q~��U�zOLn�5����o���n�ܘ�n�j�n�f��d���3��& �DJwx��:d��$��RsI���f�"GK]*%��T��Z���~�q΢�Yn�D��X��~c2d�<����E���Y;��u9���!�nW#{�K���H��S"��<ElPd��KB�/��Bi�aPC�2XƊ	�kM���9m3�-�?)����Qã|Cμ��4��O�$�}T4�>��2d(PHz�8�L���g�à�e2�eH���PN�ʇL�5�X1C�`y��fǅ~{���o��D.�8(���|[�"�Jz�`�Ѱ�+$��ɡ$_�+���X,�k/'����5h�-��D5'ҹ7'��f��I��1ʯ�g ��L�������y�ٕ�'O���Z�Q��@Υ��t�A�~� J!�]��="{��N-�>v�h~ʹ�h~ʹ^y�qX���-iW��%���i�����P��</�\X-�}��Zv˦���0�j��2�4"@)��N���
wk~��On��X�خ�X�3�}M����������\GϠ�wJ5���&���5�T�����9/q�> UPL�@�(�<��l�=���9�@-�f2��g��A��ʕ��V���~�'��d��P���=���C���[4��MMy���J�1߮ܥ���J��f�-$� IbH�z��Wk2��������Q,�t�Z?ZM@�X�&*�u�^��{?Sw�R2����[������:�\@�~N�������*ɜCu9~��97|�s�>��Փ�7�\<	��kh���z�d�^�������(��!�
�ּ�S_.d9E�})�z������*~9GZ�Bۄe�5�e��2o�uK�V�ϴ�i���چ]�.Ӯ�W�L���	�03v�S�ذ+=*��\����k���"��7�yZ+�F֋0���w�&ũ^�R���uW<̐���F�����R}����0�k5�F���s���B�0����]���s�E�[o��|�?R�َ�t.�i��w��9(5�y��J)%����G#�2�<��3ټ��ͼ"sf�IX���Yϰ¯	���F�l�?�v:�O;��SMNP-�JH0?B�*m,̈��I,���*�sM��Q�{�-f���J*�,���q�Ơ��8�ힳچ]dN^�"H$-)u�KZR�4+����<�1-@^e�A��O�Ŕq����0���p�K�	s�A3�o�D�d&���()<�U<*��j���m�K6���A�:�-��<ϯf>�<��5�%�m��g�JU{�f�~>G�4�S w����S?�3��4�C+����z�il u �2��A:���VZH�q��ջ��]�99b�|�3h��@�ub�?x�)ӫ%ƎUk����c������+��ɤ�IZ���d��)h�G_�����]1�������s9���#���d�&Ϝ��ќOq'��TAJy=��h��@B��0�XĔ�x��Y9��>M0mr�זDy�B��.~����� $97��t[�=��8���]
T�馣�*}�������0����q�a���&�X�2����z��؅�H~�V�~��� G��mev��`��p���g�G2��$1
d����1쭇*�J�]
P� ���i2�dd��i�i�ӂ��܌O�����,k�g]r�'���1�L��1U;�1�S�IR�zx�䊓��j�#Wi��C5Ngx�	w�-s�����1�����Y����/�����bs=������N=��!��w e;Ϗ'�7I�V�-'�E��R� G��VC\��'l�퐵�U+���ક�h���*p���k9Zp�J-��Z��7� �����u4Z��m�kAd�G�����l��B�ծ�$�iߓ��t�I�\:��d*�*�+냼���pP�N}�e�Im���=T�nMW��>
�6v�.F~!N�j�S����'���_��@n�O�sS��awEg���|m8��0�&&mU�L_�ꞡ/h�)�'�7�xJ�a� ��Ih����Zǰ����a�	���&�����!L�$���↎�����rH�崔C���H9�1�U�]�E	bSs�zS�Ue����a�I��E��|�l(-\�vo?�$5�W<I���<I�]�O�'�2iy�?i�IW�*ݟ�� �c����ٓ�Q�"���{C�:���j�X*�P5�m]v9[�|P��P�Տ刚�F5 ��q��Z���jT	D��?� m�O:�Y�( ��6�hT9��q�j�&���9���h8���hg��"�D7a�֌�}��1PV�b��4@�4�g�7H ͚�6�fo�m�I�p[��A��MF����/Lep6R�;�bqr�+�ɸd���<�mR��H6JQ�#M�)�y�����GX�0�d����0�x��������+c�ms�!��5 �_@��%#�CH?6��m_V�qw�+g hv���N
����͡2(��r�4����d3(	��=*�#��Ai��k��(t�ܞ�R�Q�I�B�O�ѭl�`�#�S�0�c����cD��	S��h�l�{���-�"��v���;h�NA�x���.���@d@�xi�с@R��qB���Qp��ȩ+�"��)�ٕ�+KF1�b9��Oϼ�134�*�P�D� k��L_�jU�k��>��V�,���?�r�U�A��/K�ݝ�K҆jcb�9��4�u�D|_�����
$�a�E�+�����	s����g��͘so��:X����������	�W���:��"Yj���g��Y~~A��j��"З]��qF�6N/i�e��1�ٷq�25:$3�+V�}
$o�����B�������'��Gm�����ΰ/��p��20�e@��W�
��<6�[H�)��놄Oj�2��W�h��DjN�7۷�r�"�-��*�ȴI��|	��C(﯋�r�è��B���ݧS���@x�����xNV�d�� ��Ɖ���sN������
5��c^;�����v���A�����M���_M��ā���e%W%��J���� �y�D[1�;GW �Z����+�WZ�?8�?�eb�|0 l})W��[�i�nb�r�y���mZS���y��-V�1�K���Kӝ�K��vr�iO� ���;5$ϓ�KI��\�Oh�-�W��IiB���,v@#�l 	  �O�AU;ʳ���ZB���f=���`g��a"���*�K�5v�*j*�=;�Fh;ĂM��ch���x�q�
�q�&���d��T����|l��z�?�EF_������`I�*(]��_���XV�	���H���/G�;�J�쨱�K��ժGx��j̓MC֥�wG+��ն�굎m�\��ݧ����������������9V������WV��/���R)	��p�X����)a�8�[��[R��-��-)����$[x-D�m�ʩ/�vG�v��B���]e���2��ҽ"b�7��s��Y���7��sx��0b�e��&���U%�|�f:7Z�CZ�'�f
%��$��4����+��a��������=C�kÒY9�lm�I��ԓR3vCO
Ҿu�'A�:ݓ�F&�LO
��u�'��ֹ�L�I�J�$Y�+)�L��IIv��NJ2�'�R��<���L剩�d+Ol�$cyb,%Y�ki�Z�XKK��OI���Z�R9�M�P���cU��O�Q$h��cI0<I�I,O�68��}�
^���|�t����4���"�K��祾���%�����i.���沑���i.[31S�}M�
�|��iڄc`i&d��Z�1�]��&��/'����@������CjQIK9�6���CjU(3�9�v���CjY�?�9���:�C�Q�`sH#b�]��� ]��D�K�D��m��ڧx��亡xU�w�Z�7�݁Bٌ�&�7x����A��.r�=�{�KpG�u��F�Y΂��ڡ��A�uG�����N~C�7z���ɟ3*�in�<^^ ���a0ftr r x^o���v����X>h���� ���@�xV�+.�W�5�ROJ[=)�cN���/�uO
�Ӧ'���i�^K�MR/i��ɴ䴟҅����~�5P=�b�9�oQ��O�*7D5��i�[�v�e,��A�x[�<���LU��*yY؁�Q@�NWg�M���u3<���@�� jP*&����H�z�I�K!�-�r;�(+u�)+uk(+uk)+u��x,j6��X/l������'�J/��Xp�{a[�s�����9�!�X��._���)�S�k�9:��-�����cHk�>H��0�y��'�w�3����]Y=����@q���p�9Hp�8���0����s���٥M�B�H�u���Q���N@Uh��P�Q�t�%�\��l
���=���ZWT9v�Yw�q�0`]?������+q߂���$��X��X�Ok��FfB�X������-^d��L�?��
�JKc���C45�/��Y����@�rt
� �rD�q���{^�oÅu�ͳ��p��1`���~Z�=��q`F� ��h�Mm��yFC���d�T֬�o��c�#͈�q.HD/�g�}��k�h��@��=��[�j�Ў��YOI@�m�h�)ǿ���fD��/h�sa�KvB���6b�_�pa��`��#�"�*�@H�k�,8�B��km$���x�����J�e��U|Q�L{���ve	��WY=D�������{o=��S���H�1s$���f�$��C�ܓ���!v���u!n���0D�K@�9Lk�����66�}���p���+� �^����������Q5EJ{�[K�4O��>����&�/��-v�xu�b!^�G�ja0�)���j�@}�4o��o�K�}��C`�M���W���P[��d��=D忡D�U����	uX@��J�Aq����R��K}2MK�^R�Ͻ�ϞZ��/�[�� ����6nG�,�7h9^`��H��w���(Êe�;���2�^��o���x�~ÿL�"�	����]�ޥ�A%�X��уV�����)�6Y��߃8�����{?P�TO �^7��;?=h k|c\l�$,�(�4	k�i=(�<pZ�܃Z�;�&gG��3����ˌ' �sܠ[w��S��"0;"�,�O��ٹ�� ��#� RGR ��\�W<(/h,d�D&�*�%��5%��+J�k�֓�g�����=cԵ�$�ۮ�$y�!������]`�r���� E�k��*�4gr��qd		n|G�'�5.���z RP�-I��� �.�x�'X��@+����s��:Q�m�$���8�\H.�^\9@+#p5}�˝0������<h��XN��A���]��=��Hǝ��=,�y4�e[�M���?����I��	h	{��3��ɟ����]��         �   x�3�,.�V
��q�p�wt���S��TI,J��3�LQ)�5�Tɵ53556�)�5�)�5T���.�vq�ϫ,3w�),�v5�tTI3,+q��,0�r��͋,�)�r��
O���/�0s�7��(4���������� �@&     