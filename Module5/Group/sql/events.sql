create table events (
    event_id int unsigned not null auto_increment,
    user_id int unsigned not null,
    username varchar(100) not null,
    title varchar(20) not null,
    content text default null,
    time datetime not null,
    todo_status enum('yes','no') default 'no',
    category enum('work','personal','others') default 'work',
    primary key (event_id),
    foreign key (user_id, username) references users (id, username)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
