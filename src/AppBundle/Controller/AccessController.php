<?php

namespace AppBundle\Controller;

use AppBundle\Entity\AppUser;
use AppBundle\Form\ConnexionType;
use AppBundle\Form\InscriptionType;
use AppBundle\Form\ChangementMotDePasseType;
use Symfony\Component\HttpFoundation\Request;
use Doctrine\Common\Persistence\ObjectManager;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Symfony\Component\Security\Csrf\TokenGenerator\TokenGeneratorInterface;
use Symfony\Component\EventDispatcher\GenericEvent;
use AppBundle\EventsConstants;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;


class AccessController extends Controller
{
    /**
     * @Route("/inscription",name="inscription")
     */
    public function inscriptionAction(
            Request $request,
            UserPasswordEncoderInterface $passwordEncoder,
            TokenGeneratorInterface $tokenGenerator,
            EventDispatcherInterface $eventDispatcher
        )
    {
        //create AppUser
        $user = new AppUser();
        //create AppUser form
        $form = $this->createForm(InscriptionType::class,$user);

        //handle form submussion
        $form->handleRequest($request);
        if($form->isSubmitted() && $form->isValid())
        {
            //encode password with bcrypt
            $password = $passwordEncoder->encodePassword($user,$user->getPlainPassword());
            $user->setPassword($password);
            $user->setAccountConfirmationRequestedAt(new \Datetime());
            $user->setAccountConfirmationToken($tokenGenerator->generateToken());
            // save user in database
            // dump($user);//die;
            
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($user);
            $entityManager->flush();

            //on declenche l'événement
            $event = new GenericEvent($user);
            $eventDispatcher->dispatch(EventsConstants::ON_REGISTRATION_SUCCESS_SEND_CONFIRMATION_TOKEN,$event);
            
            //set success flash message
            $this->addFlash("success", "Un email contenant un lien de confirmation valide 12 heures vous a été envoyé,");
            return $this->redirectToRoute("homepage");
        }

        return $this->render('access/inscription.html.twig',array(
            'form'=>$form->createView()
        ));
    }

    /**
     * @Route("/confirmation/{AccountConfirmationToken} ",name="confirmation")
     */
    public function confirmationAction(
            Request $request,
            $AccountConfirmationToken,
            ObjectManager $manager,
            TokenGeneratorInterface $tokenGenerator=null,
            EventDispatcherInterface $eventDispatcher
        )
    {
        $user=$this->getDoctrine()->getRepository(AppUser::class)->findOneBy(array('AccountConfirmationToken'=>$AccountConfirmationToken));
        
        if($user)
        {
            // set the token lifetime
            $max = $user->getAccountConfirmationRequestedAt()->modify('+ 6HOURS');
            // dump((new \DateTime)->diff($user->getAccountConfirmationRequestedAt()));

            //  if token is expired 
            if( new \DateTime() > $max )
            {
                
                $user->setAccountConfirmationRequestedAt(new \Datetime());
                $user->setAccountConfirmationToken($tokenGenerator->generateToken());

                $entityManager = $this->getDoctrine()->getManager();
                $entityManager->flush();

                // fires event on confirmation token expired
                $event = new GenericEvent($user);
                $eventDispatcher->dispatch(EventsConstants::ON_EXPIRED_TOKEN_SEND_NEW_CONFIRMATION_TOKEN,$event);
                // set warning message
                $this->addFlash("warning", "Ce token n'est plus valide,un nouveau lien pour valider votre compte un délais de 6 heures vous a été envoyé");

                //redirect to homepage
                return $this->redirectToRoute("homepage");
            }
            // token is ok
            $user->setAccountConfirmationRequestedAt(null);
            $user->setAccountConfirmationToken(null);
            $user->setIsActive(true);
            $user->setRoles(['ROLE_USER']);

            //update and save user data
            $manager->flush();
            $this->addFlash("success", "Votre compte est maintenant activé");
            return $this->redirectToRoute("homepage");
        }
        $this->addFlash("danger", "Ce token n\'éxiste pas");
        return $this->redirectToRoute("homepage");
    }

