<?php

require_once '../src/utils/handle_date_time.php';
require_once '../src/connection/open_connection.php';

class users{
	
	public function updateUserPassword($userId, $newPassword){
		$dbClass = new DataBase();
		return $dbClass->sendQuery("UPDATE usuario SET pass = ? WHERE id = ?", array('si', $newPassword, $userId), "BOOLE");
	}
	
	public function getUser($usuario){
		$dbClass = new DataBase();
		$responseQuery = $dbClass->sendQuery("SELECT * FROM usuario WHERE usuario = ?", array('s', $usuario), "OBJECT");
		if($responseQuery->result == 1)
			$responseQuery->message = "El Usuario ingresado no corresponde a una cuenta registrada en el sistema";
		return $responseQuery;
	}	

	public function setNewTokenAndSession($userId){
		$dbClass = new DataBase();
		$usersClass = new users();
		$newToken = bin2hex(random_bytes(32)); // 64 characters is sufficient for security
		$responseQuery = $dbClass->sendQuery('UPDATE usuario SET token = ? WHERE id = ?', array('si', $newToken, $userId), "BOOLE");
		if($responseQuery->result == 2){
			$responseQuery = null;
			$responseQuery = $usersClass->getUserById($userId);
			$objectSession = new \stdClass();
			$objectSession->id = $responseQuery->objectResult->id;
			$objectSession->usuario = $responseQuery->objectResult->usuario;
			$objectSession->token = $responseQuery->objectResult->token;
			$objectSession->empresa = $responseQuery->objectResult->empresa;
			$objectSession->permisos = $responseQuery->objectResult->permisos;
			$_SESSION['systemSession'] = $objectSession;
			unset($responseQuery->objectResult);
		}else $responseQuery->message = "Un error interno no permitio iniciar sesión con este usuario.";
		return $responseQuery;
	}
	
	public function getUserById($userId){
		$dbClass = new DataBase();
		$responseQuery = $dbClass->sendQuery("SELECT * FROM usuario WHERE id = ?", array('i', $userId), "OBJECT");
		if($responseQuery->result == 1)
			$responseQuery->message = "El identificador ingresado no corresponde a un usuario registrado.";
		return $responseQuery;
	}

	public function getItem($itemId){
		$dbClass = new DataBase();
		$responseQuery = $dbClass->sendQuery("SELECT * FROM item WHERE id = ?", array('i', $itemId), "OBJECT");
		if($responseQuery->result == 1)
			$responseQuery->message = "El identificador ingresado no corresponde a un item registrado.";
		return $responseQuery;
	}

