<?php

namespace App\Controller;

use App\Entity\Discount;
use App\Entity\Product;
use App\Repository\UserRepository;
use App\Entity\User;
use App\Form\DiscountType;
use App\Form\ProductType;
use App\Repository\ProductRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Symfony\Component\HttpFoundation\Request;
use DateTime;

use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/profile", name="profile_")
 */
class ProfileController extends AbstractController
{
    /**
     * @Route("/{id}", name="index")
     */
    public function index(Request $request, User $user): Response
    {
        return $this->render('profile/index.html.twig', [
            'user' => $user
        ]);
    }

    /**
     * @Route("/{id}/products", name="product", methods={"GET"})
     */
    public function product(ProductRepository $productRepository, User $user): Response
    {
        $products = $productRepository->findBy([
            'user' => $user->getId()
        ]);

        return $this->render('profile/products.html.twig', [
            'products' => $products
        ]);
    }

    /**
     * @Route("/{userId}/product/{productId}", name="product_show", methods={"GET"})
     * @ParamConverter("product", class="App\Entity\Product", options={"mapping": {"productId": "id"}})
     * @ParamConverter("user", class="App\Entity\User", options={"mapping": {"userId": "id"}})
     */
    public function show(Product $product): Response
    {
        return $this->render('profile/show.html.twig', [
            'product' => $product,
        ]);
    }

    /**
     * @Route("/{userId}/edit/{productId}", name="product_edit", methods={"GET","POST"})
     * @ParamConverter("product", class="App\Entity\Product", options={"mapping": {"productId": "id"}})
     * @ParamConverter("user", class="App\Entity\User", options={"mapping": {"userId": "id"}})
     */
    public function edit(Request $request, Product $product, User $user): Response
    {
        $form = $this->createForm(ProductType::class, $product);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $entityManager = $this->getDoctrine()->getManager();

            $product->setUpdatedAt(new DateTime());

            $entityManager->flush();
            $entityManager->persist($product);

            $this->addFlash('success', 'Produit modifié avec succès');

            return $this->redirectToRoute('profile_product',[
                'id' => $user->getId()
            ]);
        }

        return $this->render('profile/edit.html.twig', [
            'product' => $product,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{userId}/new", name="product_new", methods={"GET","POST"})
     * @ParamConverter("user", class="App\Entity\User", options={"mapping": {"userId": "id"}})
     */
    public function new(Request $request, User $user): Response
    {
        $product = new Product();
        $form = $this->createForm(ProductType::class, $product);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager = $this->getDoctrine()->getManager();

            $product->setUser($this->getUser());
            $product->setCreatedAt(new DateTime('now'));
            //$product->setUpdatedAt(null);

            $entityManager->persist($product);
            $entityManager->flush();

            return $this->redirectToRoute('profile_product',[
                'id' => $user->getId()
            ]);
        }

        return $this->render('profile/new.html.twig', [
            'product' => $product,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{userId}/delete/{productId}", name="product_delete", methods={"POST"})
     * @ParamConverter("product", class="App\Entity\Product", options={"mapping": {"productId": "id"}})
     * @ParamConverter("user", class="App\Entity\User", options={"mapping": {"userId": "id"}})
     */
    public function delete(Request $request, Product $product, User $user): Response
    {
        if ($this->isCsrfTokenValid('delete' . $product->getId(), $request->request->get('_token'))) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->remove($product);
            $entityManager->flush();

            $this->addFlash('success', 'Produit supprimé avec succès');
        }

        return $this->redirectToRoute('profile_product',[
            'id' => $user->getId()
        ]);
    }
}
