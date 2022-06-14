<?php

namespace App\Controller;

use App\Entity\Order;
use App\Form\OrderType;
use App\Form\PaginationType;
use App\Repository\OrderRepository;
use App\Service\SupplyManager;
use App\Service\OrderExport;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Filesystem\Exception\IOException;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\ResponseHeaderBag;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Symfony\Component\Lock\LockFactory;
use Symfony\Component\Lock\Store\FlockStore;
use Symfony\Component\Routing\Annotation\Route;

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
    public function new(Request $request, OrderRepository $orderRepository, SupplyManager $supplyManager): Response
    {
        $order = new Order();
        $form = $this->createForm(OrderType::class, $order);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            if (!$supplyManager->manage($order, SupplyManager::ORDER_ACTION_NEW)) {
                $errorMessages = $supplyManager->getErrorMessages();
                foreach ($errorMessages as $key => $val) {
                    $this->addFlash($key, $val);
                }
                $form = $this->createForm(OrderType::class, $order);
                $form->handleRequest($request);
                return $this->renderForm('order/new.html.twig', [
                    'order' => $order,
                    'form' => $form,
                ]);
            }

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
    public function edit(Request $request, Order $order, OrderRepository $orderRepository, SupplyManager $supplyManager): Response
    {
        $form = $this->createForm(OrderType::class, $order);

        $supplyManager->backupOriginalItems($order);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            if (!$supplyManager->manage($order, SupplyManager::ORDER_ACTION_EDIT)) {
                $errorMessages = $supplyManager->getErrorMessages();
                foreach ($errorMessages as $key => $val) {
                    $this->addFlash($key, $val);
                }
                $form = $this->createForm(OrderType::class, $order);
                $form->handleRequest($request);
                return $this->renderForm('order/edit.html.twig', [
                    'order' => $order,
                    'form' => $form,
                ]);
            }

            $orderRepository->add($order, true);

            return $this->redirectToRoute('app_order_edit', ['id' => $order->getId()], Response::HTTP_MOVED_PERMANENTLY);
        }

        return $this->renderForm('order/edit.html.twig', [
            'order' => $order,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_order_cancel', methods: ['POST'])]
    public function cancel(Request $request, Order $order, OrderRepository $orderRepository, SupplyManager $supplyManager): Response
    {
        if ($this->isCsrfTokenValid('cancel' . $order->getId(), $request->request->get('_token'))) {
            // return products; backend check
            if (!$supplyManager->manage($order, SupplyManager::ORDER_ACTION_CANCEL)) {
                $errorMessages = $supplyManager->getErrorMessages();
                foreach ($errorMessages as $key => $val) {
                    $this->addFlash($key, $val);
                }
                return $this->redirectToRoute('app_order_edit', ['id' => $order->getId()], Response::HTTP_MOVED_PERMANENTLY);
            }

            $orderRepository->hide($order, true);
        }

        return $this->redirectToRoute('app_order_index', [], Response::HTTP_SEE_OTHER);
    }

    #[Route('/{id}/finish', name: 'app_order_finish', methods: ['POST'])]
    public function finish(Request $request, Order $order, OrderRepository $orderRepository, SupplyManager $supplyManager): Response
    {
        if ($this->isCsrfTokenValid('finish' . $order->getId(), $request->request->get('_token'))) {
            if (!$supplyManager->manage($order, SupplyManager::ORDER_ACTION_FINISH)) {
                $errorMessages = $supplyManager->getErrorMessages();
                foreach ($errorMessages as $key => $val) {
                    $this->addFlash($key, $val);
                }
                $supplyManager->clearErrorMessages();

                return $this->redirectToRoute('app_order_edit', ['id' => $order->getId()], Response::HTTP_MOVED_PERMANENTLY);
            }

            $order->setStatus(Order::STATUS_ORDER_FINISHED);
            $orderRepository->add($order, true);
        }

        return $this->redirectToRoute('app_order_show', ['id' => $order->getId()], Response::HTTP_MOVED_PERMANENTLY);
    }

    #[Route('/{id}/export', name: 'app_order_export', methods: ['POST'])]
    public function export(Request $request, Order $order, OrderRepository $orderRepository, OrderExport $orderExport): Response
    { // TODO test lock with 2nd window at start wait(5000) blabla check read same order or even edit just remove view restriction
        if ($this->isCsrfTokenValid('export'.$order->getId(), $request->request->get('_token'))) {
            $store = new FlockStore('/var/stores');
            $factory = new LockFactory($store);
            $lock = $factory->createLock('xlsx-order-generation', 30, false);
            if ($lock->acquire()) {
                try {
                    $filename = $orderExport->export($order);
                } catch (IOException $e) {
                    throw new HttpException(418, "Unable to write file!" . $e->getMessage());
                } finally {
                    $lock->release();
                }

                $order->setSpreadsheetFilename($filename);
                $orderRepository->add($order, true);

                $lock->release();
            } else {
                throw new HttpException(418, "Unable to acquire the lock!");
            }
        }

        return $this->redirectToRoute('app_order_show', ['id' => $order->getId()], Response::HTTP_MOVED_PERMANENTLY);
    }

    #[Route('/{id}/download', name: 'app_order_download', methods: ['POST'])]
    public function download(Request $request, Order $order, string $orderExportPath): BinaryFileResponse
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

        throw new HttpException(419, "CSRF token mismatch");
    }
}
