<?php

namespace App\Controller;

use\App\Service\CallApiService;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\JsonResponse;

class AppController extends AbstractController
{
    
    /**
     * @Route("/", name="home")
     */
    public function home(CallApiService $callApiService, Request $request): Response
    {
        $roundDate = '';
        $results   = ['response' => [] ];
        $params    = $request->query->all();
        $leagues   = $callApiService->callApi('/leagues', ['country' => 'France'] );

        if ( isset($params['league']) && isset($params['season']) && isset($params['round']) ) {
            $results = $callApiService->callApi('/fixtures', [
                'league' => $params['league'],
                'season' => $params['season'],
                'round'  => $params['round']
            ]);

            if (isset($results['response'][0]['fixture']['timestamp']))
                $roundDate = date('Y-m-d', $results['response'][0]['fixture']['timestamp']);
                
        }         
        return $this->render('app/home.html.twig', [
            'leagues'   => $leagues['response'],
            'results'   => $results['response'],
            'roundDate' => $roundDate
        ]);
    }


    /**
     * @Route("/rounds", name="rounds")
     */
    public function rounds(CallApiService $callApiService, Request $request) : JsonResponse
    {
        $req = $request->query->all();

        if (isset($req['league']) && isset($req['season'])) {
            $rounds = $callApiService->callApi('/fixtures/rounds', [
                'league' => $req['league'], 
                'season' => $req['season']
            ] );
            return new JsonResponse( $rounds );
        } else {
            return new JsonResponse( );
        } 
    }

    /**
     * @Route("/details/{id<\d+>}", name="details", requirements={"id"="\d+"})
     */
    public function details(int $id, CallApiService $callApiService): Response
    {
        // $callApiService->clearCache();

        $stats =$callApiService->callApi('/fixtures', ['id' => $id] ); 
        
        $round        = $stats['response'][0]['league']['round'];
        $league_id    = $stats['response'][0]['league']['id'];
        $season       = $stats['response'][0]['league']['season'];
        $team_home_id = $stats['response'][0]['teams']['home']['id'];
        $team_away_id = $stats['response'][0]['teams']['away']['id'];
               
        $last_team_home = $callApiService->callApi('/fixtures', [
            'league' => $league_id,
            'season' => $season,            
            'team'   => $team_home_id
        ]);

        $last_team_away = $callApiService->callApi('/fixtures', [
            'league' => $league_id,
            'season' => $season,            
            'team'   => $team_away_id
        ]);

        return $this->render('app/details.html.twig', [
            'stats' => $stats,
            'last_team_home' => $last_team_home,
            'last_team_away' => $last_team_away
        ]);
    }

}