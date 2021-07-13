<?php

namespace App\Controller;

use App\Model\FigurineManager;
use App\Model\LicenseManager;
use App\Model\MakerManager;
use App\Model\OrderManager;
use App\Model\UserManager;
use App\Model\OrderItemManager;
use App\Model\CarouselManager;

class AdminController extends AbstractController
{
    public function __construct()
    {
        parent::__construct();
        if (
            (isset($_SESSION['user']) &&
                $_SESSION['user']['admin'] == 0) ||
            !isset($_SESSION['user'])
        ) {
            header('Location: /');
        }
    }

    public function index()
    {
        $figurineManager = new FigurineManager();
        $orderItemManager = new OrderItemManager();
        $userManager = new UserManager();
        //Total ventes et revenus
        $sales = $orderItemManager->totalSales();
        $totalFigurinesSold = 0;
        $totalRevenue = 0;
        foreach ($sales as $figurineOrdered) {
            $totalFigurinesSold += intval($figurineOrdered['totalBuy']);
            $totalRevenue += round(intval($figurineOrdered['totalBuy']) * floatval($figurineOrdered['price']), 2);
        }
        //Meilleurs figurines
        $items = $orderItemManager->bestSeller();
        $bestSell = $figurineManager->selectOneById($items[0]['figurine_id']);
        $bestPrice = $figurineManager->selectOneById($items[1]['figurine_id']);
        $bestSell['soldQuantity'] = $items[0]['totalBuy'];
        $bestSell['totalPrice'] = round(intval($bestSell['soldQuantity']) * floatval($bestSell['price']), 2);
        $bestPrice['soldQuantity'] = $items[1]['totalBuy'];
        $bestPrice['totalPrice'] = round(intval($bestPrice['soldQuantity']) * floatval($bestPrice['price']), 2);

        //Nombre de clients et admins
        $users = $userManager->selectAll();
        $totalUsers = 0;
        $totalAdmins = 0;
        foreach ($users as $user) {
            $totalUsers++;
            $user['admin'] == 1 ? $totalAdmins++ : false;
        }
        //Meilleur client
        $orders = $userManager->bestBuyer();
        $usersName = [];
        foreach ($orders as $user) {
            if (!in_array($user['user_name'], $usersName)) {
                $usersName[$user['user_name']] = [];
                $usersName[$user['user_name']]['name'] = $user['user_name'];
                $usersName[$user['user_name']]['email'] = $user['user_email'];
                $usersName[$user['user_name']]['date'] = $user['date_of_creation'];
                $usersName[$user['user_name']]['total'] = 0;
            }
        }
        foreach ($orders as $user) {
            $usersName[$user['user_name']]['orders'][] = $user['order_id'];
            $usersName[$user['user_name']]['total'] += floatval($user['total_price']);
        }

        $bestBuyer = reset($usersName);
        foreach ($usersName as $user) {
            $user['total'] > $bestBuyer['total'] ? $bestBuyer = $user : false;
        }

        return $this->twig->render('/Admin/index.html.twig', [
            'bestSell' => $bestSell,
            'bestPrice' => $bestPrice,
            'totalSold' => $totalFigurinesSold,
            'totalRevenue' => $totalRevenue,
            'totalUsers' => $totalUsers,
            'totalAdmins' => $totalAdmins,
            'bestUser' => $bestBuyer,
        ]);
    }

