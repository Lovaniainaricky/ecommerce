<?php

namespace App\DataFixtures;

use App\Entity\Categories;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\String\Slugger\SluggerInterface;

class CategoriesFixtures extends Fixture
{
    private $slugger;
    private $counter;

    public function __construct(SluggerInterface $slugger) 
    {
        $this->slugger = $slugger;
    }

    public function load(ObjectManager $manager): void
    {
        $parent = $this->createCategory('Informatique',null,$manager);
        
        $this->createCategory('Ordinateur portable',$parent,$manager);
        $this->createCategory('Ecrans',$parent,$manager);
        $this->createCategory('Souris',$parent,$manager);

        $parent = $this->createCategory('Mode',null,$manager);
        
        $this->createCategory('Homme',$parent,$manager);
        $this->createCategory('Femme',$parent,$manager);
        $this->createCategory('Enfant',$parent,$manager);
        
        

        $manager->flush();
    }

    public function createCategory(string $name,Categories $parent = null,ObjectManager $manager)
    {
        $caterogy = new Categories();
        $caterogy->setName($name);
        $caterogy->setSlug($this->slugger->slug($caterogy->getName())->lower());
        $caterogy->setParent($parent);
        $manager->persist($caterogy);

        $this->addReference('cat-'.$this->counter, $caterogy);
        $this->counter++;
        
        return $caterogy;
    }
}
