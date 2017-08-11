<?php

namespace AppBundle\Controller;

use AppBundle\Entity\Question;
use AppBundle\Entity\Reponse;
use AppBundle\Form\QuestionType;
use AppBundle\Form\ReponseType;
use AppBundle\Service\HandleVote;
use AppBundle\Service\VoteChecker;
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
    public function voteAction(Reponse $reponse, $vote, HandleVote $voteHandler, VoteChecker $checker)
    {

        if($checker->check($reponse, $this->getUser())){
            $voteHandler->handle($reponse, $vote);
        }else{
            $this->addFlash(
                'notice',
                'Vous ne pouvez pas voter pour votre réponse'
            );
        }

        return $this->redirectToRoute('view_question', [
            'id' => $reponse->getQuestion()->getId()
        ]);

    }

    /**
     * @Route("/question/{id}/edit", name="question_edit")
     * @Security("has_role('ROLE_USER')")
     * @ParamConverter("question", class="AppBundle:Question")
     */
    public function questionEditAction(Question $question, ObjectManager $em, Request $request)
    {

        $form = $this->createForm(QuestionType::class, $question);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $em->flush();

            $this->addFlash(
                'notice',
                'Votre question a été ajoutée. Merci.'
            );

            return $this->redirectToRoute('view_question', [
                'id' => $question->getId()
            ]);
        }

        return $this->render('default/edit_question.html.twig', [
            'question'  => $question,
            'form'      => $form->createView(),
        ]);

    }

}
