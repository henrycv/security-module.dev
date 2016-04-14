-- Adminer 4.2.4 SQLite 3 dump

DROP TABLE IF EXISTS "action";
CREATE TABLE "action" (
  "id_action" integer NOT NULL PRIMARY KEY AUTOINCREMENT,
  "name" text NOT NULL
);


DROP TABLE IF EXISTS "page";
CREATE TABLE "page" (
  "id_page" integer NOT NULL PRIMARY KEY AUTOINCREMENT,
  "name" text NOT NULL
, "page_parent_id" integer NULL DEFAULT 'NULL');


DROP TABLE IF EXISTS "page_action";
CREATE TABLE "page_action" (
  "id_age_action" integer NOT NULL PRIMARY KEY AUTOINCREMENT,
  "page_id" integer NOT NULL,
  "action_id" integer NOT NULL,
  FOREIGN KEY ("page_id") REFERENCES "page" ("id_page"),
  FOREIGN KEY ("action_id") REFERENCES "action" ("id_action") ON DELETE NO ACTION ON UPDATE NO ACTION
);


DROP TABLE IF EXISTS "role";
CREATE TABLE "role" (
  "id_role" integer NOT NULL PRIMARY KEY AUTOINCREMENT,
  "name" text NOT NULL
);

INSERT INTO "role" ("id_role", "name") VALUES (1,	'ROLE_ADMIN');
INSERT INTO "role" ("id_role", "name") VALUES (2,	'ROLE_OPERATOR');
INSERT INTO "role" ("id_role", "name") VALUES (3,	'ROLE_USER');
INSERT INTO "role" ("id_role", "name") VALUES (4,	'ROLE_ANON');

DROP TABLE IF EXISTS "role_action";
CREATE TABLE "role_action" (
  "id_role_action" integer NOT NULL PRIMARY KEY AUTOINCREMENT,
  "role_id" integer NOT NULL,
  "action_id" integer NOT NULL,
  FOREIGN KEY ("role_id") REFERENCES "role" ("id_role"),
  FOREIGN KEY ("action_id") REFERENCES "action" ("id_action")
);


-- DROP TABLE IF EXISTS "user";
-- CREATE TABLE "user" (id INTEGER NOT NULL, username VARCHAR(255) NOT NULL, email VARCHAR(255) NOT NULL, password VARCHAR(255) NOT NULL, roles CLOB NOT NULL, PRIMARY KEY(id));

-- CREATE UNIQUE INDEX "UNIQ_2DA17977E7927C74" ON "user" ("email");

-- CREATE UNIQUE INDEX "UNIQ_2DA17977F85E0677" ON "user" ("username");


DROP TABLE IF EXISTS "user_role";
CREATE TABLE "user_role" (
  "id_user_role" integer NOT NULL PRIMARY KEY AUTOINCREMENT,
  "user_id" integer NOT NULL,
  "role_id" integer NOT NULL,
  FOREIGN KEY ("user_id") REFERENCES "user" ("id"),
  FOREIGN KEY ("role_id") REFERENCES "role" ("role_id"),
  FOREIGN KEY ("role_id") REFERENCES "role" ("role_id") ON DELETE NO ACTION ON UPDATE NO ACTION
);

