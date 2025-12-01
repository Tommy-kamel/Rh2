<?php

namespace app\controllers\admin;

use app\models\employe\EmployeeModel;
use app\models\employe\DocumentModel;
use Flight;

class EmployeeController
{
    private $model;
    private $docModel;

    public function __construct()
    {
        $this->model = new EmployeeModel();
        $this->docModel = new DocumentModel();
    }

    public function afficher($id)
    {
        $employee = $this->model->getById($id);
        $postes = $this->model->getPosteHistory($id);
        $contrats = $this->model->getContratHistory($id);
        $documents = $this->docModel->listByEmployee($id);

       
        $postes_list = $this->model->getAllPostes();
        $departements_list = $this->model->getAllDepartements();

        Flight::render('admin/employee_profile', [
            'employee' => $employee,
            'postes' => $postes,
            'contrats' => $contrats,
            'documents' => $documents,
            'postes_list' => $postes_list,
            'departements_list' => $departements_list
        ]);
    }

    public function save($id)
    {
        $data = [
            'nom' => Flight::request()->data->nom,
            'prenom' => Flight::request()->data->prenom,
            'email' => Flight::request()->data->email,
            'telephone' => Flight::request()->data->telephone,
            'adresse' => Flight::request()->data->adresse,
            'cin' => Flight::request()->data->cin,
            'lieu_naissance' => Flight::request()->data->lieu_naissance,
            'photo' => null
        ];

        // Gérer upload photo
        if (!empty($_FILES['photo']['tmp_name'])) {
            $destDir = __DIR__ . '/../../public/uploads/documents/';
            if (!is_dir($destDir)) mkdir($destDir, 0755, true);
            $filename = time() . '_' . basename($_FILES['photo']['name']);
            $target = $destDir . $filename;
            if (move_uploaded_file($_FILES['photo']['tmp_name'], $target)) {
                $data['photo'] = '/uploads/documents/' . $filename;
            }
        }

        $this->model->update($id, $data);
        Flight::redirect("/employes/{$id}");
    }

    public function uploadDocument($id)
    {
        if (!empty($_FILES['document']['tmp_name'])) {
            $destDir = __DIR__ . '/../../public/uploads/documents/';
            if (!is_dir($destDir)) mkdir($destDir, 0755, true);
            $filename = time() . '_' . basename($_FILES['document']['name']);
            $target = $destDir . $filename;
            if (move_uploaded_file($_FILES['document']['tmp_name'], $target)) {
                $type = Flight::request()->data->type ?? null;
                $this->docModel->addDocument($id, $_FILES['document']['name'], '/uploads/documents/' . $filename, $type);
            }
        }
        Flight::redirect("/employes/{$id}");
    }


        public function creerContrat($id)
    {
        $data = [
            'salaire' => Flight::request()->data->salaire ?? null,
            'date_debut' => Flight::request()->data->date_debut ?? null,
            'date_fin' => Flight::request()->data->date_fin ?? null,
            'type' => Flight::request()->data->type ?? 'CDI',
            'id_poste' => Flight::request()->data->id_poste ?? null,
            'id_departement' => Flight::request()->data->id_departement ?? null,
            'periode_essai_jours' => Flight::request()->data->periode_essai_jours ?? 0
        ];

        $newId = $this->model->createContract($id, $data);
        if ($newId) {
            $this->model->addContratHistory($newId, $id, $data);
        }
        Flight::redirect("/employes/{$id}");
    }

        public function renouvelerContrat($id)
    {
        $id_contrat = Flight::request()->data->id_contrat ?? null;
        $newData = [
            'salaire' => Flight::request()->data->salaire ?? null,
            'date_debut' => Flight::request()->data->date_debut ?? null,
            'date_fin' => Flight::request()->data->date_fin ?? null,
            'type' => Flight::request()->data->type ?? null,
            'id_poste' => Flight::request()->data->id_poste ?? null,
            'id_departement' => Flight::request()->data->id_departement ?? null,
            'periode_essai_jours' => Flight::request()->data->periode_essai_jours ?? 0
        ];

        if ($id_contrat) {
            $this->model->renewContract($id_contrat, $newData);
        }
        Flight::redirect("/employes/{$id}");
    }

    public function terminerContrat($id)
    {
        $id_contrat = Flight::request()->data->id_contrat ?? null;
        $date_fin = Flight::request()->data->date_fin ?? null;
        if ($id_contrat) {
            $this->model->endContract($id_contrat, $date_fin);
        }
        Flight::redirect("/employes/{$id}");
    }

