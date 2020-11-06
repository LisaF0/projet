<?php

namespace App\Entity;

use App\Entity\Product;

class Cart
{
    private $incart = [];

    public function getContent(){
        return $this->incart;
    }
    public function add(Product $product){
        if(!array_key_exists($product->getId(), $this->incart)){
           $this->incart[$product->getId()] = [
               "product" => $product,
               "quantity" => 1
           ];
        }
        else{
            $this->incart[$product->getId()]["quantity"]++;
        }
        
    }  

    public function remove(Product $product){

    if($this->incart[$product->getId()]){
        unset($this->incart[$product->getId()]);
    }
    }
    // public function getFullCart() : array {

    // }
    // public function getTotal() : float {

    // }
}