<?php 
namespace App\Entity;

use App\Entity\Type;
use App\Entity\Appellation;
use App\Entity\Domain;

class Filter
{
    /**
     * @var Appellation[]
     */
    public $appellations = [];

    /**
     * @var Type[]
     */
    public $types = [];

    /**
     * @var Domain[]
     */
    public $domains = [];
}


?>