	public function getCountArticlesInCart($userId){
		$dbClass = new DataBase();
		$responseQuery = $dbClass->sendQuery("SELECT SUM(ca.cantidad) as total_items
												FROM carrito_articulo ca
												JOIN carrito c ON ca.carrito = c.id
												WHERE c.usuario = ?", array('i', $userId), "OBJECT");
		if($responseQuery->result == 1)
			$responseQuery->message = "El identificador ingresado no corresponde a un usuario registrado.";
		return $responseQuery;
	}

	public function getCartOfUser($userId){
		$dbClass = new DataBase();
		$responseQuery = $dbClass->sendQuery("SELECT * FROM carrito WHERE usuario = ?", array('i', $userId), "OBJECT");
		if($responseQuery->result == 1)
			$responseQuery->message = "Carrito inexistente.";
		return $responseQuery;
	}

	public function addArticleToCart($articleId, $cartId){
		$dbClass = new DataBase();
		$responseQuery = $dbClass->sendQuery("INSERT INTO carrito_articulo (carrito, articulo, cantidad)
												VALUES (?, ?, 1)
												ON DUPLICATE KEY UPDATE cantidad = cantidad + 1;", array('ii', $cartId, $articleId), "BOOLE");
		if($responseQuery->result == 2){
			
		}else $responseQuery->message = "Un error interno no permitio agregar el articulo al carrito.";
		return $responseQuery;
	}

	public function reduceArticleToCart($articleId, $cartId){
		$dbClass = new DataBase();
		$response = new \stdClass();

		// First check the current quantity
		$checkQuery = $dbClass->sendQuery("SELECT cantidad FROM carrito_articulo 
											WHERE carrito = ? AND articulo = ?", 
											array('ii', $cartId, $articleId), "OBJECT");
		if($checkQuery->result != 2){
			$response->result = 2;
			return $response;
		}

		$currentQuantity = $checkQuery->objectResult->cantidad;

		if ($currentQuantity <= 1) {
			// If quantity is 1 or less, remove the item completely
			$responseQuery = $dbClass->sendQuery("DELETE FROM carrito_articulo 
													WHERE carrito = ? AND articulo = ?", 
													array('ii', $cartId, $articleId), "BOOLE");
			if($responseQuery->result == 2){
				$response->result = 2;
				return $response;
			} else return $responseQuery;
		} else {
			// If quantity is greater than 1, reduce by 1
			$responseQuery = $dbClass->sendQuery("UPDATE carrito_articulo 
													SET cantidad = cantidad - 1 
													WHERE carrito = ? AND articulo = ?", 
													array('ii', $cartId, $articleId), "BOOLE");
			if($responseQuery->result == 2){
				$response->result = 2;
				return $response;
			} else return $responseQuery;
		}
	}

	public function getProvider($idProvider){
		$dbClass = new DataBase();
		$responseQuery = $dbClass->sendQuery("SELECT * FROM proveedor WHERE id = ?", array('i', $idProvider), "OBJECT");
		if($responseQuery->result == 1)
			$responseQuery->message = "El identificador ingresado no corresponde a un proveedor registrado.";
		return $responseQuery;
	}

	public function getArticle($id){
		$dbClass = new DataBase();
		$articulo = new \stdClass();
		$response = new \stdClass();
		$query = "SELECT 
				a.id AS articulo_id,
				a.detalle,
				a.codigo AS articulo_codigo,
				a.marca,
				p.id AS proveedor_id,
				p.RUT,
				p.nombre AS proveedor_nombre,
				pa.codigo AS codigo_proveedor
			FROM 
				articulo a
			JOIN 
				proveedor_articulo pa ON a.id = pa.articulo
			JOIN 
				proveedor p ON pa.proveedor = p.id
			WHERE 
				a.id = ?";

		$responseQuery = $dbClass->sendQuery($query, array('i', $id), "LIST");
		if($responseQuery->result == 2){
			$articulo->id = $responseQuery->listResult[0]['articulo_id'];
			$articulo->detalle = $responseQuery->listResult[0]['detalle'];
			$articulo->codigo = $responseQuery->listResult[0]['articulo_codigo'];
			$articulo->marca = $responseQuery->listResult[0]['marca'];
			$proveedores = array();
			foreach ($responseQuery->listResult as $key => $row) {
				if($row['proveedor_id'] != null)
					$proveedores[] = ['id' => $row['proveedor_id'], 'rut' => $row['RUT'], 'nombre' => $row['proveedor_nombre'], 'codigo' => $row['codigo_proveedor']];
			}
			$response->articulo = $articulo;
			$response->proveedores = $proveedores;
			$response->result = 2;
		} else return $responseQuery;
		return $response;
	}

	public function editProvider($nombre, $rut, $idProvider){
		$dbClass = new DataBase();	
		return $dbClass->sendQuery("UPDATE proveedor SET nombre = ?, rut = ? WHERE id = ?", array('ssi', $nombre, $rut, $idProvider), "BOOLE");
	}

	public function getUserData($userId){ // A partir de un userID traigo toda su info y secciones
		$dbClass = new DataBase();
		$usuario = new \stdClass();
		// Consulta para obtener al usuario y sus secciones en una sola operación
		$query = "SELECT u.*, s.id as seccion_id, s.nombre as seccion_nombre
			FROM usuario u
			LEFT JOIN usuario_seccion us ON u.id = us.usuario
			LEFT JOIN seccion s ON us.seccion = s.id
			WHERE u.id = ?";
		$responseQuery = $dbClass->sendQuery($query, array('i', $userId), "LIST");
		if($responseQuery->result == 2){
			$usuario->id = $responseQuery->listResult[0]['id'];
			$usuario->usuario = $responseQuery->listResult[0]['usuario'];
			$usuario->pass = $responseQuery->listResult[0]['pass'];
			$usuario->token = $responseQuery->listResult[0]['token'];
			$usuario->permisos = $responseQuery->listResult[0]['permisos'];
			$usuario->empresa = $responseQuery->listResult[0]['empresa'];
			$secciones = array();
			// var_dump($responseQuery->listResult);
			foreach ($responseQuery->listResult as $key => $row) {
				if($row['seccion_id'] != null)
					$secciones[] = ['id' => $row['seccion_id'], 'seccion' => $row['seccion_nombre']];
			}
			$response->usuario = $usuario;
			$response->secciones = $secciones;
			$response->result = 2;
		} else return $responseQuery;
		return $response;
	}

	public function cleanPassword($userId){
		$dbClass = new DataBase();
		$responseQuery = $dbClass->sendQuery("UPDATE usuario SET pass = null WHERE id = ?", array('i', $userId), "BOOLE");
		return $responseQuery;
	}

	public function removeRelationSectionSubSection($idSection, $idSubSection){
		$dbClass = new DataBase();
		$responseQuery = $dbClass->sendQuery("DELETE FROM seccion_subseccion WHERE seccion = ? AND subseccion = ?", array('ii', $idSection, $idSubSection), "BOOLE");
		return $responseQuery;
	}

	public function removeRelationUserSection($userId, $sectionId){
		$dbClass = new DataBase();
		$responseQuery = $dbClass->sendQuery("DELETE FROM usuario_seccion WHERE usuario_seccion.usuario = ? AND usuario_seccion.seccion = ?", array('ii', $userId, $sectionId), "BOOLE");
		return $responseQuery;
	}

	public function insertNewCart($userId){
		$dbClass = new DataBase();
		$today = date('Ymdhis');
		$responseQuery = $dbClass->sendQuery("INSERT INTO carrito (fecha, usuario) VALUES (?,?)", array('si', $today, $userId), "BOOLE");
		return $responseQuery;
	}

	public function addRelationUserSection($userId, $sectionId){
		$dbClass = new DataBase();
		$responseQuery = $dbClass->sendQuery("INSERT INTO usuario_seccion (usuario, seccion) VALUES (?,?)", array('ii', $userId, $sectionId), "BOOLE");
		return $responseQuery;
	}

	public function addRelationSectionSubSection($sectionId, $subSectionId){
		$dbClass = new DataBase();
		$responseQuery = $dbClass->sendQuery("INSERT INTO seccion_subseccion (seccion, subseccion) VALUES (?,?)", array('ii', $sectionId, $subSectionId), "BOOLE");
		return $responseQuery;
	}
	
	public function newSection($nombre, $empresa){
		$dbClass = new DataBase();
		$responseQuery = $dbClass->sendQuery("INSERT INTO seccion (nombre, empresa) VALUES (?,?)", array('si', $nombre, $empresa), "BOOLE");
		return $responseQuery;
	}

	public function newSubSection($nombre, $empresa){
		$dbClass = new DataBase();
		$responseQuery = $dbClass->sendQuery("INSERT INTO subseccion (nombre, empresa) VALUES (?,?)", array('si', $nombre, $empresa), "BOOLE");
		return $responseQuery;
	}

	public function newProvider($nombre, $rut, $empresa){
		$dbClass = new DataBase();
		$responseQuery = $dbClass->sendQuery("INSERT INTO proveedor (nombre, RUT, empresa) VALUES (?,?,?)", array('ssi', $nombre, $rut, $empresa), "BOOLE");
		return $responseQuery;
	}

	public function newArticle($detalle, $codigo, $marca, $empresa){
		$dbClass = new DataBase();
		$responseQuery = $dbClass->sendQuery("INSERT INTO articulo (detalle, codigo, marca, empresa) VALUES (?,?,?,?)", array('sssi', $detalle, $codigo, $marca, $empresa), "BOOLE");
		return $responseQuery;
	}

	public function editArticle($detalle, $codigo, $marca, $id){
		$dbClass = new DataBase();
		return $dbClass->sendQuery("UPDATE articulo SET detalle = ?, codigo = ?, marca = ? WHERE id = ?", array('sssi', $detalle, $codigo, $marca, $id), "BOOLE");
	}
	
	// ----------------------------------------------------------------------------------------------------
	public function newItem($articulo, $cantidad, $subseccion, $empresa){
		$dbClass = new DataBase();
		$posicion = 0;
		$getLastItem = $this->getLastItem($subseccion, $empresa);
		if($getLastItem->result == 2){
			$posicion = $getLastItem->item->posicion + 1;
			$responseQuery = $dbClass->sendQuery("INSERT INTO item (cantidad, posicion, articulo, empresa) VALUES (?,?,?,?)", array('iiii', $cantidad, $posicion, $articulo, $empresa), "BOOLE");
			if($responseQuery->result == 2){
				$responseQuery = $dbClass->sendQuery("INSERT INTO item_subseccion (item, subseccion) VALUES (?,?)", array('ii', $responseQuery->id, $subseccion), "BOOLE");
			} else return $responseQuery;
		} else {
			$response = new \stdClass();
			$response->result = 0;
			$response->message= "No se pudo obtener el ultimo Item de la seccion";
			return $response;
		}
		return $responseQuery;
	}
	
	public function getLastItem($subseccion, $empresa){ // Devuelve el ultimo item de la subseccion (por posicion no por id | si no hay item devuelve un fake item con todo en 0)
		$dbClass = new DataBase();
		$query = "SELECT i.*
				FROM item i
				JOIN item_subseccion i_s ON i.id = i_s.item
				WHERE i_s.subseccion = ?
				ORDER BY i.posicion DESC
				LIMIT 1";
		$responseQuery = $dbClass->sendQuery($query, array('i', $subseccion), "OBJECT");
		if($responseQuery->result == 2){
			$responseQuery->item = $responseQuery->objectResult;
		} else if($responseQuery->result == 1){
			$item = new \stdClass();
			$item->id = 0;
			$item->cantidad = 0;
			$item->posicion = 0;
			$item->articulo = 0;
			$item->empresa = $empresa;
			$responseQuery->result = 2;
			$responseQuery->item = $item;
		}
		return $responseQuery;
	}
	// ----------------------------------------------------------------------------------------------------

	public function newProveedorArticle($proveedor, $articulo, $codigo){
		$dbClass = new DataBase();
		$responseQuery = $dbClass->sendQuery("INSERT INTO proveedor_articulo (proveedor, articulo, codigo) VALUES (?,?,?)", array('iis', $proveedor, $articulo, $codigo), "BOOLE");
		return $responseQuery;
	}

	public function addProvidersToArticle($articleId, $newProviders){
		$dbClass = new DataBase();
		foreach ($newProviders as $provider) {
			$responseQuery = $dbClass->sendQuery("INSERT INTO proveedor_articulo (proveedor, articulo, codigo) VALUES (?,?,?)", array('iis', $provider->id, $articleId, $provider->codigo), "BOOLE");
			if($responseQuery->result != 2)
				return false;
		}
		return true;
	}

	public function removeProvidersToArticle($articleId, $removedProviders){
		$dbClass = new DataBase(); //" DELETE FROM `proveedor_articulo` WHERE `proveedor_articulo`.`proveedor` = 1 AND `proveedor_articulo`.`articulo` = 5"?"
		foreach ($removedProviders as $provider) {
			$responseQuery = $dbClass->sendQuery("DELETE FROM proveedor_articulo WHERE proveedor_articulo.proveedor = ? AND proveedor_articulo.articulo = ? ", array('ii', $provider->id, $articleId), "BOOLE");
			if($responseQuery->result != 2)
				return false;
		}
		return true;
	}

	public function updateProviderToArticle($articleId, $updatedProviders){
		$dbClass = new DataBase(); //" DELETE FROM `proveedor_articulo` WHERE `proveedor_articulo`.`proveedor` = 1 AND `proveedor_articulo`.`articulo` = 5"?"
		foreach ($updatedProviders as $provider) { // UPDATE usuario SET pass = null WHERE id = ?
			$responseQuery = $dbClass->sendQuery("UPDATE proveedor_articulo SET codigo = ? WHERE proveedor_articulo.proveedor = ? AND proveedor_articulo.articulo = ? ", array('sii', $provider->codigo, $provider->id, $articleId), "BOOLE");
			if($responseQuery->result != 2)
				return false;
		}
		return true;
	}

	public function changeStatusSubsection($seccion, $subseccion, $subsectionStatus){
		$dbClass = new DataBase(); //" DELETE FROM `proveedor_articulo` WHERE `proveedor_articulo`.`proveedor` = 1 AND `proveedor_articulo`.`articulo` = 5"?"
		return $dbClass->sendQuery("UPDATE seccion_subseccion SET estado = ? WHERE seccion = ? AND subseccion = ? ", array('sii', $subsectionStatus, $seccion, $subseccion), "BOOLE");
	}

	public function changeStatusSection($user, $section, $status){
		$dbClass = new DataBase(); //" DELETE FROM `proveedor_articulo` WHERE `proveedor_articulo`.`proveedor` = 1 AND `proveedor_articulo`.`articulo` = 5"?"
		return $dbClass->sendQuery("UPDATE usuario_seccion SET estado = ? WHERE usuario = ? AND seccion = ? ", array('sii', $status, $user, $section), "BOOLE");
	}

	public function getAllNormalUsers($empresa){
		$dbClass = new DataBase(); 
		$responseQuery = $dbClass->sendQuery("SELECT * FROM usuario WHERE empresa = ? AND permisos = ?", array('is', $empresa, "normal"), "LIST");
		if($responseQuery->result == 2){
			$usuarios = array();
			// var_dump($responseQuery->listResult);
			foreach ($responseQuery->listResult as $key => $row) {
				$usuarios[] = ['id' => $row['id'], 'usuario' => $row['usuario']];
			}
			$response->usuarios = $usuarios;
			$response->result = 2;
		} else return $responseQuery;
		return $response;
	}

	public function getArticlesInCart($userId){
		$dbClass = new DataBase();
		$responseQuery = $dbClass->sendQuery("SELECT ca.articulo as id, a.detalle as articulo, ca.cantidad
												FROM carrito_articulo ca
												JOIN carrito c ON ca.carrito = c.id
												JOIN articulo a ON ca.articulo = a.id 
												WHERE c.usuario = ?", array('i', $userId), "LIST");
		if($responseQuery->result == 2){
			$articulos = array();
			// var_dump($responseQuery->listResult);
			foreach ($responseQuery->listResult as $key => $row) {
				$articulos[] = ['id' => $row['id'], 'articulo' => $row['articulo'], 'cantidad' => $row['cantidad']];
			}
			$response->articulos = $articulos;
			$response->result = 2;
		} else return $responseQuery;
		return $response;
	}

	public function getArticlesInCartWithCode($userId){
		$dbClass = new DataBase();
		$responseQuery = $dbClass->sendQuery("SELECT ca.articulo as id, a.detalle as articulo, ca.cantidad as cantidad, pa.proveedor as proveedor, pa.codigo as codigo, p.nombre as proveedor_nombre
												FROM carrito_articulo ca
												JOIN carrito c ON ca.carrito = c.id
												JOIN articulo a ON ca.articulo = a.id 
												JOIN proveedor_articulo pa ON a.id = pa.articulo
                                                JOIN proveedor p ON pa.proveedor = p.id
												WHERE c.usuario = ?", array('i', $userId), "LIST");

		if($responseQuery->result == 2){
			$proveedores = array();
			
			foreach ($responseQuery->listResult as $row) {
				$proveedorId = $row['proveedor'];
				
				// Initialize the provider array if it doesn't exist yet
				if (!isset($proveedores[$proveedorId])) {
					$proveedores[$proveedorId] = array(
						'proveedor_id' => $proveedorId,
						'proveedor_nombre' => $row['proveedor_nombre'],
						'articulos' => array()
					);
				}
				
				// Add the article to this provider's articles array
				$proveedores[$proveedorId]['articulos'][] = [
					'id' => $row['id'], 
					'articulo' => $row['articulo'], 
					'cantidad' => $row['cantidad'],
					'codigo' => $row['codigo']
				];
			}
			
			$response = new \stdClass();
			// Convert associative array to indexed array if needed
			$response->proveedores = $proveedores;
			$response->result = 2;
			// var_dump($proveedores);
		} else {
			return $responseQuery;
		}
		/////////////////////////////////////////////////////////////////////////////////////////////
		// if($responseQuery->result == 2){
		// 	$articulos = array();
		// 	// var_dump($responseQuery->listResult);
		// 	foreach ($responseQuery->listResult as $key => $row) {
		// 		$articulos[] = ['id' => $row['id'], 'articulo' => $row['articulo'], 'cantidad' => $row['cantidad']];
		// 	}
		// 	$response->articulos = $articulos;
		// 	$response->result = 2;
		// } else return $responseQuery;
		return $response;
	}

	public function getAllSubSectionsOfSection($idSeccion, $empresa){
		$dbClass = new DataBase();
		$query = "SELECT sub.id, sub.nombre, sec.id as seccion_id, sec.nombre as seccion, ss.estado, ss.fecha
			FROM subseccion sub
			JOIN seccion_subseccion ss ON sub.id = ss.subseccion
			JOIN seccion sec ON ss.seccion = sec.id
			WHERE sec.id = ?
			AND sec.empresa = ?";
		$responseQuery = $dbClass->sendQuery($query, array('ii', $idSeccion, $empresa), "LIST");
		if($responseQuery->result == 2){
			$seccion = new \stdClass();
			$seccion->id = $responseQuery->listResult[0]['seccion_id'];
			$seccion->nombre = $responseQuery->listResult[0]['seccion'];
			$subsecciones = array();
			foreach ($responseQuery->listResult as $key => $row) {
				$subsecciones[] = ['id' => $row['id'], 'subseccion' => $row['nombre'], 'estado' => $row['estado'], 'fecha' => $row['fecha']];
			}
			$response->subsecciones = $subsecciones;
			$response->seccion = $seccion;
			$response->result = 2;
		} else return $responseQuery;
		return $response;
	}

	public function getUserSections($sectionId, $userId){
		$dbClass = new DataBase();
		$query = "SELECT estado, fecha
			FROM usuario_seccion
			WHERE usuario = ?
			AND seccion = ?";
		$responseQuery = $dbClass->sendQuery($query, array('ii', $userId, $sectionId), "OBJECT");
		if($responseQuery->result == 2){
			$response->seccion = $responseQuery->objectResult;
			$response->result = 2;
		} else return $responseQuery;
		return $response;
	}	

	public function getAllItemsOfSubsection($idSubSeccion, $empresa){
		$dbClass = new DataBase();
		$query = "SELECT i.id, i.cantidad, i.posicion, i.articulo as articulo_id, a.detalle as articulo_detalle, s.nombre as subseccion
			FROM item i 
			JOIN item_subseccion i_s ON i.id = i_s.item 
			JOIN subseccion s ON i_s.subseccion = s.id
			JOIN articulo a ON i.articulo = a.id
			WHERE s.id = ? 
			AND s.empresa = ?";
		$responseQuery = $dbClass->sendQuery($query, array('ii', $idSubSeccion, $empresa), "LIST");
		if($responseQuery->result == 2){
			$items = array();
			foreach ($responseQuery->listResult as $key => $row) {
				$items[] = ['id' => $row['id'], 'posicion' => $row['posicion'], 'cantidad' => $row['cantidad'], 'articulo' => $row['articulo_detalle'], 'articulo_id' => $row['articulo_id']];
			}
			$response->items = $items;
			// $response->subsection = $responseQuery->listResult[0]['subseccion'];
			$response->result = 2;
		} else return $responseQuery;
		return $response;
	}	

	public function getAllSections($empresa){ // Devuelve todas las secciones con sus respectivos usuarios
		$dbClass = new DataBase();
		$query = "SELECT s.*, u.id as usuario_id, u.usuario as usuario_nombre
			FROM seccion s
			LEFT JOIN usuario_seccion us ON s.id = us.seccion
			LEFT JOIN usuario u ON us.usuario = u.id
			WHERE s.empresa = ?";
		$responseQuery = $dbClass->sendQuery($query, array('i', $empresa), "LIST");
		if($responseQuery->result == 2){
			$secciones = array();
			$seccionesIndex = array(); // Para rastrear qué secciones ya hemos procesado
			
			foreach ($responseQuery->listResult as $row) {
				$seccionId = $row['id'];
				
				// Si esta sección no está en nuestro arreglo todavía, agrégala
				if (!isset($seccionesIndex[$seccionId])) {
					$seccionesIndex[$seccionId] = count($secciones);
					$secciones[] = [
						'id' => $seccionId, 
						'seccion' => $row['nombre'],
						'usuarios' => []
					];
				}
				
				// Si hay un usuario asociado (no es NULL), agrégalo al arreglo de usuarios
				if ($row['usuario_id'] !== null) {
					$secciones[$seccionesIndex[$seccionId]]['usuarios'][] = [
						'id' => $row['usuario_id'],
						'nombre' => $row['usuario_nombre']
					];
				}
			}
			
			$response->secciones = $secciones;
			$response->result = 2;
		} else {
			return $responseQuery;
		}
		return $response;
	}

	public function getAllSectionsOfUser($userId){ // Devuelve todas las secciones con sus respectivos usuarios
		$dbClass = new DataBase();
		$query = "SELECT s.id, s.nombre, s.empresa, us.estado, us.fecha
			FROM seccion s
			JOIN usuario_seccion us ON s.id = us.seccion
			JOIN usuario u ON u.id = us.usuario
			WHERE u.id = ?";
		$responseQuery = $dbClass->sendQuery($query, array('i', $userId), "LIST");
		if($responseQuery->result == 2){
			$secciones = array();
			foreach ($responseQuery->listResult as $row) {
				$secciones[] = [
					'id' => $row['id'], 
					'seccion' => $row['nombre'],
					'estado' => $row['estado'],
					'fecha' => $row['fecha']
				];
			}
			$response->secciones = $secciones;
			$response->result = 2;
		} else {
			return $responseQuery;
		}
		return $response;
	}

	public function getAllArticlesSimple($empresa){ // Devuelve todas los articulos y nada mas
		$dbClass = new DataBase();
		$responseQuery = $dbClass->sendQuery("SELECT * FROM articulo WHERE empresa = ?", array('i', $empresa), "LIST");
		if($responseQuery->result == 2){
			$articulos = array();
			// var_dump($responseQuery->listResult);
			foreach ($responseQuery->listResult as $key => $row) {
				$articulos[] = ['id' => $row['id'], 'detalle' => $row['detalle'], 'codigo' => $row['codigo'], 'marca' => $row['marca']];
			}
			$response->articulos = $articulos;
			$response->result = 2;
		} else return $responseQuery;
		return $response;
	}
	public function getAllArticles($empresa, $limit, $from){ // Devuelve todas los articulos con sus respectivos proveedores (Puede tener LIMIT y DESDE qué ID)
		$dbClass = new DataBase();
		$limit = $limit != 0 ? "LIMIT $limit" : "";
		$query = "SELECT a.*, p.id as proveedor_id, p.nombre as proveedor_nombre, pa.codigo as proveedor_codigo
			FROM articulo a
			LEFT JOIN proveedor_articulo pa ON a.id = pa.articulo
			LEFT JOIN proveedor p ON pa.proveedor = p.id
			WHERE a.empresa = ? AND a.id > $from $limit";
		$responseQuery = $dbClass->sendQuery($query, array('i', $empresa), "LIST");
		if($responseQuery->result == 2){
			$articulos = array();
			$articulosIndex = array(); // Para rastrear qué articulos ya hemos procesado
			
			foreach ($responseQuery->listResult as $row) {
				$articuloId = $row['id'];
				
				// Si este articulo no está en nuestro arreglo todavía, agrégala
				if (!isset($articulosIndex[$articuloId])) {
					$articulosIndex[$articuloId] = count($articulos);
					$articulos[] = [
						'id' => $articuloId, 
						'detalle' => $row['detalle'],
						'codigo' => $row['codigo'],
						'marca' => $row['marca'],
						'proveedores' => []
					];
				}
				
				// Si hay un proveedor asociado (no es NULL), agrégalo al arreglo de proveedores
				if ($row['proveedor_id'] !== null) {
					$articulos[$articulosIndex[$articuloId]]['proveedores'][] = [
						'id' => $row['proveedor_id'],
						'nombre' => $row['proveedor_nombre'],
						'codigo' => $row['proveedor_codigo']
					];
				}
			}
			
			$response->articulos = $articulos;
			$response->result = 2;
		} else {
			return $responseQuery;
		}
		return $response;
	}

	public function getAllSubSections($empresa){
		$dbClass = new DataBase();
		$responseQuery = $dbClass->sendQuery("SELECT * FROM subseccion WHERE empresa = ?", array('i', $empresa), "LIST");
		if($responseQuery->result == 2){
			$subsecciones = array();
			foreach ($responseQuery->listResult as $key => $row) {
				$subsecciones[] = ['id' => $row['id'], 'subseccion' => $row['nombre']];
			}
			$response->subsecciones = $subsecciones;
			$response->result = 2;
		} else return $responseQuery;
		return $response;
	}	

	public function getAllProviders($empresa){
		$dbClass = new DataBase();
		$responseQuery = $dbClass->sendQuery("SELECT * FROM proveedor WHERE empresa = ?", array('i', $empresa), "LIST");
		if($responseQuery->result == 2){
			$proveedores = array();
			foreach ($responseQuery->listResult as $key => $row) {
				$proveedores[] = ['id' => $row['id'], 'proveedor' => $row['nombre'], 'rut' => $row['RUT']];
			}
			$response->proveedores = $proveedores;
			$response->result = 2;
		} else return $responseQuery;
		return $response;
	}	
}