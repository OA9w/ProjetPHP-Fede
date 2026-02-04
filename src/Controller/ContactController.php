<?php

namespace App\Controller;

use App\Entity\Contact;
use App\Form\ContactType;
use App\Repository\ContactRepository;
use App\Service\GroupService;
use App\Service\PhotoUploader;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/contacts')]
class ContactController extends AbstractController
{
    #[Route('', name: 'contact_index', methods: ['GET'])]
    public function index(Request $request, ContactRepository $repo): Response
    {
        $q = $request->query->get('q', '');
        $fav = $request->query->getBoolean('fav', false);
        $contacts = $repo->search($q, $fav);


        return $this->render('contact/index.html.twig', [
            'contacts' => $contacts,
            'q' => $q,
            'fav' => $fav,
        ]);
    }

    #[Route('/new', name: 'contact_new', methods: ['GET','POST'])]
    public function new(
        Request $request,
        EntityManagerInterface $em,
        GroupService $groupService,
        PhotoUploader $uploader
    ): Response {
        $contact = new Contact();
        $form = $this->createForm(ContactType::class, $contact);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            // Groupes
            $groupNamesRaw = (string) $form->get('groupNames')->getData();
            $names = array_filter(array_map('trim', explode(',', $groupNamesRaw)));

            // Photo upload (optionnel)
            $photoFile = $form->get('photoFile')->getData();
            if ($photoFile) {
                $contact->setPhotoPath($uploader->upload($photoFile));
            }

            $em->persist($contact);
            $groupService->applyGroupNames($contact, $names);

            // (optionnel mais safe)
            $groupService->removeEmptyGroups();

            $em->flush();

            $this->addFlash('success', 'Contact créé.');
            return $this->redirectToRoute('contact_index');
        }

        return $this->render('contact/form.html.twig', [
            'form' => $form,
            'title' => 'Nouveau contact',
        ]);
    }

    #[Route('/{id}', name: 'contact_show', methods: ['GET'])]
    public function show(Contact $contact): Response
    {
        return $this->render('contact/show.html.twig', [
            'contact' => $contact,
        ]);
    }

    #[Route('/{id}/edit', name: 'contact_edit', methods: ['GET','POST'])]
    public function edit(
        Request $request,
        Contact $contact,
        EntityManagerInterface $em,
        GroupService $groupService,
        PhotoUploader $uploader
    ): Response {
        $form = $this->createForm(ContactType::class, $contact);

        // Pré-remplir groupNames
        $groupNames = array_map(fn($g) => $g->getName(), $contact->getGroups()->toArray());
        $form->get('groupNames')->setData(implode(', ', $groupNames));

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            // Groupes
            $groupNamesRaw = (string) $form->get('groupNames')->getData();
            $names = array_filter(array_map('trim', explode(',', $groupNamesRaw)));

            $groupService->applyGroupNames($contact, $names);

            // Photo upload (optionnel)
            $photoFile = $form->get('photoFile')->getData();
            if ($photoFile) {
                $contact->setPhotoPath($uploader->upload($photoFile));
            }

            // Très important : après edit, supprimer groupes devenus vides
            $groupService->removeEmptyGroups();

            $em->flush();

            $this->addFlash('success', 'Contact modifié.');
            return $this->redirectToRoute('contact_show', ['id' => $contact->getId()]);
        }

        return $this->render('contact/form.html.twig', [
            'form' => $form,
            'title' => 'Modifier contact',
        ]);
    }

    #[Route('/{id}/delete', name: 'contact_delete', methods: ['POST'])]
    public function delete(Request $request, Contact $contact, EntityManagerInterface $em, GroupService $groupService): Response
    {
        if ($this->isCsrfTokenValid('delete_contact_'.$contact->getId(), (string) $request->request->get('_token'))) {
            $em->remove($contact);

            // supprimer groupes vides AVANT flush ou après, les deux marchent ;
            // ici on flush d'abord le delete puis on clean.
            $em->flush();

            $groupService->removeEmptyGroups();
            $em->flush();

            $this->addFlash('success', 'Contact supprimé.');
        }

        return $this->redirectToRoute('contact_index');
    }

    #[Route('/{id}/favorite', name: 'contact_toggle_favorite', methods: ['POST'])]
    public function toggleFavorite(Request $request, Contact $contact, EntityManagerInterface $em): Response
    {
        if (!$this->isCsrfTokenValid('toggle_favorite_'.$contact->getId(), (string)$request->request->get('_token'))) {
            $this->addFlash('error', 'Token CSRF invalide.');
            return $this->redirectToRoute('contact_index');
        }

        $contact->setIsFavorite(!$contact->isFavorite());
        $em->flush();

        $this->addFlash('success', $contact->isFavorite() ? 'Ajouté aux favoris.' : 'Retiré des favoris.');

        // retour à la page précédente si possible
        $referer = $request->headers->get('referer');
        return $referer ? $this->redirect($referer) : $this->redirectToRoute('contact_index');
    }

}
