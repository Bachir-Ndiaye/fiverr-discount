<?php

namespace App\DataFixtures;

use App\Entity\Product;
use DateTimeImmutable;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Faker\Factory;

class ProductFixtures extends Fixture
{
   
    public function load(ObjectManager $manager)
    {
        $faker = Factory::create();

        for($i = 0; $i < 3 ; $i++){

            $product = new Product();
            $product->setName($faker->name());
            $product->setDescription($faker->text());
            $product->setCreatedAt(new DateTimeImmutable('2021-07-01'));
            $product->setUpdatedAt(new DateTimeImmutable('2021-07-02'));
            $product->setPrice($faker->numberBetween(10, 20));
            $product->setImage('https://i.pravatar.cc/300');
        
            $product->setDiscount($this->getReference('promo0'));

            $manager->persist($product);
        }
        $manager->flush();
    }

    public function getDependencies()
    {
        // Return all dependencies needed by a Prodcut
        return [
            UserFixtures::class,
            DiscountFixtures::class
        ];
    }
}
