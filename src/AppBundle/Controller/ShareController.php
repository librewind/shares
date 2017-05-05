<?php

namespace AppBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class ShareController extends Controller
{
    /**
     * @Route("/", name="rootpage")
     */
    public function listAction(Request $request)
    {
        $shares = [
            'Apple',
            'IBM',
            'Facebook',
            'Microsoft'
        ];

        return $this->render('shares/index.html.twig', [
            'shares' => $shares,
        ]);
    }
}