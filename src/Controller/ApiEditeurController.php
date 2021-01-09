<?php

namespace App\Controller;

use App\Entity\Editeur;
use App\Repository\EditeurRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class ApiEditeurController extends AbstractController
{
     /**
     * Liste des editeurs
     * @Route("/api/editeurs", name="api_editeurs", methods={"GET"})
     */
    public function liste(EditeurRepository $repo, SerializerInterface $serializer)
    {
        $editeur = $repo->findAll();
        $resultat= $serializer->serialize(
            $editeur,
            'json',
            [
                'groups'=>['listeEditeurFull']//Group de définition
            ]
        );

        return new JsonResponse($resultat, 200, [], true);//class qui herite de class response
    }

     /**
     * Afficher les editeurs
     * @Route("/api/editeurs/{id}", name="api_editeurs_show", methods={"GET"})
     */
    public function show(Editeur $editeur, SerializerInterface $serializer)
    {
      
        $resultat= $serializer->serialize(
            $editeur,
            'json',
            [
                'groups'=>['listeEditeurSimple']//Group de définition
            ]
        );

        return new JsonResponse($resultat, Response::HTTP_OK, [], true);//class qui herite de class response
    }

      /**
     * Crée un editeur
     * @Route("/api/editeurs", name="api_editeurs_create", methods={"POST"})
     */
    public function create(Request $req, EntityManagerInterface $manager ,SerializerInterface $serializer,  ValidatorInterface $validator)
    {
      $data = $req->getContent();

     $editeur = $serializer->deserialize($data, Editeur::class, 'json');

     // Gestion des erreurs de validation
     $errors = $validator->validate($editeur);
     if (count($errors)) {
         $errorsJson = $serializer->serialize($errors, 'json');
         return new JsonResponse($errorsJson, Response::HTTP_BAD_REQUEST, [], true);
     }

     $manager->persist($editeur);
     $manager->flush();

       

        return new JsonResponse("L'editeur a bien été crée", 
        Response::HTTP_CREATED, 
        [
            "location" => "/api/editeurs/".$editeur->getId()
        ], true);//class qui herite de class response
    }

      /**
     * modifier un editeur
     * @Route("/api/editeurs/{id}", name="api_editeurs_update", methods={"PUT"})
     */
    public function update(Editeur $editeur,Request $req, EntityManagerInterface $manager, SerializerInterface $serializer,  ValidatorInterface $validator)
    {
      
        $data = $req->getContent();
        $serializer->deserialize($data, Editeur::class, 'json', ['object_to_populate' => $editeur]);

     // Gestion des erreurs de validation
     $errors = $validator->validate($editeur);
     if (count($errors)) {
         $errorsJson = $serializer->serialize($errors, 'json');
         return new JsonResponse($errorsJson, Response::HTTP_BAD_REQUEST, [], true);
     }

        $manager->persist($editeur);
        $manager->flush();
       

        return new JsonResponse("L'editeur a bien été modifier", 
        Response::HTTP_OK, 
        [], true);//class qui herite de class response
        
   
    }

     /**
     *Supprimer un éditeur
     * @Route("/api/editeurs/{id}", name="api_editeurs_delete", methods={"DELETE"})
     */
    public function delete(Editeur $editeur, EntityManagerInterface $manager)
    {
        $manager->remove($editeur);
        $manager->flush();
       

        return new JsonResponse("L'editeur a bien été supprimer", 
        Response::HTTP_OK, 
        [], false);//class qui herite de class response
        
   
    }


}
