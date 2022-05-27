<?php

namespace App\Controller;

use App\Repository\StreetRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class AjaxController extends AbstractController
{
    #[Route('/findstreets', name: 'findstreets')]
    public function findStreets(Request $request, StreetRepository $streetRepository)
    {
        $key = $request->query->get('q');

        $streets = $streetRepository->filterByKey($key);

        $response = array();

        foreach ($streets as $street) {
            $response[] = [
                'id' => $street->getId(),
                'text' => $street->getName(),
            ];
        }

        return new JsonResponse($response);
    }
}
