<?php

namespace App\Controller;

use App\Entity\Discount;
use App\Entity\Product;
use App\Service\Promotion;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class DiscountController extends AbstractController
{
    /**
     * @Route("/discount/{id}", name="discount_index")
     */
    public function index(Product $product, EntityManagerInterface $entityManager, Promotion $promotion): Response
    {
        if(isset($_POST['submit'])){
            $discount = new Discount();

            // Set discount from and to dates
            $discount->setDiscountFrom($_POST['from']);
            $discount->setDiscountTo($_POST['to']);

            // Calculate new price
            $pourcentage = $_POST['submit'];

            $discount->setDiscountPrice($promotion->flash($pourcentage,$product->getPrice()));

            // Persist discount in db
            $entityManager->flush();
            $entityManager->persist($discount);

            // Link discount to product
            $product->setDiscount($discount);
            $entityManager->flush();
            $entityManager->persist($product);

        }

        return $this->redirectToRoute('home');
    }
}
