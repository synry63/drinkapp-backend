<?php
use Doctrine\Common\Persistence\Event\LifecycleEventArgs;
use Doctrine\ORM\Event\PostFlushEventArgs;
class EventListener
{
    public $pedido_item = array();
    public function postFlush(PostFlushEventArgs $args)
    {
        /*require_once 'libs/PHPMailer/PHPMailerAutoload.php';

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
        $mail->addAddress('synry63@gmail.com');     // Add a recipient

        $mail->addReplyTo('pedidos@drinkapp.pe', 'Information');
        //$mail->addCC('cc@example.com');
        //$mail->addBCC('bcc@example.com');

        //$mail->addAttachment('/var/tmp/file.tar.gz');         // Add attachments
        //$mail->addAttachment('/tmp/image.jpg', 'new.jpg');    // Optional name
        $mail->isHTML(false);                                  // Set email format to HTML

        $mail->Subject = 'flush';
        $mail->Body    = 'Hola ';
        //$mail->AltBody = 'This is the body in plain text for non-HTML mail clients';

        $mail->send();*/
    }
    public function postPersist(LifecycleEventArgs $args)
    {
        $entity = $args->getObject();
        $entityManager = $args->getObjectManager();
        if ($entity instanceof PedidoBebida) {

            /*$this->pedido_item[] = $entity;
            require_once 'libs/PHPMailer/PHPMailerAutoload.php';

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
                    $mail->addAddress('synry63@gmail.com');     // Add a recipient

                    $mail->addReplyTo('pedidos@drinkapp.pe', 'Information');
                    //$mail->addCC('cc@example.com');
                    //$mail->addBCC('bcc@example.com');

                    //$mail->addAttachment('/var/tmp/file.tar.gz');         // Add attachments
                    //$mail->addAttachment('/tmp/image.jpg', 'new.jpg');    // Optional name
                    $mail->isHTML(false);                                  // Set email format to HTML

                    $mail->Subject = 'PedidoBebida';
                    $mail->Body    = 'Hola '.$this->pedido_item[0]->cantidad;
                    //$mail->AltBody = 'This is the body in plain text for non-HTML mail clients';

                    $mail->send();*/
        }
        else if ($entity instanceof User) {
            require_once 'libs/PHPMailer/PHPMailerAutoload.php';

            $mail = new PHPMailer;

            //$mail->SMTPDebug = 3;                               // Enable verbose debug output

            $mail->isSMTP();                                      // Set mailer to use SMTP
            $mail->Host = 'gator4168.hostgator.com';  // Specify main and backup SMTP servers
            $mail->SMTPAuth = true;                               // Enable SMTP authentication
            $mail->Username = 'noreplay@drinkapp.pe';                 // SMTP username
            $mail->Password = 'V)Fok*TdC$q.';                           // SMTP password
            $mail->SMTPSecure = 'ssl';                            // Enable TLS encryption, `ssl` also accepted
            $mail->Port = 465;                                    // TCP port to connect to

            $mail->setFrom('noreplay@drinkapp.pe', 'DrinkApp');
            $mail->addAddress($entity->email,$entity->nombre);     // Add a recipient
            //$mail->addCC('cc@example.com');
            //$mail->addBCC('bcc@example.com');

            //$mail->addAttachment('/var/tmp/file.tar.gz');         // Add attachments
            //$mail->addAttachment('/tmp/image.jpg', 'new.jpg');    // Optional name
            $mail->isHTML(false);                                  // Set email format to HTML

            $mail->Subject = 'Bienvenido a DrinkApp';
            $mail->Body    = '¡Hola '.$entity->nombre.' '.$entity->apellidos.'!'."\n\n";
            $mail->Body    .= 'Estamos encantados de darte la bienvenida a Drinkapp.'."\n";
            $mail->Body    .= 'Una aplicación donde podrás ordenar fácilmente Licores, cigarrillos, snacks y demás desde tu Smartphone.'."\n";
            $mail->Body    .= 'Dentro de la ciudad de Lima, recibirás los pedidos en la puerta de tu casa o punto de reunión.'."\n\n";
            $mail->Body    .= 'Drinkapp se encuentra disponible en San Isidro, San Borja, Miraflores y Surco.'."\n";
            $mail->Body    .= 'Muy Pronto también en tu distrito.'."\n";
            $mail->Body    .= '¡Haz tu orden ahora mismo!'."\n";
            $mail->Body    .= '¡Qué Empiece la Diversión!'."\n";

            $mail->Body = utf8_decode($mail->Body);
            //$mail->AltBody = 'This is the body in plain text for non-HTML mail clients';

            /*if(!$mail->send()) {
                //echo 'Message could not be sent.';
                //echo 'Mailer Error: ' . $mail->ErrorInfo;
                file_put_contents('error.txt','Mailer Error: ' . $mail->ErrorInfo);
            } else {
                file_put_contents('error.txt','Mail sent OK');
            }*/
            $mail->send();
        }
		else if ($entity instanceof Pedido) {
/*            //$pedido = $entity->pedido;
            $pedidoBebidaRepository = $entityManager->getRepository('PedidoBebida');
            $pedido_items = $pedidoBebidaRepository->findBy(
                array('pedido' => $entity),// Critere
                array('id' => 'asc')// tri
            );
            //$test = $pedidoBebidaRepository->findAll();
            //$test = json_encode($pedido_items);

            require_once 'libs/PHPMailer/PHPMailerAutoload.php';

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
            $mail->addAddress($entity->user->email, $entity->user->nombre);     // Add a recipient

            $mail->addReplyTo('pedidos@drinkapp.pe', 'Information');
            //$mail->addCC('cc@example.com');
            //$mail->addBCC('bcc@example.com');

            //$mail->addAttachment('/var/tmp/file.tar.gz');         // Add attachments
            //$mail->addAttachment('/tmp/image.jpg', 'new.jpg');    // Optional name
            $mail->isHTML(false);                                  // Set email format to HTML

            $mail->Subject = 'after DrinkApp Pedido Numero DAPP'.$entity->id;
            $mail->Body    = 'Hola '.$entity->user->nombre."\n";
            $mail->Body    .= 'Su orden numero DAPP'.$entity->id.' ha sido confirmada.'."\n";
            $mail->Body    .= 'test quantity ='.$pedido_items[0]->cantidad;
            //$mail->AltBody = 'This is the body in plain text for non-HTML mail clients';

            $mail->send();
*/
			/*require_once 'libs/PHPMailer/PHPMailerAutoload.php';

            $mail = new PHPMailer;
            //$mail->SMTPDebug = 3;                               // Enable verbose debug output
            $mail->isSMTP();                                      // Set mailer to use SMTP
            $mail->Host = 'smtp.gmail.com';  // Specify main and backup SMTP servers
            $mail->SMTPAuth = true;                               // Enable SMTP authentication
            $mail->Username = 'synry63@gmail.com';                 // SMTP username
            $mail->Password = 'Adeline63';                           // SMTP password
            $mail->SMTPSecure = 'tls';                            // Enable TLS encryption, `ssl` also accepted
            $mail->Port = 587;                                    // TCP port to connect to

            $mail->From = 'synry63@gmail.com'; // $entity->email
            $mail->FromName = 'Mailer';
            $mail->addAddress('synry63@gmail.com', 'Patrick');     // Add a recipient
            $mail->addReplyTo('info@example.com', 'Information');

            $mail->isHTML(true);                                  // Set email format to HTML

            $mail->Subject = 'Here is the subject';
            $mail->Body    = 'Validation link <a href="http://192.168.1.253/quickdrink/registerDistribuidorConfirmacion?registerKey='.$entity->registerKey.'"> HERE </a>';
            $mail->AltBody = 'This is the body in plain text for non-HTML mail clients';

            if(!$mail->send()) {
                //echo 'Message could not be sent.';
                //echo 'Mailer Error: ' . $mail->ErrorInfo;
                file_put_contents("tsaw","Message could not be sent.");
            } else {
                file_put_contents("tsaw","Message has been sent");
            }
		*/
		}
    }
}