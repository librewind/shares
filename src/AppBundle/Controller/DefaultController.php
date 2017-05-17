<?php

namespace AppBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Symfony\Component\HttpFoundation\Response;

/**
 * Домашний контроллер :)
 */
class DefaultController extends Controller
{
    /**
     * Главная страница.
     *
     * @Route("/", name="homepage")
     * @Method("GET")
     *
     * @return Response
     */
    public function indexAction() : Response
    {
        return $this->render('home/index.html.twig');
    }
}
