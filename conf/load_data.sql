--
-- This script inserts default data to tables in database cssauhco_demo_php.
--
-- For a new site, change values of DATABASE_NAME and admin/user config below.
--
-- Usage: mysql> source add_data.sql
--
-- @By: Tom Chen
-- @Created on: 4/29/2013
-- @Last modified: 4/29/2013
--

USE db_php_ai;

INSERT INTO User (first_name, last_name, email, login, passwd, note, gid) VALUES (
  'Demo', 'Admin', 'admin@test.com', 'admin', MD5('password'), '', 0
);

INSERT INTO User (first_name, last_name, email, login, passwd, note, gid) VALUES (
  'Demo', 'Test', 'test@test.com', 'test', MD5('password'), 'Test special char <''>".', 1
);



INSERT INTO UserGroup (ID, name) VALUES (0, 'admin');
INSERT INTO UserGroup (ID, name) VALUES (1, 'user');


