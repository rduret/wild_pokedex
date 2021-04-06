<?php

namespace App\Controller;

use App\Model\UserManager;

class UserController extends AbstractController
{
    private UserManager $userManager;

    public function __construct()
    {
        parent::__construct();
        $this->userManager = new UserManager();
    }

    /**
     * List all trainers
     */
    public function list()
    {
        $trainers = $this->userManager->selectByRole(2);

        return $this->twig->render('Trainers/list.html.twig', ['trainers' => $trainers]);
    }

    /**
     * Delete a trainer
     */
    public function delete(int $id)
    {
        if (isset($_SESSION['username']) && $_SESSION['userRole'] == "admin") {
            // Getting the rowCount value which is returned at the end of deleteTrainerById function
            $rowCount = $this->userManager->deleteTrainerById($id);
            $validationMessage = $rowCount == 1 ? 'Le trainer a bien été supprimé!' : 'erreur!';
            // créer une variable de session $_SESSION['dlt_trainer_msg']
            $_SESSION['dlt_trainer_msg'] = $validationMessage;
            header('Location: /User/list');
        } else {
            header('Location: /');
        }
    }
}
