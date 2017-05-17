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

        return $this->render('portfolio/index.html.twig', [
            'portfolios' => $portfolios,
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
    public function deleteAction(Portfolio $portfolio) : RedirectResponse
    {
        $em = $this->getDoctrine()->getManager();

        $em->getRepository(Portfolio::class)->delete($portfolio);

        return $this->redirectToRoute('portfolio_index');
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
        if ('POST' == $request->getMethod()) {
            $share = $this->getDoctrine()
                ->getRepository(Share::class)
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

        $allShares = $em->getRepository(Share::class)->findAllWithExclude($portfolioShares);

        $totalProcents = $em->getRepository(Portfolio::class)->getTotalProcents($portfolio);

        $maxProportion = 1 - $totalProcents;

        return $this->render('portfolio/add_share.html.twig', [
            'allShares'     => $allShares,
            'portfolio'     => $portfolio,
            'maxProportion' => $maxProportion
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

        if ('POST' == $request->getMethod()) {
            $portfolioShare->setProportion($request->get('proportion'));

            $em->persist($portfolioShare);
            $em->flush();

            return $this->redirectToRoute('portfolio_show', ['id' => $portfolio->getId()]);
        }

        $portfolioShares = $portfolio->getPortfolioShares();

        $allShares = $em->getRepository(Share::class)->findAllWithExclude($portfolioShares);

        $totalProcents = $em->getRepository(Portfolio::class)->getTotalProcents($portfolio);

        $maxProportion = 1 - $totalProcents + $portfolioShare->getProportion();

        return $this->render('portfolio/edit_share.html.twig', [
            'allShares'      => $allShares,
            'portfolioShare' => $portfolioShare,
            'maxProportion'  => $maxProportion
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