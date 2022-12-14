create schema memobotdb;

use memobotdb;

create table Users
(
    id int not null primary key unique
);

create table Categories
(
    id      int          not null primary key unique auto_increment,
    user_id int          not null,
    name    varchar(255) not null,
    foreign key category2user (user_id) references Users (id)
);

create table Cards
(
    id          int                               not null primary key unique auto_increment,
    category_id int                               not null,
    question    varchar(255)                      not null,
    answer      text                              not null,
    grade       ENUM ('excellent', 'okay', 'bad') not null,
    foreign key cards2category (category_id) references Categories (id) on delete cascade
);

alter table categories
    add unique userIdName (user_id, name);

alter table cards
    add unique categoryIdQuestion (category_id, question);

# SET FOREIGN_KEY_CHECKS = 0;
# truncate table users;
# truncate table categories;
# truncate table cards;
# SET FOREIGN_KEY_CHECKS = 1;

SET collation_connection = 'utf8_general_ci';
ALTER DATABASE memobotdb CHARACTER SET utf8 COLLATE utf8_general_ci;

ALTER TABLE Cards
    CONVERT TO CHARACTER SET utf8 COLLATE utf8_general_ci;
ALTER TABLE Categories
    CONVERT TO CHARACTER SET utf8 COLLATE utf8_general_ci;
ALTER TABLE Users
    CONVERT TO CHARACTER SET utf8 COLLATE utf8_general_ci;