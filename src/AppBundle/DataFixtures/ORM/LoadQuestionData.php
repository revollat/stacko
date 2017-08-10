<?php
namespace AppBundle\DataFixtures\ORM;

use AppBundle\Entity\Question;
use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;

use Faker;

class LoadQuestionData extends AbstractFixture implements OrderedFixtureInterface
{
    public function load(ObjectManager $manager)
    {
        
        $faker = Faker\Factory::create('fr_FR');
        
        for($i=1;$i<=100;$i++){

            $question = new Question();
            $question->setIntitule($faker->sentence($nbWords = 7, $variableNbWords = true));
            $question->setBody($faker->text());

            $this->addReference('question' . $i , $question);
             
            $manager->persist($question);
        }
            
        $manager->flush();
    }
    
    public function getOrder()
    {
        return 10;
    }
    
}