<?php

namespace App\Controller;

use Doctrine\ORM\EntityManagerInterface;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use App\Repository\ChaussetteRepository;
use App\Form\ChaussetteType;
use App\Entity\Chaussette;
use DateTime;

final class ChaussetteController extends AbstractController
{
    #[Route('/chaussette', name: 'app_chaussette')]
    public function index(ChaussetteRepository $repository, Request $request, PaginatorInterface $paginator): Response
    {
        $filters = $this->extraireFiltresRecherche($request);

        $qb = $repository->getFilteredQueryBuilder($filters);

        $pagination = $paginator->paginate(
            $qb,
            $request->query->getInt('page', 1),
            12
        );

        // 4. RÉPONSE AJAX (Pour le rechargement partiel de la page lors du filtrage)
        if ($request->query->get('ajax')) {
            return $this->render('chaussette/_liste_chaussettes.html.twig', [
                'chaussettes' => $pagination
            ]);
        }

        // 5. AFFICHAGE NORMAL (Chargement complet de la page)
        return $this->render('chaussette/index.html.twig', [
            'chaussettes' => $pagination,

            // On passe les variables pour alimenter tes menus déroulants (select) dans le template Twig
            'liste_couleurs' => $repository->findAll(), // Récupère toutes les couleurs possibles
            'liste_types' => $repository->findAll(),    // Récupère tous les types possibles
            'liste_tailles' => $repository->findDistinctTailles(), // Méthode custom

        ]);
    }

    #[Route('/chaussettes/create', name: 'app_chaussette_create')]
    public function create(Request $request, EntityManagerInterface $entityManager, ChaussetteRepository $repo): Response
    {
        $chaussette = new Chaussette();

        // Si tu as ajouté une date de création dans ton entité
        if (method_exists($chaussette, 'setDateCreation')) {
            $chaussette->setDateCreation(new DateTime());
        }

        // Création du formulaire (Tu devras créer ce FormType via la console)
        $form = $this->createForm(ChaussetteType::class, $chaussette);
        $form->handleRequest($request);

        if ($form->isSubmitted()) {
            if ($form->isValid()) {

                // 1. Génération d'un nom automatique si l'utilisateur n'en a pas mis
                if (empty($chaussette->getNomChaussette())) {
                    $nomAutomatique = $this->genererNomChaussette($repo);
                    $chaussette->setNomChaussette($nomAutomatique);
                }

                try {
                    // 2. Sauvegarde en base de données
                    $entityManager->persist($chaussette);
                    $entityManager->flush();

                    $this->addFlash('success', 'Nouvelle chaussette ajoutée avec succès ! Nom : ' . $chaussette->getNomChaussette());

                    // Redirection vers la liste
                    return $this->redirectToRoute('app_chaussette_index');

                } catch (\Exception $e) {
                    // Petit log en cas d'erreur inattendue
                    $this->addFlash('danger', 'Erreur lors de la sauvegarde de la chaussette. Vérifiez les données.');
                }
            } else {
                $this->addFlash('danger', 'Le formulaire contient des erreurs. Veuillez vérifier les champs.');
            }
        }

        return $this->render('chaussette/create.html.twig', [
            'form' => $form->createView(),
            // Tu peux passer d'autres variables si tu as besoin d'autocomplétion (comme pour les tailles)
            'liste_tailles' => $repo->findDistinctTailles(),
        ]);
    }

    private function extraireFiltresRecherche(Request $request): array
    {
        return [
            'date_creation' => $request->query->get('date_creation'),
            'couleur' => $request->query->get('couleur'),
            'couple' => $request->query->has('couple') && $request->query->get('couple') !== ''
                ? $request->query->get('couple') === '1'
                : null,
            'taille' => $request->query->get('taille'),
            'type' => $request->query->get('type'),
        ];
    }

    /**
     * Fonction très simple pour générer un nom automatique du style "Chaussette_42"
     */
    private function genererNomChaussette(ChaussetteRepository $repo): string
    {
        // On va chercher la toute dernière chaussette enregistrée (triée par ID décroissant)
        $derniereChaussette = $repo->findOneBy([], ['id' => 'DESC']);

        if ($derniereChaussette) {
            $prochainId = $derniereChaussette->getId() + 1;
        } else {
            // C'est la toute première chaussette
            $prochainId = 1;
        }

        return 'Chaussette_' . $prochainId;
    }
}
