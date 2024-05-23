<?php
namespace App\EventListener;

use Doctrine\ORM\Event\LifecycleEventArgs;
use App\Entity\BonAchat;
use App\Entity\Transaction;

class BonAchatListener
{
    public function postLoad(BonAchat $bonAchat, LifecycleEventArgs $event): void
    {
        $this->updateEtat($bonAchat);
    }

    public function postPersist(Transaction $transaction, LifecycleEventArgs $event): void
    {
        $this->updateEtat($transaction->getBonAchat());
    }

    public function postUpdate(Transaction $transaction, LifecycleEventArgs $event): void
    {
        $this->updateEtat($transaction->getBonAchat());
    }

    private function updateEtat(BonAchat $bonAchat): void
    {
        $now = new \DateTime();
        if ($bonAchat->getDateExpire() < $now) {
            $bonAchat->setEtat(false);
        }
    }
}