    public function figurines()
    {
        $figurineManager = new FigurineManager();
        $licenseManager = new LicenseManager();
        $makerManager = new MakerManager();

        $_SESSION['errorLicenseName'] = '';
        $_SESSION['errorMakerName'] = '';

        $figurines = $figurineManager->selectAll();
        $licenses = $licenseManager->selectAll();
        $makers = $makerManager->selectAll();

        foreach ($figurines as $figurine) {
            $figurine['id'] = intval($figurine['id']);
        };

        if ($_SERVER['REQUEST_METHOD'] === "POST") {
            //Add maker
            if (
                isset($_POST['makerAdd'])
                && !empty($_POST['makerAdd'])
            ) {
                $newMakerName = trim(htmlspecialchars($_POST['makerAdd']));
                $makers = $makerManager->selectAll();
                $trimedMakerName = strtolower(str_replace(' ', '', $newMakerName));
                $error = null;
                foreach ($makers as $maker) {
                    $trimedExistingMakerName = strtolower(str_replace(' ', '', $maker['name']));
                    if ($trimedMakerName == $trimedExistingMakerName) {
                        $error = "Name already used";
                        $_SESSION['errorMakerName'] = $error;
                        header("Location: figurines#licenseMaker");
                    }
                }
                if (empty($error)) {
                    $makerManager->insert($newMakerName) ? header('Location: figurines#licenseMaker') : false;
                }
            }
            //Edit maker
            if (
                isset($_POST['makerEdit'])
                && !empty($_POST['makerEdit'])
            ) {
                $makers = $makerManager->selectAll();
                $undeditedMaker = $makerManager->selectOneById(intval($_POST['makerEditId']));
                $newMaker = $undeditedMaker;
                $newMaker['name'] = $_POST['makerEdit'];
                $trimedNewMakerName = strtolower(str_replace(' ', '', $newMaker['name']));
                $error = null;
                foreach ($makers as $maker) {
                    $trimedExistingMakerName = strtolower(str_replace(' ', '', $maker['name']));
                    var_dump($trimedExistingMakerName, $trimedNewMakerName);
                    if ($trimedNewMakerName == $trimedExistingMakerName) {
                        $error = "Name already used";
                        $_SESSION['errorMakerName'] = $error;
                        header("Location: figurines#licenseMakerMaker");
                    }
                }
                if (empty($error)) {
                    $licenseManager->update($newMaker) ? header('Location: figurines#licenseMaker') : false;
                }
            }
            //Delete maker
            if (isset($_POST['makerDeleteId'])) {
                $makerManager->delete(intval($_POST['makerDeleteId']));
                header('Location: figurines#licenseMaker');
            }
            //Add license
            if (
                isset($_POST['licenseAdd'])
                && !empty($_POST['licenseAdd'])
            ) {
                $newLicenseName = trim(htmlspecialchars($_POST['licenseAdd']));
                $licenses = $licenseManager->selectAll();
                $trimedLicenseName = strtolower(str_replace(' ', '', $newLicenseName));
                $error = null;
                foreach ($licenses as $license) {
                    $trimedExistingLicenseName = strtolower(str_replace(' ', '', $license['name']));
                    if ($trimedLicenseName == $trimedExistingLicenseName) {
                        $error = "Name already used";
                        $_SESSION['errorLicenseName'] = $error;
                        header("Location: figurines#licenseMaker");
                    }
                }
                if (empty($error)) {
                    $licenseManager->insert($newLicenseName) ? header('Location: figurines#licenseMaker') : false;
                }
            }

            //Edit license
            if (
                isset($_POST['licenseEdit'])
                && !empty($_POST['licenseEdit'])
            ) {
                $licenses = $licenseManager->selectAll();
                $undeditedLicense = $licenseManager->selectOneById(intval($_POST['licenseEditId']));
                $newLicense = $undeditedLicense;
                $newLicense['name'] = $_POST['licenseEdit'];
                $trimedNewLicenseName = strtolower(str_replace(' ', '', $newLicense['name']));
                $error = null;
                foreach ($licenses as $license) {
                    $trimedExistingLicenseName = strtolower(str_replace(' ', '', $license['name']));
                    var_dump($trimedExistingLicenseName, $trimedNewLicenseName);
                    if ($trimedNewLicenseName == $trimedExistingLicenseName) {
                        $error = "Name already used";
                        $_SESSION['errorLicenseName'] = $error;
                        header("Location: figurines#licenseMaker");
                    }
                }
                if (empty($error)) {
                    $licenseManager->update($newLicense) ? header('Location: figurines#licenseMaker') : false;
                }
            }

            //Delete license
            if (isset($_POST['licenseDeleteId'])) {
                $licenseManager->delete(intval($_POST['licenseDeleteId']));
                header('Location: figurines#licenseMaker');
            }
        }
        return $this->twig->render('Admin/figurines.html.twig', [
            'figurines' => $figurines,
            'licenses' => $licenses,
            'makers' => $makers
        ]);
    }


    public function show(int $id)
    {
        $figurineManager = new FigurineManager();
        $figurine = $figurineManager->selectOneById($id);
        $figurine['id'] = intval($figurine['id']);
        return $this->twig->render('Admin/show.html.twig', [
            "figurine" => $figurine
        ]);
    }

