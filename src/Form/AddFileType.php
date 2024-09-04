<?php

namespace App\Form;

use App\Entity\File;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\File as FileConstraint;

class AddFileType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('file', FileType::class, [
                'label' => 'Fichier',
                'mapped' => false,
                'required' => true,

                'constraints' => [
                    new FileConstraint([
                        'maxSize' => '50M',
                        'mimeTypes' => [
                            'application/pdf',
                            'application/x-pdf',
                            'image/png',
                            'image/jpeg',
                            'image/jpg',
                            'application/msword',
                            'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
                            'application/vnd.ms-excel',
                            'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
                            'application/vnd.ms-powerpoint',
                            'application/vnd.openxmlformats-officedocument.presentationml.presentation',
                            'text/plain',
                            'text/csv',
                            'application/zip',
                            'application/x-rar-compressed',
                            'application/x-7z-compressed',
                            'application/x-tar',
                            'audio/mpeg',
                            'audio/wav',
                            'video/mp4',
                            'video/x-msvideo',
                        ],
                        'mimeTypesMessage' => 'Veuillez télécharger un fichier valide (pdf, png, jpg, doc, docx, xls, xlsx, ppt, pptx, txt, csv, zip, rar, 7z, tar, mp3, wav, mp4, avi)',
                        'maxSizeMessage' => 'Le fichier est trop volumineux. Taille maximale autorisée : {{ limit }} {{ suffix }}.',
                    ]),
                ],
            ]);
        //        Pour la partie admin ( a faire plus tard)
        //            ->add('user_id', EntityType::class, [
        //                'class' => User::class,
        //                'choice_label' => 'id',
        //                'label' => 'Utilisateur',
        //            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => File::class,
        ]);
    }
}
