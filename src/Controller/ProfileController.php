<?php

namespace SallePW\SlimApp\Controller;

use DateTime;
use Psr\Container\ContainerInterface;
use SallePW\SlimApp\Model\User;
use Slim\Psr7\Request;
use Slim\Psr7\Response;
use function DI\get;

final class ProfileController
{
    private ContainerInterface $container;

    public function __construct(ContainerInterface $container)
    {
        $this->container = $container;
    }

    public function showProfile(Request $request, Response $response):Response
    {
        /*if (!isset($_SESSION['user'])){

            header("Location: /sign-in");
        }*/
        //$user = $_SESSION['user'];
        $dateBirthday = DateTime::createFromFormat('Y-m-d', "1997-07-12");
        $date = DateTime::createFromFormat('Y-m-d', "2020-05-12");
        $create = DateTime::createFromFormat('Y-m-d',  "2020-05-12");
        $user = new User("daniel@salle.url.edu", "123345Dmez", "+34612123123", $dateBirthday,  $date,  $create,'https://moonvillageassociation.org/wp-content/uploads/2018/06/default-profile-picture1.jpg', 20, "1232312312313", true);
        return $this->container->get('view')->render(
            $response,
            'profile.twig',
            [
                'email' => $user->email(),
                'birthday' => $user->birthday()->format('Y-m-d'),
                'phone' => $user->telefon(),
                'photo' => $user->photo()
            ]
        );
    }

    public function updateProfile(Request $request, Response $response):Response
    {
        /*if (!isset($_SESSION['user'])){

            header("Location: /sign-in");
        }*/
        //$user = $_SESSION['user'];

        $data = $request->getParsedBody();


        $dateBirthday = DateTime::createFromFormat('Y-m-d', "1997-07-12");
        $date = DateTime::createFromFormat('Y-m-d', "2020-05-12");
        $create = DateTime::createFromFormat('Y-m-d',  "2020-05-12");
        $user = new User("daniel@salle.url.edu", "123345Dmez", "+34612123123", $dateBirthday,  $date,  $create,'https://moonvillageassociation.org/wp-content/uploads/2018/06/default-profile-picture1.jpg', 20, "1232312312313", true);

        $errors = $this->isValid($data['photo'], $data['phone']);

        if(empty($errors))
        {
            $userComprovar = $this->container->get('user_repository')->search($user->email(), "email");
            if($userComprovar->id() > 0) {
                $this->container->get('user_repository')->editProfile($data['phone'], $data['photo'] ?? "", $user->email());
            }
        }

        return $this->container->get('view')->render(
            $response,
            'profile.twig',
            [
                'errors' => $errors,
                'email' => $user->email(),
                'birthday' => $user->birthday()->format('Y-m-d'),
                'phone' => $data['phone'],
                'photo' => $data['photo']
            ]
        );
    }

    public function showProfileSecurity(Request $request, Response $response):Response
    {
        /*if (!isset($_SESSION['user'])){

            header("Location: /sign-in");
        }*/
        //$user = $_SESSION['user'];
        return $this->container->get('view')->render(
            $response,
            'security.twig',
            []
        );
    }

    public function updateProfileSecurity(Request $request, Response $response):Response
    {
        /*if (!isset($_SESSION['user'])){

            header("Location: /sign-in");
        }
        $user = $_SESSION['user'];*/
        $data = $request->getParsedBody();

        $dateBirthday = DateTime::createFromFormat('Y-m-d', "1997-07-12");
        $date = DateTime::createFromFormat('Y-m-d', "2020-05-12");
        $create = DateTime::createFromFormat('Y-m-d',  "2020-05-12");
        $user = new User("dani@salle.url.edu", md5("1234Dani"), "+34612123123", $dateBirthday,  $date,  $create,'https://moonvillageassociation.org/wp-content/uploads/2018/06/default-profile-picture1.jpg', 20, "1232312312313", true);

        $errors = $this->isPasswordValid($user->password(), $data['passwordold'],$data['passwordnew'], $data['checkpass']);

        if(empty($errors))
        {
            $userComprovar = $this->container->get('user_repository')->search($user->email(), "email");
            if($userComprovar->id() > 0) {
                $newpassword = md5($data['passwordnew']);
                $this->container->get('user_repository')->changePassword($newpassword, $user->email());
                $errors[] = 'Password changed correctly';
            }
        }
        return $this->container->get('view')->render(
            $response,
            'security.twig',
            [
                'errors' => $errors
            ]
        );
    }

    function isValid(?string $photo, ?string $phone)
    {
        $errors = [];

        if($this->validatePhoneNumber($phone) == false)
        {
            $errors[] = 'Incorrect format of phone number';
        }

        /*if($this->validatePhoto($photo) == false)
        {
            $errors[] = 'Incorrect format of image, must be a PNG extension and the size lower than 1MB';
        }*/

        return $errors;
    }

    function isPasswordValid(?string $userPass, ?string $passwordold, ?string $passwordnew, ?string $checkpass)
    {
        $errors = [];

        if(!$this->checkPassword($userPass, $passwordold)){
            $errors[] = sprintf('The old password is incorrect');
        }
        if (!preg_match('/^(?=.*\d)(?=.*[A-Z])(?=.*[a-z])[0-9A-Za-z!-\/]{1,}$/', $passwordnew)) {
            $errors[] = sprintf('The password must contain numbers and upper and lower case letters');
        }
        if (strlen($passwordnew) < 5) {
            $errors[] = sprintf('The password should be longer than or equal to 5 characters');
        }

        if(!strcmp($passwordnew, $checkpass) == 0)
        {
            $errors[] = sprintf('The passwords should be the same');
        }

        return $errors;
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

    function validatePhoto($photo)
    {

        $file_name = $photo;
        $file = get_headers($photo, 1);
        $file_ext=strtolower(end(explode('.',$photo)));
        $bytes = $file ["Content-Length"];
        $size =  $bytes/(1024 * 1024);
        $expensions = array("png");

        if(in_array($file_ext,$expensions) === false){
           return false;
        }else if($size > 1048576) {
            return false;
        }else{
            move_uploaded_file($photo,"../../public/uploads/".$file_name);
            return true;
        }

    }

    function checkPassword (String $hash_pswd, String $pswd){
        return md5($pswd) == $hash_pswd;
    }
}