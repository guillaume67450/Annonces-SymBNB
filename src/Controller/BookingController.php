<?php

namespace App\Controller;

use App\Entity\Ad;
use App\Entity\Booking;
use App\Entity\Comment;
use App\Form\BookingType;
use App\Form\CommentType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class BookingController extends AbstractController
{
    /**
     * @Route("/ads/{slug}/book", name="booking_create")
     * @IsGranted("ROLE_USER")
     */
    public function book(Ad $ad, Request $request, EntityManagerInterface $manager)
    {   // Request : on a besoin de la request car on a besoin que le formulaire se renseigne auprès de la requête 
        //pour voir s'il a été soumis pour qu'il puisse ensuite le valider et récupérer les informations qui viennent 
        //de la requête
        $booking = new Booking();
        $form = $this->createForm(BookingType::class, $booking);

        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()) {
            $user = $this->getUser();

            $booking->setBooker($user)
                    ->setAd($ad);

            // Si les dates ne sont pas disponibles, messages d'erreur
            if(!$booking->isBookableDate()) {
                $this->addFlash(
                    'warning',
                    "Les dates que vous avez choisies ne peuvent être réservées : elles sont déjà prises."
                );
            } else {
            // Sinon enregistrement et redirection
            $manager->persist($booking);
            $manager->flush();

            return $this->redirectToRoute('booking_show', ['id' => $booking->getId(), 
            'withAlert' => true]); // le paramètre, comme il n'existe pas dans booking_show, va se placer en get
            // après le paramètre {id}, ça va donc donner : /booking/{id}?withAlert=true
            }
        }

        return $this->render('booking/book.html.twig', [
            'ad' => $ad,
            'form' => $form->createView()
        ]);
    }


    /**
     * Permet d'afficher la page d'une réservation
     * 
     * @Route("/booking/{id}", name="booking_show")
     * 
     * @param Booking $booking
     * @param Request $request
     * @param EntityManagerInterface $manager
     * @return Response
     */
    public function show(Booking $booking, Request $request, EntityManagerInterface $manager) {
        $comment = new Comment;

        $form = $this->createForm(CommentType::class, $comment);

        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()) {
            $comment->setAd($booking->getAd())
                    ->setAuthor($this->getUser());

            $manager->persist($comment);
            $manager->flush();

            $this->addFlash(
                'success',
                "Votre commentaire a bien été pris en compte !"
            );
        }

        return $this->render('booking/show.html.twig', [
            'booking' => $booking, // on lui injecte une variable 'booking' qui contiendra le contenu de $booking
            'form' => $form->createView()
        ]);
    }
}
