<?php

namespace App\Controller;

use App\Entity\Livre;
use App\Repository\AuteurRepository;
use App\Repository\EditeurRepository;
use App\Repository\GenreRepository;
use App\Repository\LivreRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class ApiLivreController extends AbstractController
{
    /**
     * Listes des livres
     * @Route("/api/livres", name="api_livres", methods={"GET"})
     */

        public function liste(LivreRepository $repo, SerializerInterface $serializer)
    {
        $livres = $repo->findAll();
        $resultat= $serializer->serialize(
            $livres,
            'json',
            [
                'groups'=>['listeLivreFull']//Group de définition
            ]
        );

        return new JsonResponse($resultat, 200, [], true);//class qui herite de class response
    }

    /**
     * Afficher un livre
     * @Route("/api/livres/{id}", name="api_livres_show", methods={"GET"})
     */
    public function show(Livre $livre, SerializerInterface $serializer)
    {
      
        $resultat= $serializer->serialize(
            $livre,
            'json',
            [
                'groups'=>['listeLivreSimple']//Group de définition
            ]
        );

        return new JsonResponse($resultat, Response::HTTP_OK, [], true);//class qui herite de class response
    }

      /**
     * Crée un livre
     * @Route("/api/livres", name="api_livres_create", methods={"POST"})
     */
    public function create(GenreRepository $genre, EditeurRepository $editeur,  AuteurRepository $auteur,
        Request $req, EntityManagerInterface $manager ,SerializerInterface $serializer, ValidatorInterface $validator)
    {
        $data = $req->getContent();
        $livre = new Livre();
        //Problème de Foreign Key
        $format = "json";
        $dataTab = $serializer->decode($data,  $format);

        $genre = $genre->find($dataTab['genre']['id']);
        $editeur = $editeur->find($dataTab['editeur']['id']);
        $auteur = $auteur->find($dataTab['auteur']['id']);

        $serializer->deserialize($data, Livre::class, 'json', ['object_to_populate' => $livre]);
        $livre->setGenre($genre);
        $livre->setAuteur($auteur);
        $livre->setEditeur($editeur);

        

       // Gestion des erreurs de validation
       $errors = $validator->validate($livre);
       if (count($errors)) {
           $errorsJson = $serializer->serialize($errors, 'json');
           return new JsonResponse($errorsJson, Response::HTTP_BAD_REQUEST, [], true);
       }

     $manager->persist($livre);
     $manager->flush();

       

        return new JsonResponse("Le livre a bien été crée", 
        Response::HTTP_CREATED, 
        [
            "location" => "/api/livres/".$livre->getId()
        ], true);//class qui herite de class response
    }

  /**
     * modifier un Livre
     * @Route("/api/livres/{id}", name="api_livres_update", methods={"PUT"})
     */
    public function update(Livre $livre, GenreRepository $genre, EditeurRepository $editeur,  AuteurRepository $auteur,
    Request $req, EntityManagerInterface $manager, SerializerInterface $serializer, ValidatorInterface $validator)
    {
      
        $data = $req->getContent();
        //Problème de Foreign Key
        $format = "json";
        $dataTab = $serializer->decode($data,  $format);

        $genre = $genre->find($dataTab['genre']['id']);
        $editeur = $editeur->find($dataTab['editeur']['id']);
        $auteur = $auteur->find($dataTab['auteur']['id']);

        $serializer->deserialize($data, Livre::class, 'json', ['object_to_populate' => $livre]);
        $livre->setGenre($genre);
        $livre->setAuteur($auteur);
        $livre->setEditeur($editeur);
      

        
        // Gestion des erreurs de validation
        $errors = $validator->validate($livre);
        if (count($errors)) {
            $errorsJson = $serializer->serialize($errors, 'json');
            return new JsonResponse($errorsJson, Response::HTTP_BAD_REQUEST, [], true);
        }

        $manager->persist($livre);
        $manager->flush();
       

        return new JsonResponse("Le livre a bien été modifier", 
        Response::HTTP_OK, 
        [], true);//class qui herite de class response
        
   
    }

    /**
     *Supprimer un livre
     * @Route("/api/livres/{id}", name="api_livres_delete", methods={"DELETE"})
     */
    public function delete(Livre $livre, EntityManagerInterface $manager)
    {
        $manager->remove($livre);
        $manager->flush();
       

        return new JsonResponse("le livre a bien été supprimer", 
        Response::HTTP_OK, 
        [], false);//class qui herite de class response
        
   
    }

}

