-- CREATE DATABASE IF NOT EXISTS `ordernet`
-- USE DATABASE `ordernet`

CREATE TABLE `empresa`(
  `id` int(11) PRIMARY KEY AUTO_INCREMENT,
  `RUT` VARCHAR(12) NOT NULL,
  `nombre` TEXT NOT NULL
);

CREATE TABLE `usuario`(
  `id` int(11) PRIMARY KEY AUTO_INCREMENT,
  `usuario` VARCHAR(50) UNIQUE NOT NULL,
  `pass` TEXT DEFAULT NULL,
  `token` TEXT DEFAULT NULL,
  `permisos` TEXT DEFAULT NULL,
  `empresa` int(11),
  FOREIGN KEY (empresa) REFERENCES `empresa`(id)
);

CREATE TABLE `seccion`(
  `id` int(11) PRIMARY KEY AUTO_INCREMENT,
  `nombre` VARCHAR(50) NOT NULL,
  `empresa` int(11),
  FOREIGN KEY (empresa) REFERENCES `empresa`(id)
);

CREATE TABLE `subseccion`(
  `id` int(11) PRIMARY KEY AUTO_INCREMENT,
  `nombre` VARCHAR(50) NOT NULL,
  `empresa` int(11),
  FOREIGN KEY (empresa) REFERENCES `empresa`(id)
);

CREATE TABLE `articulo`(
  `id` int(11) PRIMARY KEY AUTO_INCREMENT,
  `detalle` VARCHAR(120) NOT NULL,
  `codigo` VARCHAR(50) DEFAULT NULL,
  `marca` VARCHAR(50) DEFAULT NULL,
  `empresa` int(11),
  FOREIGN KEY (empresa) REFERENCES `empresa`(id)
);

CREATE TABLE `item`(
  `id` int(11) PRIMARY KEY AUTO_INCREMENT,
  `cantidad` int(11) NOT NULL,
  `posicion` int(11) NOT NULL,
  `articulo` int(11),
  `empresa` int(11),
  FOREIGN KEY (articulo) REFERENCES `articulo`(id),
  FOREIGN KEY (empresa) REFERENCES `empresa`(id)
);

CREATE TABLE `proveedor`(
  `id` int(11) PRIMARY KEY AUTO_INCREMENT,
  `RUT` VARCHAR(12) NOT NULL,
  `nombre` TEXT NOT NULL,
  `empresa` int(11),
  FOREIGN KEY (empresa) REFERENCES `empresa`(id)
);

CREATE TABLE `proveedor_articulo`(
  `proveedor` int(11),
  `articulo` int(11),
  `codigo` VARCHAR(50) DEFAULT NULL,
  PRIMARY KEY(proveedor, articulo),
  FOREIGN KEY (proveedor) REFERENCES `proveedor`(id),
  FOREIGN KEY (articulo) REFERENCES `articulo`(id)
);

CREATE TABLE `seccion_subseccion`(
  `seccion` int(11),
  `subseccion` int(11),
  `estado` TINYINT(1) NOT NULL DEFAULT 0, -- 1 = activo, 0 = inactivo
  `fecha` VARCHAR(14) DEFAULT NULL,
  PRIMARY KEY(seccion, subseccion),
  FOREIGN KEY (seccion) REFERENCES `seccion`(id),
  FOREIGN KEY (subseccion) REFERENCES `subseccion`(id)
);

CREATE TABLE `item_subseccion`(
  `item` int(11),
  `subseccion` int(11),
  PRIMARY KEY(item, subseccion),
  FOREIGN KEY (item) REFERENCES `item`(id),
  FOREIGN KEY (subseccion) REFERENCES `subseccion`(id)
);

CREATE TABLE `usuario_seccion`(
  `usuario` int(11),
  `seccion` int(11),
  `estado` TINYINT(1) NOT NULL DEFAULT 0, -- 1 = activo, 0 = inactivo
  `fecha` VARCHAR(14) DEFAULT NULL,
  PRIMARY KEY(usuario, seccion),
  FOREIGN KEY (usuario) REFERENCES `usuario`(id),
  FOREIGN KEY (seccion) REFERENCES `seccion`(id)
);

CREATE TABLE `carrito`(
  `id` int(11) PRIMARY KEY AUTO_INCREMENT,
  `fecha` VARCHAR(14) DEFAULT NULL,
  `usuario` int(11),
  FOREIGN KEY (usuario) REFERENCES `usuario`(id)
);

CREATE TABLE `carrito_articulo`(
  `carrito` int(11),
  `articulo` int(11),
  `cantidad` int(4),
  PRIMARY KEY(carrito, articulo),
  FOREIGN KEY (carrito) REFERENCES `carrito`(id),
  FOREIGN KEY (articulo) REFERENCES `articulo`(id)
);

------------------------------------------------------------
INSERT INTO `empresa` (`id`, `RUT`, `nombre`) VALUES (NULL, '120000000000', 'TorniPay');

