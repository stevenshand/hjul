use 'hare';
DROP TABLE if exists orders;
CREATE TABLE orders (
	id MEDIUMINT NOT NULL AUTO_INCREMENT, 
	fname varchar(255) NOT NULL, 
	sname varchar(255) NOT NULL, 
	order_date DATE,	
	notes text,
	model int,
	size int,
	frame_only boolean default 0,
	frame_number varchar(255),
	PRIMARY KEY (id) );
	
DROP TABLE if exists models;
CREATE TABLE models (
	id int NOT NULL, 
	name varchar(255) NOT NULL, 
	PRIMARY KEY (id) );

INSERT INTO models ( id, name ) values 
								(1, 'Stoater' ),
								(2, 'Stoater Rohloff' ),
								(3, 'Stooshie' ),
								(4, 'Stooshie Rohloff' ),
								(5, 'Bahookie' ),
								(6, 'Bahookie Rohloff' ),
								(7, 'Bahookie Single' ),
								(8, 'Hoolie' ),
								(9, 'Tumshie' ),
								(10, 'Drove' ),
								(11, 'Oykel' ),
								(12, 'Skinnymalinky' ),
								(13, 'Custom' );

DROP TABLE if exists sizes;
CREATE TABLE sizes (
	id int NOT NULL, 
	size varchar(255) NOT NULL, 
	PRIMARY KEY (id) );
	
INSERT INTO sizes ( id, size ) values 
								(1, 'X-SMALL' ),
								(2, 'SMALL' ),
								(3, 'MEDIUM' ),
								(4, 'LARGE' ),
								(5, 'X-LARGE' ),
								(6, 'CUSTOM' ),
								(7, 'TBC' );

DROP TABLE if exists status;
CREATE TABLE status (
	id int NOT NULL, 
	status varchar(255) NOT NULL, 
	PRIMARY KEY (id) );

INSERT INTO status ( id, status ) values 
								(1, 'ordered' ),
								(2, 'materials on hand' ),
								(3, 'fabrication' ),
								(4, 'finishing' ),
								(5, 'paint' ),
								(6, 'assembly' ),
								(7, 'shipped' );
											
DROP TABLE if exists payments;
CREATE TABLE payments (
	id int NOT NULL AUTO_INCREMENT, 
	order_id int NOT NULL,
	payment_date DATE NOT NULL, 
	payment DECIMAL(10,2) NOT NULL, 
	PRIMARY KEY (id) );											
											