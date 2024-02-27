create table users (
    id int unsigned not null auto_increment,
    username varchar(100) not null,
    password varchar(300) not null,
    primary key (id, username) 
) ENGINE=InnoDB DEFAULT CHARSET=utf8;