INSERT INTO `usuario` (`id`, `usuario`, `pass`, `token`, `permisos`, `empresa`) VALUES (NULL, 'diego', NULL, NULL, 'normal', '1');
INSERT INTO `usuario` (`id`, `usuario`, `pass`, `token`, `permisos`, `empresa`) VALUES (NULL, 'jose', NULL, NULL, 'normal', '1');
INSERT INTO `usuario` (`id`, `usuario`, `pass`, `token`, `permisos`, `empresa`) VALUES (NULL, 'maria', NULL, NULL, 'normal', '1');
INSERT INTO `usuario` (`id`, `usuario`, `pass`, `token`, `permisos`, `empresa`) VALUES (NULL, 'admin', NULL, NULL, 'administrador', '1');


INSERT INTO `seccion` (`id`, `nombre`, `empresa`) VALUES (NULL, 'LUNES', '1');
INSERT INTO `seccion` (`id`, `nombre`, `empresa`) VALUES (NULL, 'MARTES', '1');
INSERT INTO `seccion` (`id`, `nombre`, `empresa`) VALUES (NULL, 'MIECOLES', '1');
INSERT INTO `seccion` (`id`, `nombre`, `empresa`) VALUES (NULL, 'JUEVES', '1');
INSERT INTO `seccion` (`id`, `nombre`, `empresa`) VALUES (NULL, 'VIERNES', '1');

INSERT INTO `proveedor` (`id`, `RUT`, `nombre`, `empresa`) VALUES
(1, '120198127401', 'GOLDFARB', 1),
(2, '129180945190', 'BAHCO', 1),
(3, '120190180170', 'INGCO', 1),
(4, '120123123123', 'S.LIVIO', 1),
(5, '123456789568', 'FIVISA', 1),
(6, '128463947319', 'PAMPIN', 1),
(7, '127346210394', 'EPICENTRO', 1),
(8, '218394500987', 'VITRO', 1),
(9, '198203450098', 'TRIMANT', 1),
(10, '123431234321', 'RECORD', 1),
(11, '128374758432', 'RAMASIL', 1),
(12, '823792838453', 'VISUAR', 1),
(13, '912830364857', 'EQUUS', 1),
(14, '123123123123', 'ICAREY', 1),
(15, '123456987654', 'MONTANS', 1),
(16, '123456543218', 'VONDER', 1),
(17, '123432234234', 'URUIMPORTA', 1),
(18, '453672845093', 'ELECTRALINE', 1),
(19, '436847501924', 'CONATEL', 1),
(20, '342312236543', 'WURTH', 1),
(21, '657483740987', 'NAIDICH', 1),
(22, '746593845932', 'DEL PARQUE', 1),
(23, '918283746301', 'LAGUZZI', 1),
(24, '746593840212', 'OROFINO', 1);

INSERT INTO `subseccion` (`id`, `nombre`, `empresa`) VALUES
(1, 'Carbones, interruptor y correas', 1),
(2, 'Panel INGCO manual', 1),
(3, 'INGCO 20V', 1),
(4, 'Trailers', 1),
(5, 'Panel BAHCO', 1),
(6, 'Jardineria', 1),
(7, 'Hidrolabadoras - Aspiradoras', 1),
(8, 'Pinturas', 1),
(9, 'Pinceles y rodillos', 1),
(10, 'Herramientas INGCO electricas', 1),
(11, 'Compresores - Soldadoras', 1),
(12, 'Saloncito', 1),
(13, 'Repuestos', 1),
(14, 'Cadenas', 1),
(15, 'Canillas y Sanitaria', 1),
(16, 'Panel Mechas', 1),
(17, 'Isla', 1),
(18, 'Panel TRAMONTINA', 1),
(19, 'Islita', 1),
(20, 'Llave Allen - Torx en L', 1),
(21, 'Panel Dados en Isla', 1),
(22, 'Herramientas neumaticas', 1);

INSERT INTO `articulo` (`id`, `detalle`, `codigo`, `marca`, `empresa`) VALUES
(1, 'CORREA TRAPEZOIDAL PARA HORMIGONERA A-30', '', '', 1),
(2, 'CORREA 12PJ-787 MULTI V PARA HORMIGONERA Y CORTACESPED', NULL, NULL, 1),
(3, 'CORREA 0-410E TRAPEIZODAL PARA LAVARROPAS CARGA SUPERIOR', NULL, NULL, 1),
(4, 'CORREA 5PJE-1172  MULTI V PARA LAVARROPAS', NULL, NULL, 1),
(5, 'CORREA 5X890 MULTI V REDONDA PARA SECARROPAS', NULL, NULL, 1),
(6, 'GRAMPADORA HSG1403', NULL, 'INGO', 1),
(7, 'ENGANCHE HEMBRA DE  BOCHA PARA TRAILER 1,7/8', NULL, NULL, 1),
(8, 'JUEGO DE LLAVE TORX/ALLEN 18PCS', NULL, 'INGCO', 1),
(9, 'DESTORNILLADOR PALETA BAHCO', NULL, 'BAHCO', 1),
(10, 'EJE SOMAR 5/8', 'ER87#', 'SOMAR', 1),
(11, 'PERNO P/TERCER PUNTO 12X70', '', '', 1);

