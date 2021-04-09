<?php

namespace App\Controller;

use App\Model\AttackManager;
use App\Model\PokemonManager;
use App\Model\TeamManager;
use App\Model\TypeManager;
use App\Model\UserManager;
use Exception;

class PokemonController extends AbstractController
{
    private PokemonManager $pokemonManager;
    private TypeManager $typeManager;
    private AttackManager $attackManager;
    private TeamManager $teamManager;

    public function __construct()
    {
        parent::__construct();
        $this->typeManager = new TypeManager();
        $this->teamManager = new TeamManager();
        $this->userManager = new UserManager();
        $this->pokemonManager = new PokemonManager();
        $this->attackManager = new AttackManager();
    }

    /**
     * List all pokemons
     */
    public function list()
    {
        $pokemons = $this->pokemonManager->selectAllWithAttackTypes();
        //on veut créer une variable vide lorsque la suppression n'a pas été effectuée
        if (isset($_SESSION['dlt_pok_msg'])) {
            $validationMessage = $_SESSION['dlt_pok_msg'];
            // define a validationMessage var to keep the value of $_SESSION['dlt_pok_msg']
            unset($_SESSION['dlt_pok_msg']);
        } else {
            $validationMessage = '';
            // is empty if you are not coming from the delete function
        }

        return $this->twig->render('Pokemon/onlyListPokemon.html.twig', ['pokemons' => $pokemons, 'validationMessage' => $validationMessage, 'session' => $_SESSION]);
        // display the $validationMessage var in render if it exists
    }

    /**
     * show pokemon details
     */
    public function details(int $id): string
    {
        $pokemonsId = [];

        $pokemon = $this->pokemonManager->selectOneByIdWithAttackTypes($id);
        if(isset($_SESSION['userId'])){
            $teamId= intval($this->userManager->selectTeamIdByUserId($_SESSION['userId']));
            $teamPokemonsId = $this->teamManager->selectPokemonsInTeam($teamId);
            foreach($teamPokemonsId as $pokemonId){
                $pokemonsId[] = $pokemonId['pokemon_id'];
            }
        }

        return $this->twig->render('Pokemon/details.html.twig', [
            'pokemon' => $pokemon, 
            'session' => $_SESSION,
            'pokemonsId' => $pokemonsId]
        );
    }


