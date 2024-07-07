<?php

namespace App\EventListener;


use ApiPlatform\Symfony\EventListener\EventPriorities;
use App\Entity\BonAchat;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Event\ViewEvent;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Symfony\Component\HttpKernel\HttpKernelInterface;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Email;

class BonAchatCreatedListener implements EventSubscriberInterface
{


    private MailerInterface $mailer;
    public function __construct(MailerInterface $mailer)
    {
        $this->mailer = $mailer;
    }

    public static function getSubscribedEvents(): array
    {
        return [
            'kernel.view' => ['onKernelView', EventPriorities::POST_WRITE],
        ];
    }

    public function onKernelView(ViewEvent $event): void
    {
        $request = $event->getRequest();
        $bonAchat = $event->getControllerResult();


        if (HttpKernelInterface::MAIN_REQUEST !== $event->getRequestType() ||
            !$request->isMethod('POST') ||
            $request->attributes->get('_api_resource_class') !== BonAchat::class ||
            $request->attributes->get('_api_collection_operation_name') !== 'post'
        ) {
            if ($bonAchat instanceof BonAchat) {
                $this->sendBonAchatEmail($bonAchat);
            }
        }
    }
    private function sendBonAchatEmail(BonAchat $bonAchat): void
    {
        $magasins = "";
        foreach ($bonAchat->getMagasins() as $magasin){
            $magasins .= sprintf("- %s \n", $magasin->getNom());
        }
        $email = (new Email())
            ->from('no-reply@example.com')
            ->to($bonAchat->getUser()->getEmail())
            ->subject('Nouveau Bon d\'Achat Créé')
            ->text('un nouveau bon d\'achat a été créé:' . "\n\n" .
                'Bon ID: ' . $bonAchat->getId() . "\n" .
                'Nom d\'utilisateur: '. $bonAchat->getUser()->getEmail(). "\n" .
                "Listes des magasins: \n" . $magasins .
                'Montant: ' . $bonAchat->getMontantRestant() . "\n");
        try {
            $this->mailer->send($email);
        } catch (\Exception $e) {
            throw new HttpException(Response::HTTP_INTERNAL_SERVER_ERROR, 'Unable to send email: ' . $e->getMessage());
        }
    }
}