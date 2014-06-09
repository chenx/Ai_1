DELIMITER $$

USE mysql;

DROP PROCEDURE init_db IF EXISTS init_db $$

CREATE PROCEDURE init_db()
BEGIN

#
# Create the database.
#

DROP DATABASE IF EXISTS mdb;
CREATE DATABASE mdb;

# use '#' or '-- ' for comments.
-- use mdb;

#
# Create the user with password.
#

# There is no "DROP USER IF EXISTS ..".
# So first add an unimportant previlege to the user such that it exists, then delete it.
GRANT USAGE ON mdb.* TO 'usr'@'localhost';
DROP USER 'usr'@'localhost';

CREATE USER 'usr'@'localhost' IDENTIFIED BY 'pwd';
GRANT ALL ON mdb.* to 'usr'@'localhost';
FLUSH PRIVILEGES;

END $$

DELIMITER ;

CALL init_db();