    /**
     * @Route("/demande-nouveau-mot-de-passe",name="demande_nouveau_mot_de_passe")
     */
    public function requestNewPaswwordAction(
        Request $request,
        ObjectManager $manager,
        TokenGeneratorInterface $tokenGenerator,
        EventDispatcherInterface $eventDispatcher
        )
    {
        
        $unknownUser = Array();

        $form = $this->createFormBuilder($unknownUser)
                        ->add('email',EmailType::class,array('required'=>true))
                        ->getForm();


        $form->handleRequest($request);
        if($form->isSubmitted() && $form->isValid())
        {
            $user = $manager->getRepository(AppUser::class)->findOneByEmail($form->getData('email'));
            if($user)
            {
                //update user data
                $user->setPasswordResetToken($tokenGenerator->generateToken());
                $user->setPasswordResetRequestedAt(new \DateTime());

                //save user data
                $manager->flush();

                //fires password reset event
                $event=new GenericEvent($user);
                $eventDispatcher->dispatch(EventsConstants::ON_USER_REQUEST_PASSWORD_RESET,$event);
                //create reset link 

                // $url=$this->generateUrl('changement_mot_de_passe',array('passwordResetToken'=>$user->getPasswordResetToken()), UrlGeneratorInterface::ABSOLUTE_URL);
                // $accounConfirmation = (new \Swift_Message('Réinitialisation de votre mot de passe'))
                // ->setFrom($this->getParameter('mailer_user'))
                // ->setTo($user->getEmail())
                // ->setBody('Cliquez ce lien pour réinitialiser votre mot de passe : 
                // '.$url);
                // $this->get('mailer')->send($accounConfirmation);

                $this->addFlash("success", "Un lien pour réinitialiser votre mot de passe vous a été envoyé");
                return $this->redirectToRoute("homepage");

            }
            dump($user);die;
        }
        return $this->render('access/demande-nouveau-mot-de-passe.html.twig',array('form'=>$form->createView()));
    }

    /**
     * @Route("/changement-mot-de-passe/{passwordResetToken} ",name="changement_mot_de_passe")
     */
    public function resetPasswordAction(Request $request,UserPasswordEncoderInterface $passwordEncoder,ObjectManager $manager,$passwordResetToken)
    {
        $unknownUser = Array();
        $form = $this->createFormBuilder($unknownUser)
                        ->add('plainPassword',RepeatedType::class,array(
                            'type'=>PasswordType::class,
                            'invalid_message'=>'les mots de passe ne correspondent pas',
                            'first_options'=>array('label'=>'Mot de Passe'),
                            'second_options'=>array('label'=>'Répéter le Mot de Passe')    
                        ))
                        ->getForm();

        $form->handleRequest($request);
        if($form->isSubmitted() && $form->isValid())
        {
            $user = $manager->getRepository(AppUser::class)->findOneByPasswordResetToken($passwordResetToken);
            
            $user->setPlainPassword($form->get('plainPassword')->getData()) ;
            $password = $passwordEncoder->encodePassword($user,$user->getPlainPassword());
            $user->setPassword($password);
            $user->setPasswordResetRequestedAt(null);
            $user->setPasswordResetToken(null);
            // dump($user);die;
            //save new password
            $manager->flush();
            $this->addFlash("success", "Votre nouveau mot de passe a été enregistré");
                return $this->redirectToRoute("homepage");
        }
        return $this->render('access/changement-mot-de-passe.html.twig',array(
            'form'=>$form->createView()
        ));
    }
    /**
     * @Route("/connexion",name="connexion")
     */
    public function loginAction(Request $request,AuthenticationUtils $authenticationUtils)
    {
        // get the login error if there is one
        $error = $authenticationUtils->getLastAuthenticationError();

        // last username entered by the user
        $lastUsername = $authenticationUtils->getLastUsername();

        return $this->render('access/connexion.html.twig', array(
            'last_username' => $lastUsername,
            'error'         => $error,
        ));
       
        
        
    }

    /**
     * @Route("/deconnexion",name="deconnexion")
     */
    public function logout()
    {
        
    }
}