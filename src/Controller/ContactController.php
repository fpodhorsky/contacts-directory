<?php

namespace App\Controller;

use App\Repository\ContactRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class ContactController extends AbstractController
{
    public function __construct(
        private readonly ContactRepository $contactRepository,
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
}
