<?php

namespace App\Controller\Admin;

use App\Entity\Customer;
use App\Entity\Order;
use App\Entity\User;
use EasyCorp\Bundle\EasyAdminBundle\Config\Dashboard;
use EasyCorp\Bundle\EasyAdminBundle\Config\MenuItem;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractDashboardController;
use EasyCorp\Bundle\EasyAdminBundle\Router\AdminUrlGenerator;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class DashboardController extends AbstractDashboardController
{
    #[Route('/admin', name: 'admin')]
    public function index(): Response
    {
        // use constructor if need to inject something, don't touch index() params

        $adminUrlGenerator = $this->container->get(AdminUrlGenerator::class);
        $url = $adminUrlGenerator->setController(OrderCrudController::class)->generateUrl();
        return $this->redirect($url);

        // Option 1. You can make your dashboard redirect to some common page of your backend
        //
        // $adminUrlGenerator = $this->container->get(AdminUrlGenerator::class);
        // return $this->redirect($adminUrlGenerator->setController(OneOfYourCrudController::class)->generateUrl());

        // Option 2. You can make your dashboard redirect to different pages depending on the user
        //
        // if ('jane' === $this->getUser()->getUsername()) {
        //     return $this->redirect('...');
        // }

        // Option 3. You can render some custom template to display a proper dashboard with widgets, etc.
        // (tip: it's easier if your template extends from @EasyAdmin/page/content.html.twig)
        //
        // return $this->render('some/path/my-dashboard.html.twig');
    }

    public function configureDashboard(): Dashboard
    {
        return Dashboard::new()
            ->setTitle('Not Aquacity')
            ->setFaviconPath('favicon.ico')
            // ->renderSidebarMinimized()
            ;
    }

    public function configureMenuItems(): iterable
    {
        yield MenuItem::linkToRoute('Homepage', 'fa fa-home', 'app_main');
        yield MenuItem::section('Dashboards');
        yield MenuItem::linkToDashboard('Dashboard', 'fa fa-home');
        yield MenuItem::section('Workspace');
        yield MenuItem::linkToCrud('Customers', 'fas fa-list', Customer::class);
        yield MenuItem::linkToCrud('Orders', 'fas fa-list', Order::class);
        yield MenuItem::section('Administration');
        yield MenuItem::linkToCrud('Users', 'fas fa-list', User::class);
    }
}
