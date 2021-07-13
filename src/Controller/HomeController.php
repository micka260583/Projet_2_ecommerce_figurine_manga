<?php

/**
 * Created by PhpStorm.
 * User: aurelwcs
 * Date: 08/04/19
 * Time: 18:40
 */

namespace App\Controller;

use App\Model\FigurineManager;
use App\Model\LicenseManager;
use App\Model\MakerManager;
use App\Model\CarouselManager;
use App\Model\OrderManager;
use App\Model\OrderItemManager;
use App\Controller\CartController;

class HomeController extends AbstractController
{
    public function index()
    {
        return $this->twig->render('Home/index.html.twig');
    }

    public function accueil()
    {
        $figurineManager = new FigurineManager();
        $CarouselManager = new CarouselManager();
        $carousels = $CarouselManager->selectAll();
        $figurines = $figurineManager->selectCarousel();
        $figyuas = $figurineManager->selectCards();
        return $this->twig->render('Home/accueil.html.twig', [
            'figurines' => $figurines,
            'figyuas' => $figyuas,
            'carousel' => $carousels
        ]);
    }

    public function figurines()
    {
        $figurineManager = new FigurineManager();
        $licenseManager = new LicenseManager();
        $makerManager = new MakerManager();
        $CarouselManager = new CarouselManager();
        $carousels = $CarouselManager->selectAll();
        $figurines = $figurineManager->selectCarousel();
        $figyuas = $figurineManager->selectAll();
        $licenses = $licenseManager->selectAll();
        $makers = $makerManager->selectAll();

        return $this->twig->render('Home/figurines.html.twig', [
            'figurines' => $figurines,
            'figyuas' => $figyuas,
            'licenses' => $licenses,
            'makers' => $makers,
            'carousel' => $carousels
        ]);
    }

    public function show(int $id)
    {
        $figurineManager = new FigurineManager();
        $figurine = $figurineManager->selectOneById($id);
        $figurine['id'] = intval($figurine['id']);
        $figyuas = $figurineManager->selectByLicenseId(intval($figurine['license_id']), intval($figurine['maker_id']));
        $figurineDesc = $figurine['full_description'];
        $figurine['matiere'] = explode(':', explode(";", $figurineDesc)[0])[1];
        $figurine['taille'] = explode(':', explode(";", $figurineDesc)[1])[1];
        return $this->twig->render('Home/show.html.twig', [
            "figurine" => $figurine,
            'figyuas' => $figyuas
        ]);
    }
    //CartCONTROLLER//
    public function cart()
    {
        $cartController = new CartController();
        return $this->twig->render('Home/cart.html.twig', [
            'cart' => $cartController->cartInfos(),
            'totalCart' => $cartController->getTotalCart()
        ]);
    }

    //Contact
    public function contact()
    {

        $errors = [];
        if ($_SERVER['REQUEST_METHOD'] === "POST") {
            $data = array_map('trim', $_POST);
            if (empty($data['email'])) {
                $errors['email'] = "Le champs 'Email' est requis";
            } elseif (!filter_var($data['email'], FILTER_VALIDATE_EMAIL)) {
                $errors['email'] = "Format d'email incorrect";
            }
            
            if (empty($data['firstname'])) {
                $errors['firstname'] = "Le champs 'Prénom' est requis";
            }
            if (empty($data['lastname'])) {
                $errors['lastname'] = "Le champs 'Nom' est requis";
            }
            if (empty($data['subject'])) {
                $errors['subject'] = "Le champs 'Sujet' est requis";
            }
            if (empty($data['message'])) {
                $errors['message'] = "Le champs 'Message' est requis";
            }

            if (
                !empty($data['email'])
                && !empty($data['firstname'])
                && !empty($data['lastname'])
                && !empty($data['subject'])
                && !empty($data['message'])
            ) {
                $errors['success'] = "Super! Le message a bien été envoyé!";
            }
            if (
                empty($data['email'])
                || empty($data['firstname'])
                || empty($data['lastname'])
                || empty($data['subject'])
                || empty($data['message'])
            ) {
                $errors['error'] = "Attention! Le message n'a pu être envoyé!";
            }
        }
        return $this->twig->render('Home/contact.html.twig', [
            'errors' => $errors
        ]);
    }

