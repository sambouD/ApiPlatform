<?php

namespace App\Controller;

use App\Entity\Genre;
use App\Repository\GenreRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class ApiGenreController extends AbstractController
{
    /**
     * Liste des genres
     * @Route("/api/genres", name="api_genres", methods={"GET"})
     */
    public function liste(GenreRepository $repo, SerializerInterface $serializer)
    {
        $genres = $repo->findAll();
        $resultat= $serializer->serialize(
            $genres,
            'json',
            [
                'groups'=>['listeGenreFull']//Group de définition
            ]
        );

        return new JsonResponse($resultat, 200, [], true);//class qui herite de class response
    }

    /**
     * Afficher les genres
     * @Route("/api/genres/{id}", name="api_genres_show", methods={"GET"})
     */
    public function show(Genre $genre, SerializerInterface $serializer)
    {
      
        $resultat= $serializer->serialize(
            $genre,
            'json',
            [
                'groups'=>['listeGenreSimple']//Group de définition
            ]
        );

        return new JsonResponse($resultat, Response::HTTP_OK, [], true);//class qui herite de class response
    }

    /**
     * Crée un genre
     * @Route("/api/genres", name="api_genres_create", methods={"POST"})
     */
    public function create(Request $req, EntityManagerInterface $manager ,SerializerInterface $serializer, ValidatorInterface $validator)
    {
      $data = $req->getContent();
     // $genre = new Genre();
     // $serializer->deserialize($data, Genre::class, 'json', ['object_to_populate' => $genre]);
     $genre = $serializer->deserialize($data, Genre::class, 'json');
    
     // Gestion des erreurs de validation
     $errors = $validator->validate($genre);
     if (count($errors)) {
         $errorsJson = $serializer->serialize($errors, 'json');
         return new JsonResponse($errorsJson, Response::HTTP_BAD_REQUEST, [], true);
     }

     $manager->persist($genre);
     $manager->flush();


        return new JsonResponse("Le genre a bien été crée", 
        Response::HTTP_CREATED, 
        [
            "location" => "/api/genres/".$genre->getId()
        ], true);//class qui herite de class response
    }

    /**
     * modifier un Genre
     * @Route("/api/genres/{id}", name="api_genres_update", methods={"PUT"})
     */
    public function update(Genre $genre,Request $req, EntityManagerInterface $manager, SerializerInterface $serializer, ValidatorInterface $validator)
    {
      
        $data = $req->getContent();
        $serializer->deserialize($data, Genre::class, 'json', ['object_to_populate' => $genre]);
            
     // Gestion des erreurs de validation
     $errors = $validator->validate($genre);
     if (count($errors)) {
         $errorsJson = $serializer->serialize($errors, 'json');
         return new JsonResponse($errorsJson, Response::HTTP_BAD_REQUEST, [], true);
     }

        $manager->persist($genre);
        $manager->flush();
       

        return new JsonResponse("Le genre a bien été modifier", 
        Response::HTTP_OK, 
        [], true);//class qui herite de class response
        
   
    }

    /**
     *Supprimer un genre
     * @Route("/api/genres/{id}", name="api_genres_delete", methods={"DELETE"})
     */
    public function delete(Genre $genre, EntityManagerInterface $manager)
    {
        $manager->remove($genre);
        $manager->flush();
       

        return new JsonResponse("Le genre a bien été supprimer", 
        Response::HTTP_OK, 
        [], false);//class qui herite de class response
        
   
    }

}
