<?php

namespace App\Controller\Admin;

use App\Entity\User;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\ChoiceField;
use EasyCorp\Bundle\EasyAdminBundle\Field\EmailField;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextEditorField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;

class UserCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return User::class;
    }


//    public function configureFields(string $pageName): iterable
//    {
//        return [
//
//            EmailField::new('email'),
////            textField::new('roles'),
////            TextEditorField::new('description'),
//            ChoiceField::new('roles', 'Role Relation Type')
//                ->setChoices([
//                    'User' => 1,
//                ])
//                ->setRequired(true),
//        ];
//    }

}
