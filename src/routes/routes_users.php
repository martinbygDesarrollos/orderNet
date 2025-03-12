<?php

use Slim\App;
use Slim\Http\Request;
use Slim\Http\Response;

require_once '../src/controllers/ctr_users.php';

return function (App $app){
	$container = $app->getContainer();
	$userController = new ctr_users();

	// GET PETITIONS ---------------------------
	$app->get('/iniciar-sesion', function ($request, $response, $args) use ($container, $userController) {
		$responseCurrentSession = $userController->validateCurrentSession();
		if($responseCurrentSession->result != 2){
			$args['versionerp'] = '?'.FECHA_ULTIMO_PUSH;
			return $this->view->render($response, "signIn.twig", $args);
		} else {
			return $response->withRedirect($request->getUri()->getBaseUrl());
		}
	})->setName("SignIn");
	
	$app->get('/cerrar-session', function ($request, $response, $args) use ($container, $userController) {
		$responseCurrentSession = $userController->validateCurrentSession();
		if($responseCurrentSession->result == 2)
		session_destroy();
		return $response->withRedirect($request->getUri()->getBaseUrl());
	})->setName("SignOut");

	$app->get('/home', function ($request, $response, $args) use ($container, $userController) {
		$responseCurrentSession = $userController->validateCurrentSession();
		if($responseCurrentSession->result == 2 && $responseCurrentSession->currentSession->permisos == "administrador"){
			$args['systemSession'] = $responseCurrentSession->currentSession;
			$args['versionerp'] = '?'.FECHA_ULTIMO_PUSH;
			return $this->view->render($response, "home.twig", $args);
		}else return $response->withRedirect($request->getUri()->getBaseUrl());
	})->setName("Home");

	$app->get('/inicio', function ($request, $response, $args) use ($container, $userController) {
		$responseCurrentSession = $userController->validateCurrentSession();
		if($responseCurrentSession->result == 2 && $responseCurrentSession->currentSession->permisos == "normal"){
			$args['systemSession'] = $responseCurrentSession->currentSession;
			$args['versionerp'] = '?'.FECHA_ULTIMO_PUSH;
			$args['secciones'] = $userController->getAllSectionsOfUser($responseCurrentSession->currentSession->id)->secciones ?? array();
			$args['carrito_count'] = $userController->getCountArticlesInCart($responseCurrentSession->currentSession->id)->cantidad ?? 0;
			return $this->view->render($response, "inicio.twig", $args);
		}else return $response->withRedirect($request->getUri()->getBaseUrl());
	})->setName("homeNormalUser");
	
	$app->get('/usuarios', function ($request, $response, $args) use ($container, $userController) {
		$responseCurrentSession = $userController->validateCurrentSession();
		if($responseCurrentSession->result == 2 && $responseCurrentSession->currentSession->permisos == "administrador"){
			$args['systemSession'] = $responseCurrentSession->currentSession;
			$args['versionerp'] = '?'.FECHA_ULTIMO_PUSH;
			$args['usuarios'] = $userController->getAllNormalUsers($responseCurrentSession->currentSession->empresa)->usuarios ?? array();
			return $this->view->render($response, "users.twig", $args);
		} else {
			return $response->withRedirect($request->getUri()->getBaseUrl());
		}
	})->setName("Users");
	
	$app->get('/{id}/subsecciones', function ($request, $response, $args) use ($container, $userController) {
		$responseCurrentSession = $userController->validateCurrentSession();
		if($responseCurrentSession->result == 2 && $responseCurrentSession->currentSession->permisos == "normal"){
			$args['systemSession'] = $responseCurrentSession->currentSession;
			$args['versionerp'] = '?'.FECHA_ULTIMO_PUSH;
			$args['carrito_count'] = $userController->getCountArticlesInCart($responseCurrentSession->currentSession->id)->cantidad ?? 0;
			$idSeccion = $args['id'];
			$responseCall = $userController->getAllSubSectionsOfSection($idSeccion, $responseCurrentSession->currentSession->empresa);
			$args['subsecciones'] = $responseCall->subsecciones ?? array();
			$args['seccion'] = $responseCall->seccion ?? $userController->getSectionData($idSeccion)->seccion;
			return $this->view->render($response, "subSections-user.twig", $args);
		} else {
			return $response->withRedirect($request->getUri()->getBaseUrl());
		}
	})->setName("SubSections-User");
	
	$app->get('/{seccion}/{subseccion}/items', function ($request, $response, $args) use ($container, $userController) {
		$responseCurrentSession = $userController->validateCurrentSession();
		if($responseCurrentSession->result == 2 && $responseCurrentSession->currentSession->permisos == "normal"){
			$args['systemSession'] = $responseCurrentSession->currentSession;
			$args['versionerp'] = '?'.FECHA_ULTIMO_PUSH;
			
			$idSeccion = $args['seccion'];
			$idSubSeccion = $args['subseccion'];

			$args['carrito_count'] = $userController->getCountArticlesInCart($responseCurrentSession->currentSession->id)->cantidad ?? 0;
			// var_dump($userController->getCountArticlesInCart($responseCurrentSession->currentSession->id)->cantidad); exit;

			$responseSubeSections = $userController->getAllSubSectionsOfSection($idSeccion, $responseCurrentSession->currentSession->empresa);
			$existSubsection = false;
			foreach ($responseSubeSections->subsecciones as $subseccion) {
				if($subseccion['id'] == $idSubSeccion){
					$args['subseccionNombre'] = $subseccion['subseccion'];
					$existSubsection = true;
				}
			}
			if($existSubsection){ // Si la seccion en la ruta contiene la subseccion en la ruta OK
				$articles = $userController->getArticlesInCart($responseCurrentSession->currentSession->id)->articulos;
				$args['items'] = $userController->getAllItemsOfSubsection($idSubSeccion, $responseCurrentSession->currentSession->empresa)->items ?? array();
				foreach ($articles as $artInCart) {
					foreach ($args['items'] as &$item) {  // Note the & to modify by reference
						if ($item['articulo'] === $artInCart['articulo']) {
							// When matching articles are found, add the 'cantidad' attribute
							$item['cantidad_inCart'] = $artInCart['cantidad'];
							break;  // Exit inner loop once match is found
						}
					}
					unset($item); // Limpiamos la referencia
				}

				// foreach ($articles as $artInCart) {
				// 	echo $artInCart['articulo'] . "<br>";
				// }
				// echo "<br>";
				// foreach ($args['items'] as $item) {
				// 	echo $item['articulo'] . $item['cantidad_inCart'] . "<br>";
				// }
				// // Assuming $articles and $args['items'] are your two arrays
				// echo "<br>";
				// var_dump($args['items']);
				// foreach ($args['items'] as $item) {
				// 	echo $item['articulo'] . " " . $item['cantidad_inCart'] . "<br>";
				// }
				// var_dump($articles);
				// var_dump($args['items']);
			} else { // La subseccion de la ruta no corresponde a esa seccion ERROR
				return $response->withRedirect($request->getUri()->getBaseUrl());
			}
			return $this->view->render($response, "items-user.twig", $args);
		} else {
			return $response->withRedirect($request->getUri()->getBaseUrl());
		}
	})->setName("SubSections-User");

	$app->get('/secciones', function ($request, $response, $args) use ($container, $userController) {
		$responseCurrentSession = $userController->validateCurrentSession();
		if($responseCurrentSession->result == 2 && $responseCurrentSession->currentSession->permisos == "administrador"){
			$args['systemSession'] = $responseCurrentSession->currentSession;
			$args['versionerp'] = '?'.FECHA_ULTIMO_PUSH;
			$args['secciones'] = $userController->getAllSections($responseCurrentSession->currentSession->empresa)->secciones ?? array();
			return $this->view->render($response, "sections.twig", $args);
		} else {
			return $response->withRedirect($request->getUri()->getBaseUrl());
		}
	})->setName("Sections");

	$app->get('/secciones/{id}', function ($request, $response, $args) use ($container, $userController) { // HACER HACER HACER
		$responseCurrentSession = $userController->validateCurrentSession();
		if($responseCurrentSession->result == 2 && $responseCurrentSession->currentSession->permisos == "administrador"){
			$args['systemSession'] = $responseCurrentSession->currentSession;
			$args['versionerp'] = '?'.FECHA_ULTIMO_PUSH;
			
			$idSeccion = $args['id'];
			
			$args['subsecciones'] = $userController->getAllSubSectionsOfSection($idSeccion, $responseCurrentSession->currentSession->empresa)->subsecciones ?? array();
			return $this->view->render($response, "section_details.twig", $args);
		} else {
			return $response->withRedirect($request->getUri()->getBaseUrl());
		}
	})->setName("SubSectionsOfSection");

	$app->get('/subsecciones', function ($request, $response, $args) use ($container, $userController) {
		$responseCurrentSession = $userController->validateCurrentSession();
		if($responseCurrentSession->result == 2 && $responseCurrentSession->currentSession->permisos == "administrador"){
			$args['systemSession'] = $responseCurrentSession->currentSession;
			$args['versionerp'] = '?'.FECHA_ULTIMO_PUSH;
			$args['subsecciones'] = $userController->getAllSubSections($responseCurrentSession->currentSession->empresa)->subsecciones ?? array();
			return $this->view->render($response, "subSections.twig", $args);
		} else {
			return $response->withRedirect($request->getUri()->getBaseUrl());
		}
	})->setName("SubSections");
	
	$app->get('/proveedores', function ($request, $response, $args) use ($container, $userController) {
		$responseCurrentSession = $userController->validateCurrentSession();
		if($responseCurrentSession->result == 2 && $responseCurrentSession->currentSession->permisos == "administrador"){
			$args['systemSession'] = $responseCurrentSession->currentSession;
			$args['versionerp'] = '?'.FECHA_ULTIMO_PUSH;
			$args['proveedores'] = $userController->getAllProviders($responseCurrentSession->currentSession->empresa)->proveedores ?? array();
			return $this->view->render($response, "providers.twig", $args);
		} else {
			return $response->withRedirect($request->getUri()->getBaseUrl());
		}
	})->setName("Providers");
	
	$app->get('/articulos', function ($request, $response, $args) use ($container, $userController) {
		$responseCurrentSession = $userController->validateCurrentSession();
		if($responseCurrentSession->result == 2 && $responseCurrentSession->currentSession->permisos == "administrador"){
			$args['systemSession'] = $responseCurrentSession->currentSession;
			$args['versionerp'] = '?'.FECHA_ULTIMO_PUSH;
			$args['articulos'] = $userController->getAllArticles($responseCurrentSession->currentSession->empresa, 20, 0)->articulos ?? array();
			return $this->view->render($response, "articles.twig", $args);
		} else {
			return $response->withRedirect($request->getUri()->getBaseUrl());
		}
	})->setName("Articles");
	
	$app->get('/carrito', function ($request, $response, $args) use ($container, $userController) {
		$responseCurrentSession = $userController->validateCurrentSession();
		if($responseCurrentSession->result == 2 && $responseCurrentSession->currentSession->permisos == "normal"){
			$args['systemSession'] = $responseCurrentSession->currentSession;
			$args['versionerp'] = '?'.FECHA_ULTIMO_PUSH;
			$args['carrito_count'] = $userController->getCountArticlesInCart($responseCurrentSession->currentSession->id)->cantidad ?? 0;
			// $proveedores = $userController->getArticlesInCartWithCode($responseCurrentSession->currentSession->id)->proveedores ?? array();
			// $args['proveedores'] = $proveedores;

			$proveedores = $userController->getArticlesInCartWithCode($responseCurrentSession->currentSession->id)->proveedores ?? array();

			// Keep track of which articles we've already seen
			$seenArticles = [];

			// Create a new filtered array of providers
			$filteredProveedores = [];

			foreach ($proveedores as $proveedor) {
				$filteredArticulos = [];
				
				foreach ($proveedor['articulos'] as $articulo) {
					$articuloId = $articulo['id'];
					
					// Only keep this article if we haven't seen it before
					if (!in_array($articuloId, $seenArticles)) {
						$filteredArticulos[] = $articulo;
						$seenArticles[] = $articuloId;
					}
				}
				
				// Only add this provider if it has any articles left
				if (count($filteredArticulos) > 0) {
					$proveedor['articulos'] = $filteredArticulos;
					$filteredProveedores[] = $proveedor;
				}
			}

			// Replace the original providers with the filtered ones
			$args['proveedores'] = $filteredProveedores;

			// $args['articulos'] = $userController->getAllArticles($responseCurrentSession->currentSession->empresa, 20, 0)->articulos ?? array();
			return $this->view->render($response, "carrito.twig", $args);
		} else {
			return $response->withRedirect($request->getUri()->getBaseUrl());
		}
	})->setName("Cart");

	$app->get('/subsecciones/{id}', function ($request, $response, $args) use ($container, $userController) {
		$responseCurrentSession = $userController->validateCurrentSession();
		if($responseCurrentSession->result == 2 && $responseCurrentSession->currentSession->permisos == "administrador"){
			$args['systemSession'] = $responseCurrentSession->currentSession;
			$args['versionerp'] = '?'.FECHA_ULTIMO_PUSH;
			
			$idSubSeccion = $args['id'];
			
			$args['items'] = $userController->getAllItemsOfSubsection($idSubSeccion, $responseCurrentSession->currentSession->empresa)->items ?? array();
			return $this->view->render($response, "items.twig", $args);
		} else {
			return $response->withRedirect($request->getUri()->getBaseUrl());
		}
	})->setName("Items");
	// -------------- ---------------------------

	// POST PETITIONS ---------------------------
	$app->post('/signIn', function(Request $request, Response $response) use ($userController){
		$responseCurrentSession = $userController->validateCurrentSession();
		if($responseCurrentSession->result != 2){
			$data = $request->getParams();
			$user = $data['user'];
			$password = $data['password'];
			return json_encode($userController->signIn($user, $password));
		}else return json_encode($responseCurrentSession);
	});

	$app->post('/getUserData', function(Request $request, Response $response) use ($userController){
		$responseCurrentSession = $userController->validateCurrentSession();
		if($responseCurrentSession->result == 2){
			$data = $request->getParams();
			$userId = $data['id'];
			return json_encode($userController->getUserData($userId));
		}else return json_encode($responseCurrentSession);
	});

	$app->post('/exportOrder', function(Request $request, Response $response) use ($userController){
		$responseCurrentSession = $userController->validateCurrentSession();
		if($responseCurrentSession->result == 2){
			$data = $request->getParams();
			$articles = $data['articles'];
			$provider = $data['provider'];
			// var_dump($articles);
			// var_dump($provider);
			return json_encode($userController->exportOrder($provider, $articles));
		}else return json_encode($responseCurrentSession);
	});

	$app->post('/getAllNormalUsers', function(Request $request, Response $response) use ($userController){
		$responseCurrentSession = $userController->validateCurrentSession();
		if($responseCurrentSession->result == 2){
			return json_encode($userController->getAllNormalUsers($responseCurrentSession->currentSession->empresa));
		}else return json_encode($responseCurrentSession);
	});

	$app->post('/getAllProviders', function(Request $request, Response $response) use ($userController){
		$responseCurrentSession = $userController->validateCurrentSession();
		if($responseCurrentSession->result == 2){
			return json_encode($userController->getAllProviders($responseCurrentSession->currentSession->empresa));
		}else return json_encode($responseCurrentSession);
	});

	$app->post('/getProvider', function(Request $request, Response $response) use ($userController){
		$responseCurrentSession = $userController->validateCurrentSession();
		if($responseCurrentSession->result == 2){
			$data = $request->getParams();
			$idProvider = $data['idProvider'];
			return json_encode($userController->getProvider($idProvider));
		}else return json_encode($responseCurrentSession);
	});

	$app->post('/getAllArticlesSimple', function(Request $request, Response $response) use ($userController){
		$responseCurrentSession = $userController->validateCurrentSession();
		if($responseCurrentSession->result == 2){
			$data = $request->getParams();
			return json_encode($userController->getAllArticlesSimple($responseCurrentSession->currentSession->empresa));
		}else return json_encode($responseCurrentSession);
	});

	$app->post('/getAllArticles', function(Request $request, Response $response) use ($userController){
		$responseCurrentSession = $userController->validateCurrentSession();
		if($responseCurrentSession->result == 2){
			$data = $request->getParams();
			$lastId = $data['lastId'];
			return json_encode($userController->getAllArticles($responseCurrentSession->currentSession->empresa, 20, $lastId));
		}else return json_encode($responseCurrentSession);
	});

	$app->post('/getArticle', function(Request $request, Response $response) use ($userController){
		$responseCurrentSession = $userController->validateCurrentSession();
		if($responseCurrentSession->result == 2){
			$data = $request->getParams();
			$id = $data['articleId'];
			return json_encode($userController->getArticle($id));
		}else return json_encode($responseCurrentSession);
	});
	
	$app->post('/changeStatusSubsection', function(Request $request, Response $response) use ($userController){
		$responseCurrentSession = $userController->validateCurrentSession();
		if($responseCurrentSession->result == 2){
			$data = $request->getParams();
			$subseccion = (int) $data['subseccion'];
			$seccion = (int) $data['seccion'];
			$subsectionStatus = $data['newStatus'] == "true" ? 1 : 0;
			// var_dump($subsectionStatus); exit;
			return json_encode($userController->changeStatusSubsection($seccion, $subseccion, $subsectionStatus, $responseCurrentSession->currentSession->id, $responseCurrentSession->currentSession->empresa));
		}else return json_encode($responseCurrentSession);
	});

	$app->post('/cleanPassword', function(Request $request, Response $response) use ($userController){
		$responseCurrentSession = $userController->validateCurrentSession();
		if($responseCurrentSession->result == 2){
			$data = $request->getParams();
			$userId = $data['id'];
			return json_encode($userController->cleanPassword($userId));
		}else return json_encode($responseCurrentSession);
	});

	$app->post('/removeRelationSectionSubSection', function(Request $request, Response $response) use ($userController){
		$responseCurrentSession = $userController->validateCurrentSession();
		if($responseCurrentSession->result == 2){
			$data = $request->getParams();
			$idSection = $data['idSection'];
			$idSubSection = $data['idSubSection'];
			return json_encode($userController->removeRelationSectionSubSection($idSection, $idSubSection));
		}else return json_encode($responseCurrentSession);
	});

	$app->post('/removeRelationUserSection', function(Request $request, Response $response) use ($userController){
		$responseCurrentSession = $userController->validateCurrentSession();
		if($responseCurrentSession->result == 2){
			$data = $request->getParams();
			$userId = $data['userId'];
			$sectionId = $data['sectionId'];
			return json_encode($userController->removeRelationUserSection($userId, $sectionId));
		}else return json_encode($responseCurrentSession);
	});

	$app->post('/addRelationUserSection', function(Request $request, Response $response) use ($userController){
		$responseCurrentSession = $userController->validateCurrentSession();
		if($responseCurrentSession->result == 2){
			$data = $request->getParams();
			$userId = $data['userId'];
			$sectionId = $data['sectionId'];
			return json_encode($userController->addRelationUserSection($userId, $sectionId));
		}else return json_encode($responseCurrentSession);
	});

	$app->post('/addItemToCart', function(Request $request, Response $response) use ($userController){
		$responseCurrentSession = $userController->validateCurrentSession();
		if($responseCurrentSession->result == 2){
			$data = $request->getParams();
			$itemId = $data['id'];
			return json_encode($userController->addArticleToCart($itemId, $responseCurrentSession->currentSession->id));
		}else return json_encode($responseCurrentSession);
	});

	$app->post('/reduceItemToCart', function(Request $request, Response $response) use ($userController){
		$responseCurrentSession = $userController->validateCurrentSession();
		if($responseCurrentSession->result == 2){
			$data = $request->getParams();
			$itemId = $data['id'];
			return json_encode($userController->reduceArticleToCart($itemId, $responseCurrentSession->currentSession->id));
		}else return json_encode($responseCurrentSession);
	});

	$app->post('/addRelationSectionSubSection', function(Request $request, Response $response) use ($userController){
		$responseCurrentSession = $userController->validateCurrentSession();
		if($responseCurrentSession->result == 2){
			$data = $request->getParams();
			$sectionId = $data['sectionId'];
			$subSectionId = $data['subSectionId'];
			return json_encode($userController->addRelationSectionSubSection($sectionId, $subSectionId));
		}else return json_encode($responseCurrentSession);
	});

	$app->post('/getAllSubSections', function ($request, $response, $args) use ($userController) {
		$responseCurrentSession = $userController->validateCurrentSession();
		if($responseCurrentSession->result == 2){
			return json_encode($userController->getAllSubSections($responseCurrentSession->currentSession->empresa));
		}else return json_encode($responseCurrentSession);
	});

	$app->post('/newSection', function(Request $request, Response $response) use ($userController){
		$responseCurrentSession = $userController->validateCurrentSession();
		if($responseCurrentSession->result == 2){
			$data = $request->getParams();
			$nombre = $data['nombre'];
			return json_encode($userController->newSection($nombre, $responseCurrentSession->currentSession->empresa));
		}else return json_encode($responseCurrentSession);
	});

	$app->post('/newSubSection', function(Request $request, Response $response) use ($userController){
		$responseCurrentSession = $userController->validateCurrentSession();
		if($responseCurrentSession->result == 2){
			$data = $request->getParams();
			$nombre = $data['nombre'];
			return json_encode($userController->newSubSection($nombre, $responseCurrentSession->currentSession->empresa));
		}else return json_encode($responseCurrentSession);
	});

	$app->post('/newProvider', function(Request $request, Response $response) use ($userController){
		$responseCurrentSession = $userController->validateCurrentSession();
		if($responseCurrentSession->result == 2){
			$data = $request->getParams();
			$nombre = $data['nombre'];
			$rut = $data['rut'];
			return json_encode($userController->newProvider($nombre, $rut, $responseCurrentSession->currentSession->empresa));
		}else return json_encode($responseCurrentSession);
	});

	$app->post('/editProvider', function(Request $request, Response $response) use ($userController){
		$responseCurrentSession = $userController->validateCurrentSession();
		if($responseCurrentSession->result == 2){
			$data = $request->getParams();
			$nombre = $data['nombre'];
			$rut = $data['rut'];
			$idProvider = $data['idProvider'];
			return json_encode($userController->editProvider($nombre, $rut, $idProvider));
		}else return json_encode($responseCurrentSession);
	});

	$app->post('/editArticle', function(Request $request, Response $response) use ($userController){
		$responseCurrentSession = $userController->validateCurrentSession();
		if($responseCurrentSession->result == 2){
			$data = $request->getParams();
			$articleId = $data['articleId'];
			$detalle = $data['detalle'];
			$codigo = $data['codigo'];
			$marca = $data['marca'];
			$proveedores = $data['proveedores'];
			
			$articleId = (int) $articleId; // Convertir a entero
			$proveedores = array_map(function($proveedor) {
				return (object) [
					'id' => (int) $proveedor['id'], // Convertir id a entero
					'codigo' => $proveedor['codigo'] // Mantener codigo como string
				];
			}, $proveedores);

			$articulo = new \stdClass();
			$articulo->id = $articleId;
			$articulo->detalle = $detalle;
			$articulo->codigo = $codigo;
			$articulo->marca = $marca;
			
			return json_encode($userController->editArticle($articulo, $proveedores));
		}else return json_encode($responseCurrentSession);
	});

	$app->post('/newArticle', function(Request $request, Response $response) use ($userController){
		$responseCurrentSession = $userController->validateCurrentSession();
		if($responseCurrentSession->result == 2){
			$data = $request->getParams();
			$detalle = $data['detalle'];
			// $codigo = $data['codigo'] ;
			// $marca = $data['marca'] ;
			$codigo = (!isset($data['codigo']) || $data['codigo'] === "") ? null : $data['codigo'];
			$marca = (!isset($data['marca']) || $data['marca'] === "") ? null : $data['marca'];
			$proveedores = $data['proveedores'];
			$proveedores = array_map(function($proveedor) {
				return (object) [
					'id' => (int) $proveedor['id'], // Convertir id a entero
					'codigo' => $proveedor['codigo'] // Mantener codigo como string
				];
			}, $proveedores);
			// $codigoProveedor = $data['codigoProveedor'];
			// $codigoProveedor = (!isset($data['codigoProveedor']) || $data['codigoProveedor'] === "") ? null : $data['codigoProveedor'];
			return json_encode($userController->newArticle($detalle, $codigo, $marca, $proveedores, $responseCurrentSession->currentSession->empresa));
		}else return json_encode($responseCurrentSession);
	});
	
	$app->post('/newItem', function(Request $request, Response $response) use ($userController){
		$responseCurrentSession = $userController->validateCurrentSession();
		if($responseCurrentSession->result == 2){
			$data = $request->getParams();
			$articulo = $data['articulo'];
			$cantidad = $data['cantidad'];
			$subseccion = $data['subseccion'];
			return json_encode($userController->newItem($articulo, $cantidad, $subseccion, $responseCurrentSession->currentSession->empresa));
		}else return json_encode($responseCurrentSession);
	});

	// -------------- ---------------------------
	
	$app->get('/configuraciones', function ($request, $response, $args) use ($container, $userController) {
		$responseCurrentSession = $userController->validateCurrentSession();
		if($responseCurrentSession->result == 2){
			$args['systemSession'] = $responseCurrentSession->currentSession;
			$args['versionerp'] = '?'.FECHA_ULTIMO_PUSH;
			return $this->view->render($response, "settings.twig", $args);
		}else return $response->withRedirect($request->getUri()->getBaseUrl());
	})->setName("Settings");
}
?>