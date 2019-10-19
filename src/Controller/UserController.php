<?php
namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;

use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Symfony\Component\Translation\TranslatorInterface;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\EventDispatcher\GenericEvent;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;

use App\Form\UserType;
use App\Entity\User;
use App\Entity\UserContext;
use App\Entity\Email;
use App\Events;

class UserController extends AbstractController
{
    /**
     * @Route("/user/register", name="user_register")
     */
     public function register(Request $request, UserPasswordEncoderInterface $passwordEncoder, EventDispatcherInterface $eventDispatcher, \Swift_Mailer $mailer, TranslatorInterface $translator)
     {
         $user = new User();
         $form = $this->createForm(UserType::class, $user);
         $form->handleRequest($request);
         if ($form->isSubmitted() && $form->isValid()) {
             $password = $passwordEncoder->encodePassword($user, $user->getPassword());
             $user->setPassword($password);
             // Par defaut l'utilisateur aura toujours le rôle ROLE_USER
             $user->setRoles(['ROLE_USER']);
             // On enregistre l'utilisateur dans la base
             $em = $this->getDoctrine()->getManager();
             $em->persist($user);
             $em->flush();
             // On déclenche l'event
             // $event = new GenericEvent($user);
             // $eventDispatcher->dispatch(Events::USER_REGISTERED, $event);
             $message = (new \Swift_Message($translator->trans('user.registration')))
             ->setFrom(['slam.booking.web@gmail.com' => 'Slam Booking'])
             ->setTo($user->getEmail())
             ->setBody(
                 $this->renderView('email/registration.html.twig', array('user' => $user)),
                 'text/html'
             );
             $mailer->send($message);
             return $this->redirectToRoute('app_login');
         }
         return $this->render('user/register.html.twig', array('form' => $form->createView()));
     }
}
