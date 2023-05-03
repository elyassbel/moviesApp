<?php

namespace App\Controller;

use GuzzleHttp\Client;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Contracts\Cache\CacheInterface;
use Symfony\Contracts\Cache\ItemInterface;
use Symfony\Contracts\HttpClient\HttpClientInterface;

class MovieController extends AbstractController
{
    private $cache;
    private $httpClient;
    private $apiKey;

    public function __construct(CacheInterface $cache, HttpClientInterface $httpClient, string $apiKey)
    {
        $this->cache = $cache;
        $this->httpClient = $httpClient;
        $this->apiKey = $apiKey;
    }

    #[Route('/movie', name: 'movie_list')]
    public function index(): Response
    {
        $client = new Client();
        //$url = 'https://streaming-availability.p.rapidapi.com/v2/services';
        //$url = 'https://streaming-availability.p.rapidapi.com/v2/get/basic?country=us&imdb_id=tt0208092&output_language=fr';
        $url = 'https://streaming-availability.p.rapidapi.com/v2/search/basic?country=fr&services=netflix%2Cprime&show_type=movie';
        $response = $client->request('GET', $url,[
            'headers' => [
                'X-RapidAPI-Host' => 'streaming-availability.p.rapidapi.com',
                'X-RapidAPI-Key' => $this->apiKey,
            ],
        ]);
        $movies = json_decode($response->getBody()->getContents(), true)['result'];
        $mostPopularMovies = array_slice($movies, 0, 5);

        return $this->render('movie/index.html.twig', [
            'movies' => $mostPopularMovies,
        ]);
    }

    #[Route('/movie/{id}', name: 'movie_detail')]
    public function detail(Request $request, string $id): Response
    {
        return $this->render('movie/detail.html.twig', [
            'movie' => [],
        ]);
    }
}
