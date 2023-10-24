<?php

namespace App\Controller;

use App\Entity\Airport;
use App\Form\AdminAirportType;
use App\Repository\AirportRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

#[Route('/admin/airport')]
class AdminAirportController extends AbstractController
{
    // Page pour les Aéroports
    #[Route('/', name: 'app_admin_airport_index')]
    public function index(AirportRepository $airportRepository, EntityManagerInterface $entityManager, Request $request): Response
    {
        $title = 'Admin Airport';
        $airport = new Airport();

        // Récupère les informations de chaque ligne d'éléments du tableau Airport
        $airports = $airportRepository->findAll();

        // Crée un tableau avec 2 paramètres pris en compte AdminAirportType(c'est le formulaire qu'on à crée dans le dossier Form) & $airport(c'est des données qu'on à récupérer plutôt qui proviennent de la variable $airport qui une nouvelle instance de l'entité Airport )
        $form = $this->createForm(AdminAirportType::class, $airport);

        // Regarde les modifications du formulaire
        $form->handleRequest($request);

        // Si le form est soumis & valide donc le if exécuteras les lignes de commandes suivantes
        if ($form->isSubmitted() && $form->isValid()) {
            // Il prend une information: la région, qui vient du formulaire créé précédement.
            // Il va transformer cette région qui est de base en chiffre (int) et qui transformeras la région en mots (string)
            // La fonction setZone() permet de donner à la variable $airport la région en mots
            $airport->setZone($this->region($form->get('region')->getData()));

            // La fonction permet d'enrengistrer de nouvelle données
            $entityManager->persist($airport);
            //Synchronisation avec la base de donnée, puis vide,la mémoire attribuée à $airport
            $entityManager->flush();

            // Permet d'envoyer un message flash (avec 2 paramètres qui sont le nom du message et le contenu du message)
            $this->addFlash('crud', 'Aéroport ajouté avec succès');

            // Redirige vers la page /admin/airport/
            return $this->redirectToRoute('app_admin_airport_index', [], Response::HTTP_SEE_OTHER);
        }

        // Permet de faire un rendu de la page admin_airport/index.html.twig qui comprend la liste des aéroports, le titre de la page et le formulaire 
        return $this->render('admin_airport/index.html.twig', compact('airports', 'title', 'form'));
    }

    // Cette route sert a supprimer l'aéroport choisie via l'id. il est retirer de la base de donnée .
    #[Route('/delete/{id}', name: 'app_admin_airport_delete')]
    public function delete(Airport $airport, EntityManagerInterface $entityManager, Request $request): Response
    {
        // CSRF: Cross Site Request Forgery
        // Sur la page index.html.twig, on a un champs _token, qui est récupéré par la fonction ci-dessous par la deuxième variable. La fonction vérifie alors si le _token correspond à la chaine de charactères placé en tant que premier paramètre.
        if ($this->isCsrfTokenValid('delete' . $airport->getId(), $request->request->get('_token'))) {
            // Permet de suprimer l'aéroport ($aéroport) qui est dans la base donnée
            $entityManager->remove($airport);
            $entityManager->flush();
            $this->addFlash('crud', 'Aéroport supprimé avec succès');
        }

        return $this->redirectToRoute('app_admin_airport_index', [], Response::HTTP_SEE_OTHER);
    }

    // Cette route sert a éditer l'aéroport choisie via l'id ensuite les modifications appliqués seront envoyer à la base de donnée 
    #[Route('/edit/{id}', name: 'app_admin_airport_edit')]
    public function edit(Airport $airport, AirportRepository $airportRepository, EntityManagerInterface $entityManager, Request $request): Response
    {
        $title = 'Admin Airport Edit';
        $button_label = 'Modifier';
        $airports = $airportRepository->findAll();
        $form = $this->createForm(AdminAirportType::class, $airport);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();
            $this->addFlash('crud', 'Aéroport modifié avec succès');

            return $this->redirectToRoute('app_admin_airport_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('admin_airport/index.html.twig', compact('airports', 'form', 'title', 'button_label'));
    }

    //
    #[Route('/show/{id}', name: 'app_admin_airport_show')]
    public function show(Airport $airport): Response
    {
        $title = 'Admin Airport Show';

        return $this->render('admin_airport/show.html.twig', compact('airport', 'title'));
    }

    /**
     * La fonction sert à transformer un chiffre mis en paramètres en chaine de charactères (le nom de la région) 
     * 
     * En paramètre, la fonction prend un chiffre de 0 à 17 pour en faire une chaine de charactères qui est le nom de la région à laquelle in est lié. 
     * @param integer $valeur
     * @return string
     */
    private function region(int $valeur): string
    {
        switch ($valeur) {
            case 0:
                $region = 'Auvergne-Rhône-Alpes';
                break;
            case 1:
                $region = 'Bourgogne-Franche-Comté';
                break;
            case 2:
                $region = 'Bretagne';
                break;
            case 3:
                $region = 'Centre-Val de Loire';
                break;
            case 4:
                $region = 'Corse';
                break;
            case 5:
                $region = 'Grand Est';
                break;
            case 6:
                $region = 'Hauts-de-France';
                break;
            case 7:
                $region = 'Ile-de-France';
                break;
            case 8:
                $region = 'Normandie';
                break;
            case 9:
                $region = 'Nouvelle-Aquitaine';
                break;
            case 10:
                $region = 'Occitanie';
                break;
            case 11:
                $region = 'Pays de la Loire';
                break;
            case 12:
                $region = 'Provence-Alpes-Côte d\'Azur';
                break;
            case 13:
                $region = 'Guadeloupe';
                break;
            case 14:
                $region = 'Guyane';
                break;
            case 15:
                $region = 'Martinique';
                break;
            case 16:
                $region = 'La Réunion';
                break;
            case 17:
                $region = 'Mayotte';
                break;
        }

        return $region;
    }
}
