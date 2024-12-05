/*Creación de la base de datos sistemaweb*/
CREATE DATABASE sistemaweb;

/*Creación de tabla usuarios*/
CREATE TABLE usuarios(
    id INT(11) NOT NULL AUTO_INCREMENT,
    username VARCHAR(20),
    password VARCHAR(20),
    PRIMARY KEY (id)
    );

/*Insertar registros a la tabla usuarios*/
 

/*Creación de tabla ventas*/
CREATE TABLE ventas(
	id INT(15) NOT NULL AUTO_INCREMENT,
	num_ventas INT(15),
	total_ventas DECIMAL(15,2),
	fecha DATE,
	PRIMARY KEY (id)
	);


/*Insertar registros a la tabla ventas*/
INSERT INTO ventas (num_ventas, total_ventas, fecha) VALUES ('10', '6790', '2020-03-02');
INSERT INTO ventas (num_ventas, total_ventas, fecha) VALUES ('15', '9790', '2021-03-03');
INSERT INTO ventas (num_ventas, total_ventas, fecha) VALUES ('20', '12790', '2020-03-04');
INSERT INTO ventas (num_ventas, total_ventas, fecha) VALUES ('30', '15790', '2024-03-05');
INSERT INTO ventas (num_ventas, total_ventas, fecha) VALUES ('40', '21790', '2020-03-06');
