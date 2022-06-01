<?php

namespace App\Controller;

use App\Entity\Order;
use App\Form\OrderType;
use App\Form\PaginationType;
use App\Repository\OrderRepository;
use App\Service\OrderExport;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Exception;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use Psr\Log\LoggerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Filesystem\Exception\IOException;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Symfony\Component\HttpFoundation\HeaderUtils;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\ResponseHeaderBag;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;

#[Route('/order')]
class OrderController extends AbstractController
{
    #[Route('/', name: 'app_order_index', methods: ['GET', 'POST'])]
    public function index(Request $request, OrderRepository $orderRepository): Response
    {
        // pagination
        $paginationForm = $this->createForm(PaginationType::class);
        $paginationForm->handleRequest($request);

        $paginationOffset = max(0, $request->query->getInt('offset', 0));

        if ($paginationForm->isSubmitted() && $paginationForm->isValid()) {

            $paginationOffset = ($paginationForm->get('page')->getData() - 1) * OrderRepository::PAGINATOR_PER_PAGE;
        }

        $paginator = $orderRepository->getOrderPaginator($paginationOffset);

        return $this->render('order/index.html.twig', [
            'paginationForm' => $paginationForm->createView(),
            'orders' => $paginator,
            'paginationCap' => ceil(count($paginator) / OrderRepository::PAGINATOR_PER_PAGE),
            'previous' => $paginationOffset - OrderRepository::PAGINATOR_PER_PAGE,
            'next' => min(count($paginator), $paginationOffset + OrderRepository::PAGINATOR_PER_PAGE),
            'orderStatusFinished' => Order::STATUS_ORDER_FINISHED,
        ]);
    }

    #[Route('/new', name: 'app_order_new', methods: ['GET', 'POST'])]
    public function new(Request $request, OrderRepository $orderRepository): Response
    {
        $order = new Order();
        $form = $this->createForm(OrderType::class, $order);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $orderRepository->add($order, true);

            return $this->redirectToRoute('app_order_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('order/new.html.twig', [
            'order' => $order,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_order_show', methods: ['GET'])]
    public function show(Order $order): Response
    {
        return $this->render('order/show.html.twig', [
            'order' => $order,
            'orderStatusFinished' => Order::STATUS_ORDER_FINISHED,
            'orderStatusDraft' => Order::STATUS_ORDER_DRAFT,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_order_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Order $order, OrderRepository $orderRepository): Response
    {
        $form = $this->createForm(OrderType::class, $order);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $orderRepository->add($order, true);

            return $this->redirectToRoute('app_order_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('order/edit.html.twig', [
            'order' => $order,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_order_delete', methods: ['POST'])]
    public function delete(Request $request, Order $order, OrderRepository $orderRepository): Response
    {
        if ($this->isCsrfTokenValid('delete'.$order->getId(), $request->request->get('_token'))) {
            $orderRepository->remove($order, true);
        }

        return $this->redirectToRoute('app_order_index', [], Response::HTTP_SEE_OTHER);
    }

    #[Route('/{id}/finish', name: 'app_order_finish', methods: ['POST'])]
    public function finish(Request $request, Order $order, OrderRepository $orderRepository): Response
    {
        if ($this->isCsrfTokenValid('finish'.$order->getId(), $request->request->get('_token'))) {

            $order->setStatus(Order::STATUS_ORDER_FINISHED);
            $orderRepository->add($order, true);
        }

        return $this->redirectToRoute('app_order_show', ['id' => $order->getId()], Response::HTTP_MOVED_PERMANENTLY);
    }

    #[Route('/{id}/export', name: 'app_order_export', methods: ['POST'])]
    public function export(Request $request, Order $order, OrderRepository $orderRepository, OrderExport $orderExport): Response
    {
        if ($this->isCsrfTokenValid('export'.$order->getId(), $request->request->get('_token'))) {

            $filename = $orderExport->export($order);
            if (!$filename) {
                throw new HttpException(418, "Unable to write file");
            }
            $order->setSpreadsheetFilename($filename);
            $orderRepository->add($order, true);
        }

        return $this->redirectToRoute('app_order_show', ['id' => $order->getId()], Response::HTTP_MOVED_PERMANENTLY);
    }

    #[Route('/{id}/download', name: 'app_order_download', methods: ['POST'])]
    public function download(Request $request, Order $order, string $orderExportPath)
    {
        if ($this->isCsrfTokenValid('download'.$order->getId(), $request->request->get('_token'))) {

            $path = $orderExportPath . $order->getSpreadsheetFilename();

            $response = new BinaryFileResponse($path);
            $response->setContentDisposition(
                ResponseHeaderBag::DISPOSITION_ATTACHMENT,
                $order->getSpreadsheetFilename()
            );

            return $response;
        }

        return new HttpException(419, "CSRF token mismatch");
    }
}
