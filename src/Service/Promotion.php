<?php

namespace App\Service;

use App\Entity\Product;
use Doctrine\ORM\EntityManagerInterface;

class Promotion
{
    public function flash(int $pourcentage, int $initialPrice): int
    {
        return ($initialPrice - $initialPrice*($pourcentage/100));
    }
}
