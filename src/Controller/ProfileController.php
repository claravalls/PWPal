<?php

namespace SallePW\SlimApp\Controller;

use DateTime;
use Imagick;
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
        if (!isset($_SESSION['user'])){
            echo "<script>
            alert('Log in to access to your profile');
            window.location.href='/sign-in';
            </script>";
        }

        $user = $_SESSION['user'];
        $path = basename("public/uploads/");

        return $this->container->get('view')->render(
            $response,
            'profile.twig',
            [
                'email' => $user->email(),
                'birthday' => $user->birthday()->format('Y-m-d'),
                'phone' => $user->telefon(),
                'photo' => $path."/".$user->photo()
            ]
        );
    }

    public function updateProfile(Request $request, Response $response):Response
    {
        if (!isset($_SESSION['user'])){

            header("Location: /sign-in");
        }
        $user = $_SESSION['user'];

        $data = $request->getParsedBody();
        $errors = $this->isValid($_FILES['photo']['name'], $data['phone'], $_FILES['photo']['tmp_name']);

        if(empty($errors))
        {
            $userComprovar = $this->container->get('user_repository')->search($user->email(), "email");
            if($userComprovar->id() > 0) {
                if($_FILES['photo']['name'] == NULL)
                {
                    $_FILES['photo']['name'] = $user->photo();
                    $this->container->get('user_repository')->editProfileNotPhoto($data['phone'], $user->email());
                }else {
                    $this->container->get('user_repository')->editProfile($data['phone'], $_FILES['photo']['name'] ?? $user->photo(), $user->email());
                }
                $userComprovar = $this->container->get('user_repository')->search($user->email(), "email");
                $_SESSION['user'] = $userComprovar;
            }
        }
        $path = basename("public/uploads/");
        return $this->container->get('view')->render(
            $response,
            'profile.twig',
            [
                'errors' => $errors,
                'email' => $user->email(),
                'birthday' => $user->birthday()->format('Y-m-d'),
                'phone' => $user->telefon(),
                'photo' => $path."/".$_FILES['photo']['name'],
                'image' => $user->photo()
            ]
        );
    }

    public function showProfileSecurity(Request $request, Response $response):Response
    {
        if (!isset($_SESSION['user'])){

            header("Location: /sign-in");
        }
        $user = $_SESSION['user'];
        return $this->container->get('view')->render(
            $response,
            'security.twig',
            []
        );
    }

    public function updateProfileSecurity(Request $request, Response $response):Response
    {
        if (!isset($_SESSION['user'])){

            header("Location: /sign-in");
        }
        $user = $_SESSION['user'];
        $data = $request->getParsedBody();

        $errors = $this->isPasswordValid($user->password(), $data['passwordold'],$data['passwordnew'], $data['checkpass']);

        if(empty($errors))
        {
            $userComprovar = $this->container->get('user_repository')->search($user->email(), "email");
            if($userComprovar->id() > 0) {
                $newpassword = md5($data['passwordnew']);
                $this->container->get('user_repository')->changePassword($newpassword, $user->email());
                $errors[] = 'The password has been updated correctly';
                $userComprovar = $this->container->get('user_repository')->search($user->email(), "email");
                $_SESSION['user'] = $userComprovar;
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

    function isValid(?string $photo, ?string $phone, ?string $temp)
    {
        $errors = [];

        if($this->validatePhoneNumber($phone) == false)
        {
            $errors[] = 'Incorrect format of phone number';
        }

        if($this->validatePhoto($photo, $temp) == false)
        {
            $errors[] = 'Incorrect format of image, must be a PNG extension and the size lower than 1MB';
        }

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

    function validatePhoto($photo, $temp)
    {
        if ($_FILES['photo']['name'] == NULL){
            return true;
        }
        $path = basename("uploads/");
        $uploadfile = $path."/".basename($photo);

        $imagick = new \Imagick($temp);
        $imagick->cropImage(400, 400, 0, 0);
        $imagick->chopImage(400, 400, 0, 0);
        $imagick->writeImage($uploadfile);

        if(!empty($photo)) {
            //if (move_uploaded_file($imagick->getImageBlob(), $uploadfile)) {
            if($imagick->writeImage($uploadfile)){
                $filesize = filesize($path . "/" . basename($photo));
                $filesize = round($filesize / 1024, 2);
                if ($filesize > 1048576) {
                    return false;
                } else {
                    return true;
                }
            } else {
                return false;
            }
        }
        return true;

    }

    function checkPassword (String $hash_pswd, String $pswd){
        return md5($pswd) == $hash_pswd;
    }
}