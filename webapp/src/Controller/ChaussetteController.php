<?php

namespace App\Controller;

use Knp\Component\Pager\PaginatorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use App\Repository\ChaussetteRepository;

final class ChaussetteController extends AbstractController
{
    #[Route('/chaussette', name: 'app_chaussette')]
    public function index(ChaussetteRepository $repository, Request $request,PaginatorInterface $paginator): Response
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
            'liste_types'    => $repository->findAll(),    // Récupère tous les types possibles
            'liste_tailles'  => $repository->findDistinctTailles(), // Méthode custom

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
}
