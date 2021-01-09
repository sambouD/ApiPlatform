<?php

namespace App\Controller;

use App\Entity\Adherent;
use App\Repository\AdherentRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class ApiAdherentController extends AbstractController
{
     /**
     * Liste des adherents
     * @Route("/api/adherents", name="api_adherents", methods={"GET"})
     */
    public function liste(AdherentRepository $repo, SerializerInterface $serializer)
    {
        $adherent = $repo->findAll();
        $resultat= $serializer->serialize(
            $adherent,
            'json',
            [
                'groups'=>['listeAdherentFull']//Group de définition
            ]
        );

        return new JsonResponse($resultat, 200, [], true);//class qui herite de class response
    }

     /**
     * Afficher les adherents
     * @Route("/api/adherents/{id}", name="api_adherents_show", methods={"GET"})
     */
    public function show(Adherent $adherent, SerializerInterface $serializer)
    {
      
        $resultat= $serializer->serialize(
            $adherent,
            'json',
            [
                'groups'=>['listeAdherentSimple']//Group de définition
            ]
        );

        return new JsonResponse($resultat, Response::HTTP_OK, [], true);//class qui herite de class response
    }

      /**
     * Crée un adherents
     * @Route("/api/adherents", name="api_adherents_create", methods={"POST"})
     */
    public function create(Request $req, EntityManagerInterface $manager ,SerializerInterface $serializer, ValidatorInterface $validator)
    {
      $data = $req->getContent();
   
     $adherent = $serializer->deserialize($data, Adherent::class, 'json');
     
      // Gestion des erreurs de validation
      $errors = $validator->validate($adherent);
      if (count($errors)) {
          $errorsJson = $serializer->serialize($errors, 'json');
          return new JsonResponse($errorsJson, Response::HTTP_BAD_REQUEST, [], true);
      }
 
     $manager->persist($adherent);
     $manager->flush();

       

        return new JsonResponse("L'adherent a bien été crée", 
        Response::HTTP_CREATED, 
        [
            "location" => "/api/adherents/".$adherent->getId()
        ], true);//class qui herite de class response
    }

     /**
     * modifier un adherent
     * @Route("/api/adherents/{id}", name="api_adherents_update", methods={"PUT"})
     */
    public function update(Adherent $adherent,Request $req, EntityManagerInterface $manager, SerializerInterface $serializer, ValidatorInterface $validator)
    {
      
        $data = $req->getContent();
        $serializer->deserialize($data, Adherent::class, 'json', ['object_to_populate' => $adherent]);
        
        // Gestion des erreurs de validation
        $errors = $validator->validate($adherent);
        if (count($errors)) {
            $errorsJson = $serializer->serialize($errors, 'json');
            return new JsonResponse($errorsJson, Response::HTTP_BAD_REQUEST, [], true);
        }
       
        $manager->persist($adherent);
        $manager->flush();
       

        return new JsonResponse("L'adherent a bien été modifier", 
        Response::HTTP_OK, 
        [], true);//class qui herite de class response
        
   
    }

    /**
     *Supprimer un adherent
     * @Route("/api/adherents/{id}", name="api_adherents_delete", methods={"DELETE"})
     */
    public function delete(Adherent $adherent, EntityManagerInterface $manager)
    {
        $manager->remove($adherent);
        $manager->flush();
       

        return new JsonResponse("L'adherent a bien été supprimer", 
        Response::HTTP_OK, 
        [], false);//class qui herite de class response
        
   
    }


}
