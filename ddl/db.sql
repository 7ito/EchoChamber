CREATE DATABASE IF NOT EXISTS echochamber;

USE echochamber;

DROP TABLE IF EXISTS Community;
DROP TABLE IF EXISTS User;
DROP TABLE IF EXISTS Post;
DROP TABLE IF EXISTS Comment;

CREATE TABLE User (
    userID INTEGER AUTO_INCREMENT,
    username VARCHAR(20),
    password VARCHAR(50),
    PRIMARY KEY (userID)
)

CREATE TABLE Community (
    communityID INTEGER AUTO_INCREMENT,
    name VARCHAR(50),
    description VARCHAR(255),
    rules VARCHAR(255),
    banner VARCHAR(50),
    creatorID INTEGER,
    PRIMARY KEY (communityID),
    FOREIGN KEY (creatorID) REFERENCES User(userID)
        ON DELETE SET NULL ON UPDATE CASCADE
)

CREATE TABLE Post (
    postID INTEGER AUTO_INCREMENT,
    title VARCHAR(30), 
    body VARCHAR(255),
    whenPosted DATETIME, 
    points INTEGER,
    posterID INTEGER,
    communityID INTEGER,
    PRIMARY KEY (postID),
    FOREIGN KEY (posterID) REFERENCES User(userID)
        ON DELETE SET NULL ON UPDATE CASCADE,
    FOREIGN KEY (communityID) REFERENCES Community(communityID)
        ON DELETE SET NULL ON UPDATE CASCADE
)

CREATE TABLE Comment (
    commentID INTEGER AUTO_INCREMENT,
    text VARCHAR(255),
    whenPosted DATETIME, 
    points INTEGER, 
    replyTo INTEGER,
    commenterID INTEGER,
    postID INTEGER,
    PRIMARY KEY (commentID),
    FOREIGN KEY (replyTo) REFERENCES Comment(commentID)
        ON DELETE SET NULL ON UPDATE CASCADE,
    FOREIGN KEY (commenterID) REFERENCES User(userID)
        ON DELETE SET NULL ON UPDATE CASCADE,
    FOREIGN KEY (postID) REFERENCES Post(postID)
        ON DELETE SET NULL ON UPDATE CASCADE
)

INSERT User (username, password) VALUES ('bobthebuilder', 'canhefixit');
INSERT User (username, password) VALUES ('brolak', 'password');
INSERT User (username, password) VALUES ('mkleo', 'goat');
INSERT User (username, password) VALUES ('miamiheat', 'jimmybutler');

INSERT Community (name, description, rules, banner, creatorID) VALUES ('MechanicalKeyboards', 'A place to discuss mechanical keyboards', 'Do not be cringe', 'banner', 1);
INSERT Community (name, description, rules, banner, creatorID) VALUES ('NBA', 'A place to discuss the NBA/basketball', 'Do not post like this is r/nba', 'nbabanner', 4);