    public function add()
    {
        $errors = [];
        $licenseManager = new LicenseManager();
        $makerManager = new MakerManager();

        $licenses = $licenseManager->selectAll();
        $makers = $makerManager->selectAll();

        $figurineManager = new FigurineManager();
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            if (
                !empty($_POST['name'])
                && !empty($_POST['description'])
                && !empty($_POST['license'])
                && !empty($_POST['maker'])
                && !empty($_POST['price'])
                && !empty($_POST['priceReduction'])
                && !empty($_POST['quantity'])
                && !empty($_POST['matiere'])
                && !empty($_POST['taille'])
                && !empty($_FILES['image']['name'])
            ) {
                $baseDir = 'assets/images/figurines';

                if (!in_array($_POST['license'], scandir(($baseDir)))) {
                    mkdir($baseDir . '/' . $_POST['license']);
                };
                $newDirLic = $baseDir . '/' . $_POST['license'];

                if (!in_array($_POST['maker'], scandir($newDirLic))) {
                    mkdir($newDirLic . '/' . $_POST['maker']);
                };
                $newDirMak = $newDirLic . '/' . $_POST['maker'];

                $uploadDir = $newDirMak . '/' .
                    str_replace(" ", "", strtolower(trim(htmlspecialchars($_POST['name'])))) . '/';
                mkdir($uploadDir);
                $uploadFile = $uploadDir . basename($_FILES['image']['name']);
                $extension = pathinfo($_FILES['image']['name'], PATHINFO_EXTENSION);
                $extensions_ok = ['jpg', 'jpeg', 'png', 'webp'];
                $maxFileSize = 2097152;

                if ((!in_array($extension, $extensions_ok))) {
                    $errors[] = 'Veuillez sélectionner une image de type Jpg, Jpeg, Png ou Webp !';
                }

                $fileTmpName = $_FILES['image']['tmp_name'];
                if (file_exists($fileTmpName) && filesize($_FILES['image']['tmp_name']) > $maxFileSize) {
                    $errors[] = "Votre fichier doit faire moins de 2M !";
                }

                $fullDesc = 'Matière:' . $_POST['matiere'] . ';Taille:' . $_POST['taille'];
                $figurine = [
                    "name" => trim(htmlspecialchars($_POST['name'])),
                    "description" => trim(htmlspecialchars($_POST['description'])),
                    "full_drescription" => $fullDesc,
                    "license" => intval($_POST['license']),
                    "maker" =>  intval($_POST['maker']),
                    "price" => floatval($_POST['price']),
                    "priceReduction" => floatval($_POST['priceReduction']),
                    "quantity" => intval($_POST['quantity']),
                    "image" => $uploadFile
                ];
                $figurineId = $figurineManager->insert($figurine);
                $figurine = $figurineManager->selectOneById($figurineId);
                header('Location: show/' . $figurineId);

                move_uploaded_file($_FILES['image']['tmp_name'], $uploadFile);
            } else {
                $errors['global'] = 'Tout les champs sont requis';
            }
        }