INSERT INTO `seccion_subseccion` (`seccion`, `subseccion`) VALUES
(1, 1),
(1, 2),
(1, 3),
(1, 4),
(2, 5),
(2, 6),
(2, 7),
(2, 8),
(2, 9),
(2, 10),
(3, 11),
(3, 12),
(3, 13),
(3, 14),
(4, 15),
(4, 16),
(4, 17),
(4, 18),
(5, 19),
(5, 20),
(5, 21),
(5, 22);

INSERT INTO `proveedor_articulo` (`proveedor`, `articulo`, `codigo`) VALUES
(1, 5, '1111'),
(1, 6, 'HSG14018'),
(1, 8, 'HHKSET0181'),
(2, 1, 'A'),
(2, 9, '611-6'),
(2, 10, 'we'),
(3, 1, 'B'),
(3, 2, NULL),
(3, 3, NULL),
(3, 4, NULL),
(3, 10, 'a'),
(4, 2, ''),
(4, 7, NULL),
(14, 11, '');


























INSERT INTO `usuario_seccion` (`usuario`, `seccion`) VALUES ('2', '1');
INSERT INTO `usuario_seccion` (`usuario`, `seccion`) VALUES ('2', '2');
INSERT INTO `usuario_seccion` (`usuario`, `seccion`) VALUES ('3', '3');
INSERT INTO `usuario_seccion` (`usuario`, `seccion`) VALUES ('1', '4');
INSERT INTO `usuario_seccion` (`usuario`, `seccion`) VALUES ('1', '5');

INSERT INTO `subseccion` (`id`, `nombre`, `empresa`) VALUES (NULL, 'Carbones, interruptor y correas', '1');
INSERT INTO `subseccion` (`id`, `nombre`, `empresa`) VALUES (NULL, 'Panel INGCO manual', '1');
INSERT INTO `subseccion` (`id`, `nombre`, `empresa`) VALUES (NULL, 'INGCO 20V', '1');
INSERT INTO `subseccion` (`id`, `nombre`, `empresa`) VALUES (NULL, 'Trailers', '1');
INSERT INTO `subseccion` (`id`, `nombre`, `empresa`) VALUES (NULL, 'Panel BAHCO', '1');
INSERT INTO `subseccion` (`id`, `nombre`, `empresa`) VALUES (NULL, 'Jardineria', '1');
INSERT INTO `subseccion` (`id`, `nombre`, `empresa`) VALUES (NULL, 'Hidrolabadoras - Aspiradoras', '1');
INSERT INTO `subseccion` (`id`, `nombre`, `empresa`) VALUES (NULL, 'Pinturas', '1');
INSERT INTO `subseccion` (`id`, `nombre`, `empresa`) VALUES (NULL, 'Pinceles y rodillos', '1');
INSERT INTO `subseccion` (`id`, `nombre`, `empresa`) VALUES (NULL, 'Herramientas INGCO electricas', '1');
INSERT INTO `subseccion` (`id`, `nombre`, `empresa`) VALUES (NULL, 'Compresores - Soldadoras', '1');
INSERT INTO `subseccion` (`id`, `nombre`, `empresa`) VALUES (NULL, 'Saloncito', '1');
INSERT INTO `subseccion` (`id`, `nombre`, `empresa`) VALUES (NULL, 'Repuestos', '1');
INSERT INTO `subseccion` (`id`, `nombre`, `empresa`) VALUES (NULL, 'Cadenas', '1');
INSERT INTO `subseccion` (`id`, `nombre`, `empresa`) VALUES (NULL, 'Canillas y Sanitaria', '1');
INSERT INTO `subseccion` (`id`, `nombre`, `empresa`) VALUES (NULL, 'Panel Mechas', '1');
INSERT INTO `subseccion` (`id`, `nombre`, `empresa`) VALUES (NULL, 'Isla', '1');
INSERT INTO `subseccion` (`id`, `nombre`, `empresa`) VALUES (NULL, 'Panel TRAMONTINA', '1');
INSERT INTO `subseccion` (`id`, `nombre`, `empresa`) VALUES (NULL, 'Islita', '1');
INSERT INTO `subseccion` (`id`, `nombre`, `empresa`) VALUES (NULL, 'Llave Allen - Torx en L', '1');
INSERT INTO `subseccion` (`id`, `nombre`, `empresa`) VALUES (NULL, 'Panel Dados en Isla', '1');
INSERT INTO `subseccion` (`id`, `nombre`, `empresa`) VALUES (NULL, 'Herramientas neumaticas', '1');

INSERT INTO `seccion_subseccion` (`seccion`, `subseccion`) VALUES ('1', '1'), ('1', '2'), ('1', '3'), ('1', '4'), ('2', '5'), ('2', '6'), ('2', '7'), ('2', '8'), ('2', '9'), ('2', '10'), ('3', '11'), ('3', '12'), ('3', '13'), ('3', '14'), ('4', '15'), ('4', '16'), ('4', '17'), ('4', '18'), ('5', '19'), ('5', '20'), ('5', '21'), ('5', '22');