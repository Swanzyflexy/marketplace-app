<?php

// src/DataFixtures/AppFixtures.php

namespace App\DataFixtures;

use App\Entity\AdCategory;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class CategoryFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        // Define the category and subcategory list
        $categories = [
            'Jobs and Careers' => ['Job Opportunities', 'Resumes'],
            'Real Estate' => ['Apartments for Rent', 'Houses for Sale', 'Commercial Real Estate', 'Vacation Rentals'],
            'Vehicles' => ['Cars', 'Motorcycles', 'Boats', 'RVs', 'Trucks'],
            'For Sale' => ['Furniture', 'Household Items', 'Electronics', 'Clothing and Accessories', 'Appliances', 'Sporting Goods', 'Others'],
            'Services' => ['Home Services', 'Professional Services', 'Event Services', 'Beauty and Spa'],
            'Community' => ['Events', 'Classes', 'Lost and Found', 'Volunteers'],
            'Personals' => ['Men Seeking Women', 'Women Seeking Men', 'Casual Encounters'],
            'Pets' => ['Dogs', 'Cats', 'Birds', 'Pet Services'],
            'Matrimonial' => ['Brides', 'Grooms', 'Matrimonial Services'],
            'Education' => ['Tutoring', 'Classes', 'Training'],
            'Health and Wellness' => ['Fitness', 'Health Products', 'Alternative Medicine'],
            'Buy and Sell' => ['Antiques', 'Collectibles', 'Books', 'Musical Instruments'],
            'Business and Industrial' => ['Business for Sale', 'Equipment and Tools', 'Office Space'],
            'Technology' => ['Computers', 'Software', 'Gadgets'],
            'Travel' => ['Hotels', 'Vacation Packages', 'Travel Services'],
            'Announcements' => ['General Announcements', 'Legal Notices'],
            'Hobbies and Leisure' => ['Arts and Crafts', 'Gaming', 'Photography'],
            'Free Stuff' => ['Giveaways', 'Free Services'],
        ];

        // Load the categories and subcategories into the database
        foreach ($categories as $categoryName => $subcategories) {
            $category = new AdCategory();
            $category->setName($categoryName);
            $category->setParentCategory(null);
            $category->setCreatedAt(new \DateTimeImmutable());
            $manager->persist($category);

            foreach ($subcategories as $subcategoryName) {
                $subcategory = new AdCategory();
                $subcategory->setName($subcategoryName);
                $subcategory->setParentCategory($category);
                $subcategory->setCreatedAt(new \DateTimeImmutable());
                $manager->persist($subcategory);
            }
        }

        $manager->flush();
    }
}