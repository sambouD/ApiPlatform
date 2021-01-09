<?php

namespace App\Controller;

use App\Entity\Nationalite;
use Doctrine\ORM\EntityManagerInterface;
use App\Repository\NationaliteRepository;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class ApiNationaliteController extends AbstractController
{
   /**
     * Liste des nationalites
     * @Route("/api/nationalites", name="api_nationalites", methods={"GET"})
     */
    public function liste(NationaliteRepository $repo, SerializerInterface $serializer)
    {
        $nationalite = $repo->findAll();
        $resultat= $serializer->serialize(
            $nationalite,
            'json',
            [
                'groups'=>['listeNationaliteFull']//Group de définition
            ]
        );

        return new JsonResponse($resultat, 200, [], true);//class qui herite de class response
    }

    /**
     * Afficher les nationalites
     * @Route("/api/nationalites/{id}", name="api_nationalites_show", methods={"GET"})
     */
    public function show(Nationalite $nationalite, SerializerInterface $serializer)
    {
      
        $resultat= $serializer->serialize(
            $nationalite,
            'json',
            [
                'groups'=>['listeNationaliteSimple']//Group de définition
            ]
        );

        return new JsonResponse($resultat, Response::HTTP_OK, [], true);//class qui herite de class response
    }

      /**
     * Crée un nationalite
     * @Route("/api/nationalites", name="api_nationalites_create", methods={"POST"})
     */
    public function create(Request $req, EntityManagerInterface $manager ,SerializerInterface $serializer, ValidatorInterface $validator)
    {
      $data = $req->getContent();
     $nationalite = $serializer->deserialize($data, Nationalite::class, 'json');

     // Gestion des erreurs de validation
     $errors = $validator->validate($nationalite);
     if (count($errors)) {
         $errorsJson = $serializer->serialize($errors, 'json');
         return new JsonResponse($errorsJson, Response::HTTP_BAD_REQUEST, [], true);
     }

     $manager->persist($nationalite);
     $manager->flush();

       

        return new JsonResponse("La nationalité a bien été crée", 
        Response::HTTP_CREATED, 
        [
            "location" => "/api/nationalites/".$nationalite->getId()
        ], true);//class qui herite de class response
    }

    
    /**
     * modifier une nationalité
     * @Route("/api/nationalites/{id}", name="api_nationalites_update", methods={"PUT"})
     */
    public function update(Nationalite $nationalite,Request $req, EntityManagerInterface $manager, SerializerInterface $serializer, ValidatorInterface $validator)
    {
      
        $data = $req->getContent();
        $serializer->deserialize($data, Nationalite::class, 'json', ['object_to_populate' => $nationalite]);
            
        // Gestion des erreurs de validation
        $errors = $validator->validate($nationalite);
        if (count($errors)) {
            $errorsJson = $serializer->serialize($errors, 'json');
            return new JsonResponse($errorsJson, Response::HTTP_BAD_REQUEST, [], true);
        }
        
        $manager->persist($nationalite);
        $manager->flush();
       

        return new JsonResponse("La nationalité a bien été modifier", 
        Response::HTTP_OK, 
        [], true);//class qui herite de class response
        
   
    }

    /**
     *Supprimer une nationalite
     * @Route("/api/nationalites/{id}", name="api_nationalites_delete", methods={"DELETE"})
     */
    public function delete(Nationalite $nationalite, EntityManagerInterface $manager)
    {
        $manager->remove($nationalite);
        $manager->flush();
       

        return new JsonResponse("La nationalite a bien été supprimer", 
        Response::HTTP_OK, 
        [], false);//class qui herite de class response
        
   
    }

}
