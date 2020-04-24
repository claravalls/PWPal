<?php


namespace SallePW\SlimApp\Controller;

use DateTime;
use Psr\Container\ContainerInterface;
use SallePW\SlimApp\Model\User;
use Slim\Psr7\Request;
use Slim\Psr7\Response;
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\Exception;

class ValidateController
{
    private ContainerInterface $container;

    public function __construct(ContainerInterface $container)
    {
        $this->container = $container;
    }
    public function validateUser(Request $request, Response $response): Response
    {
        $data = $request->getParsedBody();

        //$messages = $this->container->get('flash')->getMessages();

        //$notifications = $messages['notifications'] ?? [];
        $type = $data['action'] ?? '';

        if($type == "up"){
            $errors = $this->isValid($data['email'], $data['password'], $data['birthday']);
            if(empty($errors))
            {
                $user = new User(
                    $data['email'] ?? '',
                    $data['password'] ?? '',
                    $data['phone'] ?? '',
                    new DateTime(),
                    new DateTime(),
                    new DateTime()
                );
                $this->sendEmail($data['email']);
                header("Location: ./sign-in");
            }

            return $this->container->get('view')->render(
                $response,
                'signup.twig',
                [
                    'errors' => $errors
                ]
            );
        }
        else if ($type == "in"){
            $errors = $this->isValid($data['email'], $data['password'], NULL);
            if(empty($errors))
            {
                //COMPROVAR A LA DATABASE

            }
            header("Location: ./");

            return $this->container->get('view')->render(
                $response,
                'signIN.twig',
                [
                    'errors' => $errors
                ]
            );
        }
    }

    function isValid(?string $email, ?string $password, ?string $birthday)
    {
        $errors = [];

        if ($this->validEmail($email)==false) {
            $errors[] = sprintf('The email %s is not valid', $email);
        }

        if (!preg_match('/^(?=.*\d)(?=.*[A-Z])(?=.*[a-z])[0-9A-Za-z!-\/]{1,}$/', $password)) {
            $errors[] = sprintf('The password must contain numbers and upper and lower case letters');
        }
        if (strlen($password) < 5) {
            $errors[] = sprintf('The password should be longer than or equal to 5 characters');
        }
        if($birthday != NULL && $this->validateAge($birthday) == false)
        {
            $errors[] = 'Only users of legal age (more than 18 years) can be registered';
        }
        return $errors;
    }

    public function validEmail(?string $email){

        if(filter_var($email, FILTER_VALIDATE_EMAIL) === false){
            return FALSE;
        }

        $domain = explode("@", $email, 2);

        if($domain[1] == "salle.url.edu")
        {
            return true;
        }
        return false;

    }

    public function validateAge($birthday, $age = 18)
    {
        if(is_string($birthday)) {
            $birthday = strtotime($birthday);
        }

        if(time() - $birthday < $age * 31536000)  {
            return false;
        }
        return true;
    }

    public function sendEmail($email)
    {
        // Instantiation and passing `true` enables exceptions
        $mail = new PHPMailer(true);
        $token = md5(rand(0, 1000), true);
        try {
            //Server settings
            $mail->SMTPDebug = SMTP::DEBUG_SERVER;                      // Enable verbose debug output
            $mail->isSMTP();                                            // Send using SMTP
            $mail->Host       = 'smtp.mailtrap.io';                    // Set the SMTP server to send through
            $mail->Username = '48693ab62ec89e';
            $mail->Password = 'bb3226c89d3f90';
            $mail->Port = 2525;                                 // TCP port to connect to
            $mail->SMTPAuth = true;

            //Recipients
            $mail->setFrom($email, 'Mailer');
            $mail->addReplyTo($email, 'Information');
            $mail->addCC($email);
            $mail->addBCC($email);

            // Content
            $mail->isHTML(true);                                  // Set email format to HTML
            $mail->Subject = 'Activation Email';
            $mail->Body    = 'For activation click the link <b>pwpay.test/activate?token='.$token.'</b>';
            $mail->AltBody = 'This is the body in plain text for non-HTML mail clients';

            $mail->send();
            $errors[] =  'Message has been sent';
        }catch (Exception $e) {
            $errors[] =  "Message could not be sent. Mailer Error: {$mail->ErrorInfo}";
        }
    }
}