DELIMITER $$

USE mysql $$

DROP PROCEDURE IF EXISTS init_db $$

CREATE PROCEDURE init_db()
BEGIN

#
# Create the database.
#

DROP DATABASE IF EXISTS pibb_0001;
CREATE DATABASE pibb_0001
    DEFAULT CHARACTER SET utf8
    DEFAULT COLLATE utf8_general_ci
;

#
# Create the user with password.
#

# There is no "DROP USER IF EXISTS ..".
# So first add an unimportant previlege to the user such that it exists, then delete it.
GRANT USAGE ON pibb_0001.* TO 'pibb_0001'@'localhost';
DROP USER 'pibb_0001'@'localhost';

CREATE USER 'pibb_0001'@'localhost' IDENTIFIED BY 'Uxsfl29RkG53T';
GRANT ALL ON pibb_0001.* to 'pibb_0001'@'localhost';
FLUSH PRIVILEGES;

END $$

DELIMITER ;

CALL init_db();

USE pibb_0001;

