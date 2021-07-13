<?php

namespace App\Controller;

use App\Model\FigurineManager;
use App\Model\LicenseManager;
use App\Model\MakerManager;

class CartController extends AbstractController
{
    public function addToCart()
    {
        $data = [];
        $figurineManager = new FigurineManager();
        $availableQty = $figurineManager->selectOneById(intval($_POST['id']))['quantity'];
        if ($availableQty > 0) {
            if (!isset($_SESSION['cart'][intval($_POST['id'])])) {
                $_SESSION['cart'][intval($_POST['id'])] = 0;
            }

            if ($_SESSION['cart'][intval($_POST['id'])] >= $availableQty) {
                $data['error'] = "Vous avez atteint la limite disponible en stock de la figurine";
            } else {
                $_SESSION['cart'][intval($_POST['id'])]++;
            }
            $data['post'] = $_SESSION['cart'][intval($_POST['id'])];
        } else {
            $data['error'] = "Cette figurine n'est pas disponible.";
        }
        $figurineManager = new FigurineManager();
        $data['name'] = $figurineManager->selectOneById(intval($_POST['id']))['name'];
        $data['total'] = $this->getTotalQuantity();
        return json_encode($data);
    }

    public function add()
    {
        $data = [];
        $_SESSION['cart'][intval($_POST['id'])] = intval($_POST['qty']);
        $_POST['id'] = intval($_POST['id']);
        $_POST['qty'] = intval($_POST['qty']);
        $data['post'] = $_POST;
        $figurineManager = new FigurineManager();
        $data['name'] = $figurineManager->selectOneById(intval($_POST['id']))['name'];
        $data['total'] = $this->getTotalQuantity();

        return json_encode($data);
    }

    public function deleteFromCart()
    {
        $figurineManager = new FigurineManager();
        $data = [];
        $data['name'] = $figurineManager->selectOneById(intval($_POST['id']))['name'];
        unset($_SESSION['cart'][$_POST['id']]);
        $data['post'] = $_POST;

        return json_encode($data);
    }

    public function cartInfos()
    {
        $artManager = new FigurineManager();
        if (isset($_SESSION['cart'])) {
            $cart = $_SESSION['cart'];
            $cartInfos = [];
            foreach ($cart as $id => $qty) {
                $article = $artManager->selectOneById($id);
                $article['qty'] = $qty;
                $cartInfos[] = $article;
            }
            return $cartInfos;
        }
        return false;
    }
    public function getTotalCart()
    {
        $total = 0;
        if ($this->cartInfos() != false) {
            foreach ($this->cartInfos() as $article) {
                $total += $article['price'] * $article['qty'];
            }
        }
        return $total;
    }

    public function getTotalQuantity()
    {
        $total = 0;
        if ($this->cartInfos() != false) {
            foreach ($this->cartInfos() as $article) {
                $total += $article['qty'];
            }
        }
        return $total;
    }
}
