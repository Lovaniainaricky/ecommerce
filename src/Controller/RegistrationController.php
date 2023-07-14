<?php

namespace App\Controller;

use App\Entity\Users;
use App\Form\RegistrationFormType;
use App\Repository\UsersRepository;
use App\Security\UsersAuthenticator;
use App\Service\JWTService;
use App\Service\SendMailService;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Query\Expr\Func;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Authentication\UserAuthenticatorInterface;
use Symfony\Contracts\Translation\TranslatorInterface;

class RegistrationController extends AbstractController
{
    /**
     * @Route("/inscription", name="app_register")
     */
    public function register(Request $request, UserPasswordHasherInterface $userPasswordHasher, UserAuthenticatorInterface $userAuthenticator, UsersAuthenticator $authenticator, EntityManagerInterface $entityManager,SendMailService $mail, JWTService $jwt): Response
    {
        $user = new Users();
        $form = $this->createForm(RegistrationFormType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            // encode the plain password
            $user->setPassword(
            $userPasswordHasher->hashPassword(
                    $user,
                    $form->get('plainPassword')->getData()
                )
            );

            $entityManager->persist($user);
            $entityManager->flush();

            //On genere le token pour 3H de temps
            $header = [
                'alg' => 'H256',
                'typ' => 'JWT',
            ];

            //on crée le Payload
            $payload = [
                'user_id' => $user->getId()
            ];

            $token = $jwt->generate($header,$payload, $this->getParameter('app.jwtsecret'));
            // dd($token);
            // do anything else you need here, like send an email
            //on envoi mail
            $mail->send(
                'no-reply@monsite.net',
                $user->getEmail(),
                'Activation de votre compte sur le site ecommerce sf',
                'register',
                compact('user','token')
            );
            //Compact c'est comme un array user => user

            return $userAuthenticator->authenticateUser(
                $user,
                $authenticator,
                $request
            );
        }

        return $this->render('registration/register.html.twig', [
            'registrationForm' => $form->createView(),
        ]);
    }

    /**
     * @Route("/verifyuser/{token}", name="verify_user")
     */
    public function verifyUser($token,JWTService $jwt,UsersRepository $usersRepository,EntityManagerInterface $em) : Response 
    {
        // dd($jwt->checkToken($token, $this->getParameter('app.jwtsecret')));

        if ($jwt->isValid($token) && !$jwt->isExpired($token) && $jwt->checkToken($token, $this->getParameter('app.jwtsecret'))) {
            $payload = $jwt->getPayload($token);

            //pour recuperer l'id du user dans payload
            $user = $usersRepository->find($payload["user_id"]);

            //on verifie que l'utilisateur existe en n'est pas encore verifier
            if ($user && !$user->getIsVerified()) {
                $user->setIsVerified(1);
                $em->flush($user);

                $this->addFlash("success", "utilisateur validé");
                return $this->redirectToRoute('profile_index');
            }

        }else {
            $this->addFlash("danger", "le token est invalide ou a expiré");
            return $this->redirectToRoute('app_login');
        }

    }

    /**
     * @Route("/renvoiverif", name="resend_verif")
     */
    public function resendVerification(SendMailService $mail, JWTService $jwt, UsersRepository $usersRepository)
    {
        $user = $this->getUser();

        if (!$user) {
            $this->addFlash("danger", "Vous devez être connecté pour accéder à cette page");
            return $this->redirectToRoute('app_login');
        }

        if ($user->getIsVerified()) {
            $this->addFlash("warning", "l'utilisateur est déjà activé");
            return $this->redirectToRoute('profile_index');
        }

        //On genere le token pour 3H de temps
        $header = [
            'alg' => 'H256',
            'typ' => 'JWT',
        ];

        //on crée le Payload
        $payload = [
            'user_id' => $user->getId()
        ];

        $token = $jwt->generate($header,$payload, $this->getParameter('app.jwtsecret'));
        // dd($token);
        // do anything else you need here, like send an email
        //on envoi mail
        $mail->send(
            'no-reply@monsite.net',
            $user->getEmail(),
            'Activation de votre compte sur le site ecommerce sf',
            'register',
            compact('user','token')
        );
        //Compact c'est comme un array user => user

        $this->addFlash("success", "Email de vérification envoyer");
        return $this->redirectToRoute('profile_index'); 
    }
}
