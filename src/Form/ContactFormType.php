<?php

namespace App\Form;

use App\Entity\Contact;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\Email;
use Symfony\Component\Validator\Constraints\NotBlank;

class ContactFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('slug', TextType::class, ['required' => false, 'disabled' => true])
            ->add('first_name', TextType::class, [
                'label' => 'Jméno',
                'trim' => true,
                'constraints' => [
                    new NotBlank(message: 'Vyplňte prosím jméno.'),
                ],
            ])
            ->add('last_name', TextType::class, [
                'label' => 'Příjmení',
                'trim' => true,
                'constraints' => [
                    new NotBlank(message: 'Vyplňte prosím příjmení.'),
                ],
            ])
            ->add('phone_number', TextType::class, [
                'label' => 'Telefon',
                'required' => false
            ])
            ->add('email', EmailType::class, ['label' => 'Email', 'constraints' => [
                new NotBlank(),
                new Email(message: 'Zadejte prosím platnou emailovou adresu.')
            ]])
            ->add('note', TextareaType::class, [
                'label' => 'Poznámka',
                'required' => false
            ])
            ->add('submit', SubmitType::class, ['label' => 'Uložit']);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Contact::class,
        ]);
    }
}
