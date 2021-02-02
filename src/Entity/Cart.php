<?php

namespace App\Entity;

use App\Entity\Product;

class Cart
{
    private $incart = [];

    public function getQuantity()
    {
        //on vérifie qu'il y ai qqchose dans le panier
        if($this->incart){
            // Pour chaque ligne du panier, on fait la somme des quantités
            return array_reduce($this->incart, function($total, $item){
                return $total+= $item['quantity'];
            });
        }
    }

    public function getQuantityOfProduct(Product $product)
    {
        return $this->incart[$product->getId()]["quantity"];
    }
    
    public function getFullCart()
    {
        return $this->incart;
    }

    public function add(Product $product, $qtt)
    {
        // si le produit n'existe pas dans le panier
        if(!array_key_exists($product->getId(), $this->incart)){
           $this->incart[$product->getId()] = [
               "product" => $product,
               "quantity" => $qtt
           ];
        }
        else{
            $this->incart[$product->getId()]["quantity"]+=$qtt; 
        } 
    }  

    public function remove(Product $product)
    {

        if($this->incart[$product->getId()]){
            unset($this->incart[$product->getId()]);
        }
    }

    public function getTotal($incart) : float 
    {
        $total = 0;
        foreach($incart as $cartLine){
            $totalCartLine = $cartLine['product']->getUnitPrice() * $cartLine['quantity'];
            $total += $totalCartLine;
        }
        return $total;
    }

    public function clear()
    {
        $this->incart = [];
    }


    /**
     * Get the value of incart
     */ 
    public function getIncart()
    {
        return $this->incart;
    }
}