<?php

namespace AppBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\JsonResponse;
use AppBundle\Entity\Portfolio;
use AppBundle\Entity\Share;
use AppBundle\Entity\PortfolioShare;

/**
 * Контроллер Portfolio.
 *
 * @Route("portfolio")
 */
class PortfolioController extends Controller
{
    /**
     * Выводит список всех портфелей.
     *
     * @Route("/", name="portfolio_index")
     * @Method("GET")
     */
    public function indexAction()
    {
        $user = $this->getUser();

        $portfolios = $user->getPortfolios();

        return $this->render('portfolio/index.html.twig', [
            'portfolios' => $portfolios,
        ]);
    }

    /**
     * Создает новый портфель.
     *
     * @Route("/new", name="portfolio_new")
     * @Method({"GET", "POST"})
     */
    public function newAction(Request $request)
    {
        $portfolio = new Portfolio();

        $form = $this->createForm('AppBundle\Form\PortfolioType', $portfolio);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();

            $portfolio->setUser($this->getUser());

            $em->getRepository('AppBundle:Portfolio')->save($portfolio);

            return $this->redirectToRoute('portfolio_index');
        }

        return $this->render('portfolio/new.html.twig', [
            'portfolio' => $portfolio,
            'form'      => $form->createView(),
        ]);
    }

    /**
     * Отображает портфель.
     *
     * @Route("/{id}", name="portfolio_show")
     * @Method("GET")
     */
    public function showAction(Portfolio $portfolio)
    {
        $portfolioShares = $portfolio->getPortfolioShares();

        $allShares = $this->getDoctrine()
            ->getRepository('AppBundle:Share')
            ->findAll();

        $shares = [
            'KO'   => 0.9,
            'YHOO' => 0.1,
        ];

        $shareDataImport = $this->container->get('app.share_data_import');

        $portfolioYield = $shareDataImport->fetchYield($shares, 24);

        $deleteForm = $this->createDeleteForm($portfolio);

        $em = $this->getDoctrine()->getManager();

        $totalProcents = $em->getRepository('AppBundle:Portfolio')->getTotalProcents($portfolio);

        return $this->render('portfolio/show.html.twig', [
            'portfolio'       => $portfolio,
            'allShares'       => $allShares,
            'portfolioShares' => $portfolioShares,
            'portfolioYield'  => $portfolioYield,
            'delete_form'     => $deleteForm->createView(),
            'totalProcents'   => $totalProcents,
        ]);
    }

    /**
     * Отображает форму для редактирования портфеля.
     *
     * @Route("/{id}/edit", name="portfolio_edit")
     * @Method({"GET", "POST"})
     */
    public function editAction(Request $request, Portfolio $portfolio)
    {
        $deleteForm = $this->createDeleteForm($portfolio);

        $editForm = $this->createForm('AppBundle\Form\PortfolioType', $portfolio);

        $editForm->handleRequest($request);

        if ($editForm->isSubmitted() && $editForm->isValid()) {
            $this->getDoctrine()->getManager()->flush();

            return $this->redirectToRoute('portfolio_index');
        }

        return $this->render('portfolio/edit.html.twig', [
            'portfolio'   => $portfolio,
            'edit_form'   => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        ]);
    }

    /**
     * Удаляет портфолио.
     *
     * @Route("/{id}", name="portfolio_delete")
     * @Method("DELETE")
     */
    public function deleteAction(Request $request, Portfolio $portfolio)
    {
        $form = $this->createDeleteForm($portfolio);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->remove($portfolio);
            $em->flush($portfolio);
        }

        return $this->redirectToRoute('portfolio_index');
    }

    /**
     * @Route("/{id}/share", name="share_add")
     * @Method({"GET", "POST"})
     */
    public function addShareAction(Request $request, Portfolio $portfolio)
    {
        if ('POST' == $request->getMethod()) {
            $share = $this->getDoctrine()
                ->getRepository('AppBundle:Share')
                ->find($request->get('share_id'));

            if (!$share) {
                throw $this->createNotFoundException(
                    'No share found for id '.$request->get('share_id')
                );
            }

            $proportion = $request->get('proportion');

            $portfolioShare = new PortfolioShare();

            $portfolioShare->setPortfolio($portfolio);

            $portfolioShare->setShare($share);

            $portfolioShare->setProportion($proportion);

            $em = $this->getDoctrine()->getManager();
            $em->persist($portfolioShare);
            $em->flush();

            return $this->redirectToRoute('portfolio_show', ['id' => $portfolio->getId()]);
        }

        $portfolioShares = $portfolio->getPortfolioShares();

        $em = $this->getDoctrine()->getManager();

        $allShares = $em->getRepository('AppBundle:Share')->findAllWithExclude($portfolioShares);

        $totalProcents = $em->getRepository('AppBundle:Portfolio')->getTotalProcents($portfolio);

        $maxProportion = 1 - $totalProcents;

        return $this->render('portfolio/add_share.html.twig', [
            'allShares'     => $allShares,
            'portfolio'     => $portfolio,
            'maxProportion' => $maxProportion
        ]);
    }

    /**
     * @Route("/{portfolioId}/share/{shareId}", name="share_delete")
     * @ParamConverter("portfolio", options={"mapping": {"portfolioId": "id"}})
     * @ParamConverter("share", options={"mapping": {"shareId": "id"}})
     * @Method("DELETE")
     */
    public function deleteShareAction(Portfolio $portfolio, Share $share)
    {
        $em = $this->getDoctrine()->getManager();

        $portfolioShare = $em->getRepository('AppBundle:PortfolioShare')
            ->findOneBy(['portfolio' => $portfolio, 'share' => $share]);

        $em = $this->getDoctrine()->getManager();
        $em->remove($portfolioShare);
        $em->flush();

        return $this->redirectToRoute('portfolio_show', ['id' => $portfolio->getId()]);
    }

    /**
     * Создает форму для удаления портфеля.
     *
     * @param Portfolio $portfolio Портфель
     *
     * @return \Symfony\Component\Form\Form Форма
     */
    private function createDeleteForm(Portfolio $portfolio)
    {
        return $this->createFormBuilder()
            ->setAction($this->generateUrl('portfolio_delete', ['id' => $portfolio->getId()]))
            ->setMethod('DELETE')
            ->getForm();
    }
}