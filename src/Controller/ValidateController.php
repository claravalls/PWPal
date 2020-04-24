<?php


namespace SallePW\SlimApp\Controller;

use DateTime;
use Psr\Container\ContainerInterface;
use SallePW\SlimApp\Model\User;
use SallePW\SlimApp\Repository\MySQLUserRepository;
use Slim\Psr7\Request;
use Slim\Psr7\Response;
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\Exception;
use function DI\get;

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
            $errors = $this->isValid($data['email'], $data['password'], $data['birthday'], $data['phone']);
            if(empty($errors))
            {
                $userComprovar = $this->container->get('user_repository')->search($data['email']);
                if($userComprovar == NULL) {

                    $password = md5($data['password']);
                    $dateBirthday = DateTime::createFromFormat('Y-m-d', $data['birthday']);
                    $user = new User(
                        $data['email'] ?? '',
                        $password ?? '',
                        $data['phone'] ?? '',
                        $dateBirthday,
                        new DateTime(),
                        new DateTime(),
                        false
                    );

                    $this->sendEmail($data['email']);
                    $this->container->get('user_repository')->save($user);

                    header("Location: ./sign-in");
                }else{
                    $errors[] = sprintf('This email is already in use.');
                }
            }

            return $this->container->get('view')->render(
                $response,
                'signup.twig',
                [
                    'errors' => $errors,
                    'email' => $data['email'],
                    'password' => $data['password'],
                    'birthday' => $data['birthday'],
                    'phone' => $data['phone']
                ]
            );
        }
        else if ($type == "in"){
            $errors = $this->isValid($data['email'], $data['password'], NULL, NULL);
            if(empty($errors))
            {
                $user = $this->container->get('user_repository')->search($data['email']);
                if($user->id() > 0 && $user->isActive() && $this->checkPassword($user->password(), $data['password'])){
                    // TODO: redirigir l'usuari al perfil
                    header("Location: ./");
                }
                $errors[] = "Incorrect credentials";
            }

            return $this->container->get('view')->render(
                $response,
                'signin.twig',
                [
                    'errors' => $errors,
                    'email' => $data['email'],
                    'password' => $data['password']
                ]
            );
        }
    }

    function isValid(?string $email, ?string $password, ?string $birthday, ?string $phone)
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
        if($this->validatePhoneNumber($phone) == false)
        {
            $errors[] = 'Incorrect format of phone number';
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

    function validatePhoneNumber($phone)
    {
        if(strlen($phone) ==0) {
            return true;
        }else {
            $filtered_phone_number = filter_var($phone, FILTER_SANITIZE_NUMBER_INT);
            if ($phone != NULL) {
                if (strlen($filtered_phone_number) < 10 || strlen($filtered_phone_number) > 14) {
                    return false;
                } else {
                    if (substr($filtered_phone_number, 0, 3) == '+34') {
                        return true;
                    } else {
                        return false;
                    }
                }
            }
        }
    }

    public function sendEmail($email)
    {
        // Instantiation and passing `true` enables exceptions
        $mail = new PHPMailer(true);
        $token = md5(rand(0, 1000));
        try {
            //Server settings
            //$mail->SMTPDebug = SMTP::DEBUG_SERVER;                      // Enable verbose debug output
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
            $url = 'pwpay.test/activate?token='.$token.'';
            $mail->Body    = 'For activation click the link <a href='.$url.'></a>';
            $mail->AltBody = 'This is the body in plain text for non-HTML mail clients';

            $mail->send();
            $errors[] =  'Message has been sent';
        }catch (Exception $e) {
            $errors[] =  "Message could not be sent. Mailer Error: {$mail->ErrorInfo}";
        }
    }

    function checkPassword (String $hash_pswd, String $pswd){
        return md5($pswd) == $hash_pswd;
    }
}