<?php

namespace App\Controller;

use App\Model\UserManager;
use App\Model\OrderManager;

class UserController extends AbstractController
{
    public function index()
    {
        if (!isset($_SESSION['user'])) {
            header('Location: /');
        }
        $orderManager = new OrderManager();
        $orders = $orderManager->selectByUserId($_SESSION['user']['id']);
        return $this->twig->render('User/index.html.twig', [
            "orders" => $orders
        ]);
    }

    public function edit()
    {
        $error = [];
        if (!isset($_SESSION['user'])) {
            header('Location: /');
        }
        $userManager = new UserManager();
        if ($_SERVER['REQUEST_METHOD'] === "POST") {
            $error = [];
            if (
                isset($_POST['name']) &&
                isset($_POST['email']) &&
                isset($_POST['password']) &&
                !empty($_POST['name']) &&
                !empty($_POST['email']) &&
                !empty($_POST['password'])
            ) {
                $error = [];
                $actualUser = $_SESSION['user'];
                $tmpUser = [
                    'id' => intval($_SESSION['user']['id']),
                    'name' => trim(htmlspecialchars($_POST['name'])),
                    'email' => trim($_POST['email']),
                    'password' => md5($_POST['password'])
                ];
                if (!filter_var($_POST['email'], FILTER_VALIDATE_EMAIL)) {
                    $error['email'] = "Invalid email format";
                }
                if (
                    $userManager->selectOneByEmail($tmpUser['email']) &&
                    $userManager->selectOneByEmail($tmpUser['email'])['email'] != $actualUser['email']
                ) {
                    $error['email'] = "This email is already taken";
                }
                if ($userManager->selectOneById($tmpUser['id'])['password'] != $tmpUser['password']) {
                    $error['password'] = "Incorrect password";
                }
                if (
                    isset($_POST['newPassword']) &&
                    isset($_POST['newPasswordVerif']) &&
                    !empty($_POST['newPassword']) &&
                    !empty($_POST['newPasswordVerif'])
                ) {
                    if ($_POST['newPassword'] != $_POST['newPasswordVerif']) {
                        $error['newPassword'] = "Verification does not match password";
                    }
                    $tmpUser['password'] = md5($_POST['newPassword']);
                }
                if (
                    (isset($_POST['country']) &&
                    isset($_POST['city']) &&
                    isset($_POST['zip']) &&
                    isset($_POST['street'])) &&
                    (!empty($_POST['country']) ||
                    !empty($_POST['city']) ||
                    !empty($_POST['zip']) ||
                    !empty($_POST['street']))
                ) {
                    $tmpUser['adress'] = [
                        "country" => $_POST['country'],
                        "city" => ucfirst(trim(htmlspecialchars($_POST['city']))),
                        "zip" => trim(htmlspecialchars($_POST['zip'])),
                        "street" => ucfirst(trim(htmlspecialchars($_POST['street'])))
                    ];

                    if (
                        empty($_POST['country']) ||
                        empty($_POST['city']) ||
                        empty($_POST['zip']) ||
                        empty($_POST['street'])
                    ) {
                        $error['billing'] = "All fields need to be completed to add a billing adress";
                    }
                }
                if (empty($error)) {
                    $userManager->update($tmpUser);
                    $_SESSION['user'] = $userManager->selectOneById($tmpUser['id']);
                    if ($_SESSION['user'] != '') {
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
                    header('Location: /user/index');
                }
            }
            $error['fields'] = "All fields are required";
        }
        return $this->twig->render('User/edit.html.twig', [
            'error' => $error
        ]);
    }
    public function order(int $id)
    {
        $orderManager = new OrderManager();
        return $this->twig->render('/User/order.html.twig', [
            "details" => $orderManager->selectDetails($id),
            "order" => $orderManager->selectOneById($id)
        ]);
    }
}
