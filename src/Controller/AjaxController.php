<?php

namespace App\Controller;

use App\Repository\CustomerRepository;
use App\Repository\ProductRepository;
use App\Repository\StreetRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class AjaxController extends AbstractController
{
    #[Route('/find_streets_list', name: 'find_streets_list')]
    public function findStreetsList(Request $request, StreetRepository $streetRepository)
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

    #[Route('/find_customers_list', name: 'find_customers_list')]
    public function findCustomersList(Request $request, CustomerRepository $customerRepository)
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

    #[Route('/find_products_list', name: 'find_products_list')]
    public function findProductsList(Request $request, ProductRepository $productRepository)
    {
        $key = $request->query->get('q');

        $products = $productRepository->filterByKey($key);

        $response = array();

        foreach ($products as $product) {
            $response[] = [
                'id' => $product->getId(),
                'text' => $product->getName(),
            ];
        }

        return new JsonResponse($response);
    }

    #[Route('/find_product_one', name: 'find_product_one')]
    public function findProductOne(Request $request, ProductRepository $productRepository)
    {
        $product = $productRepository->findOneByIdHideHidden($request->get('id'));
        $price = $product->getPrice();
        $quantityInStock = $product->getQuantityInStock();
        return new JsonResponse([
            'price' => $price,
            'quantityInStock' => $quantityInStock,
        ]);
    }
}
