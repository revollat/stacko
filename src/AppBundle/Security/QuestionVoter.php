<?php
namespace AppBundle\Security;

use AppBundle\Entity\Question;
use AppBundle\Entity\User;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;

class QuestionVoter extends Voter
{
    const EDIT = 'edit';

    protected function supports($attribute, $subject)
    {
        if (!in_array($attribute, array(self::EDIT))) {
            return false;
        }

        if (!$subject instanceof Question) {
            return false;
        }

        return true;
    }

    protected function voteOnAttribute($attribute, $subject, TokenInterface $token)
    {
        $user = $token->getUser();
        if (!$user instanceof User) {
            return false;
        }

        /** @var Question $question */
        $question = $subject;

        switch ($attribute) {
            case self::EDIT:
                return $user === $question->getUser();
        }

        throw new \LogicException('This code should not be reached!');
    }
}