    public function order()
    {
        $error = [];
        \Stripe\Stripe::setApiKey(API_KEY);
        $stripe = new \Stripe\StripeClient(API_KEY);
        $cartController = new CartController();
        $orderManager = new OrderManager();
        $orderItemManager = new OrderItemManager();
        $figurineManager = new FigurineManager();
        if (!isset($_SESSION['cart']) || count($_SESSION['cart']) == 0) {
            return $this->twig->render('home/failure.html.twig');
        }
        if ($_SERVER['REQUEST_METHOD'] === "POST") {
            if (
                (isset($_POST['country']) &&
                    isset($_POST['city']) &&
                    isset($_POST['zip']) &&
                    isset($_POST['street'])) &&
                (!empty($_POST['country']) ||
                    !empty($_POST['city']) ||
                    !empty($_POST['zip']) ||
                    !empty($_POST['street'])) &&
                (isset($_POST['countryFact']) &&
                    isset($_POST['cityFact']) &&
                    isset($_POST['zipFact']) &&
                    isset($_POST['streetFact'])) &&
                (!empty($_POST['countryFact']) ||
                    !empty($_POST['cityFact']) ||
                    !empty($_POST['zipFact']) ||
                    !empty($_POST['streeFactt']))
            ) {
                $delivery_address = [
                    "country" => $_POST['country'],
                    "city" => ucfirst(trim(htmlspecialchars($_POST['city']))),
                    "zip" => trim(htmlspecialchars($_POST['zip'])),
                    "street" => ucfirst(trim(htmlspecialchars($_POST['street'])))
                ];
                $billing_address = [
                    "country" => $_POST['countryFact'],
                    "city" => ucfirst(trim(htmlspecialchars($_POST['cityFact']))),
                    "zip" => trim(htmlspecialchars($_POST['zipFact'])),
                    "street" => ucfirst(trim(htmlspecialchars($_POST['streetFact'])))
                ];
                $data['delivery_address'] = $delivery_address;
                $data['billing_address'] = $billing_address;
                $data['delivery_address_str'] = '';
                $data['billing_address_str'] = '';
                foreach ($data['delivery_address'] as $key => $value) {
                    $data['delivery_address_str'] .= $key . ':' . $value . ";";
                }
                foreach ($data['billing_address'] as $key => $value) {
                    $data['billing_address_str'] .= $key . ':' . $value . ";";
                }
                try {
                    //CUSTOMER
                    $result = [
                        'source' => $_POST['stripeToken'],
                        'description' => 'description',
                        'email' => 'admin@admin.com'
                    ];
                    $customer = $stripe->customers->create($result);
                    // CHARGE
                    $charge = \Stripe\Charge::create([
                        'amount' => $cartController->getTotalCart() * 100,
                        'currency' => 'eur',
                        'description' => 'Example charge',
                        'customer' => $customer->id,
                        'statement_descriptor' => 'Custom descriptor',
                    ]);
                    $transacUrl = $charge->receipt_url;
                    $_SESSION['transaction'] = [
                        'stripe' => $transacUrl
                    ];
                } catch (\Stripe\Exception\ApiErrorException $e) {
                      // Since it's a decline, \Stripe\Exception\CardException will be caught
                    $error['status'] = $e->getHttpStatus();
                    $error['type'] = $e->getError()->type;
                    $error['code'] =  $e->getError()->code;
                    // param is '' in this case
                    $error['param'] = $e->getError()->param;
                    $error['message'] = $e->getError()->message;
                }
                if (empty($error)) {
                    isset($_SESSION['user']) ? $curId = $_SESSION['user']['id'] : $curId = 2;
                    $order = [
                        'user_id' => $curId,
                        'billing_address' => $data['billing_address_str'],
                        'delivery_address' => $data['delivery_address_str'],
                        'total_price' => $cartController->getTotalCart(),
                        'order_date' => Date("Y-m-d"),
                        'status' => 'Confirmée',
                    ];
                    $idCommand = $orderManager->insert($order);

                    foreach ($_SESSION['cart'] as $idArticle => $qty) {
                        $figurineManager->removeQuantity($idArticle, $qty);
                        $newCommandArticle = [
                            'order_id' => $idCommand,
                            'figurine_id' => $idArticle,
                            'figurine_quantity' => $qty
                        ];
                        $orderItemManager->insert($newCommandArticle);
                        unset($_SESSION['cart']);
                        return $this->twig->render('Home/success.html.twig');
                    }
                }
            }
        }
        return $this->twig->render('Home/order.html.twig', [
            'error' => $error
        ]);
    }

    public function orderDetail(int $id)
    {
        $orderManager = new OrderManager();
        return $this->twig->render('/Order/detail.html.twig', [
            "details" => $orderManager->selectDetails($id),
            "order" => $orderManager->selectOneById($id)
        ]);
    }

    public function notFound()
    {
        return $this->twig->render('Home/notFound.html.twig');
    }
}
