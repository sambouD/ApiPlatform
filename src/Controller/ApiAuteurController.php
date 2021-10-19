<?php

namespace App\Controller;

use App\Entity\Auteur;
use App\Repository\AuteurRepository;
use App\Repository\NationaliteRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class ApiAuteurController extends AbstractController
{
     /**
     * Liste des auteurs
     * @Route("/api/auteurs", name="api_auteurs", methods={"GET"})
     */
    public function liste(AuteurRepository $repo, SerializerInterface $serializer)
    {
        $auteurs = $repo->findAll();
        $resultat= $serializer->serialize(
            $auteurs,
            'json',
            [
                'groups'=>['listeAuteurFull']//Group de définition
            ]
        );

        return new JsonResponse($resultat, 200, [], true);//class qui herite de class response
    }

    /**
     * Afficher un auteur
     * @Route("/api/auteurs/{id}", name="api_auteurs_show", methods={"GET"})
     */
    public function show(Auteur $auteur, SerializerInterface $serializer)
    {
      
        $resultat= $serializer->serialize(
            $auteur,
            'json',
            [
                'groups'=>['listeAuteurSimple']//Group de définition
            ]
        );

        return new JsonResponse($resultat, Response::HTTP_OK, [], true);//class qui herite de class response
    }

      /**
     * Crée un auteur
     * @Route("/api/auteurs", name="api_auteurs_create", methods={"POST"})
     */
    public function create(NationaliteRepository $nationalite, Request $req, EntityManagerInterface $manager ,
    SerializerInterface $serializer, ValidatorInterface $validator)
    {
      $data = $req->getContent();
      $format = "json";
      $dataTab = $serializer->decode($data, $format);
      $auteur = new Auteur();
      $nationalite = $nationalite->find($dataTab['nationalite']['id']);
      
      $serializer->deserialize($data, Auteur::class, 'json', ['object_to_populate' => $auteur]);
      $auteur->setNationalite($nationalite);

       // Gestion des erreurs de validation
       $errors = $validator->validate($auteur);
       if (count($errors)) {
           $errorsJson = $serializer->serialize($errors, 'json');
           return new JsonResponse($errorsJson, Response::HTTP_BAD_REQUEST, [], true);
       }

     $manager->persist($auteur);
     $manager->flush();

       

        return new JsonResponse("L'auteur a bien été crée", 
        Response::HTTP_CREATED, 
        [
            "location" => "/api/auteurs/".$auteur->getId()
        ], true);//class qui herite de class response
    }

  /**
     * modifier un Auteur
     * @Route("/api/auteurs/{id}", name="api_auteurs_update", methods={"PUT"})
     */
    public function update(Auteur $auteur,Request $req,NationaliteRepository $nationalite, EntityManagerInterface $manager,
     SerializerInterface $serializer, ValidatorInterface $validator)
    {
      
        $data = $req->getContent();
        //Problème de Foreign Key
        $format = "json";
        $dataTab = $serializer->decode($data,  $format);
        $nationalite = $nationalite->find($dataTab['nationalite']['id']);
        
        $serializer->deserialize($data, Auteur::class, 'json', ['object_to_populate' => $auteur]);
        $auteur->setNationalite($nationalite);
        
        // Gestion des erreurs de validation
        $errors = $validator->validate($auteur);
        if (count($errors)) {
            $errorsJson = $serializer->serialize($errors, 'json');
            return new JsonResponse($errorsJson, Response::HTTP_BAD_REQUEST, [], true);
        }

        $manager->persist($auteur);
        $manager->flush();
       
        return new JsonResponse("L'auteur a bien été modifier", 
        Response::HTTP_OK, 
        [], true);//class qui herite de class response
   
    }

    /**
     *Supprimer un auteur
     * @Route("/api/auteurs/{id}", name="api_auteurs_delete", methods={"DELETE"})
     */
    public function delete(Auteur $auteur, EntityManagerInterface $manager)
    {
        $manager->remove($auteur);
        $manager->flush();
       

        return new JsonResponse("L'auteur a bien été supprimer", 
        Response::HTTP_OK, 
        [], false);//class qui herite de class response
        
   
    }
}
