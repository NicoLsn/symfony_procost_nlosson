<?php

namespace App\DataFixtures;

use App\Entity\Profession;
use App\Entity\Project;
use App\Entity\User;
use App\Entity\UserProject;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\Persistence\ObjectManager;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class AppFixtures extends Fixture
{
    /**
     * @var ObjectManager
     */
    private $manager;
    private $encoder;

    public function __construct(UserPasswordEncoderInterface $encoder)
    {
        $this->encoder = $encoder;
    }

    public function load(ObjectManager $manager)
    {
        $this->manager = $manager;

        //Create data
        $this->createProfessions();
        $this->createUsers();
        $this->createProjects();
        $this->createTimes();

        $this->manager->flush();
    }

    private function createUsers(): void
    {
        //Accounts user
        for ($i = 1; $i < 7; $i++) {
            $n =rand(0,150);
            $user = (new User())
                ->setFirstName('Utilisateur' . $n)
                ->setLastName("Nom" . $n)
                ->setEmail('usager' . $n . '@gmail.com')
                ->setProfession($this->getReference('prof-' . $i))
                ->setCost(60)
                ->setDate(new \DateTime())
                ->setPassword("user")
                ->setRoles(["ROLE_USER"]);

            $encoded = $this->encoder->encodePassword($user, $user->getPassword());
            $user->setPassword($encoded);

            $this->manager->persist($user);
            $this->addReference("user-" . $i, $user);
        }

        //Account admin
        $user = (new User())
            ->setFirstName("Nicolas")
            ->setLastName("Losson")
            ->setEmail('nicolas.losson@outlook.fr')
            ->setProfession($this->getReference('prof-1'))
            ->setCost(255)
            ->setDate(new \DateTime())
            ->setPassword("nicolas")
            ->setRoles(["ROLE_ADMIN"]);
            
        $encoded = $this->encoder->encodePassword($user, $user->getPassword());
        $user->setPassword($encoded);

        $this->manager->persist($user);
        $this->addReference("user-" . $i, $user);
    }

    private function createProfessions(): void
    {
        $profs = ["Webmaster",
            "Chef de projet",
            "Développeur web back-end",
            "Développeur web frond-end",
            "Développeur mobile",
            "Développeur software",
            "Analyste développeur"];

        for ($i = 1; $i < 7; $i++) {
            $prof = (new Profession())->setName($profs[$i]);

            $this->manager->persist($prof);
            $this->addReference('prof-' . $i, $prof);
        }
    }

    private function createProjects(): void
    {
        for ($i = 1; $i < 7; $i++) {

            $n =rand(0,150);

            $project = (new Project())
                ->setName("Projet" . $n)
                ->setCreationDate(new \DateTime())
                ->setDescription("Voici la description du projet numéro " . $n)
                ->setSellingPrice($i * 120);

            $this->manager->persist($project);

            $this->addReference("project-" . $i, $project);
        }
    }

    private function createTimes(): void
    {
        for ($i = 1; $i < 7; $i++) {

            $userProject =(new UserProject())
                ->setUser($this->getReference('user-'.$i))
                ->setProject($this->getReference('project-'.$i))
                ->setAddedAt(new \DateTime())
                ->setTime($i);

            $this->manager->persist($userProject);
        }
    }
}
