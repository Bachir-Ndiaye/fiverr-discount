<?php

namespace App\Controller;

use App\Repository\ProductRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class HomeController extends AbstractController
{
    /**
     * @Route("/", name="home")
     */
    public function index(Request $request, ProductRepository $productRepository): Response
    {
        //dd($productRepository->findAll());
        return $this->render('home/index.html.twig', [
            'products' => $productRepository->findAll()
        ]);
    }
}
