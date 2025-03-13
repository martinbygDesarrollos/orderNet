<?php

require_once '../src/class/defaultclass/users.php';
require_once '../src/filemanagement/excel_management.php';

class ctr_users{
	public function signIn($user, $password){
		$userClass = new users();
		$response = new \stdClass();

		$responseGetUser = $userClass->getUser($user);
		if($responseGetUser->result == 2){
			if(is_null($responseGetUser->objectResult->pass)){
				// Hash the password before storing it
				$hashedPassword = password_hash($password, PASSWORD_DEFAULT);
				$responseUpdatePassword = $userClass->updateUserPassword($responseGetUser->objectResult->id, $hashedPassword);
				if($responseUpdatePassword->result == 2){
					$userClass->verifyCart($responseGetUser->objectResult->id);
					return $userClass->setNewTokenAndSession($responseGetUser->objectResult->id);
				}else return $responseUpdatePassword;
			} else {
				// Use password_verify instead of strcmp
				if(password_verify($password, $responseGetUser->objectResult->pass)){
					$userClass->verifyCart($responseGetUser->objectResult->id);
					return $userClass->setNewTokenAndSession($responseGetUser->objectResult->id);
				} else {
					$response->result = 0;
					$response->message = "Usuario y contraseña no coinciden por favor vuelva a ingresarlos.";
				}
			}

		}else return $responseGetUser;

		return $response;
	}

	public function validateCurrentSession(){
		$userClass = new users();
		$response = new \stdClass();

		if(isset($_SESSION['systemSession'])){
			$currentSession = $_SESSION['systemSession'];
			$responseGetUser = $userClass->getUserById($currentSession->id);
			if($responseGetUser->result == 2){
				if(strcmp($currentSession->token, $responseGetUser->objectResult->token) == 0){
					$response->result = 2;
					$response->currentSession = $currentSession;
				}else{
					$response->result = 0;
					$response->message = "La sesión del usuario caducó por favor vuelva a ingresar.";
				}
			}else{
				$response->result = 0;
				$response->message = "La sesión detectada no es valida, por favor vuelva a ingresar.";
			}
		}else{
			$response->result = 0;
			$response->message = "Actulamente no hay una sesión activa en el sistema.";
		}
		return $response;
	}

	public function getAllNormalUsers($empresa){
		$userClass = new users();
		$response = new \stdClass();
		$responseGetAllNormalUsers = $userClass->getAllNormalUsers($empresa);
		if($responseGetAllNormalUsers->result == 2){
			$response->result = 2;
			$response->usuarios = $responseGetAllNormalUsers->usuarios;
		}else return $responseGetAllNormalUsers;
		return $response;
	}

	public function getAllSections($empresa){
		$userClass = new users();
		$response = new \stdClass();
		$responseGetSections = $userClass->getAllSections($empresa);
		if($responseGetSections->result == 2){
			$response->result = 2;
			$response->secciones = $responseGetSections->secciones;
		}else return $responseGetSections;
		return $response;
	}

	public function getAllSectionsOfUser($id){
		$userClass = new users();
		$response = new \stdClass();
		$responseGetSections = $userClass->getAllSectionsOfUser($id);
		if($responseGetSections->result == 2){
			$response->result = 2;
			$response->secciones = $responseGetSections->secciones;
		}else return $responseGetSections;
		return $response;
	}

	public function getAllSubSections($empresa){
		$userClass = new users();
		$response = new \stdClass();
		$responseGetSubSections = $userClass->getAllSubSections($empresa);
		if($responseGetSubSections->result == 2){
			$response->result = 2;
			$response->subsecciones = $responseGetSubSections->subsecciones;
		}else return $responseGetSubSections;
		return $response;
	}

	public function getAllProviders($empresa){
		$userClass = new users();
		$response = new \stdClass();
		$responseGetProviders = $userClass->getAllProviders($empresa);
		if($responseGetProviders->result == 2){
			$response->result = 2;
			$response->proveedores = $responseGetProviders->proveedores;
		}else return $responseGetProviders;
		return $response;
	}

