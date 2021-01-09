<?php
namespace App\Controller\Admin;

use App\Entity\Adherent;
use App\Entity\Genre;
use App\Entity\Auteur;
use App\Entity\Editeur;
use App\Entity\Livre;
use App\Entity\Nationalite;
use App\Entity\Pret;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use EasyCorp\Bundle\EasyAdminBundle\Config\MenuItem;
use EasyCorp\Bundle\EasyAdminBundle\Config\Dashboard;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractDashboardController;


class DashboardController extends AbstractDashboardController
{
    /**
     * @Route("/admin", name="admin")
     * 
     * @return Response
     * 
     */
    public function index(): Response
    {
        return parent::index();
    }

    public function configureDashboard(): Dashboard
    {
        return Dashboard::new()
            ->setTitle('ProjetAPI');
    }

    public function configureMenuItems(): iterable
    {
        yield MenuItem::linktoDashboard('Dashboard', 'fa fa-home');
        //Listes des Nationalités
        yield MenuItem::linkToCrud('Listes des Nationalités', 'fas fa-globe', Nationalite::class);
        //Listes des Genres
        yield MenuItem::linkToCrud('Listes des Genres', 'fas fa-book', Genre::class);
        //Listes des Editeurs 
        yield MenuItem::linkToCrud('Listes des Editeurs', 'fas fa-edit', Editeur::class);
        //Listes des Auteurs
        yield MenuItem::linkToCrud('Listes des auteurs', 'fas fa-user', Auteur::class);
        //Listes des Livres 
        yield MenuItem::linkToCrud('Listes des Livres', 'fas fa-book-open', Livre::class);
        //Listes des Adherents
        yield MenuItem::linkToCrud('Listes des Adherents', 'fas fa-address-card', Adherent::class);
        //Listes des Pret
        yield MenuItem::linkToCrud('Listes des Prets', 'fas fa-address-card', Pret::class);


    }
}
