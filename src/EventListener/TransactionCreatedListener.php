<?php
namespace App\EventListener;

use ApiPlatform\Symfony\EventListener\EventPriorities;
use App\Entity\Transaction;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpKernel\Event\ViewEvent;
use Symfony\Component\HttpKernel\HttpKernelInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Email;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Symfony\Component\HttpFoundation\Response;

class TransactionCreatedListener implements EventSubscriberInterface
{
    private $em;
    private $mailer;

    public function __construct(EntityManagerInterface $em, MailerInterface $mailer)
    {
        $this->em = $em;
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
        $transaction = $event->getControllerResult();


        // Check if the current request is a POST request for the Transaction entity
        if (HttpKernelInterface::MAIN_REQUEST !== $event->getRequestType() ||
            !$request->isMethod('POST') ||
            $request->attributes->get('_api_resource_class') !== Transaction::class ||
            $request->attributes->get('_api_collection_operation_name') !== 'post'
        ) {
            if ($transaction instanceof Transaction) {
                $this->sendTransactionEmail($transaction);
            }
        }

    }

    private function sendTransactionEmail(Transaction $transaction): void
    {
        $email = (new Email())
            ->from('no-reply@example.com')
            ->to($transaction->getBon()->getUser()->getEmail())
            ->subject('New Transaction Created')
            ->text('A new transaction has been created with the following details:' . "\n\n" .
                'Transaction ID: ' . $transaction->getId() . "\n" .
                'Magasin: ' . $transaction->getMagasin()->getNom() . "\n" .
                'Montant: ' . $transaction->getMontant() . "\n" .
                'Date: ' . $transaction->getDateTransaction()->format('Y-m-d H:i:s'));

        try {
            $this->mailer->send($email);
        } catch (\Exception $e) {
            throw new HttpException(Response::HTTP_INTERNAL_SERVER_ERROR, 'Unable to send email: ' . $e->getMessage());
        }
    }
}
