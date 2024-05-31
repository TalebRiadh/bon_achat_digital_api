<?php

namespace App\Services;

use App\Entity\BonAchat;
use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;

class BonAchatService
{
    public function __construct(
        protected EntityManagerInterface $em)
    {
    }

    /**
     * @throws \Exception
     */
    public function CreateBonAchat($data)
    {
        /**
         * @var BonAchat $bonAchat
         */
        $bonAchat = new BonAchat();
        $bonAchat->setMontantInitial($data['montant_initial']);
        $bonAchat->setMontantRestant($data['montant_initial']);
        $bonAchat->setDateCreation(new \DateTime());
        $bonAchat->setDateExpire(new \DateTime($data['date_expire']));
        $user = $this->em->getRepository(User::class)->find($data['user_id']);
        $bonAchat->setUser($user);
        $this->em->persist($bonAchat);
        $this->em->flush();
        return $bonAchat;
    }

}