	public function getAllArticlesSimple($empresa){
		$userClass = new users();
		$response = new \stdClass();
		$responseGetArticles = $userClass->getAllArticlesSimple($empresa);
		if($responseGetArticles->result == 2){
			$response->result = 2;
			$response->articulos = $responseGetArticles->articulos;
		}else return $responseGetArticles;
		return $response;
	}

	public function getAllArticles($empresa, $limit, $from){
		$userClass = new users();
		$response = new \stdClass();
		$responseGetArticles = $userClass->getAllArticles($empresa, $limit, $from);
		if($responseGetArticles->result == 2){
			$response->result = 2;
			$response->articulos = $responseGetArticles->articulos;
		}else return $responseGetArticles;
		return $response;
	}

	public function getAllSubSectionsOfSection($idSeccion, $empresa){
		$userClass = new users();
		$response = new \stdClass();
		$responseGetSubSections = $userClass->getAllSubSectionsOfSection($idSeccion, $empresa);
		if($responseGetSubSections->result == 2){
			$response->result = 2;
			$response->subsecciones = $responseGetSubSections->subsecciones;
			$response->seccion = $responseGetSubSections->seccion;
		}else return $responseGetSubSections;
		return $response;
	}

	public function getAllItemsOfSubsection($idSubSeccion, $empresa){
		$userClass = new users();
		$response = new \stdClass();
		$responseGetItems = $userClass->getAllItemsOfSubsection($idSubSeccion, $empresa);
		if($responseGetItems->result == 2){
			$response->result = 2;
			$response->items = $responseGetItems->items;
		}else return $responseGetItems;
		return $response;
	}

	public function getUserData($userId){
		$userClass = new users();
		$response = new \stdClass();
		$responseGetUser = $userClass->getUserData($userId);
		if($responseGetUser->result == 2){
			$response->result = 2;
			$response->usuario = $responseGetUser->usuario;
			$response->secciones = $responseGetUser->secciones;
		}else return $responseGetUser;
		return $response;
	}

	public function getSectionData($idSeccion){
		$userClass = new users();
		$response = new \stdClass();
		$responseGetSection = $userClass->getSectionData($idSeccion);
		if($responseGetSection->result == 2){
			$response->result = 2;
			$response->seccion = $responseGetSection->objectResult;
		}else return $responseGetSection;
		return $response;
	}

	public function getSubSectionData($idSubSeccion){
		$userClass = new users();
		$response = new \stdClass();
		$responseGetSubSection = $userClass->getSubSectionData($idSubSeccion);
		if($responseGetSubSection->result == 2){
			$response->result = 2;
			$response->subseccion = $responseGetSubSection->objectResult;
		}else return $responseGetSubSection;
		return $response;
	}

	public function getArticle($id){
		$userClass = new users();
		$response = new \stdClass();
		$responseGetArticle = $userClass->getArticle($id);
		if($responseGetArticle->result == 2){
			$response->result = 2;
			$response->articulo = $responseGetArticle->articulo;
			$response->proveedores = $responseGetArticle->proveedores;
		}else return $responseGetArticle;
		return $response;
	}

	public function getProvider($idProvider){
		$userClass = new users();
		$response = new \stdClass();
		$responseGetProvider = $userClass->getProvider($idProvider);
		if($responseGetProvider->result == 2){
			$response->result = 2;
			$response->proveedor = $responseGetProvider->objectResult;
		}else return $responseGetProvider;
		return $response;
	}

	public function editProvider($nombre, $rut, $idProvider){
		$userClass = new users();
		$response = new \stdClass();
		$responseGetProvider = $userClass->editProvider($nombre, $rut, $idProvider);
		if($responseGetProvider->result == 2){
			$response->result = 2;
		}else return $responseGetProvider;
		return $response;
	}

	public function newItemOrder($positions){
		$userClass = new users();
		$response = new \stdClass();
		foreach ($positions as $item) {
			// var_dump(intval($item['id']) );
			// var_dump(intval($item['position']));
			$responseNewPositions = $userClass->setNewItemOrder(intval($item['id']), intval($item['position']));
			if($responseNewPositions->result != 2){
				$response->result = 1;
				$response->message = "Error al momento de re-ordenar los items de la sección";
				return $response;
			}
		}
		$response->result = 2;
		return $response;
	}

