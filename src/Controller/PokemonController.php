<?php

namespace App\Controller;

use App\Model\AttackManager;
use App\Model\PokemonManager;
use App\Model\TypeManager;
use Exception;

class PokemonController extends AbstractController
{
    private PokemonManager $pokemonManager;
    private TypeManager $typeManager;
    private AttackManager $attackManager;

    public function __construct()
    {
        parent::__construct();
        $this->typeManager = new TypeManager();
        $this->pokemonManager = new PokemonManager();
        $this->attackManager = new AttackManager();
    }

    public function add()
    {
        $types = $this->typeManager->selectAll();
        $attacks = $this->attackManager->selectAll();

        //if we just submitted the form to add a Pokemon
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $errors = [];
            $pokemonValues = [];
            $allowedMime = ['jpg', 'jpeg', 'png' ];
            $sizeMax = 2000000;

            //Checking if name is set and not too long
            if (!isset($_POST['name']) || trim($_POST['name']) == '') {
                $errors[] = "You need to give a name to your pokemon.";
            } elseif (strlen($_POST['name']) > 50) {
                $errors[] = "Name must not be longer than 50 characters";
            } else {
                $pokemonValues['name'] = $_POST['name'];
            }

            //Checking if types exist in database if not null
            for ($i = 1; $i <= 2; $i++) {
                $type_exist = false;
                foreach ($types as $type) {
                    if ($type['id'] === $_POST['type' . $i]) {
                        $type_exist = true;
                    }
                }

                if (!$type_exist && trim($_POST['type' . $i]) !== "") {
                    $errors[] = "Type $i does not exist. Please select a correct value.";
                } else {
                    $pokemonValues['type' . $i] = $_POST['type' . $i];
                }
            }

            //Checking if attacks exist in database if not null
            for ($i = 1; $i <= 4; $i++) {
                $attack_exist = false;
                foreach ($attacks as $attack) {
                    if ($attack['id'] === $_POST['attack' . $i]) {
                        $attack_exist = true;
                    }
                }

                if (!$attack_exist && trim($_POST['attack' . $i]) !== "") {
                    $errors[] = "Attack $i does not exist. Please select a correct value.";
                } else {
                    $pokemonValues['attack' . $i] = $_POST['attack' . $i];
                }
            }

            //Trying to upload file if is set and no errors before
            if (!isset($_FILES['image']['name']) || $_FILES['image']['name'] == '') {
                $errors[] = "You need to add an image for this pokemon.";
            } elseif (empty($errors)) {
                try {
                    $file = $_FILES['image'];
                    if (!in_array(pathinfo($file['name'], PATHINFO_EXTENSION), $allowedMime)) {
                        throw new Exception("Format file " . $file['name'] . " is not accepted.");
                    }
                    if ($file['size'] > $sizeMax) {
                        throw new Exception("File " . $file['name'] . " is too big : " . $file['size'] . "($sizeMax Octets MAX) ");
                    }
                    //Upload le fichier
                    $file = $_FILES['image'];
                    $uploadDir = "assets/images/";
                    $extension = pathinfo($file['name'], PATHINFO_EXTENSION);
                    $fileNameUpload = uniqid() . '.' . $extension;
                    $uploadFile = $uploadDir . $fileNameUpload;
                    move_uploaded_file($file['tmp_name'], $uploadFile);
                    $pokemonValues['filePath'] = $uploadFile;
                } catch (Exception $e) {
                    $errors[] =  $e->getMessage();
                }
            }

            //if errors
            if (!empty($errors)) {
                return $this->twig->render('Pokemon/add.html.twig', ['types' => $types, 'attacks' => $attacks, 'errors' => $errors]);
            } else {
                // else if validation is ok, upload file then insert and redirect
                $id = $this->pokemonManager->insert($pokemonValues);
                //header('Location:/item/show/' . $id);
            }
        }
        //else we show the form
        return $this->twig->render('Pokemon/add.html.twig', ['types' => $types, 'attacks' => $attacks]);
    }


    public function list()
    {
        // si $_SESSION['dlt_pok_msg'] existe, utiliser isset pour vérifier son existence
        // $message = $_SESSION['dlt_pok_msg']
        // detruire $_SESSION['dlt_pok_msg']
        $pokemons = $this->pokemonManager->selectAllWithAttackTypes();
        return $this->twig->render('Pokemon/list.html.twig', ['pokemons' => $pokemons]);
    }
    public function delete(int $id)
    {
        // Getting the rowCount value which is returned at the end of deletePokemonFromList function
        $rowCount = $this->pokemonManager->deletePokemonFromList($id);
        $validationMessage = $rowCount == 1 ? 'Le pokémon a bien été retiré de la liste!' : 'erreur!';
        // créer une variable de session $_SESSION['dlt_pok_msg']
        header('Location: /Pokemon/list');
        //
    }
    




    /**
     * List items
     */
/*     public function index(): string
    {
        $itemManager = new ItemManager();
        $items = $itemManager->selectAll('title');

        return $this->twig->render('Item/index.html.twig', ['items' => $items]);
    } */


    /**
     * Show informations for a specific item
     */
/*     public function show(int $id): string
    {
        $itemManager = new ItemManager();
        $item = $itemManager->selectOneById($id);

        return $this->twig->render('Item/show.html.twig', ['item' => $item]);
    } */


    /**
     * Edit a specific item
     */
/*     public function edit(int $id): string
    {
        $itemManager = new ItemManager();
        $item = $itemManager->selectOneById($id);

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            // clean $_POST data
            $item = array_map('trim', $_POST);

            // TODO validations (length, format...)

            // if validation is ok, update and redirection
            $itemManager->update($item);
            header('Location: /item/show/' . $id);
        }

        return $this->twig->render('Item/edit.html.twig', [
            'item' => $item,
        ]);
    } */


    /**
     * Add a new item
     */
/*     public function add(): string
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            // clean $_POST data
            $item = array_map('trim', $_POST);

            // TODO validations (length, format...)

            // if validation is ok, insert and redirection
            $itemManager = new ItemManager();
            $id = $itemManager->insert($item);
            header('Location:/item/show/' . $id);
        }

        return $this->twig->render('Item/add.html.twig');
    } */


    /**
     * Delete a specific item
     */
    /*     public function delete(int $id)
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $itemManager = new ItemManager();
            $itemManager->delete($id);
            header('Location:/item/index');
        }
    }  */
}
