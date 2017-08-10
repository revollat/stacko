<?php

namespace AppBundle\Controller;

use AppBundle\Entity\Question;
use AppBundle\Entity\Reponse;
use AppBundle\Form\ReponseType;
use Doctrine\Common\Persistence\ObjectManager;
use Knp\Component\Pager\Paginator;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Component\HttpFoundation\Response;

class DefaultController extends Controller
{
    /**
     * @Route("/", name="homepage")
     */
    public function indexAction(Request $request)
    {
        return $this->render('default/index.html.twig');
    }

    /**
     * @Route("/listing/{page}", name="listing")
     */
    public function listingAction(Paginator $paginator, ObjectManager $em, $page = 1)
    {
        $query = $em->createQuery('SELECT q FROM AppBundle:Question q ORDER BY q.intitule ASC');
        $pagination = $paginator->paginate($query,$page);

        return $this->render('default/listing.html.twig', [
            'pagination' => $pagination
        ]);
    }

    /**
     * @Route("/question/{id}", name="view_question")
     * @Security("has_role('ROLE_USER')")
     * @ParamConverter("question", class="AppBundle:Question")
     */
    public function viewQuestionAction(Question $question, Request $request, ObjectManager $em)
    {

        $form = $this->createForm(ReponseType::class);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $reponse = $form->getData();
            $reponse->setUser($this->getUser());
            $reponse->setQuestion($question);
            $em->persist($reponse);
            $em->flush();

            $this->addFlash(
                'notice',
                'Votre réponse a été ajoutée. Merci.'
            );

            return $this->redirectToRoute('view_question', [
                'id' => $question->getId()
            ]);
        }

        return $this->render('default/view_question.html.twig', [
            'question'  => $question,
            'form'      => $form->createView(),
        ]);
    }

    /**
     * @Route("/reponse/{id}/vote/{vote}", name="reponse_vote", requirements={"vote": "▲|▼"})
     * @Security("has_role('ROLE_USER')")
     * @ParamConverter("reponse", class="AppBundle:Reponse")
     */
    public function voteAction(Reponse $reponse, $vote, ObjectManager $em)
    {
        $current_vote = $reponse->getVote();
        $new_vote = $vote == "▲" ? ++$current_vote : --$current_vote ;
        $reponse->setVote($new_vote);
        $em->persist($reponse);
        $em->flush();
        return $this->redirectToRoute('view_question', [
            'id' => $reponse->getQuestion()->getId()
        ]);

    }
}