	public function getCountArticlesInCart($userId){
		$userClass = new users();
		$response = new \stdClass();
		$responseGetCountArticles = $userClass->getCountArticlesInCart($userId);
		if($responseGetCountArticles->result == 2){
			$response->result = 2;
			$response->cantidad = $responseGetCountArticles->objectResult->total_items;
		}else return $responseGetCountArticles;
		return $response;
	}

	public function addArticleToCart($itemId, $userId){ // HERE
		$userClass = new users();
		$response = new \stdClass();
		$response->result = 1;
		$cartId = null;
		$articleId = $userClass->getItem($itemId)->objectResult->articulo;
		$responseGetCart = $userClass->getCartOfUser($userId);


		// var_dump($responseGetCart); exit;
		if($responseGetCart->result == 2){
			$cartId = $responseGetCart->objectResult->id;
		} else if($responseGetCart->result == 1){
			$responseInsertCart = $userClass->insertNewCart($userId);
			// var_dump($responseInsertCart); exit;
			if($responseInsertCart->result == 2)
				$cartId = $responseInsertCart->id;
		}
		
		if(isset($cartId)){
			$responseInsertArticle = $userClass->addArticleToCart($articleId, $cartId);
			$responseGetCountArticles = $userClass->getCountArticlesInCart($userId);
			$response->cantidad = $responseGetCountArticles->objectResult->total_items;
			$response->id = $responseInsertArticle->id;
			$response->result = 2;
		} else {
			$response->result = 0;
			$response->message = "Error interno del servidor";
		}
		return $response;
	}

	public function getArticlesInCartWithCode($userId){
		$userClass = new users();
		return $userClass->getArticlesInCartWithCode($userId);
	}

	public function getArticlesInCart($userId){
		$userClass = new users();
		return $userClass->getArticlesInCart($userId);
	}

	public function exportOrder($provider, $articles){
		$excel = new excel_management();
		// $response = new \stdClass();
		return $excel->exportOrder($provider, $articles);
	}

	public function reduceArticleToCart($itemId, $userId){ // HERE
		$userClass = new users();
		$response = new \stdClass();
		$response->result = 1;
		$cartId = null;
		$articleId = $userClass->getItem($itemId)->objectResult->articulo;
		$responseGetCart = $userClass->getCartOfUser($userId);

		if($responseGetCart->result == 2){
			$cartId = $responseGetCart->objectResult->id;
		} else if($responseGetCart->result == 1){
			$responseInsertCart = $userClass->insertNewCart($userId);
			if($responseInsertCart->result == 2)
				$cartId = $responseInsertCart->id;
		}
		
		if(isset($cartId)){
			$responseReduceArticle = $userClass->reduceArticleToCart($articleId, $cartId);
			$responseGetCountArticles = $userClass->getCountArticlesInCart($userId);
			$response->cantidad = $responseGetCountArticles->objectResult->total_items ?? 0;
			$response->id = $responseReduceArticle->id;
			$response->result = 2;
			// var_dump($response->cantidad);
		} else {
			$response->result = 0;
			$response->message = "Error interno del servidor";
		}
		return $response;
	}

	public function changeStatusSubsection($seccion, $subseccion, $subsectionStatus, $userId, $empresa){
		$userClass = new users();
		$response = new \stdClass();
		$response->result = 1;
		$responseGetProvider = $userClass->changeStatusSubsection($seccion, $subseccion, $subsectionStatus);
		if($responseGetProvider->result == 2){
			$responseGetSubSection = $this->getAllSubSectionsOfSection($seccion, $empresa);
			if($responseGetSubSection->result == 2){
				$changeStatusToTrue = true;

				foreach ($responseGetSubSection->subsecciones as $subseccion) {
					if($subseccion['estado'] == 0){
						$changeStatusToTrue = false;
					}
				}
				
				$responseGetSection = $userClass->getUserSections($seccion, $userId);
				if($responseGetSection->result == 2){
					if($responseGetSection->seccion->estado == 0 && $changeStatusToTrue){ // esta sin hacer y hay que hacerlo	
						if($this->changeStatusSection($userId, $seccion, 1)->result == 2){
							$response->result = 2;
						}
					} else if($responseGetSection->seccion->estado == 1){
						if($this->changeStatusSection($userId, $seccion, 0)->result == 2){
							$response->result = 2;
						}
					}
				}
			}
			$response->result = 2;
		}else return $responseGetProvider;
		return $response;
	}

