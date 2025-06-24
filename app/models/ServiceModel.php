<?php
namespace App\Models;

class ServiceModel
{
    public static function getMenu()
    {
        return [
            'Vegetable' => [
                'Mixed Vegetable in Oyster Sauce', 'Butter Vegetable', 'Buttered Chopsuey', '7 Kinds', 'Chopsuey', 'Lumpiang Sariwa', 'Lumpiang Ubod'
            ],
            'Pasta' => [
                'Crispy Canton', 'Spaghetti', 'Pancit Bihon', 'Pancit Bihon Canton', 'Carbonara', 'Pesto Pasta', 'Tuna Pasta', 'Fettuccine Alfredo'
            ],
            'Dessert' => [
                'Fruit Cocktail', 'Fruit Salad w/ Buko', 'Fruit Salad', 'Buko Salad', 'Mixed Fresh Fruit', 'Puto w/ Cheese', 'Vegetable Salad', 'Chicken Macaroni Salad', 'Potato Salad', 'Coleslaw'
            ],
            'Beverage' => [
                'Soda', 'Iced Tea', 'Four Seasons', 'Red Iced Tea', 'Bottled Water'
            ],
            'Seafoods' => [
                'Fish Fillet w/ Sweet and Sour Sauce', 'Fish Fillet w/ Tartar Sauce', 'Calamares Fritto', 'Steamed Fish in Lemon Butter Sauce', 'Extra Order = Additional', 'Seafoods Galore'
            ],
            'Beef' => [
                'Roast Beef w/ Mushroom Gravy', 'Beef Mechado', 'Beef Caldereta', 'Beef Teriyaki', 'Beef w/ Brocolli', 'Beef Alamania', 'Beef Brocolli w/ Liverspread'
            ],
            'Pork' => [
                'Roast Pork w/ Mushroom Sauce', 'Sweet & Sour Pork', 'Lechon Kawali', 'Pork Caldereta', 'Pork Barbeque', 'Pork Mechado', 'Pork Cheese', 'Pork Kebab', 'Meatballs on the Nest'
            ],
            'Chicken' => [
                'Chicken Cordon Bleu', 'Chicken Barbeque', 'Breaded Chicken Lollipop', 'Chicken Rollade', 'Fried Chicken', 'Chicken Fillet', 'Chicken Kebab', 'Chicken Adobo', 'Chicken Cheese', 'Chicken Buffalo Wing'
            ]
        ];
    }

    public static function getLocations()
    {
        return ['Manila', 'Quezon City', 'Makati', 'Pasig', 'Taguig'];
    }
} 