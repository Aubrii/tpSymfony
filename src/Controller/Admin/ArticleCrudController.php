<?php

namespace App\Controller\Admin;

use App\Entity\Article;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;


use EasyCorp\Bundle\EasyAdminBundle\Field\AssociationField;
use EasyCorp\Bundle\EasyAdminBundle\Field\ImageField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;

class ArticleCrudController extends AbstractCrudController
{


    public static function getEntityFqcn(): string
    {
        return Article::class;
    }
//    public function configureFilters(Filters $filters): Filters
//    {
//        return $filters
//            ->add(EntityFilter::new('destination'))
//        ;
//    }


    public function configureFields(string $pageName): iterable
    {
//        return [
//
//            IdField::new('id'),
//            TextField::new('title'),
//            TextEditorField::new('description'),
//
//        ];
                yield AssociationField::new('destination');
                yield TextField::new('titre');
                yield TextField::new('description');
//                yield ImageField::new('photo')
//                    ->setBasePath('/uploads/photos')
//                    ->setLabel('Photo')
////                    ->onlyOnIndex()
            ;

    }

}
