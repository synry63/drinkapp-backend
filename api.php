<?php
	require_once "bootstrap.php";
	require_once("Rest.inc.php");

	class API extends REST {

		public $data = "";
		private $em = NULL;
		public function __construct($em){
			parent::__construct();				// Init parent contructor
			$this->em = $em;
		}
		/*
		 * Public method for access api.
		 * This method dynmically call the method based on the query string
		 *
		 */
		public function processApi(){
            //$this->response('',404); // TEMP
            $func = strtolower(trim(str_replace("/","",$_REQUEST['rquest'])));
			if((int)method_exists($this,$func) > 0)
				$this->$func();
			else
				$this->response('',404);				// If the method not exist with in this class, response would be "Page not found".
		}

        /**
         * private mathod
         * @param $plain_text
         * @param $hash
         * check password validity
         * require PHP 5.5
         */
        private function is_password_valid($plain_text,$hash){
            if (password_verify($plain_text, $hash)) {
                return true;
            } else {
                return false;
            }
        }

        /**
         * private mathod
         * @param $plain_text
         * @return string
         * get password hash
         * require PHP 5.5
         */
        private function generatePasswordHash($plain_text){
            return password_hash($plain_text, PASSWORD_DEFAULT);
        }
        /*
         *	Simple updateUser API
         *  updateUser must be POST method
         *  user object
        */
        private function updateUser_v2(){
            if($this->get_request_method() != "POST"){
                $this->response('',406);
            }
            $id = trim($this->_request['id']);
            $nombre = trim($this->_request['nombre']);
            $apellidos = trim($this->_request['apellidos']);
            $email = trim($this->_request['email']);
            $celular = trim($this->_request['celular']);
            $userRepository = $this->em->getRepository('User');
            $user = $userRepository->find($id);
            if($user!=NULL){
                $user->nombre = $nombre;
                $user->apellidos = $apellidos;
                if($user->email!=$email){
                    $this->checkEmailExistAndNotNull($email,$userRepository);
                    $user->email = $email;
                }
                $user->celular = $celular;
                $this->em->merge($user);
                $this->em->flush();

                $this->response($this->json($user), 200);
            }
            $error = array('status' => "Failed", "msg" => utf8_encode("El usuario no existe"));
            $this->response($this->json($error), 400);
        }

        /*
       *	Simple getDistritos API
       *  getDistritos must be GET method
       *  user object
       */
        private function getDistritos(){
            if($this->get_request_method() != "GET"){
                $this->response('',406);
            }
            $distritoRepository = $this->em->getRepository('Distrito');
            $distritos = $distritoRepository->findAll();

            $this->response($this->json($distritos), 200);
        }
		/*
		 *	Simple updateUser API
		 *  updateUser must be POST method
		 *  user object
		 */
		private function updateUser(){
			if($this->get_request_method() != "POST"){
				$this->response('',406);
			}
			$id = trim($this->_request['id']);
			$nombre = trim($this->_request['nombre']);
			$apellidos = trim($this->_request['apellidos']);
			$celular = trim($this->_request['celular']);
			$userRepository = $this->em->getRepository('User');
			$user = $userRepository->find($id);
			if($user!=NULL){
				$user->nombre = $nombre;
				$user->apellidos = $apellidos;
				$user->celular = $celular;
				$this->em->merge($user);
				$this->em->flush();

				$this->response($this->json($user), 200);
			}
			$error = array('status' => "Failed", "msg" => utf8_encode("El usuario no existe"));
			$this->response($this->json($error), 400);

		}
        private function pedido_clear(){
            if($this->get_request_method() != "GET"){
                $this->response('',406);
            }

            if($_SERVER['HTTP_ORIGIN'] =="http://drinkapp.pe" || $_SERVER['HTTP_ORIGIN']=="http://www.drinkapp.pe"){
                $idPedido = trim($this->_request['id']);

                $pedidoRepository = $this->em->getRepository('Pedido');
                $pedido = $pedidoRepository->find($idPedido);
                $pedido->estado = 'entregado';
                $this->em->merge($pedido);
                $this->em->flush();

                $ok = array("msg" => "noty({text: 'PEDIDO ENTREGADO CONFIRMADO',type: 'success',timeout: 3000})");
                $this->response($this->json($ok), 200);
            }
        }

        private function pedido_cancel(){
            if($this->get_request_method() != "GET"){
                $this->response('',406);
            }
            if($_SERVER['HTTP_ORIGIN'] =="http://drinkapp.pe" || $_SERVER['HTTP_ORIGIN']=="http://www.drinkapp.pe"){
                $idPedido = trim($this->_request['id']);

                $pedidoRepository = $this->em->getRepository('Pedido');
                $pedido = $pedidoRepository->find($idPedido);
                $pedido->estado = 'anulado';
                $this->em->merge($pedido);
                $this->em->flush();

                $ok = array("msg" => "noty({text: 'PEDIDO ANULADO CONFIRMADO',type: 'success',timeout: 3000})");
                $this->response($this->json($ok), 200);
            }
        }
		/*private function json2($data){
			$serializer = JMS\Serializer\SerializerBuilder::create()->build();
			return $serializer->serialize($data, 'json');

		}*/
        private function rate_us(){
            if($this->get_request_method() != "POST"){
                $this->response('',406);
            }
            $idUsuario = trim($this->_request['id']);

            $userRepository = $this->em->getRepository('User');
            $user = $userRepository->find($idUsuario);
            if($user!=null){
                $user->rate_us = 1;
                $this->em->merge($user);
                $this->em->flush();

                $this->response($this->json($user),200);
            }
            $error = array('status' => "Failed", "msg" => "Error al momento de definir el rate");
            $this->response($this->json($error), 400);


        }
        private function getPedidos_dev(){
            if($this->get_request_method() != "GET"){
                $this->response('',406);
            }

            //$pedidoRepository = $this->em->getRepository('Pedido');
            //$pedidos = $pedidoRepository->findAll();

            $qb = $this->em->createQueryBuilder();
            $qb->select('p')
                ->from('Pedido','p')
                ->where('p.estado = :state')
                ->setParameter('state', 'anulado');
            //->groupBy('p')
            //->innerJoin('c', 'Bebida', 'b', 'c.id = b.categoria_id')
            ;

            $query = $qb->getQuery();
            $pedidos = $query->getResult();


            $this->response($this->json($pedidos), 200);
            //$this->response($out, 200);
        }
		private  function getPedidos(){
			if($this->get_request_method() != "GET"){
				$this->response('',406);
			}
			//$pedidoRepository = $this->em->getRepository('Pedido');
			//$pedidos = $pedidoRepository->findAll();

			$qb = $this->em->createQueryBuilder();
			$qb->select('p')
				->from('Pedido','p')
                ->where('p.estado = :state')
                ->setParameter('state', 'pendiente');
                //->groupBy('p')
			//->innerJoin('c', 'Bebida', 'b', 'c.id = b.categoria_id')
			;

			$query = $qb->getQuery();
			$pedidos = $query->getResult();


			$this->response($this->json($pedidos), 200);
			//$this->response($out, 200);
		}
		/*
		 *	Simple loginFacebook with facebook + register User if new API
		 *  loginFacebook must be POST method
		 *  User facebook object
		 */
		private function loginFacebook(){
			$id_facebook = trim($this->_request['id']);
			$email = trim($this->_request['email']);


			$qb = $this->em->createQueryBuilder();
			$qb->select('u')
				->from('User', 'u')
				->where($qb->expr()->like('u.id_facebook',':idFacebookParam'))->setParameter('idFacebookParam', $id_facebook);
				//->orWhere($qb->expr()->like('u.email',':emailParam'))->setParameter('emailParam', $email);
            try{
                $query = $qb->getQuery();
                $user = $query->getSingleResult();
            }
            catch(Exception $e) {
                $user = null;
            }
			if(empty($user)){ // so register user without password

				$nombre = trim($this->_request['first_name']);
				$apellidos = trim($this->_request['last_name']);
				$u = new User();
				$u->nombre = $nombre;
				$u->apellidos = $apellidos;
                if(!empty($email)){
                    $u->email = $email;
                }
				$u->active = 1;
				$u->id_facebook = $id_facebook;
				$date = new \DateTime("now");
				$u->fechaRegistro = $date->getTimestamp();

				$this->em->persist($u);
				$this->em->flush();

				$this->response($this->json($u),201);
			}
			else{
				$this->response($this->json($user),200);
			}

		}
		/*
		 *	Simple login API
		 *  Login must be POST method
		 *  email : <USER EMAIL>
		 *  pwd : <USER PASSWORD>
		 */

		private function login(){

			// Cross validation if the request method is POST else it will return "Not Acceptable" status
			if($this->get_request_method() != "POST"){
				$this->response('',406);
			}

            $email = trim($this->_request['email']);
            $password = trim($this->_request['password']);

			//$postdata = file_get_contents("php://input");
			//$test = json_decode($postdata);


			// Input validations
			if(!empty($email) and !empty($password)){
				if(filter_var($email, FILTER_VALIDATE_EMAIL)){
					$userRepository = $this->em->getRepository('User');
					$user = $userRepository->findOneBy(array('email' => $email,'password' => $password));
					if($user!=NULL){
						// If success everythig is good send header as "OK" and user details
						$this->response($this->json($user), 200);
					}
					//$this->response('', 204);	// If no records "No Content" status*/
				}
			}
			// If invalid inputs "Bad Request" status message and reason
			$error = array('status' => "Failed", "msg" => "Dirección de correo electrónico o contraseña no válida");
			$this->response($this->json($error), 400);
		}
		/*
		 *	Simple loginDistribuidor API
		 *  loginDistribuidor must be POST method
		 *  email : <USER EMAIL>
		 *  pwd : <USER PASSWORD>
		 */
		private function loginDistribuidor(){
			// Cross validation if the request method is POST else it will return "Not Acceptable" status
			if($this->get_request_method() != "POST"){
				$this->response('',406);
			}
			$email = trim($this->_request['email']);
			$password = trim($this->_request['password']);
			// Input validations
			if(!empty($email) and !empty($password)){
				if(filter_var($email, FILTER_VALIDATE_EMAIL)){
					$distribuidorRepository = $this->em->getRepository('Distribuidor');
					$d = $distribuidorRepository->findOneBy(array('email' => $email,'password' => $password));

					if($d!=NULL){
						if($d->active){
							// If success everythig is good send header as "OK" and d details
							$this->response($this->json($d), 200);
						}
						else{
							$error = array('status' => "Failed", "msg" => "Distribuidor must be active");
							$this->response($this->json($error), 400);
						}

					}
					//$this->response('', 204);	// If no records "No Content" status*/
				}
			}
			// If invalid inputs "Bad Request" status message and reason
			$error = array('status' => "Failed", "msg" => "Invalid Email address or Password");
			$this->response($this->json($error), 400);
		}

        /**
         *	Simple registerConfirmacion API
         *  registerConfirmacion must be POST method
         *  registerKey : <User bool>
         */
        private function registerConfirmacion(){
            if($this->get_request_method() != "GET"){
                $this->response('',406);
            }
            $registerKey = trim($this->_request['registerKey']);
            if(!empty($registerKey)){
                $userRepository = $this->em->getRepository('User');
                $user = $userRepository->findOneBy(array('registerKey' => $registerKey));
                if($user!=NULL){
                    $user->active = 1;
                    $this->em->merge($user);
                    $this->em->flush();
                }

            }


        }
        /**
         *	Simple registerDistribuidorConfirmacion API
         *  registerDistribuidorConfirmacion must be POST method
         *  registerKey : <Distribuidor bool>
         */
        private function registerDistribuidorConfirmacionEmail(){
            if($this->get_request_method() != "GET"){
                $this->response('',406);
            }
            $registerKey = trim($this->_request['registerKey']);
            if(!empty($registerKey)){
                $distribuidorRepository = $this->em->getRepository('Distribuidor');
                $d = $distribuidorRepository->findOneBy(array('registerKey' => $registerKey));
                if($d!=NULL){
					$d->validateEmail = 1;
                    $this->em->merge($d);
                    $this->em->flush();
					$this->response($this->json($d), 200);
                }

            }


        }
		/*
		 *	Simple register API
		 *  register must be POST method
		 *  nombre : <USER nombre>
		 *  apellidos : <USER apellidos>
		 *  email : <USER email>
		 *  password : <USER password>
		 *  confirm_password : <USER confirm_password>
		 */
		private function register(){
			// Cross validation if the request method is POST else it will return "Not Acceptable" status
			if($this->get_request_method() != "POST"){
				$this->response('',406);
			}

			$email = trim($this->_request['email']);
			$password = trim($this->_request['password']);
			$nombre = trim($this->_request['nombre']);
			$apellidos = trim($this->_request['apellidos']);
			$confirm_password = trim($this->_request['confirm_password']);
			$direccion = trim($this->_request['direccion']);

			if(!empty($email) and !empty($password) and !empty($nombre) and !empty($apellidos) and !empty($confirm_password)){
				if(filter_var($email, FILTER_VALIDATE_EMAIL) && $password==$confirm_password){
					$userRepository = $this->em->getRepository('User');
					$user = $userRepository->findOneBy(array('email' => $email));
					if($user!=NULL){
						// If invalid inputs "Bad Request" status message and reason
						$error = array('status' => "Failed", "msg" => "Email address allready exist");
						$this->response($this->json($error), 400);
					}
					$u = new User();
					$u->nombre = $nombre;
					$u->apellidos = $apellidos;
					$u->email = $email;
					$u->password = $password;
					$u->active = 1;
                    $u->registerKey = uniqid();
                    $date = new \DateTime("now");
                    $u->fechaRegistro = $date->getTimestamp();
					$this->em->persist($u);

					if(isset($direccion) && !empty($direccion)){
						$direccion = json_decode($direccion,TRUE);
						if($direccion==NULL){
							$error = array('status' => "Failed", "msg" => "Direccion Invalid Data");
							$this->response($this->json($error), 400);
						}
						$calle = trim($direccion['calle']);
						$referencias = trim($direccion['referencias']);
						$nombre = trim($direccion['nombre']);
						$distrito = trim($direccion['distrito']);
						$numero = trim($direccion['numero']);
						$piso_apt = trim($direccion['piso_apt']);
						$telefono = trim($direccion['telefono']);
						if(!empty($calle) and !empty($referencias) and !empty($nombre) and !empty($distrito) and !empty($numero) and !empty($piso_apt) and !empty($telefono)){
							$dir = new Direccion();
							$dir->nombre = $nombre;
							$dir->calle = $calle;
							$dir->referencias = $referencias;
							$dir->numero = $numero;
							$dir->piso_apt = $piso_apt;
							$dir->distrito = $distrito;
							$dir->telefono = $telefono;
							$dir->user = $u;
							$this->em->persist($dir);
						}
					}


					$this->em->flush();
					$this->response($this->json($u),200);
				}
				// If invalid inputs "Bad Request" status message and reason
				$error = array('status' => "Failed", "msg" => "Invalid Email address or Password do not match");
				$this->response($this->json($error), 400);
			}
			// If invalid inputs "Bad Request" status message and reason
			$error = array('status' => "Failed", "msg" => "Invalid Data");
			$this->response($this->json($error), 400);
		}

		/*
		 *	Simple registerDistribuidor API
		 *  registerDistribuidor must be POST method
		 *  nombre : <Distribuidor nombre>
		 *  email : <Distribuidor email>
		 *  password : <Distribuidor password>
		 *  confirm_password : <Distribuidor confirm_password>
		 *  tiempoDelivery : <Distribuidor tiempoDelivery>
		 *  descripcion : <Distribuidor descripcion>
		 */
		private function registerDistribuidor(){
			// Cross validation if the request method is POST else it will return "Not Acceptable" status
			if($this->get_request_method() != "POST"){
				$this->response('',406);
			}

			$email = trim($this->_request['email']);
			$password = trim($this->_request['password']);
			$nombre = trim($this->_request['nombre']);
			$descripcion = trim($this->_request['descripcion']);
			$confirm_password = trim($this->_request['confirm_password']);
			$direccion = trim($this->_request['direccion']);
			$tiempoDelivery = trim($this->_request['tiempoDelivery']);

			if(!empty($email) and !empty($password) and !empty($nombre) and !empty($confirm_password) && !empty($tiempoDelivery)){
				if(filter_var($email, FILTER_VALIDATE_EMAIL) && $password==$confirm_password){
					$distribuidorRepository = $this->em->getRepository('Distribuidor');
					$distribuidor = $distribuidorRepository->findOneBy(array('email' => $email));
					if($distribuidor!=NULL){
						// If invalid inputs "Bad Request" status message and reason
						$error = array('status' => "Failed", "msg" => "Email address allready exist");
						$this->response($this->json($error), 400);
					}
					$d = new Distribuidor();
					$d->nombre = $nombre;
					$d->email = $email;
					$d->password = $password;
					if(is_numeric($tiempoDelivery)==false){
							$error = array('status' => "Failed", "msg" => "tiempo delivery must be numeric");
							$this->response($this->json($error), 400);
					}
					$d->tiempoDelivery = $tiempoDelivery;
					$d->descripcion = $descripcion;
					$d->active = 0;
					$d->validateEmail = 0;
                    $d->registerKey = uniqid();
                    $date = new \DateTime("now");
                    $d->fechaRegistro = $date->getTimestamp();
					$this->em->persist($d);

					if(isset($direccion) && !empty($direccion)){
						$direccion = json_decode($direccion,TRUE);
						if($direccion==NULL){
							$error = array('status' => "Failed", "msg" => "Direccion Invalid Data");
							$this->response($this->json($error), 400);
						}
						$calle = trim($direccion['calle']);
						$referencias = trim($direccion['referencias']);
						$nombre = trim($direccion['nombre']);
						$distrito = trim($direccion['distrito']);
						$numero = trim($direccion['numero']);
						$piso_apt = trim($direccion['piso_apt']);
						$telefono = trim($direccion['telefono']);
						if(!empty($calle) and !empty($referencias) and !empty($nombre) and !empty($distrito) and !empty($numero) and !empty($piso_apt) and !empty($telefono)){
							$dir = new Direccion();
							$dir->nombre = $nombre;
							$dir->calle = $calle;
							$dir->referencias = $referencias;
							$dir->numero = $numero;
							$dir->piso_apt = $piso_apt;
							$dir->distrito = $distrito;
							$dir->telefono = $telefono;
							$dir->distribuidor = $d;
							$this->em->persist($dir);
						}
					}


					$this->em->flush();
					$this->response($this->json($d),200);
				}
				// If invalid inputs "Bad Request" status message and reason
				$error = array('status' => "Failed", "msg" => "Invalid Email address or Password do not match");
				$this->response($this->json($error), 400);
			}
			// If invalid inputs "Bad Request" status message and reason
			$error = array('status' => "Failed", "msg" => "Invalid Data");
			$this->response($this->json($error), 400);
		}

        /**
         * SLUGIFY A STRING
         * @param $text
         * @return mixed|string
         *
         */
        private  function slugify($text)
        {
            // replace non letter or digits by -
            $text = preg_replace('~[^\\pL\d]+~u', '-', $text);

            // trim
            $text = trim($text, '-');

            // transliterate
            $text = iconv('utf-8', 'us-ascii//TRANSLIT', $text);

            // lowercase
            $text = strtolower($text);

            // remove unwanted characters
            $text = preg_replace('~[^-\w]+~', '', $text);

            if (empty($text))
            {
                return 'n-a';
            }

            return $text;
        }
        /**
         * simple INIT a dirreccion (NOT API FUNCTION)
         * @param $calle
         * @param $referencias
         * @param $nombre
         * @param $distrito
         * @param $numero
         * @param $piso_apt
         * @param $telefono
         * @return Direccion
         */
        private function initDireccion($calle,$referencias,$nombre,$distrito,$slug,$custom_name = null,$isOtherName = false){

            $dir = new Direccion();

            $dir->nombre = $nombre;
            $dir->calle = $calle;
            $dir->referencias = $referencias;
            $dir->distrito = $distrito;
            $dir->slug = $slug;
			$dir->custom_name = $custom_name;
			$dir->is_other_name = $isOtherName;
            return $dir;
        }
        private function deleteDireccion(){
            $idDir = trim($this->_request['id']);
            $userRepository = $this->em->getRepository('Direccion');
            $dir = $userRepository->find($idDir);

			try {
				$this->em->remove($dir);
				$this->em->flush();

				$this->response($this->json($idDir),200);
			}
			catch(Exception $e){
				$error = array('status' => "Failed", "msg" => "esta dirección está asociada a sus pedidos y no puede ser borrada");
				$this->response($this->json($error), 400);
			}


        }
		private function editDireccion_v2(){
			if($this->get_request_method() != "POST"){
				$this->response('',406);
			}

			$idUsuario = $this->_request['id_user'];

			$idDir = trim($this->_request['id']);
			$calle = trim($this->_request['calle']);
			$referencias = trim($this->_request['referencias']);
			//$nombre = trim($this->_request['nombre']);
			$custom_name = trim($this->_request['custom_name']);
			$distrito = trim($this->_request['distrito']);
			if(!empty($idUsuario) and !empty($calle) and !empty($custom_name) and !empty($distrito)){
				$userRepository = $this->em->getRepository('User');
				$user = $userRepository->find($idUsuario);
				$the_dir = null;
				$new_slug = '';
				// change value inside de array
				for($i=0;$i<count($user->direcciones);$i++){

					if($user->direcciones[$i]->id==$idDir){

						// change this dir
						if($custom_name=="other" && !empty($custom_name)){
							$new_slug = $this->slugify($custom_name);
							$user->direcciones[$i]->slug = $new_slug;
							$user->direcciones[$i]->referencias = $referencias;
							$user->direcciones[$i]->nombre = $custom_name;
							$user->direcciones[$i]->custom_name = $custom_name;
							$user->direcciones[$i]->calle = $calle;
							$user->direcciones[$i]->distrito = $distrito;
							$user->direcciones[$i]->is_other_name = true;
						}
						else{
							$new_slug = $this->slugify($custom_name);
							$user->direcciones[$i]->slug = $new_slug;
							$user->direcciones[$i]->referencias = $referencias;
							$user->direcciones[$i]->nombre = $custom_name;
							$user->direcciones[$i]->custom_name = $custom_name;
							$user->direcciones[$i]->calle = $calle;
							$user->direcciones[$i]->distrito = $distrito;
							$user->direcciones[$i]->is_other_name = false;
						}
						break;
					}
				}

				for($j=0;$j<count($user->direcciones);$j++){
					$a_dir = $user->direcciones[$j];

					if($a_dir->slug==$new_slug && $j!=$i){
						$error = array('status' => "Failed", "msg" => "Este lugar ya lo registraste");
						$this->response($this->json($error), 400);
					}
				}

				$this->em->persist($user);
				$this->em->flush();

				$this->response($this->json($user->direcciones),200);
			}
		}
        /**
         * editDirecion
         * idUsurio:<USER id>
        *  direccion:<Direccion direccion>
         */
        private function editDireccion(){
			if($this->get_request_method() != "POST"){
				$this->response('',406);
			}

			$idUsuario = $this->_request['id_user'];

			$idDir = trim($this->_request['id']);
			$calle = trim($this->_request['calle']);
			$referencias = trim($this->_request['referencias']);
			$nombre = trim($this->_request['nombre']);
			$custom_name = trim($this->_request['custom_name']);
			$distrito = trim($this->_request['distrito']);
			if(!empty($idUsuario) and !empty($calle) and !empty($nombre) and !empty($distrito)){
				$userRepository = $this->em->getRepository('User');
				$user = $userRepository->find($idUsuario);
				$the_dir = null;
				$new_slug = '';
				// change value inside de array
				for($i=0;$i<count($user->direcciones);$i++){

					if($user->direcciones[$i]->id==$idDir){

						// change this dir
						if($nombre=="other" && !empty($custom_name)){
							$new_slug = $this->slugify($custom_name);
							$user->direcciones[$i]->slug = $new_slug;
							$user->direcciones[$i]->referencias = $referencias;
							$user->direcciones[$i]->nombre = $nombre;
							$user->direcciones[$i]->custom_name = $custom_name;
							$user->direcciones[$i]->calle = $calle;
							$user->direcciones[$i]->distrito = $distrito;
							$user->direcciones[$i]->is_other_name = true;
						}
						else{
							$new_slug = $this->slugify($nombre);
							$user->direcciones[$i]->slug = $new_slug;
							$user->direcciones[$i]->referencias = $referencias;
							$user->direcciones[$i]->nombre = $nombre;
							$user->direcciones[$i]->custom_name = $nombre;
							$user->direcciones[$i]->calle = $calle;
							$user->direcciones[$i]->distrito = $distrito;
							$user->direcciones[$i]->is_other_name = false;
						}
					break;
					}
				}

				for($j=0;$j<count($user->direcciones);$j++){
					$a_dir = $user->direcciones[$j];

					if($a_dir->slug==$new_slug && $j!=$i){
						$error = array('status' => "Failed", "msg" => "Este lugar ya lo registraste");
						$this->response($this->json($error), 400);
					}
				}

				$this->em->persist($user);
				$this->em->flush();

				$this->response($this->json($user->direcciones),200);
			}
        }
		private function addDireccion_v2(){
			if($this->get_request_method() != "POST"){
				$this->response('',406);
			}
			$idUsuario = $this->_request['id_user'];
			$calle = trim($this->_request['calle']);
			$referencias = trim($this->_request['referencias']);
			$custom_name = trim($this->_request['custom_name']);
			$distrito = trim($this->_request['distrito']);

			if(!empty($idUsuario) and !empty($calle) and !empty($custom_name) and !empty($distrito)){
				//if(!empty($idUsuario) and !empty($calle) and !empty($referencias) and !empty($nombre) and !empty($distrito) and !empty($numero) and !empty($piso_apt) and !empty($telefono)){
				$userRepository = $this->em->getRepository('User');
				$user = $userRepository->find($idUsuario);
				if($user!=NULL){
					$name_slug = '';

					if($custom_name=="other"){

						//$name_slug = $this->slugify($custom_name);
						//$dir = $this->initDireccion($calle,$referencias,$nombre,$distrito,$name_slug,$custom_name,true);
					}
					else{
						$name_slug = $this->slugify($custom_name);
						$dir = $this->initDireccion($calle,$referencias,$custom_name,$distrito,$name_slug,$custom_name);
					}
					foreach ($user->direcciones as $a_dir){
						if($a_dir->slug==$name_slug){
							$error = array('status' => "Failed", "msg" => "Este lugar ya lo registraste");
							$this->response($this->json($error), 400);
						}
					}

					$dir->user = $user;

					$this->em->persist($dir);
					$this->em->flush();

					$user->direcciones[] = $dir;
					//unset($dir->user);
					$this->response($this->json($user->direcciones),200);
				}


			}
			// If invalid inputs "Bad Request" status message and reason
			$error = array('status' => "Failed", "msg" => "Invalid Data");
			$this->response($this->json($error), 400);


		}
        /*
         *	Simple addDireccion API
         *  addDireccion must be POST method
            idUsurio:<USER id>
            direccion:<Direccion direccion>
         */
		private function addDireccion(){
            if($this->get_request_method() != "POST"){
                $this->response('',406);
            }

			$idUsuario = $this->_request['id_user'];

			$calle = trim($this->_request['calle']);
			$referencias = trim($this->_request['referencias']);
			$nombre = trim($this->_request['nombre']);
            $custom_name = trim($this->_request['custom_name']);
            $distrito = trim($this->_request['distrito']);
			//$distrito = trim($this->_request['distrito']);
			//$numero = trim($this->_request['numero']);
			//$piso_apt = trim($this->_request['piso_apt']);
			//$telefono = trim($this->_request['telefono']);
            if(!empty($idUsuario) and !empty($calle) and !empty($nombre) and !empty($distrito)){
			//if(!empty($idUsuario) and !empty($calle) and !empty($referencias) and !empty($nombre) and !empty($distrito) and !empty($numero) and !empty($piso_apt) and !empty($telefono)){
                $userRepository = $this->em->getRepository('User');
				$user = $userRepository->find($idUsuario);
				if($user!=NULL){
                    $name_slug = '';

                    if($nombre=="other" && !empty($custom_name)){

						$name_slug = $this->slugify($custom_name);
						$dir = $this->initDireccion($calle,$referencias,$nombre,$distrito,$name_slug,$custom_name,true);
                    }
                    else{
                        $name_slug = $this->slugify($nombre);
						$dir = $this->initDireccion($calle,$referencias,$nombre,$distrito,$name_slug,$nombre);
                    }
                    foreach ($user->direcciones as $a_dir){
                        if($a_dir->slug==$name_slug){
                            $error = array('status' => "Failed", "msg" => "Este lugar ya lo registraste");
                            $this->response($this->json($error), 400);
                        }
                    }

                    $dir->user = $user;

					$this->em->persist($dir);
					$this->em->flush();

                    $user->direcciones[] = $dir;
					//unset($dir->user);
					$this->response($this->json($user->direcciones),200);
				}


			}
			// If invalid inputs "Bad Request" status message and reason
			$error = array('status' => "Failed", "msg" => "Invalid Data");
			$this->response($this->json($error), 400);
		}

		/*
		 *	Simple setFavorito API
		 *  setFavorito must be POST method
         *  favorito : <Pedido bool>
		 *  idPedido : <Pedido idPedido>
		 */
        private function setFavorito(){
            if($this->get_request_method() != "POST"){
                $this->response('',406);
            }
            $favorito = trim($this->_request['favorito']);
            $idPedido = trim($this->_request['idPedido']);
            if(!empty($favorito) && !empty($idPedido)){
                $pedidoRepository = $this->em->getRepository('Pedido');
                $pedido = $pedidoRepository->find($idPedido);
                if($pedido!=NULL){
                    $bool = filter_var($favorito, FILTER_VALIDATE_BOOLEAN);
                    $pedido->favorito = $bool;
                    $this->em->persist($pedido);
                    $this->em->flush();
                    unset($pedido->user);
                    $this->response($this->json($pedido),200);

                }
                $error = array('status' => "Failed", "msg" => "Invalid Pedido");
                $this->response($this->json($error), 400);
            }
            $error = array('status' => "Failed", "msg" => "Invalid Data");
            $this->response($this->json($error), 400);

        }
        /*
         *	Simple getUser API
         *  getUser must be GET method
         *  idUser : <User int>
         */
        private function getUser(){
            if($this->get_request_method() != "GET"){
                $this->response('',406);
            }
            $idUsuario = trim($this->_request['id']);
            if(!empty($idUsuario)){
                $userRepository = $this->em->getRepository('User');
                $user = $userRepository->find($idUsuario);
                if($user!=NULL){
                    $this->response($this->json($user),200);
                }
            }
            $error = array('status' => "Failed", "msg" => "Invalid Data");
            $this->response($this->json($error), 400);
        }
        /*
         *	Simple getRecibo API
         *  getRecibo must be GET method
         */
        private function getRecibo(){
            if($this->get_request_method() != "GET"){
                $this->response('',406);
            }
            $config = parse_ini_file("config/ini/basic_config.ini");
            $arr = array();
            $arr [] = $config["boleta"];
            $arr [] = $config["factura"];
            $this->response($this->json($arr),200);
        }
        private function getOrderItems_get(){
            if($this->get_request_method() != "GET"){
                $this->response('',406);
            }
            $idPedido = trim($this->_request['id']);

            $pedidoRepository = $this->em->getRepository('Pedido');
            $order = $pedidoRepository->find($idPedido);

            $pedidoBebidaRepository = $this->em->getRepository('PedidoBebida');
            $pedido_items = $pedidoBebidaRepository->findBy(
                array('pedido' => $order),// Critere
                array('id' => 'asc')// tri
            );

            $this->response($this->json($pedido_items),200);
        }
        /**
         * Simple getLastOrderItems API
         * Pedido ID
         */
        private function getOrderItems(){
            if($this->get_request_method() != "POST"){
                $this->response('',406);
            }
            $idPedido = trim($this->_request['id']);

            $pedidoRepository = $this->em->getRepository('Pedido');
            $lastOrder = $pedidoRepository->find($idPedido);
            $pedidoBebidaRepository = $this->em->getRepository('PedidoBebida');

            $pedido_items = $pedidoBebidaRepository->findBy(
                array('pedido' => $lastOrder),// Critere
                array('id' => 'asc')// tri
            );

            $this->response($this->json($pedido_items),200);

        }
        /**
         * Simple getUserPuntos API
         * User ID
         */
        private function getUserPuntos(){
            if($this->get_request_method() != "POST"){
                $this->response('',406);
            }
            $idUsuario = trim($this->_request['id']);
            $userRepository = $this->em->getRepository('User');
            $user = $userRepository->find($idUsuario);
            if($user!=NULL){
                if(empty($user->puntos)) $user->puntos = 0;
                $this->response($this->json($user->puntos),200);
            }
        }
		/**
		 * Simple getLastOrderByUser API
		 * User ID
		 */
		private function getUserLastOrder(){
			if($this->get_request_method() != "POST"){
				$this->response('',406);
			}
			$idUsuario = trim($this->_request['id']);

			$userRepository = $this->em->getRepository('User');
			$user = $userRepository->find($idUsuario);

			if($user!=NULL){
				$pedidoRepository = $this->em->getRepository('Pedido');
				$lastOrder = $pedidoRepository->findOneBy(
					array('user' => $user),// Critere
					array('id' => 'desc'),// tri
					1
				);
                if($lastOrder==NULL){
                    $error = array('status' => "Failed", "msg" => 'ningun pedido hasta ahora');
                    $this->response($this->json($error), 400);
                }
                else{
                    $this->response($this->json($lastOrder),200);
                }


			}
		}

        /*
         *	Simple getCategorias API
         *  getCategorias must be GET method
         */
        private function getCategorias(){
            if($this->get_request_method() != "GET"){
                $this->response('',406);
            }
            //$categoriaRepository = $this->em->getRepository('Categoria');

			$qb = $this->em->createQueryBuilder();
			$qb->select('c', 'b')
				->from('Categoria','c')
				->innerJoin('c.bebidas', 'b')
				//->innerJoin('b.categoria', 'c')
				->where('b.active = :active')
                ->addOrderBy('c.order', 'ASC')
                ->addOrderBy('b.price', 'ASC')

				->setParameter('active', true);
				//->innerJoin('c', 'Bebida', 'b', 'c.id = b.categoria_id')
			;

			$query = $qb->getQuery();
			$categorias = $query->getResult();
			//$test = $query->getResult();
			/*$error = array('status' => "Failed", "msg" => $bebidas);
			$this->response($this->json($error), 400);*/

            /*$categorias = $categoriaRepository->findBy(
                array(), // Critere
                array('order' => 'asc')        // Tri
            );*/
            $this->response($this->json($categorias),200);

        }
        /*
         *	Simple getBebidasPorCategoria API
         *  getBebidasPorCategoria must be GET method
         *  idCategoria : <Categoria int>
         */
        private function getBebidasPorCategoria(){
            if($this->get_request_method() != "GET"){
                $this->response('',406);
            }
            $idCategoria = trim($this->_request['id']);
            if(!empty($idCategoria)){
                $categoriaRepository = $this->em->getRepository('Categoria');
                $categoria = $categoriaRepository->find($idCategoria);
                if($categoria!=NULL){
                    $bebidaRepository = $this->em->getRepository('Bebida');
                    $bebidas = $bebidaRepository->findBy(
                        array('categoria' => $categoria), // Critere
                        array('nombre' => 'asc')        // Tri
                    );
                    $this->response($this->json($bebidas),200);
                }
            }
            // If invalid inputs "Bad Request" status message and reason
            $error = array('status' => "Failed", "msg" => "Invalid Data");
            $this->response($this->json($error), 400);
        }
        /*
         *	Simple getBebida API
         *  getBebida must be GET method
         *  idBebida : <Bebida int>
         */
        private function getBebida(){
            if($this->get_request_method() != "GET"){
                $this->response('',406);
            }
            $idBebida = trim($this->_request['id']);
            $bebidaRepository = $this->em->getRepository('Bebida');
            $bebida = $bebidaRepository->find($idBebida);
            if($bebida!=NULL){
                $this->response($this->json($bebida),200);
            }
            $error = array('status' => "Failed", "msg" => "Invalid Data");
            $this->response($this->json($error), 400);

        }
        /*
         *	Simple getBebidas API
         *  getBebidas must be GET method
         */
        private function getBebidas(){
            if($this->get_request_method() != "GET"){
                $this->response('',406);
            }
            $bebidaRepository = $this->em->getRepository('Bebida');
            $bebidas = $bebidaRepository->findAll();
            if($bebidas!=NULL){
                $this->response($this->json($bebidas),200);
            }
            $error = array('status' => "Failed", "msg" => "Invalid Data");
            $this->response($this->json($error), 400);
        }
        /*
         *	Simple getPedido API
         *  getPedido must be GET method
         *  idPedido : <Pedido int>
         */
        private function getPedido(){
            if($this->get_request_method() != "GET"){
                $this->response('',406);
            }
            $idPedido = trim($this->_request['id']);
            if(!empty($idPedido)){
                $pedidoRepository = $this->em->getRepository('Pedido');
                $pedido = $pedidoRepository->find($idPedido);
                if($pedido!=NULL){
                    $pedido->user->direcciones = array();
					$this->response($this->json($pedido),200);
                }
            }

            $error = array('status' => "Failed", "msg" => "Invalid Data");
            $this->response($this->json($error), 400);
        }
        /*
         *	Simple searchBebida API
         *  searchBebida must be GET method
         *  nombre : <nombre string>
         */
        private function searchBebida(){
            if($this->get_request_method() != "GET"){
                $this->response('',406);
            }
            $nombre = trim($this->_request['nombre']);
            if(!empty($nombre)){
                $qb = $this->em->createQueryBuilder();
                $qb->select('b')
                    ->from('Bebida', 'b')
                    ->where($qb->expr()->like('b.nombre',':nombreParam'))->setParameter('nombreParam', '%' . $nombre . '%')
                    ->orderBy('b.nombre', 'ASC');

                $query = $qb->getQuery();
                $bebidas = $query->getResult();

                $this->response($this->json($bebidas),200);
            }
            // If invalid inputs "Bad Request" status message and reason
            $error = array('status' => "Failed", "msg" => "Invalid Data");
            $this->response($this->json($error), 400);
        }
		/*
        *  Simple getMisDirecciones API
        *  getMisDirecciones must be GET method
        *  idUsuario : <idUsuario int>
        */
		private function getMisDirecciones(){
			if($this->get_request_method() != "GET"){
                $this->response('',406);
            }
            $idUsuario = trim($this->_request['id']);
			if(!empty($idUsuario)){
				$userRepository = $this->em->getRepository('User');
                $user = $userRepository->find($idUsuario);
				if($user!=NULL){
                    $this->response($this->json($user->direcciones),200);
                }
			}
			// If invalid inputs "Bad Request" status message and reason
            $error = array('status' => "Failed", "msg" => "Invalid Data");
            $this->response($this->json($error), 400);
		}
        /*
        *	Simple getPedidosFavoritos API
        *  getPedidosFavoritos must be GET method
        *  idUsuario : <idUsuario int>
        */
        private function getPedidosFavoritos(){
            if($this->get_request_method() != "GET"){
                $this->response('',406);
            }
            $idUsuario = trim($this->_request['id']);
            if(!empty($idUsuario)){
                $userRepository = $this->em->getRepository('User');
                $user = $userRepository->find($idUsuario);
                if($user!=NULL){
                    $pedidoRepository = $this->em->getRepository('Pedido');
                    $pedidos = $pedidoRepository->findBy(
                        array('user' => $user,'favorito'=>true), // Critere
                        array('fechaPendente' => 'desc')        // Tri
                    );
                    $this->response($this->json($pedidos),200);
                }

            }
            // If invalid inputs "Bad Request" status message and reason
            $error = array('status' => "Failed", "msg" => "Invalid Data");
            $this->response($this->json($error), 400);
        }
        /*
        *	Simple getPedidosPorDistribuidor API
        *  getPedidosPorDistribuidor must be GET method
        *  Distribuidor : <Distribuidor int>
        */
        private function getPedidosPorDistribuidor(){
            if($this->get_request_method() != "GET"){
                $this->response('',406);
            }
            $idDistribuidor = trim($this->_request['id']);
            $estado = trim($this->_request['estado']);

            if(!empty($idDistribuidor)){
                $distribuidorRepository = $this->em->getRepository('Distribuidor');
                $distribuidor = $distribuidorRepository->find($idDistribuidor);
                if($distribuidor!=NULL){
                    $pedidoRepository = $this->em->getRepository('Pedido');
                    $arr_param = array('distribuidor' => $distribuidor);
                    if(isset($estado) && ($estado=="pendiente" || $estado=="aceptado" || $estado=="entregado")){
                        $arr_param = array('distribuidor' => $distribuidor,'estado'=>$estado);
                    }
                    $pedidos = $pedidoRepository->findBy(
                        $arr_param, // Critere
                        array('fechaPendente' => 'desc')        // Tri
                    );

                    $this->response($this->json($pedidos),200);
                }

            }
            // If invalid inputs "Bad Request" status message and reason
            $error = array('status' => "Failed", "msg" => "Invalid Data");
            $this->response($this->json($error), 400);
        }
        /*
        *	Simple acceptarPedido API
        *  acceptarPedido must be POST method
        *  Distribuidor : <DistribuidorID int>
        *  Pedido : <PedidoID int>
        */
        private function acceptarPedido(){
            if($this->get_request_method() != "POST"){
                $this->response('',406);
            }
            $idDistribuidor = trim($this->_request['id_distribuidor']);
            $idPedido = trim($this->_request['id_pedido']);

            if(!empty($idDistribuidor) && !empty($idPedido)){
                $distribuidorRepository = $this->em->getRepository('Distribuidor');
                $distribuidor = $distribuidorRepository->find($idDistribuidor);

                $pedidoRepository = $this->em->getRepository('Pedido');
                $pedido = $pedidoRepository->find($idPedido);
                if($distribuidor!=NULL && $pedido!=NULL){
                    $pedido->distribuidor = $distribuidor;
                    $this->em->merge($pedido);
                    $this->em->flush();
                    $this->response($this->json($pedido),200);
                }

            }
            // If invalid inputs "Bad Request" status message and reason
            $error = array('status' => "Failed", "msg" => "Invalid Data");
            $this->response($this->json($error), 400);
        }


        /**
         * @param $email
         * @param $userRepository
         * check if email allready exist
         */
        private function checkEmailExistAndNotNull($email,$userRepository){

            if(empty($email)){
                $error = array('status' => "Failed", "msg" => "El correo electrónico no puede estar vacío");
                $this->response($this->json($error), 400);
            }
            $user = $userRepository->findOneBy(array('email' => $email));
            if($user!=NULL){
                $error = array('status' => "Failed", "msg" => "Este correo electrónico ya está registrado");
                $this->response($this->json($error), 400);
            }
        }
        private function addPedido_v5(){
            if($this->get_request_method() != "POST"){
                $this->response('',406);
            }
            date_default_timezone_set('America/Lima');
            $number_of_the_week =(int) date('N');
            if($number_of_the_week>1 && $number_of_the_week<=5){
                $time_start = (int) date('G');
                //$time_start = explode(':',$time_start);
                if($time_start<18 && $time_start>=1){
                    $error = array('status' => "time", "msg" => "Estamos cerrados. Abierto de martes a sabado a partir de las 6:pm");
                    $this->response($this->json($error), 403);
                }
            }
            else if($number_of_the_week==6){ // 6 saturday
                $time_start = (int) date('G');
                //$time_start = explode(':',$time_start);
                if($time_start<18 && $time_start>=3){
                    $error = array('status' => "time", "msg" => "Estamos cerrados. Abierto de martes a sabado a partir de las 6:pm");
                    $this->response($this->json($error), 403);
                }

            }
            else if($number_of_the_week==7){ // 7 sunday
                $time_start = (int) date('G');
                //$time_start = explode(':',$time_start);
                if($time_start>=3){
                    $error = array('status' => "time", "msg" => "Estamos cerrados. Abierto de martes a sabado a partir de las 6:pm");
                    $this->response($this->json($error), 403);
                }
            }
            else{ // 1 monday
                $error = array('status' => "time", "msg" => "Estamos cerrados. Abierto de martes a sabado a partir de las 6:pm");
                $this->response($this->json($error), 403);
            }

            $validation_test = array('new_user'=>false,'new_dir'=>false);

            $dir = $this->_request['dir'];
            $user = $this->_request['user'];
            $pedido = $this->_request['order'];
            $payment_type_id = $this->_request['payment_id'];



            if(!empty($user) && !empty($dir) && !empty($pedido['pList']) && !empty($payment_type_id)){ // valid data
                $userRepository = $this->em->getRepository('User');
                $user_id = $user['id'];
                if($user_id!=0){ // add pedido with existant user
                    $user_temp = null;
                    $user_temp = $user;
                    // get the user
                    $user = $userRepository->find($user['id']);

                    // from where user come from and recomandation to user
                    if(isset($this->_request['coming_from'])){
                        $coming_from = $this->_request['coming_from'];
                        $user->come_from = $coming_from['type'];
                        if($coming_from['type']=="a-friend"){
                            // he recomande this email
                            $email_to_score_up = $coming_from['text'];
                            if($email_to_score_up!=$user->email){
                                $user_to_score_up = $userRepository->findOneBy(array('email' => $email_to_score_up));
                                if($user_to_score_up!=NULL){
                                    $user_to_score_up->puntos++;
                                    $this->em->merge($user_to_score_up);
                                }
                            }

                        }
                    }
                    // end from where user come from and recomandation to user

                    $user->apellidos = $user_temp['apellidos'];
                    $user->nombre = $user_temp['nombre'];
                    if($user->email!=$user_temp['email']){
                        $this->checkEmailExistAndNotNull($user_temp['email'],$userRepository);
                        $user->email = $user_temp['email'];
                    }


                    $user->celular = $user_temp['celular'];

                    $this->em->merge($user);

                    unset($user_temp);

                }
                else{

                    // create new user
                    $new_user = new User();
                    $new_user->apellidos = $user['apellidos'];
                    $new_user->nombre = $user['nombre'];
                    $new_user->active = 1;
                    $date = new \DateTime("now");
                    $new_user->fechaRegistro = $date->getTimestamp();
                    $new_user->email = $user['email'];
                    $new_user->password = $user['password'];
                    $new_user->celular = $user['celular'];

                    // from where user come from and recomandation to user
                    if(isset($this->_request['coming_from'])){

                        $coming_from = $this->_request['coming_from'];
                        $new_user->come_from = $coming_from['type'];

                        if($coming_from['type']=="a-friend"){
                            // he recomande this email
                            $email_to_score_up = $coming_from['text'];
                            if($email_to_score_up!=$new_user->email){
                                $user_to_score_up = $userRepository->findOneBy(array('email' => $email_to_score_up));
                                if($user_to_score_up!=NULL){
                                    $user_to_score_up->puntos++;
                                    $this->em->merge($user_to_score_up);
                                }
                            }

                        }
                    }
                    // end from where user come from and recomandation to user

                    $this->checkEmailExistAndNotNull($new_user->email,$userRepository);


                    $this->em->persist($new_user);
                    $validation_test['new_user'] = true; // valid is new user
                    $user = $new_user;
                }

                // get payment type
                $pagoTipoRepository = $this->em->getRepository('PagoTipo');
                $pagoTipo = $pagoTipoRepository->find($payment_type_id);
                if($payment_type_id==3){
                    $amount_obj = $this->_request['amount_obj'];
                    $amount = $amount_obj['amount'];
                }

                // get all bebidas
                $bebidaRepository = $this->em->getRepository('Bebida');
                $bebidas = $bebidaRepository->findAll();

                $idDir =  $dir['id'];  // check if new direccion or nor
                if($idDir!=0){ // is not a new Direccion
                    $dirRepository = $this->em->getRepository('Direccion');
                    $new_calle = $dir['calle'];
                    $new_distrito = $dir['distrito'];
                    $new_referencias = (isset($dir['referencias']) ? $dir['referencias'] : '');
                    $dir = $dirRepository->find($idDir);

                    $dir->referencias = $new_referencias;
                    $dir->calle = $new_calle;
                    $dir->distrito = $new_distrito;

                    $this->em->merge($dir);

                }
                else{ // it is a new dir

                    $name_slug = $this->slugify($dir['value']);
                    $referencias = (isset($dir['referencias']) ? $dir['referencias'] : '');
                    $dir = $this->initDireccion($dir['calle'],$referencias,$dir['value'],$dir['distrito'],$name_slug,$dir['value'],true);
                    $dir->user = $user;

                    if(isset($user->direcciones)){
                        foreach ($user->direcciones as $a_dir){ // if not new user check if address put allready exist
                            if($a_dir->slug==$name_slug){
                                $error = array('status' => "Failed", "msg" => "Este lugar ya lo registraste");
                                $this->response($this->json($error), 400);
                            }
                        }
                    }
                    $this->em->persist($dir);
                    $validation_test['new_dir'] = true; // valid is new user
                }

                $p = new Pedido();
                $date = new \DateTime("now");
                $p->fechaPendente = $date->getTimestamp();
                $p->estado = "pendiente";
                $p->user = $user;
                $p->pagoTipo = $pagoTipo;
                $p->direccion = $dir;
                if(isset($amount)){
                    $p->pagoEffectivoCantidad = $amount;
                }
                $this->em->persist($p);


                // create order
                $total_to_pay = 0;
                $free_delivery = false;
                for  ($i=0;$i<count($pedido['pList']);$i++){
                    $an_item = $pedido['pList'][$i];
                    $cpt = 0;
                    $trouve = false;
                    while ($cpt<count($bebidas) && $trouve==false){
                        if($an_item['p']['id']==$bebidas[$cpt]->id){
                            if(!$free_delivery && $bebidas[$cpt]->freeDelivery==true){
                                $free_delivery = true;
                            }
                            $pb = new PedidoBebida();
                            $pb->bebida = $bebidas[$cpt];
                            $pb->cantidad = (int)$an_item['q'];
                            $pb->pedido = $p;
                            $this->em->persist($pb);
                            $total_to_pay+= $pb->cantidad * floatval($pb->bebida->price);

                            $trouve = true;
                        }
                        else{
                            $cpt++;
                        }
                    }

                }

                if(!$free_delivery){
                    $total_to_pay+= 3.5; // + delivery charge
                }
                // check if amount > total to pay

                if(isset($amount)){
                    $amount = floatval($amount);
                    if($total_to_pay>$amount){
                        $error = array('status' => "Failed", "msg" => "El monto de efectivo debe ser superior al monto a pagar");
                        $this->response($this->json($error), 400);
                    }
                }
                $this->em->flush();
                if($validation_test['new_user'] && $validation_test['new_dir']){ // completly new
                    $array_dir = array();
                    $array_dir[] = $dir;
                    $user->direcciones = $array_dir;
                }
                else if(!$validation_test['new_user'] && $validation_test['new_dir']){ // direccion is new only

                    $user->direcciones[] = $dir;
                }

                //MOI TEST
                $pedidoRepository = $this->em->getRepository('Pedido');
                $lastOrder = $pedidoRepository->findOneBy(
                    array('user' => $user),// Critere
                    array('id' => 'desc'),// tri
                    1
                );
                $pedidoBebidaRepository = $this->em->getRepository('PedidoBebida');

                $pedido_items = $pedidoBebidaRepository->findBy(
                    array('pedido' => $lastOrder),// Critere
                    array('id' => 'asc')// tri
                );


                require_once 'libs/PHPMailer/PHPMailerAutoload.php';
                // email to clients
                $mail = new PHPMailer;

//$mail->SMTPDebug = 3;                               // Enable verbose debug output

                $mail->isSMTP();                                      // Set mailer to use SMTP
                $mail->Host = 'gator4168.hostgator.com';  // Specify main and backup SMTP servers
                $mail->SMTPAuth = true;                               // Enable SMTP authentication
                $mail->Username = 'pedidos@drinkapp.pe';                 // SMTP username
                $mail->Password = 'q*@aRBW#+*Hc';                           // SMTP password
                $mail->SMTPSecure = 'ssl';                            // Enable TLS encryption, `ssl` also accepted
                $mail->Port = 465;                                    // TCP port to connect to

                $mail->setFrom('pedidos@drinkapp.pe', 'Pedidos DrinkApp');
                $mail->addAddress($user->email, $user->nombre);     // Add a recipient

                $mail->addReplyTo('pedidos@drinkapp.pe', 'Information');
                //$mail->addCC('cc@example.com');
                //$mail->addBCC('bcc@example.com');

                //$mail->addAttachment('/var/tmp/file.tar.gz');         // Add attachments
                //$mail->addAttachment('/tmp/image.jpg', 'new.jpg');    // Optional name
                $mail->isHTML(false);                                  // Set email format to HTML

                $mail->Subject = 'DrinkApp Pedido Numero DAPP'.$lastOrder->id;
                $mail->Body    = 'Hola '.$user->nombre."\n";
                $mail->Body    .= 'Su orden numero DAPP'.$lastOrder->id.' ha sido confirmada.'."\n\n";
                $mail->Body    .= 'Resumen Pedido :'."\n";
                foreach ($pedido_items as $item){
                    $mail->Body    .=$item->cantidad.' X '.$item->bebida->nombre."\n";
                }
                $mail->Body    .="\n";
                $mail->Body    .= "Total a pagar : S/. ".number_format(($total_to_pay),2, '.', '');

                $mail->Body = utf8_decode($mail->Body);
                //$mail->AltBody = 'This is the body in plain text for non-HTML mail clients';

                $mail->send();
                //END MOI TEST
                // email to admin
                $mail2 = new PHPMailer;

//$mail->SMTPDebug = 3;                               // Enable verbose debug output

                $mail2->isSMTP();                                      // Set mailer to use SMTP
                $mail2->Host = 'gator4168.hostgator.com';  // Specify main and backup SMTP servers
                $mail2->SMTPAuth = true;                               // Enable SMTP authentication
                $mail2->Username = 'pedidos@drinkapp.pe';                 // SMTP username
                $mail2->Password = 'q*@aRBW#+*Hc';                           // SMTP password
                $mail2->SMTPSecure = 'ssl';                            // Enable TLS encryption, `ssl` also accepted
                $mail2->Port = 465;                                    // TCP port to connect to

                $mail2->setFrom('pedidos@drinkapp.pe', 'Pedidos DrinkApp');
                //$mail->addCC('cc@example.com');
                //$mail->addBCC('bcc@example.com');
                //$mail->addAttachment('/var/tmp/file.tar.gz');         // Add attachments
                //$mail->addAttachment('/tmp/image.jpg', 'new.jpg');    // Optional name
                $mail2->isHTML(false);                                  // Set email format to HTML

                $mail2->Subject = 'DrinkApp Pedido Numero DAPP'.$lastOrder->id;

                $mail2->Body    = 'Pedido de : '.$user->nombre.' '.$user->apellidos."\n";
                $mail2->Body    .= 'Orden numero DAPP'.$lastOrder->id.' ha sido confirmada.'."\n\n";
                $mail2->Body    .= 'Resumen Pedido :'."\n";
                foreach ($pedido_items as $item){
                    $mail2->Body    .=$item->cantidad.' X '.$item->bebida->nombre."\n";
                }
                $mail2->Body    .="\n";
                $mail2->Body    .= "Total a pagar : S/. ".number_format(($total_to_pay),2, '.', '');

                $mail2->addAddress('pedidos@drinkapp.pe');     // Add a recipient

                $mail2->send();

                $this->response($this->json($user),200);
            }
        }
        private function addPedido_v4(){
            if($this->get_request_method() != "POST"){
                $this->response('',406);
            }
            date_default_timezone_set('America/Lima');
            $number_of_the_week =(int) date('N');
            if($number_of_the_week>1 && $number_of_the_week<=5){
                $time_start = (int) date('G');
                //$time_start = explode(':',$time_start);
                if($time_start<18 && $time_start>=1){
                    $error = array('status' => "time", "msg" => "Estamos cerrados. Abierto de martes a sabado a partir de las 6:pm");
                    $this->response($this->json($error), 403);
                }
            }
            else if($number_of_the_week==6){ // 6 saturday
                $time_start = (int) date('G');
                //$time_start = explode(':',$time_start);
                if($time_start<18 && $time_start>=3){
                    $error = array('status' => "time", "msg" => "Estamos cerrados. Abierto de martes a sabado a partir de las 6:pm");
                    $this->response($this->json($error), 403);
                }

            }
            else if($number_of_the_week==7){ // 7 sunday
                $time_start = (int) date('G');
                //$time_start = explode(':',$time_start);
                if($time_start>=3){
                    $error = array('status' => "time", "msg" => "Estamos cerrados. Abierto de martes a sabado a partir de las 6:pm");
                    $this->response($this->json($error), 403);
                }
            }
            else{ // 1 monday
                $error = array('status' => "time", "msg" => "Estamos cerrados. Abierto de martes a sabado a partir de las 6:pm");
                $this->response($this->json($error), 403);
            }

            $validation_test = array('new_user'=>false,'new_dir'=>false);

            $dir = $this->_request['dir'];
            $user = $this->_request['user'];
            $pedido = $this->_request['order'];
            $payment_type_id = $this->_request['payment_id'];



            if(!empty($user) && !empty($dir) && !empty($pedido['pList']) && !empty($payment_type_id)){ // valid data
                $userRepository = $this->em->getRepository('User');
                $user_id = $user['id'];
                if($user_id!=0){ // add pedido with existant user
                    $user_temp = null;
                    $user_temp = $user;
                    // get the user
                    $user = $userRepository->find($user['id']);

                    // from where user come from and recomandation to user
                    if(isset($this->_request['coming_from'])){
                        $coming_from = $this->_request['coming_from'];
                        $user->come_from = $coming_from['type'];
                        if($coming_from['type']=="a-friend"){
                            // he recomande this email
                            $email_to_score_up = $coming_from['text'];
                            if($email_to_score_up!=$user->email){
                                $user_to_score_up = $userRepository->findOneBy(array('email' => $email_to_score_up));
                                if($user_to_score_up!=NULL){
                                    $user_to_score_up->puntos++;
                                    $this->em->merge($user_to_score_up);
                                }
                            }

                        }
                    }
                    // end from where user come from and recomandation to user

                    $user->apellidos = $user_temp['apellidos'];
                    $user->nombre = $user_temp['nombre'];
                    $user->celular = $user_temp['celular'];

                    $this->em->merge($user);

                    unset($user_temp);

                }
                else{

                    // create new user
                    $new_user = new User();
                    $new_user->apellidos = $user['apellidos'];
                    $new_user->nombre = $user['nombre'];
                    $new_user->active = 1;
                    $date = new \DateTime("now");
                    $new_user->fechaRegistro = $date->getTimestamp();
                    $new_user->email = $user['email'];
                    $new_user->password = $user['password'];
                    $new_user->celular = $user['celular'];

                    // from where user come from and recomandation to user
                    if(isset($this->_request['coming_from'])){

                        $coming_from = $this->_request['coming_from'];
                        $new_user->come_from = $coming_from['type'];

                        if($coming_from['type']=="a-friend"){
                            // he recomande this email
                            $email_to_score_up = $coming_from['text'];
                            if($email_to_score_up!=$new_user->email){
                                $user_to_score_up = $userRepository->findOneBy(array('email' => $email_to_score_up));
                                if($user_to_score_up!=NULL){
                                    $user_to_score_up->puntos++;
                                    $this->em->merge($user_to_score_up);
                                }
                            }

                        }
                    }
                    // end from where user come from and recomandation to user

                    $user = $userRepository->findOneBy(array('email' => $new_user->email));

                    if($user!=NULL){
                        $error = array('status' => "Failed", "msg" => "Este correo electrónico ya está registrado");
                        $this->response($this->json($error), 400);
                    }

                    $this->em->persist($new_user);
                    $validation_test['new_user'] = true; // valid is new user
                    $user = $new_user;
                }

                // get payment type
                $pagoTipoRepository = $this->em->getRepository('PagoTipo');
                $pagoTipo = $pagoTipoRepository->find($payment_type_id);
                if($payment_type_id==3){
                    $amount_obj = $this->_request['amount_obj'];
                    $amount = $amount_obj['amount'];
                }

                // get all bebidas
                $bebidaRepository = $this->em->getRepository('Bebida');
                $bebidas = $bebidaRepository->findAll();

                $idDir =  $dir['id'];  // check if new direccion or nor
                if($idDir!=0){ // is not a new Direccion
                    $dirRepository = $this->em->getRepository('Direccion');
                    $new_calle = $dir['calle'];
                    $new_distrito = $dir['distrito'];
                    $new_referencias = (isset($dir['referencias']) ? $dir['referencias'] : '');
                    $dir = $dirRepository->find($idDir);

                    $dir->referencias = $new_referencias;
                    $dir->calle = $new_calle;
                    $dir->distrito = $new_distrito;

                    $this->em->merge($dir);

                }
                else{ // it is a new dir

                    $name_slug = $this->slugify($dir['value']);
                    $referencias = (isset($dir['referencias']) ? $dir['referencias'] : '');
                    $dir = $this->initDireccion($dir['calle'],$referencias,$dir['value'],$dir['distrito'],$name_slug,$dir['value'],true);
                    $dir->user = $user;

                    if(isset($user->direcciones)){
                        foreach ($user->direcciones as $a_dir){ // if not new user check if address put allready exist
                            if($a_dir->slug==$name_slug){
                                $error = array('status' => "Failed", "msg" => "Este lugar ya lo registraste");
                                $this->response($this->json($error), 400);
                            }
                        }
                    }
                    $this->em->persist($dir);
                    $validation_test['new_dir'] = true; // valid is new user
                }

                $p = new Pedido();
                $date = new \DateTime("now");
                $p->fechaPendente = $date->getTimestamp();
                $p->estado = "pendiente";
                $p->user = $user;
                $p->pagoTipo = $pagoTipo;
                $p->direccion = $dir;
                if(isset($amount)){
                    $p->pagoEffectivoCantidad = $amount;
                }
                $this->em->persist($p);


                // create order
                $total_to_pay = 0;
                $free_delivery = false;
                for  ($i=0;$i<count($pedido['pList']);$i++){
                    $an_item = $pedido['pList'][$i];
                    $cpt = 0;
                    $trouve = false;
                    while ($cpt<count($bebidas) && $trouve==false){
                        if($an_item['p']['id']==$bebidas[$cpt]->id){
                            if(!$free_delivery && $bebidas[$cpt]->freeDelivery==true){
                                $free_delivery = true;
                            }
                            $pb = new PedidoBebida();
                            $pb->bebida = $bebidas[$cpt];
                            $pb->cantidad = (int)$an_item['q'];
                            $pb->pedido = $p;
                            $this->em->persist($pb);
                            $total_to_pay+= $pb->cantidad * floatval($pb->bebida->price);

                            $trouve = true;
                        }
                        else{
                            $cpt++;
                        }
                    }

                }

                if(!$free_delivery){
                    $total_to_pay+= 3.5; // + delivery charge
                }
                // check if amount > total to pay

                if(isset($amount)){
                    $amount = floatval($amount);
                    if($total_to_pay>$amount){
                        $error = array('status' => "Failed", "msg" => "El monto de efectivo debe ser superior al monto a pagar");
                        $this->response($this->json($error), 400);
                    }
                }
                $this->em->flush();
                if($validation_test['new_user'] && $validation_test['new_dir']){ // completly new
                    $array_dir = array();
                    $array_dir[] = $dir;
                    $user->direcciones = $array_dir;
                }
                else if(!$validation_test['new_user'] && $validation_test['new_dir']){ // direccion is new only

                    $user->direcciones[] = $dir;
                }

                //MOI TEST
                $pedidoRepository = $this->em->getRepository('Pedido');
                $lastOrder = $pedidoRepository->findOneBy(
                    array('user' => $user),// Critere
                    array('id' => 'desc'),// tri
                    1
                );
                $pedidoBebidaRepository = $this->em->getRepository('PedidoBebida');

                $pedido_items = $pedidoBebidaRepository->findBy(
                    array('pedido' => $lastOrder),// Critere
                    array('id' => 'asc')// tri
                );


                require_once 'libs/PHPMailer/PHPMailerAutoload.php';
                // email to clients
                $mail = new PHPMailer;

//$mail->SMTPDebug = 3;                               // Enable verbose debug output

                $mail->isSMTP();                                      // Set mailer to use SMTP
                $mail->Host = 'gator4168.hostgator.com';  // Specify main and backup SMTP servers
                $mail->SMTPAuth = true;                               // Enable SMTP authentication
                $mail->Username = 'pedidos@drinkapp.pe';                 // SMTP username
                $mail->Password = 'q*@aRBW#+*Hc';                           // SMTP password
                $mail->SMTPSecure = 'ssl';                            // Enable TLS encryption, `ssl` also accepted
                $mail->Port = 465;                                    // TCP port to connect to

                $mail->setFrom('pedidos@drinkapp.pe', 'Pedidos DrinkApp');
                $mail->addAddress($user->email, $user->nombre);     // Add a recipient

                $mail->addReplyTo('pedidos@drinkapp.pe', 'Information');
                //$mail->addCC('cc@example.com');
                //$mail->addBCC('bcc@example.com');

                //$mail->addAttachment('/var/tmp/file.tar.gz');         // Add attachments
                //$mail->addAttachment('/tmp/image.jpg', 'new.jpg');    // Optional name
                $mail->isHTML(false);                                  // Set email format to HTML

                $mail->Subject = 'DrinkApp Pedido Numero DAPP'.$lastOrder->id;
                $mail->Body    = 'Hola '.$user->nombre."\n";
                $mail->Body    .= 'Su orden numero DAPP'.$lastOrder->id.' ha sido confirmada.'."\n\n";
                $mail->Body    .= 'Resumen Pedido :'."\n";
                foreach ($pedido_items as $item){
                    $mail->Body    .=$item->cantidad.' X '.$item->bebida->nombre."\n";
                }
                $mail->Body    .="\n";
                $mail->Body    .= "Total a pagar : S/. ".number_format(($total_to_pay),2, '.', '');

                $mail->Body = utf8_decode($mail->Body);
                //$mail->AltBody = 'This is the body in plain text for non-HTML mail clients';

                $mail->send();
                //END MOI TEST
                // email to admin
                $mail2 = new PHPMailer;

//$mail->SMTPDebug = 3;                               // Enable verbose debug output

                $mail2->isSMTP();                                      // Set mailer to use SMTP
                $mail2->Host = 'gator4168.hostgator.com';  // Specify main and backup SMTP servers
                $mail2->SMTPAuth = true;                               // Enable SMTP authentication
                $mail2->Username = 'pedidos@drinkapp.pe';                 // SMTP username
                $mail2->Password = 'q*@aRBW#+*Hc';                           // SMTP password
                $mail2->SMTPSecure = 'ssl';                            // Enable TLS encryption, `ssl` also accepted
                $mail2->Port = 465;                                    // TCP port to connect to

                $mail2->setFrom('pedidos@drinkapp.pe', 'Pedidos DrinkApp');
                //$mail->addCC('cc@example.com');
                //$mail->addBCC('bcc@example.com');
                //$mail->addAttachment('/var/tmp/file.tar.gz');         // Add attachments
                //$mail->addAttachment('/tmp/image.jpg', 'new.jpg');    // Optional name
                $mail2->isHTML(false);                                  // Set email format to HTML

                $mail2->Subject = 'DrinkApp Pedido Numero DAPP'.$lastOrder->id;

                $mail2->Body    = 'Pedido de : '.$user->nombre.' '.$user->apellidos."\n";
                $mail2->Body    .= 'Orden numero DAPP'.$lastOrder->id.' ha sido confirmada.'."\n\n";
                $mail2->Body    .= 'Resumen Pedido :'."\n";
                foreach ($pedido_items as $item){
                    $mail2->Body    .=$item->cantidad.' X '.$item->bebida->nombre."\n";
                }
                $mail2->Body    .="\n";
                $mail2->Body    .= "Total a pagar : S/. ".number_format(($total_to_pay),2, '.', '');

                $mail2->addAddress('pedidos@drinkapp.pe');     // Add a recipient

                $mail2->send();

                $this->response($this->json($user),200);
            }
        }

        private function addPedido_v3(){
            if($this->get_request_method() != "POST"){
                $this->response('',406);
            }
            date_default_timezone_set('America/Lima');
            $number_of_the_week =(int) date('N');
            if($number_of_the_week>1 && $number_of_the_week<=5){
                $time_start = (int) date('G');
                //$time_start = explode(':',$time_start);
                if($time_start<18 && $time_start>=1){
                    $error = array('status' => "time", "msg" => "Estamos cerrados. abierto a partir de las 6:pm");
                    $this->response($this->json($error), 403);
                }
            }
            else{
                $time_start = (int) date('G');
                //$time_start = explode(':',$time_start);
                if($time_start<18 && $time_start>=3){
                    $error = array('status' => "time", "msg" => "Estamos cerrados. abierto a partir de las 6:pm");
                    $this->response($this->json($error), 403);
                }
            }

            $validation_test = array('new_user'=>false,'new_dir'=>false);

            $dir = $this->_request['dir'];
            $user = $this->_request['user'];
            $pedido = $this->_request['order'];
            $payment_type_id = $this->_request['payment_id'];



            if(!empty($user) && !empty($dir) && !empty($pedido['pList']) && !empty($payment_type_id)){ // valid data
                $userRepository = $this->em->getRepository('User');
                $user_id = $user['id'];
                if($user_id!=0){ // add pedido with existant user
                    $user_temp = null;
                    $user_temp = $user;
                    // get the user
                    $user = $userRepository->find($user['id']);

                    // from where user come from and recomandation to user
                    if(isset($this->_request['coming_from'])){
                        $coming_from = $this->_request['coming_from'];
                        $user->come_from = $coming_from['type'];
                        if($coming_from['type']=="a-friend"){
                            // he recomande this email
                            $email_to_score_up = $coming_from['text'];
                            if($email_to_score_up!=$user->email){
                                $user_to_score_up = $userRepository->findOneBy(array('email' => $email_to_score_up));
                                if($user_to_score_up!=NULL){
                                    $user_to_score_up->puntos++;
                                    $this->em->merge($user_to_score_up);
                                }
                            }

                        }
                    }
                    // end from where user come from and recomandation to user

                    $user->apellidos = $user_temp['apellidos'];
                    $user->nombre = $user_temp['nombre'];
                    $user->celular = $user_temp['celular'];

                    $this->em->merge($user);

                    unset($user_temp);

                }
                else{

                    // create new user
                    $new_user = new User();
                    $new_user->apellidos = $user['apellidos'];
                    $new_user->nombre = $user['nombre'];
                    $new_user->active = 1;
                    $date = new \DateTime("now");
                    $new_user->fechaRegistro = $date->getTimestamp();
                    $new_user->email = $user['email'];
                    $new_user->password = $user['password'];
                    $new_user->celular = $user['celular'];

                    // from where user come from and recomandation to user
                    if(isset($this->_request['coming_from'])){

                        $coming_from = $this->_request['coming_from'];
                        $new_user->come_from = $coming_from['type'];

                        if($coming_from['type']=="a-friend"){
                            // he recomande this email
                            $email_to_score_up = $coming_from['text'];
                            if($email_to_score_up!=$new_user->email){
                                $user_to_score_up = $userRepository->findOneBy(array('email' => $email_to_score_up));
                                if($user_to_score_up!=NULL){
                                    $user_to_score_up->puntos++;
                                    $this->em->merge($user_to_score_up);
                                }
                            }

                        }
                    }
                    // end from where user come from and recomandation to user

                    $user = $userRepository->findOneBy(array('email' => $new_user->email));

                    if($user!=NULL){
                        $error = array('status' => "Failed", "msg" => "Este correo electrónico ya está registrado");
                        $this->response($this->json($error), 400);
                    }

                    $this->em->persist($new_user);
                    $validation_test['new_user'] = true; // valid is new user
                    $user = $new_user;
                }

                // get payment type
                $pagoTipoRepository = $this->em->getRepository('PagoTipo');
                $pagoTipo = $pagoTipoRepository->find($payment_type_id);
                if($payment_type_id==3){
                    $amount_obj = $this->_request['amount_obj'];
                    $amount = $amount_obj['amount'];
                }

                // get all bebidas
                $bebidaRepository = $this->em->getRepository('Bebida');
                $bebidas = $bebidaRepository->findAll();

                $idDir =  $dir['id'];  // check if new direccion or nor
                if($idDir!=0){ // is not a new Direccion
                    $dirRepository = $this->em->getRepository('Direccion');
                    $new_calle = $dir['calle'];
                    $new_distrito = $dir['distrito'];
                    $new_referencias = (isset($dir['referencias']) ? $dir['referencias'] : '');
                    $dir = $dirRepository->find($idDir);

                    $dir->referencias = $new_referencias;
                    $dir->calle = $new_calle;
                    $dir->distrito = $new_distrito;

                    $this->em->merge($dir);

                }
                else{ // it is a new dir

                    $name_slug = $this->slugify($dir['value']);
                    $referencias = (isset($dir['referencias']) ? $dir['referencias'] : '');
                    $dir = $this->initDireccion($dir['calle'],$referencias,$dir['value'],$dir['distrito'],$name_slug,$dir['value'],true);
                    $dir->user = $user;

                    if(isset($user->direcciones)){
                        foreach ($user->direcciones as $a_dir){ // if not new user check if address put allready exist
                            if($a_dir->slug==$name_slug){
                                $error = array('status' => "Failed", "msg" => "Este lugar ya lo registraste");
                                $this->response($this->json($error), 400);
                            }
                        }
                    }
                    $this->em->persist($dir);
                    $validation_test['new_dir'] = true; // valid is new user
                }

                $p = new Pedido();
                $date = new \DateTime("now");
                $p->fechaPendente = $date->getTimestamp();
                $p->estado = "pendiente";
                $p->user = $user;
                $p->pagoTipo = $pagoTipo;
                $p->direccion = $dir;
                if(isset($amount)){
                    $p->pagoEffectivoCantidad = $amount;
                }
                $this->em->persist($p);


                // create order
                $total_to_pay = 0;
                for  ($i=0;$i<count($pedido['pList']);$i++){
                    $an_item = $pedido['pList'][$i];
                    $cpt = 0;
                    $trouve = false;
                    while ($cpt<count($bebidas) && $trouve==false){
                        if($an_item['p']['id']==$bebidas[$cpt]->id){

                            $pb = new PedidoBebida();
                            $pb->bebida = $bebidas[$cpt];
                            $pb->cantidad = (int)$an_item['q'];
                            $pb->pedido = $p;
                            $this->em->persist($pb);
                            $total_to_pay+= $pb->cantidad * floatval($pb->bebida->price);

                            $trouve = true;
                        }
                        else{
                            $cpt++;
                        }
                    }

                }
                $total_to_pay+= 3.5; // + delivery charge
                // check if amount > total to pay

                if(isset($amount)){
                    $amount = floatval($amount);
                    if($total_to_pay>$amount){
                        $error = array('status' => "Failed", "msg" => "El monto de efectivo debe ser superior al monto a pagar");
                        $this->response($this->json($error), 400);
                    }
                }
                $this->em->flush();
                if($validation_test['new_user'] && $validation_test['new_dir']){ // completly new
                    $array_dir = array();
                    $array_dir[] = $dir;
                    $user->direcciones = $array_dir;
                }
                else if(!$validation_test['new_user'] && $validation_test['new_dir']){ // direccion is new only

                    $user->direcciones[] = $dir;
                }

                //MOI TEST
                $pedidoRepository = $this->em->getRepository('Pedido');
                $lastOrder = $pedidoRepository->findOneBy(
                    array('user' => $user),// Critere
                    array('id' => 'desc'),// tri
                    1
                );
                $pedidoBebidaRepository = $this->em->getRepository('PedidoBebida');

                $pedido_items = $pedidoBebidaRepository->findBy(
                    array('pedido' => $lastOrder),// Critere
                    array('id' => 'asc')// tri
                );


                require_once 'libs/PHPMailer/PHPMailerAutoload.php';
                // email to clients
                $mail = new PHPMailer;

//$mail->SMTPDebug = 3;                               // Enable verbose debug output

                $mail->isSMTP();                                      // Set mailer to use SMTP
                $mail->Host = 'gator4168.hostgator.com';  // Specify main and backup SMTP servers
                $mail->SMTPAuth = true;                               // Enable SMTP authentication
                $mail->Username = 'pedidos@drinkapp.pe';                 // SMTP username
                $mail->Password = 'q*@aRBW#+*Hc';                           // SMTP password
                $mail->SMTPSecure = 'ssl';                            // Enable TLS encryption, `ssl` also accepted
                $mail->Port = 465;                                    // TCP port to connect to

                $mail->setFrom('pedidos@drinkapp.pe', 'Pedidos DrinkApp');
                $mail->addAddress($user->email, $user->nombre);     // Add a recipient

                $mail->addReplyTo('pedidos@drinkapp.pe', 'Information');
                //$mail->addCC('cc@example.com');
                //$mail->addBCC('bcc@example.com');

                //$mail->addAttachment('/var/tmp/file.tar.gz');         // Add attachments
                //$mail->addAttachment('/tmp/image.jpg', 'new.jpg');    // Optional name
                $mail->isHTML(false);                                  // Set email format to HTML

                $mail->Subject = 'DrinkApp Pedido Numero DAPP'.$lastOrder->id;
                $mail->Body    = 'Hola '.$user->nombre."\n";
                $mail->Body    .= 'Su orden numero DAPP'.$lastOrder->id.' ha sido confirmada.'."\n\n";
                $mail->Body    .= 'Resumen Pedido :'."\n";
                foreach ($pedido_items as $item){
                    $mail->Body    .=$item->cantidad.' X '.$item->bebida->nombre."\n";
                }
                $mail->Body    .="\n";
                $mail->Body    .= "Total a pagar : S/. ".number_format(($total_to_pay),2, '.', '');

                $mail->Body = utf8_decode($mail->Body);
                //$mail->AltBody = 'This is the body in plain text for non-HTML mail clients';

                $mail->send();
                //END MOI TEST
                // email to admin
                $mail2 = new PHPMailer;

//$mail->SMTPDebug = 3;                               // Enable verbose debug output

                $mail2->isSMTP();                                      // Set mailer to use SMTP
                $mail2->Host = 'gator4168.hostgator.com';  // Specify main and backup SMTP servers
                $mail2->SMTPAuth = true;                               // Enable SMTP authentication
                $mail2->Username = 'pedidos@drinkapp.pe';                 // SMTP username
                $mail2->Password = 'q*@aRBW#+*Hc';                           // SMTP password
                $mail2->SMTPSecure = 'ssl';                            // Enable TLS encryption, `ssl` also accepted
                $mail2->Port = 465;                                    // TCP port to connect to

                $mail2->setFrom('pedidos@drinkapp.pe', 'Pedidos DrinkApp');
                //$mail->addCC('cc@example.com');
                //$mail->addBCC('bcc@example.com');
                //$mail->addAttachment('/var/tmp/file.tar.gz');         // Add attachments
                //$mail->addAttachment('/tmp/image.jpg', 'new.jpg');    // Optional name
                $mail2->isHTML(false);                                  // Set email format to HTML

                $mail2->Subject = 'DrinkApp Pedido Numero DAPP'.$lastOrder->id;

                $mail2->Body    = 'Pedido de : '.$user->nombre.' '.$user->apellidos."\n";
                $mail2->Body    .= 'Orden numero DAPP'.$lastOrder->id.' ha sido confirmada.'."\n\n";
                $mail2->Body    .= 'Resumen Pedido :'."\n";
                foreach ($pedido_items as $item){
                    $mail2->Body    .=$item->cantidad.' X '.$item->bebida->nombre."\n";
                }
                $mail2->Body    .="\n";
                $mail2->Body    .= "Total a pagar : S/. ".number_format(($total_to_pay),2, '.', '');

                $mail2->addAddress('pedidos@drinkapp.pe');     // Add a recipient

                $mail2->send();

                $this->response($this->json($user),200);
            }

        }
        private function addPedido_v2(){
            if($this->get_request_method() != "POST"){
                $this->response('',406);
            }

			$validation_test = array('new_user'=>false,'new_dir'=>false);

            $dir = $this->_request['dir'];
            $user = $this->_request['user'];
            $pedido = $this->_request['order'];
            $payment_type_id = $this->_request['payment_id'];



            if(!empty($user) && !empty($dir) && !empty($pedido['pList']) && !empty($payment_type_id)){ // valid data
                $userRepository = $this->em->getRepository('User');
                $user_id = $user['id'];
                if($user_id!=0){ // add pedido with existant user
					$user_temp = null;
					$user_temp = $user;
                    // get the user
                    $user = $userRepository->find($user['id']);

                    // from where user come from and recomandation to user
                    if(isset($this->_request['coming_from'])){
                        $coming_from = $this->_request['coming_from'];
                        $user->come_from = $coming_from['type'];
                        if($coming_from['type']=="a-friend"){
                            // he recomande this email
                            $email_to_score_up = $coming_from['text'];
                            if($email_to_score_up!=$user->email){
                                $user_to_score_up = $userRepository->findOneBy(array('email' => $email_to_score_up));
                                if($user_to_score_up!=NULL){
                                    $user_to_score_up->puntos++;
                                    $this->em->merge($user_to_score_up);
                                }
                            }

                        }
                    }
                    // end from where user come from and recomandation to user

					$user->apellidos = $user_temp['apellidos'];
					$user->nombre = $user_temp['nombre'];
					$user->celular = $user_temp['celular'];

					$this->em->merge($user);

					unset($user_temp);

                }
                else{

                    // create new user
                    $new_user = new User();
                    $new_user->apellidos = $user['apellidos'];
                    $new_user->nombre = $user['nombre'];
                    $new_user->active = 1;
                    $date = new \DateTime("now");
                    $new_user->fechaRegistro = $date->getTimestamp();
                    $new_user->email = $user['email'];
                    $new_user->password = $user['password'];
                    $new_user->celular = $user['celular'];

                    // from where user come from and recomandation to user
                    if(isset($this->_request['coming_from'])){

                        $coming_from = $this->_request['coming_from'];
                        $new_user->come_from = $coming_from['type'];

                        if($coming_from['type']=="a-friend"){
                            // he recomande this email
                            $email_to_score_up = $coming_from['text'];
                            if($email_to_score_up!=$new_user->email){
                                $user_to_score_up = $userRepository->findOneBy(array('email' => $email_to_score_up));
                                if($user_to_score_up!=NULL){
                                    $user_to_score_up->puntos++;
                                    $this->em->merge($user_to_score_up);
                                }
                            }

                        }
                    }
                    // end from where user come from and recomandation to user

                    $user = $userRepository->findOneBy(array('email' => $new_user->email));

                    if($user!=NULL){
                        $error = array('status' => "Failed", "msg" => "Este correo electrónico ya está registrado");
                        $this->response($this->json($error), 400);
                    }

                    $this->em->persist($new_user);
					$validation_test['new_user'] = true; // valid is new user
                    $user = $new_user;
                }

                // get payment type
                $pagoTipoRepository = $this->em->getRepository('PagoTipo');
                $pagoTipo = $pagoTipoRepository->find($payment_type_id);
                if($payment_type_id==3){
                    $amount_obj = $this->_request['amount_obj'];
                    $amount = $amount_obj['amount'];
                }

                // get all bebidas
                $bebidaRepository = $this->em->getRepository('Bebida');
                $bebidas = $bebidaRepository->findAll();

                $idDir =  $dir['id'];  // check if new direccion or nor
                if($idDir!=0){ // is not a new Direccion
                    $dirRepository = $this->em->getRepository('Direccion');
					$new_calle = $dir['calle'];
					$new_distrito = $dir['distrito'];
					$new_referencias = (isset($dir['referencias']) ? $dir['referencias'] : '');
                    $dir = $dirRepository->find($idDir);

					$dir->referencias = $new_referencias;
					$dir->calle = $new_calle;
					$dir->distrito = $new_distrito;

					$this->em->merge($dir);

                }
                else{ // it is a new dir

                    $name_slug = $this->slugify($dir['value']);
                    $referencias = (isset($dir['referencias']) ? $dir['referencias'] : '');
                    $dir = $this->initDireccion($dir['calle'],$referencias,$dir['value'],$dir['distrito'],$name_slug,$dir['value'],true);
                    $dir->user = $user;

					if(isset($user->direcciones)){
						foreach ($user->direcciones as $a_dir){ // if not new user check if address put allready exist
							if($a_dir->slug==$name_slug){
								$error = array('status' => "Failed", "msg" => "Este lugar ya lo registraste");
								$this->response($this->json($error), 400);
							}
						}
					}
                    $this->em->persist($dir);
					$validation_test['new_dir'] = true; // valid is new user
                }

                $p = new Pedido();
                $date = new \DateTime("now");
                $p->fechaPendente = $date->getTimestamp();
                $p->estado = "pendiente";
                $p->user = $user;
                $p->pagoTipo = $pagoTipo;
                $p->direccion = $dir;
                if(isset($amount)){
                    $p->pagoEffectivoCantidad = $amount;
                }
				$this->em->persist($p);


                // create order
                $total_to_pay = 0;
                for  ($i=0;$i<count($pedido['pList']);$i++){
                    $an_item = $pedido['pList'][$i];
                    $cpt = 0;
                    $trouve = false;
                    while ($cpt<count($bebidas) && $trouve==false){
                        if($an_item['p']['id']==$bebidas[$cpt]->id){

                            $pb = new PedidoBebida();
                            $pb->bebida = $bebidas[$cpt];
                            $pb->cantidad = (int)$an_item['q'];
                            $pb->pedido = $p;
                            $this->em->persist($pb);
                            $total_to_pay+= $pb->cantidad * floatval($pb->bebida->price);

                            $trouve = true;
                        }
                        else{
                            $cpt++;
                        }
                    }

                }
                $total_to_pay+= 3.5; // + delivery charge
                // check if amount > total to pay

                if(isset($amount)){
                    $amount = floatval($amount);
                    if($total_to_pay>$amount){
                        $error = array('status' => "Failed", "msg" => "El monto de efectivo debe ser superior al monto a pagar");
                        $this->response($this->json($error), 400);
                    }
                }
                $this->em->flush();
				if($validation_test['new_user'] && $validation_test['new_dir']){ // completly new
					$array_dir = array();
					$array_dir[] = $dir;
					$user->direcciones = $array_dir;
				}
				else if(!$validation_test['new_user'] && $validation_test['new_dir']){ // direccion is new only

					$user->direcciones[] = $dir;
				}

				//MOI TEST
				$pedidoRepository = $this->em->getRepository('Pedido');
				$lastOrder = $pedidoRepository->findOneBy(
					array('user' => $user),// Critere
					array('id' => 'desc'),// tri
					1
				);
				$pedidoBebidaRepository = $this->em->getRepository('PedidoBebida');

				$pedido_items = $pedidoBebidaRepository->findBy(
					array('pedido' => $lastOrder),// Critere
					array('id' => 'asc')// tri
				);


				require_once 'libs/PHPMailer/PHPMailerAutoload.php';
                // email to clients
				$mail = new PHPMailer;

//$mail->SMTPDebug = 3;                               // Enable verbose debug output

				$mail->isSMTP();                                      // Set mailer to use SMTP
				$mail->Host = 'gator4168.hostgator.com';  // Specify main and backup SMTP servers
				$mail->SMTPAuth = true;                               // Enable SMTP authentication
				$mail->Username = 'pedidos@drinkapp.pe';                 // SMTP username
				$mail->Password = 'q*@aRBW#+*Hc';                           // SMTP password
				$mail->SMTPSecure = 'ssl';                            // Enable TLS encryption, `ssl` also accepted
				$mail->Port = 465;                                    // TCP port to connect to

				$mail->setFrom('pedidos@drinkapp.pe', 'Pedidos DrinkApp');
				$mail->addAddress($user->email, $user->nombre);     // Add a recipient

				$mail->addReplyTo('pedidos@drinkapp.pe', 'Information');
				//$mail->addCC('cc@example.com');
				//$mail->addBCC('bcc@example.com');

				//$mail->addAttachment('/var/tmp/file.tar.gz');         // Add attachments
				//$mail->addAttachment('/tmp/image.jpg', 'new.jpg');    // Optional name
				$mail->isHTML(false);                                  // Set email format to HTML

				$mail->Subject = 'DrinkApp Pedido Numero DAPP'.$lastOrder->id;
				$mail->Body    = 'Hola '.$user->nombre."\n";
				$mail->Body    .= 'Su orden numero DAPP'.$lastOrder->id.' ha sido confirmada.'."\n\n";
				$mail->Body    .= 'Resumen Pedido :'."\n";
				foreach ($pedido_items as $item){
					$mail->Body    .=$item->cantidad.' X '.$item->bebida->nombre."\n";
				}
                $mail->Body    .="\n";
                $mail->Body    .= "Total a pagar : S/. ".number_format(($total_to_pay),2, '.', '');

                $mail->Body = utf8_decode($mail->Body);
				//$mail->AltBody = 'This is the body in plain text for non-HTML mail clients';

				$mail->send();
				//END MOI TEST
                // email to admin
                $mail2 = new PHPMailer;

//$mail->SMTPDebug = 3;                               // Enable verbose debug output

                $mail2->isSMTP();                                      // Set mailer to use SMTP
                $mail2->Host = 'gator4168.hostgator.com';  // Specify main and backup SMTP servers
                $mail2->SMTPAuth = true;                               // Enable SMTP authentication
                $mail2->Username = 'pedidos@drinkapp.pe';                 // SMTP username
                $mail2->Password = 'q*@aRBW#+*Hc';                           // SMTP password
                $mail2->SMTPSecure = 'ssl';                            // Enable TLS encryption, `ssl` also accepted
                $mail2->Port = 465;                                    // TCP port to connect to

                $mail2->setFrom('pedidos@drinkapp.pe', 'Pedidos DrinkApp');
                //$mail->addCC('cc@example.com');
                //$mail->addBCC('bcc@example.com');
                //$mail->addAttachment('/var/tmp/file.tar.gz');         // Add attachments
                //$mail->addAttachment('/tmp/image.jpg', 'new.jpg');    // Optional name
                $mail2->isHTML(false);                                  // Set email format to HTML

                $mail2->Subject = 'DrinkApp Pedido Numero DAPP'.$lastOrder->id;

                $mail2->Body    = 'Pedido de : '.$user->nombre.' '.$user->apellidos."\n";
                $mail2->Body    .= 'Orden numero DAPP'.$lastOrder->id.' ha sido confirmada.'."\n\n";
                $mail2->Body    .= 'Resumen Pedido :'."\n";
                foreach ($pedido_items as $item){
                    $mail2->Body    .=$item->cantidad.' X '.$item->bebida->nombre."\n";
                }
                $mail2->Body    .="\n";
                $mail2->Body    .= "Total a pagar : S/. ".number_format(($total_to_pay),2, '.', '');

                $mail2->addAddress('pedidos@drinkapp.pe');     // Add a recipient

                $mail2->send();

                $this->response($this->json($user),200);
            }


        }
		/*
		Simple addPedido API
		addPedido must be POST method
		idUsurio:{int}
		pedido: {obj}
		pago:{TipoDePago} // valores possible string : effectivo ; visa ; mastercard
		favorito:{bool} // parametro opcional (default false) valores possible bool  : true ; false
		factura:{factura} //  parametro opcional
		pagoEffectivoCantidad // parametro opcional si pago por tarjeta
		nota:{string} // parametro opcional
		recibo:{bool} // parametro opcional valores possible bool  : factura ; boleta

		tipo_direccion:(string) // valores possible   : casa ; departamento ; trabajo ; otros
        calle:(string)
		referencias:(string)
		nombre:(string) // parametro opcional si no seleccionado "otros"
		distrito:(string)
		numero:(string)
		piso_apt:(string)
		telefono:(string)
		 */
		private function addPedido(){
			if($this->get_request_method() != "POST"){
				$this->response('',406);
			}
            $dir = $this->_request['dir'];
            $user = $this->_request['user'];
            $pedido = $this->_request['order'];
            $payment_type_id = $this->_request['payment_id'];
			$error = array('status' => "Failed", "msg" => $payment_type_id);
			$this->response($this->json($error), 400);

			// CONFIG parameter
			$config = parse_ini_file("config/ini/basic_config.ini",true);

			$facturaStr = $config['factura'];
			$boletaStr = $config['boleta'];
			$tiempoDeliveryMax = $config["tiempoDeliveryMax"];
			$precioDelivery = $config["deliveryPrice"];
			// POST parameter
			$idUsuario = trim($this->_request['idUsuario']);
			$pedido = trim($this->_request['pedido']);
			$pago = trim($this->_request['pago']);
			$favorito = trim($this->_request['favorito']);
			$pagoEffectivoCantidad = trim($this->_request['pagoEffectivoCantidad']);
			$factura = trim($this->_request['factura']);
			$nota = trim($this->_request['nota']);
			$recibo = trim($this->_request['recibo']);
            $direccion = trim($this->_request['direccion']);
            $direccion = json_decode($direccion,true);
			if(!empty($idUsuario) and !empty($pedido) and !empty($pago) and $direccion!=NULL &&
                isset($config["tipo_direccion"][$direccion["tipo_direccion"]])){

				$userRepository = $this->em->getRepository('User');
				$direccionRepository = $this->em->getRepository('Direccion');
				$bebidaRepository = $this->em->getRepository('Bebida');
				$pagoTipoRepository = $this->em->getRepository('PagoTipo');
				$user = $userRepository->find($idUsuario);
				if($user!=NULL){
                    $calle = trim($direccion['calle']);
                    $referencias = trim($direccion['referencias']);
                    $tipo_direccion = trim($direccion['tipo_direccion']);

                    // get name we allreay know for casa ; departamento ; trabajo
                    if(isset($config["tipo_direccion"][$tipo_direccion]) && $config["tipo_direccion"][$tipo_direccion]!="otros"){
                        $nombre = $config["tipo_direccion"][$tipo_direccion];
                    }
                    // otherwise get his name for this address
                    else{
                        $nombre = strtolower(trim($direccion['nombre']));
                    }

                    $distrito = trim($direccion['distrito']);
                    $numero = trim($direccion['numero']);
                    $piso_apt = trim($direccion['piso_apt']);
                    $telefono = trim($direccion['telefono']);
                    // check user direcciones
                    $trouve = false;
                    $cpt = 0;
                    while($trouve==false && $cpt<count($user->direcciones)){
                        $a_dir = $user->direcciones[$cpt];
                        if($a_dir->nombre==$nombre){
                            $the_dir = $direccionRepository->find($a_dir->id);
                            $the_dir->calle = $calle;
                            $the_dir->referencias = $referencias;
                            $the_dir->numero = $numero;
                            $the_dir->piso_apt = $piso_apt;
                            $the_dir->distrito = $distrito;
                            $the_dir->telefono = $telefono;
                            $this->em->merge($the_dir);
                            $trouve = true;
                        }
                        $cpt++;
                    }
                    if($trouve==false){
                        $the_dir = $this->initDireccion($calle,$referencias,$nombre,$distrito,$numero,$piso_apt,$telefono);
                        $the_dir->user = $user;
                        $this->em->persist($the_dir);
                    }
                    //end check direcciones
                    $bebidas = $bebidaRepository->findAll();
					$arrBebidasCantidad = json_decode($pedido,TRUE);
					if($arrBebidasCantidad!=NULL){
						$p = new Pedido();
						if(isset($favorito) && !empty($favorito)){
							$p->favorito = filter_var($favorito, FILTER_VALIDATE_BOOLEAN);
						}
						$date = new \DateTime("now");
						$p->fechaPendente = $date->getTimestamp();
						$date = new \DateTime("now");
						$date->modify('+'.$tiempoDeliveryMax.' minutes');
						$p->estado = "pendiente";
						$p->fechaDeEntrega = $date->getTimestamp();
						$p->user = $user;
						$pt = $pagoTipoRepository->findOneBy(array('nombre' => $pago));
						$p->pagoTipo = $pt;
                        $p->direccion = $the_dir;

						if(isset($factura) && !empty($factura) && $recibo==$facturaStr){
							$factura = json_decode($factura);
							if($factura!=NULL && !empty($factura->razon_social) && !empty($factura->ruc)){
								$fac = new Factura();
								$fac->razon_social = trim($factura->razon_social);
								$fac->ruc = trim($factura->ruc);
								$p->factura = $fac;
							}
							else{
								// If invalid inputs "Bad Request" status message and reason
								$error = array('status' => "Failed", "msg" => "Invalid Factura");
								$this->response($this->json($error), 400);
							}
						}
						$p->nota = $nota;
						$p->precioDelivery = (float)$precioDelivery;

						if((!isset($pagoEffectivoCantidad) || empty($pagoEffectivoCantidad)) && $recibo==$boletaStr){
							// If invalid inputs "Bad Request" status message and reason
							$error = array('status' => "Failed", "msg" => "Must have Cash");
							$this->response($this->json($error), 400);

						}
						$p->pagoEffectivoCantidad = (float)$pagoEffectivoCantidad;
						$p->recibo = $recibo;

						$this->em->persist($p);
						// work on Pedido
						foreach ($arrBebidasCantidad as $idB => $cantidad){
							$cpt = 0;
							$trouve = false;
							while ($cpt<count($bebidas) && $trouve==false){
								if($bebidas[$cpt]->id==(int)$idB){
									$pm = new PedidoBebida();
									$pm->bebida = $bebidas[$cpt];
									$pm->cantidad = (int)$cantidad;
									$pm->pedido = $p;
									$this->em->persist($pm);
									$trouve = true;
								}
								else $cpt++;
							}
						}
						$this->em->flush();
                        unset($p->user->direcciones);
						$this->response($this->json($p),200);
					}
					// If invalid inputs "Bad Request" status message and reason
					$error = array('status' => "Failed", "msg" => "Invalid Pedido");
					$this->response($this->json($error), 400);
				}
				// If invalid inputs "Bad Request" status message and reason
				$error = array('status' => "Failed", "msg" => "Invalid User");
				$this->response($this->json($error), 400);
			}
			// If invalid inputs "Bad Request" status message and reason
			$error = array('status' => "Failed", "msg" => "Invalid Data");
			$this->response($this->json($error), 400);
		}

		/*
		 *	Encode response into JSON
		*/
		private function json($data){
				$serializer = JMS\Serializer\SerializerBuilder::create()->build();
				return $serializer->serialize($data, 'json');

		}

	}

	// Initiiate Library

	$api = new API($entityManager);
	$api->processApi();
?>