<?php

namespace App\Controller;

use App\Entity\Pret;
use App\Entity\Livre;
use App\Repository\PretRepository;
use App\Repository\LivreRepository;
use App\Repository\AdherentRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class ApiPretController extends AbstractController
{
    /**
     * @Route("/api/pret", name="api_pret")
     */
 /**
     * Listes des prets
     * @Route("/api/prets", name="api_prets", methods={"GET"})
     */

    public function liste(PretRepository $repo, SerializerInterface $serializer)
    {
        $prets = $repo->findAll();
        $resultat= $serializer->serialize(
            $prets,
            'json',
            [
                'groups'=>['listePretFull']//Group de définition
            ]
        );

        return new JsonResponse($resultat, 200, [], true);//class qui herite de class response
    }

    /**
     * Afficher un pret
     * @Route("/api/prets/{id}", name="api_prets_show", methods={"GET"})
     */
    public function show(Pret $pret, SerializerInterface $serializer)
    {
      
        $resultat= $serializer->serialize(
            $pret,
            'json',
            [
                'groups'=>['listePretSimple']//Group de définition
            ]
        );

        return new JsonResponse($resultat, Response::HTTP_OK, [], true);//class qui herite de class response
    }

      /**
     * Crée un pret
     * @Route("/api/prets", name="api_prets_create", methods={"POST"})
     */
    public function create(LivreRepository $livre, AdherentRepository $adherent,
        Request $req, EntityManagerInterface $manager ,SerializerInterface $serializer, ValidatorInterface $validator)
    {
        $data = $req->getContent();
        //Problème de Foreign Key
        $pret = new Pret();
        $format = "json";
        $dataTab = $serializer->decode($data,  $format);

        $livre = $livre->find($dataTab['livre']['id']);
        $adherent = $adherent->find($dataTab['adherent']['id']);
        
        $serializer->deserialize($data, Pret::class, 'json', ['object_to_populate' => $pret]);

        $pret->setLivre($livre);
        $pret->setAdherent($adherent);
        

       // Gestion des erreurs de validation
       $errors = $validator->validate($pret);
       if (count($errors)) {
           $errorsJson = $serializer->serialize($errors, 'json');
           return new JsonResponse($errorsJson, Response::HTTP_BAD_REQUEST, [], true);
       }

     $manager->persist($pret);
     $manager->flush();

       

        return new JsonResponse("Le pret a bien été crée", 
        Response::HTTP_CREATED, 
        [
            "location" => "/api/prets/".$pret->getId()
        ], true);//class qui herite de class response
    }

  /**
     * modifier un Pret
     * @Route("/api/prets/{id}", name="api_prets_update", methods={"PUT"})
     */
    public function update(Pret $pret, LivreRepository $livre, AdherentRepository $adherent,
    Request $req, EntityManagerInterface $manager, SerializerInterface $serializer, ValidatorInterface $validator)
    {
      
        $data = $req->getContent();
        //Problème de Foreign Key
        $format = "json";
        $dataTab = $serializer->decode($data,  $format);

        $livre = $livre->find($dataTab['livre']['id']);
        $adherent = $adherent->find($dataTab['adherent']['id']);
        
        $serializer->deserialize($data, Pret::class, 'json', ['object_to_populate' => $pret]);

        $pret->setLivre($livre);
        $pret->setAdherent($adherent);

        
        // Gestion des erreurs de validation
        $errors = $validator->validate($pret);
        if (count($errors)) {
            $errorsJson = $serializer->serialize($errors, 'json');
            return new JsonResponse($errorsJson, Response::HTTP_BAD_REQUEST, [], true);
        }

        $manager->persist($pret);
        $manager->flush();
       

        return new JsonResponse("Le pret a bien été modifier", 
        Response::HTTP_OK, 
        [], true);//class qui herite de class response
        
   
    }

    /**
     *Supprimer un pret
     * @Route("/api/prets/{id}", name="api_prets_delete", methods={"DELETE"})
     */
    public function delete(Pret $pret, EntityManagerInterface $manager)
    {
        $manager->remove($pret);
        $manager->flush();
       

        return new JsonResponse("le pret a bien été supprimer", 
        Response::HTTP_OK, 
        [], false);//class qui herite de class response
        
   
    }
}
