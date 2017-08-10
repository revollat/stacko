<?php

namespace AppBundle\Controller;

use AppBundle\Entity\Question;
use Doctrine\Common\Persistence\ObjectManager;
use Knp\Component\Pager\Paginator;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;

class DefaultController extends Controller
{
    /**
     * @Route("/", name="homepage")
     */
    public function indexAction(Request $request)
    {
        // replace this example code with whatever you need
        return $this->render('default/index.html.twig', [
            'base_dir' => realpath($this->getParameter('kernel.project_dir')).DIRECTORY_SEPARATOR,
        ]);
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
     * @ParamConverter("question", class="AppBundle:Question")
     */
    public function viewQuestionAction(Question $question)
    {
        return $this->render('default/view_question.html.twig', [
            'question' => $question
        ]);
    }
}
