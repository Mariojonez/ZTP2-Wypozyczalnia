<?php

/**
 * Reservation controller.
 */

namespace App\Controller;

use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Core\User\UserInterface;
use App\Entity\Reservation;
use App\Form\Type\ChangeStatusType;
use App\Form\Type\ReservationType;
use App\Repository\ReservationRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use Symfony\Contracts\Translation\TranslatorInterface;

/**
 * Class ReservationController.
 */
class ReservationController extends AbstractController
{
    /**
     * Constructor.
     *
     * @param TranslatorInterface   $translator            Translator
     * @param ReservationRepository $reservationRepository Reservation repository
     */
    public function __construct(private readonly TranslatorInterface $translator, private readonly ReservationRepository $reservationRepository)
    {
    }

    /**
     * Creates a new reservation.
     *
     * @param Request $request The HTTP request
     *
     * @return Response HTTP response
     */
    #[Route('/reservation/new', name: 'reservation_new')]
    public function new(Request $request): Response
    {
        $user = $this->getUser();

        if (!$user instanceof UserInterface) {
            throw new AccessDeniedException('You must be logged in to create a reservation.');
        }

        $reservation = new Reservation();
        $reservation->setUser($user);

        $form = $this->createForm(ReservationType::class, $reservation);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $selectedTask = $form->get('task')->getData();
            $reservation->setTask($selectedTask);

            $reservation->setStatus('label.pending');

            $this->reservationRepository->save($reservation);

            $this->addFlash(
                'success',
                $this->translator->trans('message.created_successfully')
            );

            return $this->redirectToRoute('reservation_list');
        }

        return $this->render('reservation/new.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    /**
     * Lists all reservations.
     *
     * @return Response HTTP response
     */
    #[Route('/reservations', name: 'reservation_list')]
    public function list(): Response
    {
        // Get the current logged-in user
        $user = $this->getUser();

        if (!$user instanceof UserInterface) {
            throw $this->createAccessDeniedException('You must be logged in to view reservations.');
        }

        // Check if the user is an admin
        if ($this->isGranted('ROLE_ADMIN')) {
            // Fetch all reservations
            $reservations = $this->reservationRepository->findAll();
        } else {
            // Fetch reservations associated with the current user
            $reservations = $this->reservationRepository->findBy(['user' => $user]);
        }

        return $this->render('reservation/list.html.twig', [
            'reservations' => $reservations,
        ]);
    }

    /**
     * Change status action.
     *
     * @param Request     $request     HTTP request
     * @param Reservation $reservation Reservation entity
     *
     * @return Response HTTP response
     */
    #[Route('/reservations/{id}/change-status', name: 'reservation_change_status', methods: ['GET', 'POST'])]
    #[IsGranted('CHANGE_STATUS', subject: 'reservation')]
    public function changeStatus(Request $request, Reservation $reservation): Response
    {
        $form = $this->createForm(
            ChangeStatusType::class,
            $reservation,
            [
                'method' => 'POST',
                'action' => $this->generateUrl('reservation_change_status', ['id' => $reservation->getId()]),
            ]
        );
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->reservationRepository->save($reservation);

            $this->addFlash(
                'success',
                $this->translator->trans('message.edited_successfully')
            );

            return $this->redirectToRoute('reservation_list');
        }

        return $this->render(
            'reservation/change_status.html.twig',
            [
                'form' => $form->createView(),
                'reservation' => $reservation,
            ]
        );
    }
}
