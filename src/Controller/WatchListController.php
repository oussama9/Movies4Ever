<?php


namespace App\Controller;


use App\Entity\WatchList;
use App\Repository\MovieRepository;
use App\Repository\WatchListRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class WatchListController extends ApiController
{
    /**
     * @Route("/lists", methods="GET")
     */
    public function showWatchLists(Request $request, WatchListRepository $watchListRepository)
    {
        $responseData = null;
        if($request->get('id')) {
            $responseData = $watchListRepository->find($request->get('id'));
            if (!$responseData) {
                return $this->respondNotFound('list not found with this id');
            }
        } else {
            $responseData = $watchListRepository->findAll();
        }
        return $this->respond($responseData);
    }

    /**
     * @Route("/lists", methods="POST")
     */
    public function addWatchList(Request $request, MovieRepository $movieRepository, EntityManagerInterface $em)
    {
        $request = $this->transformJsonBody($request);
        if (! $request) {
            return $this->respondBadRequestError('Please provide a valid request!');
        }

        if (!$request->get('name')) {
            return $this->respondBadRequestError('Please provide a name!');
        }
        if (!$request->get('movies') || !is_array($request->get('movies')) || empty($request->get('movies'))) {
            return $this->respondBadRequestError('Please provide a valid list of movies');
        }
        $watchList = new WatchList();
        $watchList->setName($request->get('name'));
        $watchList->setDescription($request->get('description'));
        foreach ($request->get('movies') as $movieId) {
            $movie = $movieRepository->find($movieId);
            if (!$movie) {
                return $this->respondNotFound('there is no movie with the Id : '.$movieId);
            }
            $watchList->addMovie($movie);
        }
        $em->persist($watchList);
        $em->flush();
        return $this->respondCreated($watchList);
    }

    /**
     * @Route("/lists", methods="PUT")
     */
    public function editWatchList(Request $request, WatchListRepository $watchListRepository, EntityManagerInterface $em)
    {
        $request = $this->transformJsonBody($request);

        if (!$request) {
            return $this->respondBadRequestError('Please provide a valid request!');
        }

        // validate the id
        if (!$request->get('id')) {
            return $this->respondBadRequestError('Please provide an id!');
        }

        $list = $watchListRepository->find($request->get('id'));
        if (!$list) {
            return $this->respondNotFound('no list found with this id ');
        }

        if ( $request->get('name')) {
            $list->setName($request->get('name'));
        }
        if ( $request->get('description')) {
            $list->setDescription($request->get('description'));
        }
        $em->merge($list);
        $em->flush();

        return $this->respond($list);
    }

    /**
     * @Route("/listMovies", methods="PUT")
     */
    public function addMovieToWatchList(Request $request, WatchListRepository $watchListRepository, MovieRepository $movieRepository, EntityManagerInterface $em)
    {
        $request = $this->transformJsonBody($request);
        if (!$request) {
            return $this->respondBadRequestError('Please provide a valid request!');
        }

        if (!$request->get('listId')) {
            return $this->respondBadRequestError('Please provide an id to get the list !');
        }
        $watchList= $watchListRepository->find($request->get('listId'));
        if (!$watchList){
            return $this->respondNotFound('there is no list with the Id : '.$request->get('listId'));
        }

        if (!$request->get('movieId')) {
            return $this->respondBadRequestError('Please provide an id for the movie');
        }
        $movie=$movieRepository->find($request->get('movieId'));
        if (!$movie){
            return $this->respondNotFound('there is no movie with the Id : '.$request->get('movieId'));
        }
        $watchList->addMovie($movie);
        $em->merge($watchList);
        $em->flush();
        return $this->respondCreated($watchList);
    }

    /**
     * @Route("/listMovies", methods="DELETE")
     */
    public function deleteMovieFromWatchList(Request $request, WatchListRepository $watchListRepository, MovieRepository $movieRepository, EntityManagerInterface $em)
    {
        $listId = $request->get('listId');
        if (!$listId) {
            return $this->respondBadRequestError('Please provide an id to get the list !');
        }
        $watchList= $watchListRepository->find($listId);
        if (!$watchList){
            return $this->respondNotFound('there is no list with the Id : '.$listId);
        }

        $movieId = $request->get('movieId');
        if (!$movieId) {
            return $this->respondBadRequestError('Please provide an id for the movie you want to delete');
        }
        $movie=$movieRepository->find($movieId);
        if (!$movie){
            return $this->respondNotFound('there is no movie with the Id : '.$movieId);
        }
        $watchList->removeMovie($movie);
        $em->merge($watchList);
        $em->flush();
        return $this->respondCreated($watchList);
    }

    /**
     * @Route("/lists", methods="DELETE")
     */
    public function deleteList(Request $request, WatchListRepository $watchListRepository, EntityManagerInterface $em)
    {
        if (! $request->get('id')) {
            return $this->respondBadRequestError('Please provide an id!');
        } else {
            $listToDelete = $watchListRepository->find($request->get('id'));
            if (!$listToDelete) {
                return $this->respondNotFound('list not found with this id');
            }

            // merge the new movie
            $em->remove($listToDelete);
            $em->flush();
        }

        $data = [
            'message' => 'list deleted',
        ];

        return $this->respond($data);
    }

}