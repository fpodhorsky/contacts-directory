<?php

namespace App\Controller;

use App\Entity\Contact;
use App\Form\ContactFormType;
use App\Repository\ContactRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bridge\Doctrine\Attribute\MapEntity;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\String\Slugger\SluggerInterface;

final class ContactController extends AbstractController
{
    public function __construct(
        private readonly ContactRepository $contactRepository,
        private readonly EntityManagerInterface $entityManager,
        private readonly SluggerInterface $slugger
    ) {}

    #[Route('/', name: 'app_contact')]
    public function index(Request $request): Response
    {
        $resultsPerPage = 5;
        $currentPage = intval($request->query->get('page', 1));
        $contactsTotal = $this->contactRepository->count();
        $allPagesCount = (int) ceil($contactsTotal / $resultsPerPage);

        if ($currentPage < 1) {
            return $this->redirectToRoute('app_contact');
        } elseif ($currentPage > $allPagesCount && $contactsTotal > 0) {
            return $this->redirectToRoute('app_contact', ['page' => $allPagesCount]);
        }

        $contacts = $this->contactRepository->getPaginatedContacts($currentPage, $resultsPerPage);
        $hasPrevPage = $currentPage > 1;
        $hasNextPage = $currentPage < $allPagesCount;

        return $this->render('contact/index.html.twig', [
            'contacts' => $contacts,
            'contactsTotal' => $contactsTotal,
            'allPagesCount' => $allPagesCount,
            'currentPage' => $currentPage,
            'hasPrevPage' => $hasPrevPage,
            'hasNextPage' => $hasNextPage,
        ]);
    }

    #[Route('/contact/new', name: 'app_contact_new')]
    public function new(Request $request): Response
    {
        $contact = new Contact();
        $form = $this->createForm(ContactFormType::class, $contact);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            /** @var Contact $contact */
            $contact = $form->getData();

            $slug = $this->generateUniqueSlug($contact);
            $contact->setSlug($slug);

            $this->entityManager->persist($contact);
            $this->entityManager->flush();

            return $this->redirectToRoute('app_contact_update', ['slug' => $contact->getSlug()]);
        }

        return $this->render('contact/detail.html.twig', [
            'form' => $form,
            'title' => 'Nový kontakt'
        ]);
    }

    #[Route('/{slug}', name: 'app_contact_update')]
    public function update(
        #[MapEntity(mapping: ['slug' => 'slug'])] Contact $contact,
        Request $request
    ): Response {
        $form = $this->createForm(ContactFormType::class, $contact);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            /** @var Contact $contact */
            $contact = $form->getData();

            $slug = $this->generateUniqueSlug($contact);
            $contact->setSlug($slug);

            $this->entityManager->persist($contact);
            $this->entityManager->flush();

            return $this->redirectToRoute('app_contact_update', ['slug' => $contact->getSlug()]);
        }

        return $this->render('contact/detail.html.twig', [
            'form' => $form,
            'contact' => $contact,
            'title' => 'Upravit kontakt'
        ]);
    }

    #[Route('/contact/{id}/delete', name: 'app_contact_delete')]
    public function delete(Contact $contact, Request $request): Response
    {
        $this->entityManager->remove($contact);
        $this->entityManager->flush();

        return $this->redirect($request->headers->get('referer') ?? '/');
    }

    private function generateUniqueSlug(Contact $contact): string
    {
        $baseSlug = $this->slugger
            ->slug($contact->getFirstName() . ' ' . $contact->getLastName())
            ->lower();

        $slug = $baseSlug;
        $i = 2;

        while (
            ($existing = $this->contactRepository->findOneBy(['slug' => $slug]))
            && $existing->getId() !== $contact->getId()
        ) {
            $slug = $baseSlug . '-' . $i;
            $i++;
        }

        return $slug;
    }
}
