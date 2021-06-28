<?php

namespace App\DataFixtures;

use App\Entity\Discount;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class DiscountFixtures extends Fixture
{
    const DISCOUNT_TYPE = ['flash','oneweek'];

    public function load(ObjectManager $manager)
    {
       for($i = 0; $i < sizeof(self::DISCOUNT_TYPE); $i++){

           $discount = new Discount();
           $discount->setType(self::DISCOUNT_TYPE[$i]);
           $this->addReference('promo'.$i, $discount);

           $manager->persist($discount);
       }

       $manager->flush();
    }
}