	public function changeStatusSection($user, $section, $status){
		$userClass = new users();
		$responseGetProvider = $userClass->changeStatusSection($user, $section, $status);
		if($responseGetProvider->result == 2){
			$response->result = 2;
		}else return $responseGetProvider;
		return $response;
	}

	public function editArticle($article, $proveedores){ // ACA
		$userClass = new users();
		$response = new \stdClass();
		$responseGetArticle = $userClass->getArticle($article->id);

		if ($responseGetArticle->result == 2) {

			if($userClass->editArticle($article->detalle, $article->codigo, $article->marca, $article->id)->result != 2){
				$response->result = 0;
				$response->message = "Error al actualizar los datos del articulo";
				return $response;
			}

			// Create arrays of just the IDs for comparison
			$dbProviderIds = array_column($responseGetArticle->proveedores, 'id');
			$sentProviderIds = [];
			
			// Create a map of sent providers by ID for easy access
			$sentProvidersMap = [];
			foreach ($proveedores as $proveedorEnviado) {
				$sentProviderIds[] = $proveedorEnviado->id;
				$sentProvidersMap[$proveedorEnviado->id] = $proveedorEnviado;
			}
			
			// Create a map of DB providers by ID
			// Convert the DB providers from arrays to objects
			$dbProvidersMap = [];
			foreach ($responseGetArticle->proveedores as $proveedorDB) {
				// Convert array to object
				$proveedorObject = (object) $proveedorDB;
				$dbProvidersMap[$proveedorObject->id] = $proveedorObject;
			}
			
			// Find new provider IDs (in sent but not in DB)
			$newProviderIds = array_diff($sentProviderIds, $dbProviderIds);
			
			// Find removed provider IDs (in DB but not in sent)
			$removedProviderIds = array_diff($dbProviderIds, $sentProviderIds);
			
			// Create arrays of complete provider objects
			$newProviders = [];
			foreach ($newProviderIds as $id) {
				$newProviders[] = $sentProvidersMap[$id];
			}
			
			$removedProviders = [];
			foreach ($removedProviderIds as $id) {
				$removedProviders[] = $dbProvidersMap[$id];
			}

			$commonProviderIds = array_intersect($sentProviderIds, $dbProviderIds);
			$updatedProviders = [];

			foreach ($commonProviderIds as $id) {
				$sentProvider = $sentProvidersMap[$id];
				$dbProvider = $dbProvidersMap[$id];
				
				// Compare the codigo values
				if ($sentProvider->codigo != $dbProvider->codigo) {
					// This provider exists in both lists but has a different codigo value
					$updatedProviders[] = $sentProvider; // Use the sent provider object with updated codigo
				}
			}
			
			$resultNewProviders = true;
			if (!empty($newProviders)) {
				$resultNewProviders = $userClass->addProvidersToArticle($article->id, $newProviders);
			}
			$resultRemovedProviders = true;
			if (!empty($removedProviders)) {
				$resultRemovedProviders = $userClass->removeProvidersToArticle($article->id, $removedProviders);
			}
			$resultUpdatedProviders = true;
			if (!empty($updatedProviders)) {
				$resultUpdatedProviders = $userClass->updateProviderToArticle($article->id, $updatedProviders);
			}

			if($resultUpdatedProviders === false || $resultRemovedProviders === false || $resultNewProviders === false){
				$response->result = 1;
				$response->message = "Error al actualizar los proveedores";
			} else{
				$response->result = 2;
			}
		} else {
			return $responseGetArticle;
		}
		return $response;
	}

