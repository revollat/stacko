<?php
namespace AppBundle\Service;

use AppBundle\Entity\Reponse;
use FOS\UserBundle\Model\UserInterface;

class VoteChecker {


    public function check(Reponse $reponse, UserInterface $user){
        return $reponse->getUser() != $user;
    }

}