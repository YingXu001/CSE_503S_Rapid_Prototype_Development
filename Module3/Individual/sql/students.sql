MariaDB [wustl]> show create table students;
+----------+---------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------+
| Table    | Create Table                                                                                                                                                                                                                                |
+----------+---------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------+
| students | CREATE TABLE `students` (
  `id` mediumint(8) unsigned NOT NULL,
  `first_name` varchar(50) NOT NULL,
  `last_name` varchar(50) NOT NULL,
  `email_address` varchar(50) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 |
+----------+---------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------+
1 row in set (0.00 sec)

-- create table students (
--     id MEDIUMINT UNSIGNED not null,
--     first_name VARCHAR(50) not null,
--     last_name VARCHAR(50) not null,
--     email_address VARCHAR(50) not null,
--     primary key (id)
-- ) ENGINE=InnoDB DEFAULT CHARSET=utf8;
