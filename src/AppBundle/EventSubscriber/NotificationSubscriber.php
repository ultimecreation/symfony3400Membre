<?php
// App\EventSubscriber\RegistrationNotifySubscriber.php
namespace AppBundle\EventSubscriber;
 
use App\Entity\AppUser;
use AppBundle\EventsConstants;
use Symfony\Component\EventDispatcher\GenericEvent;

use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\Security\Csrf\TokenGenerator\TokenGeneratorInterface;

/**
 * Envoi un mail de bienvenue à chaque creation d'un utilisateur
 *
 */
class NotificationSubscriber implements EventSubscriberInterface
{
    private $mailer;
    private $sender;
 
    public function __construct(\Swift_Mailer $mailer,
                                TokenGeneratorInterface $tokenGenerator,
                                UrlGeneratorInterface $urlGenerator,
                                $sender
                                )
    {
        // On injecte notre expediteur et la classe pour envoyer des mails
        $this->mailer = $mailer;
        $this->tokenGenerator=$tokenGenerator;
        $this->urlGenerator=$urlGenerator;
        $this->sender=$sender;
    }
 
    public static function getSubscribedEvents(): array
    {
        return [
            // le nom de l'event et le nom de la fonction qui sera déclenché
            EventsConstants::ON_REGISTRATION_SUCCESS_SEND_CONFIRMATION_TOKEN => 'onUserRegistration',
            EventsConstants::ON_EXPIRED_TOKEN_SEND_NEW_CONFIRMATION_TOKEN => 'onConfirmationTokenExpired',
            EventsConstants::ON_USER_REQUEST_PASSWORD_RESET => 'onUserRequestPasswordReset'
        ];
    }
 
    public function onUserRegistration(GenericEvent $event)
    {
        /** @var AppUser $user */
        $user = $event->getSubject();

        $url=$this->urlGenerator->generate('confirmation',array('AccountConfirmationToken'=>$user->getAccountConfirmationToken()), UrlGeneratorInterface::ABSOLUTE_URL);

        // send confirmation email
        
        $accounConfirmation = (new \Swift_Message('Confirmez votre compte'))
        ->setFrom($this->sender)
        ->setTo($user->getEmail())
        ->setBody('Cliquez ce lien pour confirmer votre inscription : 
        '.$url);
        $this->mailer->send($accounConfirmation);
    }

    public function onConfirmationTokenExpired(GenericEvent $event)
    {
        /** @var AppUser $user */
        $user = $event->getSubject();

        // send confirmation email
        $url=$this->urlGenerator->generate('confirmation',array('AccountConfirmationToken'=>$user->getAccountConfirmationToken()), UrlGeneratorInterface::ABSOLUTE_URL);
        $accounConfirmation = (new \Swift_Message('Confirmez votre compte'))
        ->setFrom($this->sender)
        ->setTo($user->getEmail())
        ->setBody('Cliquez ce lien pour confirmer votre inscription : 
        '.$url);
        $this->mailer->send($accounConfirmation);
    }

    public function onUserRequestPasswordReset(GenericEvent $event)
    {
        /** @var AppUser $user */
        $user = $event->getSubject();

        $url=$this->urlGenerator->generate('changement_mot_de_passe',array('passwordResetToken'=>$user->getPasswordResetToken()), UrlGeneratorInterface::ABSOLUTE_URL);
        $accounConfirmation = (new \Swift_Message('Réinitialisation de votre mot de passe'))
        ->setFrom($this->sender)
        ->setTo($user->getEmail())
        ->setBody('Cliquez ce lien pour réinitialiser votre mot de passe : 
        '.$url);
        $this->mailer->send($accounConfirmation);
    }
}