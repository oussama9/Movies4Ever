<?php
namespace App\Controller;

use App\Entity\Movie;
use App\Repository\MovieRepository;
use Doctrine\ORM\EntityManagerInterface;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class MovieController extends ApiController
{
    /**
     * @Route("/movies", methods="GET")
     */
    public function showMovies(Request $request, MovieRepository $movieRepository)
    {
        $responseData = null;
        if($request->get('id')) {
            $responseData = $movieRepository->find($request->get('id'));
            if (!$responseData) {
                return $this->respondNotFound('movie not found with this id');
            }
        } else if ($request->get('title')) {
            $responseData = $movieRepository->findByTitle($request->get('title'));
            if (!$responseData) {
                return $this->respondNotFound('No results found for :'.$request->get('title'));
            }
        } else {
            $responseData = $movieRepository->findAll();
        }

        return $this->respond($responseData);
    }

    /**
     * @Route("/movies", methods="POST")
     */
    public function create(Request $request, MovieRepository $movieRepository, EntityManagerInterface $em)
    {
        $request = $this->transformJsonBody($request);

        if (! $request) {
            return $this->respondBadRequestError('Please provide a valid request!');
        }

        // validate the title
        if (! $request->get('title')) {
            return $this->respondBadRequestError('Please provide a title!');
        }
        // validate the release Year
        if (! $request->get('releaseYear')) {
            return $this->respondBadRequestError('Please provide a Release Year');
        }
        if (! $request->get('description')) {
            return $this->respondBadRequestError('Please provide a description');
        }
        // persist the new movie
        $movie = new Movie();
        $movie->setNote($request->get('note'));
        $movie->setTitle($request->get('title'));
        $movie->setCountry($request->get('country'));
        $movie->setDirector($request->get('director'));
        $movie->setReleaseYear($request->get('releaseYear'));
        $movie->setDescription($request->get('description'));
        $em->persist($movie);
        $em->flush();

        return $this->respondCreated($movie);
    }



    /**
     * @Route("/movies", methods="PUT")
     */
    public function updateMovie(Request $request, MovieRepository $movieRepository, EntityManagerInterface $em)
    {
        $request = $this->transformJsonBody($request);

        if (!$request) {
            return $this->respondBadRequestError('Please provide a valid request!');
        }

        // validate the id
        if (!$request->get('id')) {
            return $this->respondBadRequestError('Please provide an id!');
        }

        $movie = $movieRepository->find($request->get('id'));
        if (!$movie) {
            return $this->respondNotFound('no movie found with this id ');
        }

        if ( $request->get('title')) {
            $movie->settitle($request->get('title'));
        }
        if ( $request->get('releaseYear')) {
            $movie->setReleaseYear($request->get('releaseYear'));
        }
        if ( $request->get('description')) {
            $movie->setDescription($request->get('description'));
        }
        if ( $request->get('originalTitle')) {
            $movie->setOriginalTitle($request->get('originalTitle'));
        }
        if ( $request->get('country')) {
            $movie->setCountry($request->get('country'));
        }
        if ( $request->get('director')) {
            $movie->setDirector($request->get('director'));
        }
        if ( $request->get('note')) {
            $movie->setNote($request->get('note'));
        }

        // merge the new movie
        $em->merge($movie);
        $em->flush();

        return $this->respond($movie);
    }

    /**
     * @Route("/movies", methods="DELETE")
     */
    public function deleteMovie(Request $request, MovieRepository $movieRepository, EntityManagerInterface $em)
    {
        if (!$request->get('id')) {
            return $this->respondBadRequestError('Please provide an id!');
        } else {
            $movieToDelete = $movieRepository->find($request->get('id'));
            if (!$movieToDelete) {
                return $this->respondNotFound('movie not found with this id');
            }
            $em->remove($movieToDelete);
            $em->flush();
        }
        $data = [
            'message' => 'movie deleted',
        ];
        return $this->respond($data);
    }

}