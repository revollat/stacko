<?php
namespace AppBundle\Service;

use AppBundle\Entity\Reponse;
use Doctrine\Common\Persistence\ObjectManager;

class HandleVote {

    private $em;

    public function __construct(ObjectManager $em)
    {
        $this->em = $em;
    }

    public function handle(Reponse $reponse, $vote){
        $current_vote = $reponse->getVote();
        $new_vote = $vote == "▲" ? ++$current_vote : --$current_vote ;
        $reponse->setVote($new_vote);
        $this->em->persist($reponse);
        $this->em->flush();
    }

}