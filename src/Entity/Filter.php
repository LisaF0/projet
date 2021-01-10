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

    /**
     * @var null|integer
     */
    public $max;

    /**
     * @var null|integer
     */
    public $min;

    // /**
    //  * @var integer
    //  */
    // public $page = 1;
}


?>