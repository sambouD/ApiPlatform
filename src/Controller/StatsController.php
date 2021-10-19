<?php

namespace App\Controller;

use App\Repository\AdherentRepository;
use App\Repository\LivreRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class StatsController extends AbstractController
{
    /**
     * @Route(
     *       path="apiPlatform/adherents/nbPretsParAdherent",
     *       name="adherents_nbPrets",
     *       methods={"Get"} 
     * 
     * )
     */
    public function nombrePretsParAdherent(AdherentRepository $repo)
    {
      $nbPretParAdherent = $repo->nbPretsParAdherent();
    return $this->json($nbPretParAdherent);
    }

    /**
     * Renvoie les 5 meilleurs livres
     * @Route(
     *       path="apiPlatform/livres/meilleurslivres",
     *       name="meilleurslivres",
     *       methods={"Get"} 
     * 
     * )
     */
    public function meilleurslivres(LivreRepository $repo){
         $meilleursLivres = $repo->TrouveMeilleursLivres();
         return $this->json($meilleursLivres);
    }
}
