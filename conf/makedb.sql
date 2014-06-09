--
-- This scripts create tables in a database.
--
-- For a new site, change values of DATABASE_NAME and admin/user config below.
--
-- Usage: mysql> source makedb.sql
--    or: shell> mysql < makedb.sql
--
-- Note:
--   - MD5(): result is 32bytes.
--     See: https://dev.mysql.com/doc/refman/4.1/en/encryption-functions.html
--
-- @By: Tom Chen
-- @Created on: 4/25/2013
-- @Last modified: 4/29/2013
--

USE db_php_ai;

DROP TABLE IF EXISTS User;
CREATE TABLE User (
    ID         int NOT NULL PRIMARY KEY AUTO_INCREMENT,
    first_name varchar(50) NOT NULL,
    last_name  varchar(50) NOT NULL,
    email      varchar(100) NOT NULL,
    login      varchar(50) NOT NULL UNIQUE,
    passwd     varchar(32) NOT NULL,             -- MD5 value, 32 bits.
    note       varchar(50),
    gid        int NOT NULL DEFAULT 1            -- UserGroup ID.
);


DROP TABLE IF EXISTS UserGroup;
CREATE TABLE UserGroup (
    ID int NOT NULL PRIMARY KEY,
    name varchar(20) -- 'group' is a reserved word.
);


--
-- This table can be used to construct view/edit form.
-- Could use result from "show columns from [table]", but title is the same as field name.
-- Use ` to delimit reserved words.
--
DROP TABLE IF EXISTS Schema_TblCol;
CREATE TABLE Schema_TblCol (
    ID int    NOT NULL PRIMARY KEY AUTO_INCREMENT,
    TableName varchar(100) NOT NULL,
    Title     varchar(100),
    Field     varchar(100) NOT NULL,
    `Type`    varchar(100) NOT NULL,
    `Null`    varchar(100),
    `Key`     varchar(100),
    `Default` varchar(100),
    Extra     varchar(100)
);
ALTER TABLE Schema_TblCol ADD UNIQUE KEY (TableName, Field);

