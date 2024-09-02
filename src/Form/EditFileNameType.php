<?php

namespace App\Form;

use App\Entity\File;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class EditFileNameType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $file = $options['data'];
        $fileName = pathinfo($file->getName(), PATHINFO_FILENAME);

        $builder
            ->add('name', TextType::class, [
                'label' => 'Nom du fichier',
                'required' => true,
                'data' => $fileName,
                'attr' => [
                    'class' => 'form-control',
                    'placeholder' => 'Entrez le nouveau nom du fichier',
                ],
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => File::class,
        ]);
    }
}
