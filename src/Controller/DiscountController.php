<?php

namespace App\Controller;

use App\Entity\Discount;
use App\Entity\Product;
use App\Service\Promotion;
use DateTime;
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
            $discount->setDiscountFrom(new \DateTime($_POST['from']));
            $discount->setDiscountTo(new DateTime($_POST['to']));
            $discount->setType('flash');

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

            //Add flash message
            $this->addFlash('success', 'Votre promotion sur le produit '.$product->getName().' a bien été pris en compte. ');

            return $this->redirectToRoute('profile_product',[
                'id' => $product->getUser()->getId()
            ]);
        }

        return $this->redirectToRoute('home');
    }
}
