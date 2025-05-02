<?php

namespace App\Form;

use App\Entity\Users;
use App\Entity\Articles;
use App\Entity\Categories;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Validator\Constraints\File;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;

class ArticlesType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('title', TextType::class, [
                'label'=> 'Titre de l\'article',
            ])
            ->add('content', TextareaType::class, [
                'label'=> 'Contenu de l\'arrticle',
            ])
            ->add('image',FileType::class, [
                'label'=>'Image de Votre article',
                'data_class' => null,
                'constraints' =>[
                    new File([
                        'maxSize' => '1024k',
                        'mimeTypes' => [
                            'image/png',
                            'image/jpeg',
                        ],
                        'mimeTypesMessage' => 'Merci d\'insérer une image valide (.jpeg ou .png)'
                    ])
                ]
            ])
          
            ->add('category', EntityType::class, [
                'class' => Categories::class,
                'choice_label' => 'name',
                'multiple' => true,
                'expanded' => true
            ])
            ->add('Submit', SubmitType::class, [
                'label' => 'Enregister'
            ])

        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Articles::class,
        ]);
    }
}