    /**
     * Add a pokemon
     */
    public function add()
    {
        $this->checkLogin();

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
                    $pokemonValues['filePath'] = '/' . $uploadFile;
                } catch (Exception $e) {
                    $errors[] =  $e->getMessage();
                }
            }

            //Trying to upload model3d file if is set and no errors before
            if (isset($_FILES['model3d']['name']) && $_FILES['model3d']['name'] !== '' && empty($errors)) {
                try {
                    $file = $_FILES['model3d'];
                    if (pathinfo($file['name'], PATHINFO_EXTENSION) !== "glb") {
                        throw new Exception("Format file " . $file['name'] . " is not accepted.");
                    }

                    //Upload le fichier
                    $file = $_FILES['model3d'];
                    $uploadDir = "assets/models/";
                    $extension = pathinfo($file['name'], PATHINFO_EXTENSION);
                    $fileNameUpload = uniqid() . '.' . $extension;
                    $uploadFile = $uploadDir . $fileNameUpload;
                    move_uploaded_file($file['tmp_name'], $uploadFile);
                    $pokemonValues['modelPath'] = '/' . $uploadFile;
                } catch (Exception $e) {
                    $errors[] =  $e->getMessage();
                }
            } else {
                $pokemonValues['modelPath'] = "";
            }

            //if errors
            if (!empty($errors)) {
                return $this->twig->render('Pokemon/add.html.twig', ['types' => $types, 'attacks' => $attacks, 'errors' => $errors]);
            } else {
                // else if validation is ok, upload file then insert and redirect
                $id = $this->pokemonManager->insert($pokemonValues);
                header('Location:/pokemon/details/' . $id);
            }
        }
        //else we show the form
        return $this->twig->render('Pokemon/add.html.twig', ['types' => $types, 'attacks' => $attacks]);
    }


    public function delete(int $id)
    {
        $this->checkLogin();
        
        //Delete Files attached to the pokemon
        $pokemon = $this->pokemonManager->selectOneById($id);
        $pathImage = substr($pokemon['image'], 1);
        $pathModel = substr($pokemon['model3d'], 1);

        if ($pathImage !== "" && file_exists($pathImage)) {
            unlink($pathImage);
        }

        if ($pathModel !== "" && file_exists($pathModel)) {
            unlink($pathModel);
        }

        // Getting the rowCount value which is returned at the end of deletePokemonFromList function
        $rowCount = $this->pokemonManager->deletePokemonFromList($id);
        $validationMessage = $rowCount == 1 ? 'Le pokémon a bien été retiré de la liste!' : 'erreur!';
        $_SESSION['dlt_pok_msg'] = $validationMessage;
        header('Location: /Pokemon/list');
        // dans la fonction delete on doit rajouter la suppression du fichier
    }

    /**
     * Check if an attack or type exist in a pokemon with name
     */
    private function isValueExist($value, $array)
    {
        foreach($array as $item){
            if($item['id'] == $value){
                return $item['id'];
            }
        }
        return false;
    }


    public function update($id)
    {
        $this->checkLogin();
        
        $allowedMime = ['jpg', 'jpeg', 'png' ];
        $sizeMax = 2000000;
        $newPokemon = [];
        $errors = [];
        $types = $this->typeManager->selectAll();
        $attacks = $this->attackManager->selectAll();
        $oldPokemon = $this->pokemonManager->selectOneByIdWithAttackTypes($id);
        $pokemonId = intval($oldPokemon['id']);

        // first value of pokemon
        if ($oldPokemon) {
            if (($_SERVER['REQUEST_METHOD'] == 'POST')) {
                // checking if new value 'name' is set
                if (isset($_POST['name']) && trim($_POST['name']) !== '') {
                    $newPokemon['name'] = $_POST['name'];
                } else {
                    $newPokemon['name'] = $oldPokemon['name'];
                }

                //pour chaque type initialisé avec les anciennes
                    //si valeur != "" et pas déjà présente dans les anciennes
                        //si type$i déjà attribué alors update type actuel = nouvelle valeur (type$i = $oldPokemon['types'][$i-1]))
                        //sinon insert type actuel = nouvelle valeur
                for ($i = 1; $i <= 2; $i++) {
                    $newValue = intval($_POST['type' . $i]);

                    if (isset($oldPokemon['types'])) {
                        if ($newValue !== 0) {
                            if (!$this->isValueExist($newValue, $oldPokemon['types'])) {
                                if (isset($oldPokemon['types'][$i-1]['id'])) {
                                    $this->pokemonManager->updatePokemonType($pokemonId, $newValue, $oldPokemon['types'][$i-1]['id']);
                                } else {
                                    $this->pokemonManager->addTypeToPokemon($newValue, $pokemonId);
                                }
                            }
                        } elseif ($newValue == 0 && isset($oldPokemon['types'][$i-1]['id'])) {
                            $this->pokemonManager->deletePokemonType($pokemonId, $oldPokemon['types'][$i-1]['id']);
                        }
                    }  else {
                        if($newValue !== 0){
                            $this->pokemonManager->addTypeToPokemon($newValue, $pokemonId);
                        }
                    }
                }

                for ($i = 1; $i <= 4; $i++) {
                    $newValue = intval($_POST['attack' . $i]);

                    if (isset($oldPokemon['attacks'])) {
                        if ($newValue !== 0) {
                            if (!$this->isValueExist($newValue, $oldPokemon['attacks'])) {
                                if (isset($oldPokemon['attacks'][$i-1]['id'])) {
                                    $this->pokemonManager->updatePokemonattack($pokemonId, $newValue, $oldPokemon['attacks'][$i-1]['id']);
                                } else {
                                    $this->pokemonManager->addAttackToPokemon($newValue, $pokemonId);
                                }
                            }
                        } elseif ($newValue == 0 && isset($oldPokemon['attacks'][$i-1]['id'])) {
                            $this->pokemonManager->deletePokemonAttack($pokemonId, $oldPokemon['attacks'][$i-1]['id']);
                        }
                    }  else {
                        if($newValue !== 0){
                            $this->pokemonManager->addAttackToPokemon($newValue, $pokemonId);
                        }
                    }
                }
    
                // same for image
                if (isset($_FILES['image']['name']) && $_FILES['image']['name'] !== '') {
                    if (empty($errors)) {
                        try {
                            $file = $_FILES['image'];
                            if (!in_array(pathinfo($file['name'], PATHINFO_EXTENSION), $allowedMime)) {
                                throw new Exception("Format file " . $file['name'] . " is not accepted.");
                            }
                            if ($file['size'] > $sizeMax) {
                                throw new Exception("File " . $file['name'] . " is too big : " . $file['size'] . "($sizeMax Octets MAX) ");
                            }
                            // Destroy old file
                            $pathImage = substr($oldPokemon['image'], 1);
                            if (file_exists($pathImage)) {
                                unlink($pathImage);
                            }
                            unset($oldPokemon['image']);
                            //Upload le fichier
                            $uploadDir = "assets/images/";
                            $extension = pathinfo($file['name'], PATHINFO_EXTENSION);
                            $fileNameUpload = uniqid() . '.' . $extension;
                            $uploadFile = $uploadDir . $fileNameUpload;
                            move_uploaded_file($file['tmp_name'], $uploadFile);
                            $newPokemon['image'] = '/' . $uploadFile;
                        } catch (Exception $e) {
                            $errors[] =  $e->getMessage();
                        }
                    }
                } else {
                    $newPokemon['image'] = $oldPokemon['image'];
                }
                // same for 3d model
                if (isset($_FILES['model3d']['name']) && $_FILES['model3d']['name'] !== '') {
                    if (empty($errors)) {
                        try {
                            $file = $_FILES['model3d'];
                            if (pathinfo($file['name'], PATHINFO_EXTENSION) !== "glb") {
                                throw new Exception("Format file " . $file['name'] . " is not accepted.");
                            }
                            if ($file['size'] > $sizeMax) {
                                throw new Exception("File " . $file['name'] . " is too big : " . $file['size'] . "($sizeMax Octets MAX) ");
                            }
                            // Destroy old file
                            $pathModel = substr($oldPokemon['model3d'], 1);
                            if (file_exists($pathModel)) {
                                unlink($pathModel);
                            }
                            //Upload le fichier
                            $uploadDir = "assets/models/";
                            $extension = pathinfo($file['name'], PATHINFO_EXTENSION);
                            $fileNameUpload = uniqid() . '.' . $extension;
                            $uploadFile = $uploadDir . $fileNameUpload;
                            move_uploaded_file($file['tmp_name'], $uploadFile);
                            $newPokemon['model3d'] = '/' . $uploadFile;
                        } catch (Exception $e) {
                            $errors[] =  $e->getMessage();
                        }
                    }
                } else {
                    $newPokemon['model3d'] = $oldPokemon['model3d'];
                }
                if (!empty($errors)) {
                    return $this->twig->render('Pokemon/update.html.twig', ['id' => $id, 'pokemon' => $oldPokemon, 'errors' => $errors, 'types' => $types, 'attacks' => $attacks]);
                } else {
                    $this->pokemonManager->updatePokemon($newPokemon, $oldPokemon);
                    header('Location: /pokemon/details/' . $pokemonId);
                }
            }
            return $this->twig->render('Pokemon/update.html.twig', ['id' => $id, 'pokemon' => $oldPokemon, 'types' => $types, 'attacks' => $attacks]);
        } else {
            header('Location: /');
        }
    }



    /**
     * Check if the user is logged in as admin
     */
    private function checkLogin()
    {
        //Redirect to HOME if we are not logged in as admin
        if (!isset($_SESSION['userRole'])) {
            header('Location: /');
        } elseif ($_SESSION['userRole'] != 'admin') {
            header('Location: /');
        }
    }
}
