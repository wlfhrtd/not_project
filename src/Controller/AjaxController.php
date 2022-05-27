<?php

namespace App\Controller;

use App\Repository\CustomerRepository;
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

    #[Route('/findcustomers', name: 'findcustomers')]
    public function findCustomers(Request $request, CustomerRepository $customerRepository)
    {
        $key = $request->query->get('q');

        $customers = $customerRepository->filterByKey($key);

        $response = array();

        foreach ($customers as $customer) {
            $response[] = [
                'id' => $customer->getId(),
                'text' => $customer->getLastName() . ' ' . $customer->getFirstName() . ' ' . $customer->getMiddleName(),
            ];
        }

        return new JsonResponse($response);
    }
}
