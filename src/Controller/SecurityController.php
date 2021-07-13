<?php

namespace App\Controller;

use App\Model\UserManager;

class SecurityController extends AbstractController
{
    public function register()
    {
        $userManager = new UserManager();
        $error = [];
        if ($_SERVER['REQUEST_METHOD'] === "POST") {
            $error = [];
            if (
                isset($_POST['name']) &&
                isset($_POST['email']) &&
                isset($_POST['password']) &&
                isset($_POST['passwordVerif']) &&
                !empty($_POST['name']) &&
                !empty($_POST['email']) &&
                !empty($_POST['password']) &&
                !empty($_POST['passwordVerif'])
            ) {
                $tmpUser = [
                    'name' => trim(htmlspecialchars($_POST['name'])),
                    'email' => $_POST['email'],
                    'password' => md5($_POST['password']),
                    'passwordVerif' => md5($_POST['passwordVerif'])
                ];
                if (!filter_var($_POST['email'], FILTER_VALIDATE_EMAIL)) {
                    $error['email'] = "Invalid email format";
                }
                if ($userManager->selectOneByEmail($tmpUser['email'])) {
                    $error['email'] = "This email is already taken";
                }
                if ($tmpUser['password'] != $tmpUser['passwordVerif']) {
                    $error['password'] = "Passwords do not match";
                }
                if (empty($error)) {
                    $user = [
                        'name' => $tmpUser['name'],
                        'email' => $tmpUser['email'],
                        'password' => $tmpUser['password']
                    ];
                    $userManager->insert($user);
                    $_SESSION['user'] = $userManager->selectOneByEmail($user['email']);
                    header('Location: /Home/accueil');
                }
            } else {
                $error['fields'] = "All fields are required";
            }
        }
        return $this->twig->render('Security/register.html.twig', [
            'error' => $error
        ]);
    }

    public function login()
    {
        $userManager = new UserManager();
        $error = [];
        if ($_SERVER['REQUEST_METHOD'] === "POST") {
            if (
                isset($_POST['emailLogin']) &&
                isset($_POST['passwordLogin']) &&
                !empty($_POST['emailLogin']) &&
                !empty($_POST['passwordLogin'])
            ) {
                if (!filter_var($_POST['emailLogin'], FILTER_VALIDATE_EMAIL)) {
                    $error['email'] = "Invalid email format";
                }
                $tmpUser = $userManager->selectOneByEmail($_POST['emailLogin']);
                if (!$tmpUser) {
                    $error['email'] = "User not found";
                } else {
                    if (md5($_POST['passwordLogin']) != $tmpUser['password']) {
                        $error['password'] = "Incorrect password";
                    }
                }
                if (empty($error)) {
                    $_SESSION['user'] = $tmpUser;
                    if ($_SESSION['user']['adress'] != '') {
                        $adress = [];
                        $tmpAdress = explode(";", $_SESSION['user']['adress'], 4);
                        $i = 0;
                        foreach ($tmpAdress as $field) {
                            $field[-1] == ";" ? $tmpAdress[$i] = substr($field, 0, -1) : false;
                            $adress[explode(":", $field)[0]] = explode(":", $tmpAdress[$i])[1];
                            $i++;
                        }
                        $_SESSION['user']['adress'] = $adress;
                    }
                    header('Location: /Home/accueil');
                }
            } else {
                $error['fields'] = "All fields are required";
            }
        }
        return $this->twig->render('Security/login.html.twig', [
            'error' => $error
        ]);
    }
    public function logout()
    {
        session_unset();
        session_destroy();
        header('Location: /Home/accueil');
    }
}
