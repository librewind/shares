<?php

namespace AppBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\JsonResponse;
use AppBundle\Entity\Portfolio;
use AppBundle\Entity\Share;
use AppBundle\Entity\PortfolioShare;
use AppBundle\Form\PortfolioType;
use AppBundle\Form\PortfolioShareType;
use Symfony\Component\Form\Form;

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
     *
     * @return Response
     */
    public function indexAction() : Response
    {
        $user = $this->getUser();

        $portfolios = $user->getPortfolios();

        $deleteForms = [];
        foreach ($portfolios as $portfolio) {
            $deleteForms[] = $this->createDeleteForm($portfolio)->createView();
        }

        return $this->render('portfolio/index.html.twig', [
            'portfolios'  => $portfolios,
            'delete_forms' => $deleteForms,
        ]);
    }

    /**
     * Создает новый портфель.
     *
     * @Route("/new", name="portfolio_new")
     * @Method({"GET", "POST"})
     *
     * @param Request $request
     *
     * @return Response|RedirectResponse
     */
    public function newAction(Request $request)
    {
        $portfolio = new Portfolio();

        $form = $this->createForm(PortfolioType::class, $portfolio);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();

            $portfolio->setUser($this->getUser());

            $em->getRepository(Portfolio::class)->save($portfolio);

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
     *
     * @param Portfolio $portfolio
     *
     * @return Response
     */
    public function showAction(Portfolio $portfolio) : Response
    {
        $portfolioShares = $portfolio->getPortfolioShares();

        $em = $this->getDoctrine()->getManager();

        $allShares = $em->getRepository(Share::class)->findAllWithExclude($portfolioShares);

        $totalProcents = $em->getRepository(Portfolio::class)->getTotalProcents($portfolio);

        return $this->render('portfolio/show.html.twig', [
            'portfolio'       => $portfolio,
            'allShares'       => $allShares,
            'portfolioShares' => $portfolioShares,
            'totalProcents'   => $totalProcents,
        ]);
    }

    /**
     * Отображает форму для редактирования портфеля.
     *
     * @Route("/{id}/edit", name="portfolio_edit")
     * @Method({"GET", "POST"})
     *
     * @param Request   $request
     * @param Portfolio $portfolio
     *
     * @return Response|RedirectResponse
     */
    public function editAction(Request $request, Portfolio $portfolio)
    {
        $editForm = $this->createForm(PortfolioType::class, $portfolio);

        $editForm->handleRequest($request);

        if ($editForm->isSubmitted() && $editForm->isValid()) {
            $this->getDoctrine()->getManager()->flush();

            return $this->redirectToRoute('portfolio_index');
        }

        return $this->render('portfolio/edit.html.twig', [
            'portfolio' => $portfolio,
            'edit_form' => $editForm->createView(),
        ]);
    }

    /**
     * Удаляет портфолио.
     *
     * @Route("/{id}", name="portfolio_delete")
     * @Method("DELETE")
     *
     * @param Portfolio $portfolio
     *
     * @return RedirectResponse
     */
    public function deleteAction(Request $request, Portfolio $portfolio) : RedirectResponse
    {
        $form = $this->createDeleteForm($portfolio);
        $form->handleRequest($request);

        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();

            $user = $this->getUser();

            if ($portfolio->getUser() === $user) {
                $em->getRepository(Portfolio::class)->delete($portfolio);
            }
        }

        return $this->redirectToRoute('portfolio_index');
    }

    /**
     * Создает форму для удаления портфеля.
     *
     * @param Portfolio $portfolio
     *
     * @return Form
     */
    private function createDeleteForm(Portfolio $portfolio)
    {
        return $this->createFormBuilder()
            ->setAction($this->generateUrl('portfolio_delete', ['id' => $portfolio->getId()]))
            ->setMethod('DELETE')
            ->getForm();
    }

    /**
     * Добавляет акцию в портфель.
     *
     * @Route("/{id}/share", name="share_add")
     * @Method({"GET", "POST"})
     *
     * @param Request   $request
     * @param Portfolio $portfolio
     *
     * @return Response|RedirectResponse
     */
    public function addShareAction(Request $request, Portfolio $portfolio)
    {
        $portfolioShare = new PortfolioShare();

        $portfolioShare->setPortfolio($portfolio);

        $form = $this->createForm(PortfolioShareType::class, $portfolioShare, [
            'entity_manager' => $this->get('doctrine.orm.entity_manager'),
            'portfolio'      => $portfolio
        ]);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($portfolioShare);
            $em->flush();

            return $this->redirectToRoute('portfolio_show', ['id' => $portfolio->getId()]);
        }

        return $this->render('portfolio/add_share.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    /**
     * Редактирует акцию в портфеле.
     *
     * @Route("/{portfolioId}/share/{shareId}", name="share_edit")
     * @ParamConverter("portfolio", options={"mapping": {"portfolioId": "id"}})
     * @ParamConverter("share", options={"mapping": {"shareId": "id"}})
     * @Method({"GET", "POST"})
     *
     * @param Request   $request
     * @param Portfolio $portfolio
     * @param Share     $share
     *
     * @return Response|RedirectResponse
     */
    public function editShareAction(Request $request, Portfolio $portfolio, Share $share)
    {
        $em = $this->getDoctrine()->getManager();

        $portfolioShare = $em->getRepository(PortfolioShare::class)
            ->findOneBy(['portfolio' => $portfolio, 'share' => $share]);

        $form = $this->createForm(PortfolioShareType::class, $portfolioShare, [
            'entity_manager' => $this->get('doctrine.orm.entity_manager'),
            'portfolio'      => $portfolio
        ]);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($portfolioShare);
            $em->flush();

            return $this->redirectToRoute('portfolio_show', ['id' => $portfolio->getId()]);
        }

        return $this->render('portfolio/edit_share.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    /**
     * Удаляет акцию из портфеля.
     *
     * @Route("/{portfolioId}/share/{shareId}", name="share_delete")
     * @ParamConverter("portfolio", options={"mapping": {"portfolioId": "id"}})
     * @ParamConverter("share", options={"mapping": {"shareId": "id"}})
     * @Method("DELETE")
     *
     * @param Portfolio $portfolio
     * @param Share     $share
     *
     * @return RedirectResponse
     */
    public function deleteShareAction(Portfolio $portfolio, Share $share) : RedirectResponse
    {
        $em = $this->getDoctrine()->getManager();

        $portfolioShare = $em->getRepository(PortfolioShare::class)
            ->findOneBy(['portfolio' => $portfolio, 'share' => $share]);

        $em = $this->getDoctrine()->getManager();
        $em->remove($portfolioShare);
        $em->flush();

        return $this->redirectToRoute('portfolio_show', ['id' => $portfolio->getId()]);
    }

    /**
     * Подсчет доходности портфеля.
     *
     * @Route("/{id}/calc", name="portfolio_calc")
     * @Method("GET")
     *
     * @param Portfolio $portfolio
     *
     * @return JsonResponse
     */
    public function calculationAction(Portfolio $portfolio) : JsonResponse
    {
        $shareDataImport = $this->get('app.share_data_import');

        try {
            $result = $shareDataImport->fetchMonthlyYield($portfolio);
        } catch (\RuntimeException $e) {
            $result = [
                'error'     => true,
                'error_msg' => $e->getMessage(),
            ];
        }

        return new JsonResponse($result);
    }
}