        public function ajouterPosteHistory($id)
    {
        $data = [
            'id_poste' => Flight::request()->data->id_poste ?? null,
            'id_departement' => Flight::request()->data->id_departement ?? null,
            'date_debut' => Flight::request()->data->date_debut ?? null,
            'date_fin' => Flight::request()->data->date_fin ?? null,
            'motif' => Flight::request()->data->motif ?? null
        ];

        if (empty($data['id_poste']) || empty($data['date_debut'])) {
            $_SESSION['flash_message'] = ['type' => 'error', 'message' => 'Poste et date de début obligatoires.'];
            Flight::redirect("/employes/{$id}");
            return;
        }

        $ok = $this->model->addPosteHistory($id, $data);

        // optionnel : appliquer le poste au contrat courant si demandé
        if ($ok && (Flight::request()->data->apply_to_current ?? null) == '1') {
            // Mettre à jour le dernier contrat de l'employé
            $sql = "UPDATE contrat SET id_poste = :id_poste, id_departement = :id_departement WHERE id_contrat = (
                        SELECT id_contrat FROM contrat WHERE id_employe = :id_employe ORDER BY date_debut DESC LIMIT 1
                    )";
            Flight::db()->prepare($sql)->execute([
                'id_poste' => $data['id_poste'],
                'id_departement' => $data['id_departement'],
                'id_employe' => $id
            ]);
        }

        $_SESSION['flash_message'] = ['type' => $ok ? 'success' : 'error', 'message' => $ok ? 'Historique ajouté.' : 'Erreur lors de l\'ajout.'];
        Flight::redirect("/employes/{$id}");
    }

    

    public function contrat($id)
    {
        $employee = $this->model->getById($id);
        $contrats = $this->model->getContratHistory($id);
        Flight::render('admin/employee_contract', [
            'employee' => $employee,
            'contrats' => $contrats
        ]);
    }

    public function postes($id)
    {
        $employee = $this->model->getById($id);
        $postes = $this->model->getPosteHistory($id);
        $postes_list = $this->model->getAllPostes();
        $departements_list = $this->model->getAllDepartements();
        Flight::render('admin/employee_postes', [
            'employee' => $employee,
            'postes' => $postes,
            'postes_list' => $postes_list,
            'departements_list' => $departements_list
        ]);
    }

    public function documents($id)
    {
        $employee = $this->model->getById($id);
        $documents = $this->docModel->listByEmployee($id);
        Flight::render('admin/employee_documents', [
            'employee' => $employee,
            'documents' => $documents
        ]);
    }

        public function ajouter()
    {
        // listes de référence si besoin (postes / départements)
        $postes = $this->model->getAllPostes();
        $departements = $this->model->getAllDepartements();
        Flight::render('admin/employee_create', [
            'postes_list' => $postes,
            'departements_list' => $departements
        ]);
    }

        public function store()
    {
        $data = [
            'nom' => Flight::request()->data->nom ?? '',
            'prenom' => Flight::request()->data->prenom ?? '',
            'date_naissance' => Flight::request()->data->date_naissance ?? null,
            'email' => Flight::request()->data->email ?? null,
            'mot_de_passe' => Flight::request()->data->mot_de_passe ?? 'changeme',
            'sexe' => Flight::request()->data->sexe ?? null,
            'telephone' => Flight::request()->data->telephone ?? null,
            'adresse' => Flight::request()->data->adresse ?? null,
            'numero_cnaps' => Flight::request()->data->numero_cnaps ?? null,
            'cin' => Flight::request()->data->cin ?? null,
            'lieu_naissance' => Flight::request()->data->lieu_naissance ?? null,
            'photo' => null
        ];

        // upload photo si présente
        if (!empty($_FILES['photo']['tmp_name'])) {
            $destDir = __DIR__ . '/../../public/uploads/documents/';
            if (!is_dir($destDir)) mkdir($destDir, 0755, true);
            $filename = time() . '_' . basename($_FILES['photo']['name']);
            $target = $destDir . $filename;
            if (move_uploaded_file($_FILES['photo']['tmp_name'], $target)) {
                $data['photo'] = '/uploads/documents/' . $filename;
            }
        }

        $newId = $this->model->create($data);
        if ($newId) {
            $_SESSION['flash_message'] = ['type' => 'success', 'message' => 'Employé créé.'];
            Flight::redirect("/employes/{$newId}");
        } else {
            $_SESSION['flash_message'] = ['type' => 'error', 'message' => 'Erreur lors de la création.'];
            Flight::redirect('/employes/ajouter');
        }
    }

public function liste()
{
    // Récupération des informations de session
    $user_type = $_SESSION['user_type'] ?? null;
    $nom_departement = $_SESSION['nom_departement'] ?? null;
    $id_departement = $_SESSION['id_departement'] ?? null;

    // Si l'utilisateur appartient au département "Ressources Humaines" (nom ou type), il voit tous les employés
    if ($user_type === 'Ressources Humaines' || $nom_departement === 'Ressources Humaines' || $id_departement === 1) {
        $employes = $this->model->getAllEmployes();
    } else {
        // Sinon filtrer par département via le contrat actif
        if ($id_departement !== null) {
            $employes = $this->model->getEmployesParDepartement($id_departement);
        } else {
            // Par sécurité, si pas de département en session, renvoyer une liste vide
            $employes = [];
        }
    }

    Flight::render('admin/employees_list', [
        'employes' => $employes
    ]);
}

}