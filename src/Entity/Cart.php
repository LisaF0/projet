<?php

namespace App\Entity;

use App\Entity\Product;

class Cart
{
    private $incart = [];

    /**
     * Méthode permettant de récupérer la quantité d'un produit dans le panier
     */
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

    /**
     * Méthode permettant de retourner la quantité d'un certain produit
     */
    public function getQuantityOfProduct(Product $product)
    {
        return $this->incart[$product->getId()]["quantity"];
    }
    
    /**
     * Méthode permettant de récupérer les données du panier
     */
    public function getFullCart()
    {
        return $this->incart;
    }

    /**
     * Méthode permettant d'ajouter un produit eu panier
     */
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

    /**
     * Méthode permettant de retirer un produit du panier
     */
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

    /**
     * Méthode permettant de vider le panier
     */
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