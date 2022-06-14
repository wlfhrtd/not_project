<?php

namespace App\Controller\Admin;

use App\Entity\Street;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;

class StreetCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return Street::class;
    }

    /*
    public function configureFields(string $pageName): iterable
    {
        return [
            IdField::new('id'),
            TextField::new('title'),
            TextEditorField::new('description'),
        ];
    }
    */
}
