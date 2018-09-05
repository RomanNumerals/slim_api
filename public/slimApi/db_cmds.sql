CREATE DATABASE apiDB;

USE apiDB;

CREATE TABLE fruit (
	id INT(11) AUTO_INCREMENT PRIMARY KEY NOT NULL,
	fruit_name VARCHAR(25) NOT NULL,
	fruit_color VARCHAR(20) NOT NULL,
	season VARCHAR(10) NOT NULL,
	calories INT(500) NOT NULL,
	description VARCHAR(256) NOT NULL
);

INSERT INTO fruit (
	fruit_name, 
	fruit_color, 
	season, 
	calories, 
	description) 
VALUES (
	':fruit_name', 
	':fruit_color',
	':season',
	':calories',
	':description'
	);