#Create database
CREATE DATABASE IF NOT EXISTS rfid;

USE rfid;

#Create table for id
CREATE TABLE IF NOT EXISTS id_rfid (
	id INT UNSIGNED NOT NULL AUTO_INCREMENT UNIQUE,
	rfid_uid VARCHAR(255) NOT NULL,
  name VARCHAR(255) NOT NULL,
  created TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY ( id )
);

#Create table for access log
CREATE TABLE IF NOT EXISTS data_id(
	id INT UNSIGNED NOT NULL AUTO_INCREMENT UNIQUE,
	user_id INT UNSIGNED NOT NULL,
	clock_in TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
	picture VARCHAR(255),
	PRIMARY KEY (id)
);

#Create table for login
CREATE TABLE IF NOT EXISTS users(
	id INT UNSIGNED NOT NULL AUTO_INCREMENT UNIQUE,
	username VARCHAR(50) NOT NULL,
	password VARCHAR(255) NOT NULL,
	created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
	PRIMARY KEY (id)
);
	