	public function cleanPassword($userId){
		$userClass = new users();
		$response = new \stdClass();
		$responseCleanPass = $userClass->cleanPassword($userId);
		if($responseCleanPass->result == 2){
			$response->result = 2;
			$response->message = "Contraseña borrada con exito!";
		}else return $responseCleanPass;
		return $response;
	}

	public function addRelationSectionSubSection($sectionId, $subSectionId){
		$userClass = new users();
		$response = new \stdClass();
		$responseAddRelation = $userClass->addRelationSectionSubSection($sectionId, $subSectionId);
		if($responseAddRelation->result == 2){
			$response->result = 2;
			$response->message = "Relación creada con exito!";
		}else return $responseAddRelation;
		return $response;
	}

	public function removeRelationSectionSubSection($idSection, $idSubSection){
		$userClass = new users();
		$response = new \stdClass();
		$responseRemoveRelation = $userClass->removeRelationSectionSubSection($idSection, $idSubSection);
		if($responseRemoveRelation->result == 2){
			$response->result = 2;
			$response->message = "Relación borrada con exito!";
		}else return $responseRemoveRelation;
		return $response;
	}

	public function removeRelationUserSection($userId, $sectionId){
		$userClass = new users();
		$response = new \stdClass();
		$responseRemoveRelation = $userClass->removeRelationUserSection($userId, $sectionId);
		if($responseRemoveRelation->result == 2){
			$response->result = 2;
			$response->message = "Relación borrada con exito!";
		}else return $responseRemoveRelation;
		return $response;
	}

	public function addRelationUserSection($userId, $sectionId){
		$userClass = new users();
		$response = new \stdClass();
		$responseAddRelation = $userClass->addRelationUserSection($userId, $sectionId);
		if($responseAddRelation->result == 2){
			$response->result = 2;
			$response->message = "Relación creada con exito!";
		}else return $responseAddRelation;
		return $response;
	}

	public function newSection($nombre, $empresa){
		$userClass = new users();
		$response = new \stdClass();
		$responseNewSection = $userClass->newSection($nombre, $empresa);
		if($responseNewSection->result == 2){
			$response->result = 2;
			$response->message = "Nuevo inventario creado con exito!";
		}else return $responseNewSection;
		return $response;
	}

	public function newSubSection($nombre, $empresa){
		$userClass = new users();
		$response = new \stdClass();
		$responseNewSubSection = $userClass->newSubSection($nombre, $empresa);
		if($responseNewSubSection->result == 2){
			$response->result = 2;
			$response->message = "Nueva Sección creada con exito!";
		}else return $responseNewSubSection;
		return $response;
	}
	
	public function newItem($articulo, $cantidad, $subseccion, $empresa){
		$userClass = new users();
		$response = new \stdClass();
		$responseNewItem = $userClass->newItem($articulo, $cantidad, $subseccion, $empresa);
		if($responseNewItem->result == 2){
			$response->result = 2;
			$response->message = "Nuevo Item creado con exito!";
		}else return $responseNewItem;
		return $response;
	}

	public function newProvider($nombre, $rut, $empresa){
		$userClass = new users();
		$response = new \stdClass();
		$responseNewProvider = $userClass->newProvider($nombre, $rut, $empresa);
		if($responseNewProvider->result == 2){
			$response->result = 2;
			$response->message = "Nuevo Proveedor creado con exito!";
		}else return $responseNewProvider;
		return $response;
	}
	
	public function newArticle($detalle, $codigo, $marca, $proveedores, $empresa){ // ACA
		$userClass = new users();
		$response = new \stdClass();
		$responseNewArticle = $userClass->newArticle($detalle, $codigo, $marca, $empresa);
		if($responseNewArticle->result == 2){
			$resultNewProviders = true;
			$resultNewProviders = $userClass->addProvidersToArticle($responseNewArticle->id, $proveedores);
			// $responseNewProveedorArticle = $userClass->newProveedorArticle($proveedor, $responseNewArticle->id, $codigoProveedor);
			if($resultNewProviders == true){
				$response->result = 2;
				$response->message = "Nuevo Articulo creado con exito!";
			} else {
				$response->result = 1;
				$response->message = "Error al establecer los proveedores";
			}
		}else return $responseNewArticle;
		return $response;
	}
}