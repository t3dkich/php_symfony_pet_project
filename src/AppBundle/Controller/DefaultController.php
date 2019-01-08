<?php

namespace AppBundle\Controller;


use AppBundle\Service\OfferServiceInterface;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

/**
 * Class DefaultController
 * @property OfferServiceInterface offerService
 */
class DefaultController extends Controller
{

    public function __construct(OfferServiceInterface $offerService)
    {
        $this->offerService = $offerService;
    }

    /**
     * @Route("/", name="homepage")
     */
    public function indexAction()
    {

        return $this->render('default/index.html.twig', [
            'base_dir' => realpath($this->getParameter('kernel.project_dir')) . DIRECTORY_SEPARATOR,
            'offers' => $this->offerService->getOneOfEachType()
        ]);
    }

    /**
     * @Route("/api/cities.json", name="api_cities")
     */
    public function bgCities()
    {

        $response = new BinaryFileResponse('bg.json');
        $response->headers->set('Content-Type', 'application/json');

        return $response;
    }
}