        return $this->twig->render('Admin/add.html.twig', [
            "licenses" => $licenses,
            "makers" => $makers,
            "errors" => $errors
        ]);
    }

    public function editCarousel()
    {
        $errors = [];

        $CarouselManager = new CarouselManager();
        $carousels = $CarouselManager->selectAll();

        if ($_SERVER['REQUEST_METHOD'] === "POST") {
            $uploadDir = 'assets/images/banner/';
            $carousel = [];
            $i = 1;
            foreach ($_FILES as $image) {
                $uploadFile = $uploadDir . basename($image['name']);
                $extension = pathinfo($image['name'], PATHINFO_EXTENSION);
                $extensions_ok = ['jpg', 'jpeg', 'png', 'webp'];
                $maxFileSize = 2097152;
                if (!in_array($extension, $extensions_ok)) {
                    $errors[] = 'Veuillez sélectionner une image de type Jpg ou Jpeg ou Png ou Webp ! Imgage ' . $i;
                }
                $fileTmpName = $image['tmp_name'];
                if (file_exists($fileTmpName) && filesize($image['tmp_name']) > $maxFileSize) {
                    $errors[] = "Votre fichier doit faire moins de 2M0 ! Imgage " . $i;
                }
                if (empty($error) && $uploadFile != $uploadDir) {
                    array_push($carousel, [
                        'id' => $_POST['id' . $i],
                        "image" => $uploadFile,
                    ]);
                }
                $i++;
                move_uploaded_file($image['tmp_name'], $uploadFile);
            }
            foreach ($carousel as $image) {
                $CarouselManager->updateCarousel($image);
            }
            header('Location: /admin/index');
        }
        return $this->twig->render('Admin/editCarousel.html.twig', [
            'carousels' => $carousels
        ]);
    }

    public function edit(int $id)
    {
        $figurineManager = new FigurineManager();
        $figurine = $figurineManager->selectOneById($id);

        $figurineId = intval($figurine['id']);
        $figurineFullDesc = $figurine['full_description'];
        $figurineFullDescArr = explode(";", $figurineFullDesc);
        $figurine['matiere'] = explode(":", $figurineFullDescArr[0])[1];
        $figurine['taille'] = explode(":", $figurineFullDescArr[1])[1];

        if ($_SERVER['REQUEST_METHOD'] === "POST") {
            $_POST['id'] = $figurineId;
            if (
                !empty($_POST['name'])
                && !empty($_POST['description'])
                && !empty($_POST['license'])
                && !empty($_POST['maker'])
                && !empty($_POST['price'])
                && !empty($_POST['priceReduction'])
                && !empty($_POST['quantity'])
                && !empty($_POST['matiere'])
                && !empty($_POST['taille'])
            ) {
                $beforeFigurine = $figurine;
                $uploadFile = $beforeFigurine['image_main'];
                if (!empty($_FILES['image']['name'])) {
                    $path = implode('/', array_slice(explode('/', $uploadFile), 0, 6)) . '/';
                    $extension = pathinfo($_FILES['image']['name'], PATHINFO_EXTENSION);
                    $extensions_ok = ['jpg', 'jpeg', 'png', 'webp'];
                    $maxFileSize = 2097152;

                    if ((!in_array($extension, $extensions_ok))) {
                        $errors[] = 'Veuillez sélectionner une image de type Jpg, Jpeg, Png ou Webp !';
                    }

                    $fileTmpName = $_FILES['image']['tmp_name'];
                    if (file_exists($fileTmpName) && filesize($_FILES['image']['tmp_name']) > $maxFileSize) {
                        $errors[] = "Votre fichier doit faire moins de 2M !";
                    }

                    if (empty($errors)) {
                        if (file_exists($uploadFile)) {
                            unlink($uploadFile);
                        }
                        $uploadFile = $path . $_FILES['image']['name'];
                        move_uploaded_file($_FILES['image']['tmp_name'], $uploadFile);
                    }
                }
                $fullDesc = 'Matière:' . $_POST['matiere'] . ';Taille:' . $_POST['taille'];
                $figurine = [
                    "id" => $figurineId,
                    "name" => trim(htmlspecialchars($_POST['name'])),
                    "description" => trim(htmlspecialchars($_POST['description'])),
                    "full_description" => $fullDesc,
                    "license" => intval($_POST['license']),
                    "maker" =>  intval($_POST['maker']),
                    "price" => floatval($_POST['price']),
                    "priceReduction" => floatval($_POST['priceReduction']),
                    "quantity" => intval($_POST['quantity']),
                    "image" => $uploadFile
                ];
                $figurineManager->update($figurine);
                $figurine = $figurineManager->selectOneById($figurineId);
                header('Location: ../show/' . $figurineId);
            }
        }
        $licenseManager = new LicenseManager();
        $licenses = $licenseManager->selectAll();
        $makerManager = new MakerManager();
        $makers = $makerManager->selectAll();
        return $this->twig->render('Admin/edit.html.twig', [
            'figurine' => $figurine,
            'licenses' => $licenses,
            'makers' => $makers
        ]);
    }

    public function delete(int $id)
    {
        $figurineManager = new FigurineManager();
        $figurine = $figurineManager->selectOneById($id);
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $currentDir = implode("/", array_slice(explode("/", $figurine['image_main']), 0, 6));
            foreach (scandir($currentDir . '/') as $file) {
                if ($file != "." && $file != "..") {
                    unlink($currentDir . '/' . $file);
                }
            }
            rmdir($currentDir);
            $figurineManager->delete($id);
            $this->figurines();
            header('Location: ../figurines');
        }
        return $this->twig->render('Admin/delete.html.twig', [
            'figurine' => $figurine
        ]);
    }

    public function users()
    {
        $userManager = new UserManager();
        $users = $userManager->selectAll();

        if ($_SERVER['REQUEST_METHOD'] === "POST") {
            if (
                isset($_POST['admin']) &&
                !empty($_POST['admin'])
            ) {
                $userManager->admin(intval($_POST['id']), intval($_POST['admin']));
            }
            header('Location: /admin/users');
        }
        return $this->twig->render('/Admin/users.html.twig', [
            'users' => $users
        ]);
    }

    public function orders(int $id = 0)
    {
        $user = [];
        $userManager = new UserManager();
        $orderManager = new OrderManager();
        $userOrders = [];
        if ($id != 0) {
            $userOrders = $orderManager->selectAllFromOneUser($id);
        }
        $users = $userManager->selectAll();
        $orders = $orderManager->selectAll();
        return $this->twig->render('/Admin/orders.html.twig', [
            'users' => $users,
            'userOrders' => $userOrders,
            'orders' => $orders
        ]);
    }

    public function orderDetails(int $id)
    {
        $orderManager = new OrderManager();
        return $this->twig->render('/Admin/orderDetails.html.twig', [
            "details" => $orderManager->selectDetails($id),
            "order" => $orderManager->selectOneById($id)
        ]);
    }
}
