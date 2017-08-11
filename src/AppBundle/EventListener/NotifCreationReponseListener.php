<?php 
namespace AppBundle\EventListener;

use AppBundle\Entity\Reponse;
use Symfony\Bundle\FrameworkBundle\Routing\Router;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Doctrine\ORM\Event\LifecycleEventArgs;
use Doctrine\ORM\Event\PreUpdateEventArgs;
use AppBundle\Entity\Livre;
use AppBundle\Service\FileUploader;
use Symfony\Component\HttpFoundation\File\File;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

class NotifCreationReponseListener
{


    protected $mailer;
    protected $router;

    public function __construct(\Swift_Mailer $mailer, Router $router)
    {
        $this->mailer = $mailer;
        $this->router = $router;
    }

    public function postPersist(LifecycleEventArgs $args)
    {
        $reponse = $args->getEntity();
        if (!$reponse instanceof Reponse) {
            return;
        }

        $message = \Swift_Message::newInstance()
            ->setSubject('Ajout d\'une nouvelle réponse')
            ->setFrom('admin@example.com')
            ->setTo('admin@example.com')
            ->addPart(
                "Ajout d'une nouvelle réponse :  \n\n"
                . "Question : ". $reponse->getQuestion()->getBody() . "\n\n"
                . "Réponse proposée : " . $reponse->getBody() . "\n"
                . "Lien de validation : " . $this->router->generate('reponse_valid', ['id' => $reponse->getId()], UrlGeneratorInterface::ABSOLUTE_URL)
            );

        $this->mailer->send($message);
    }



    
}