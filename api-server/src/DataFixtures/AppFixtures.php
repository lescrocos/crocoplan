<?php

namespace App\DataFixtures;

use App\Entity\Enfant;
use App\Entity\Famille;
use App\Entity\Garde;
use App\Entity\JourPlanning;
use App\Entity\MoisPlanning;
use DateInterval;
use DatePeriod;
use DateTime;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Faker;

class AppFixtures extends Fixture
{

    private $faker;


    public function __construct()
    {
        $this->faker = Faker\Factory::create('fr_FR');
    }


    public function load(ObjectManager $manager)
    {
        $faker = $this->faker;

        $familles = [];

        // Création de 20 familles
        for ($i = 0; $i < 20; $i++) {
            $famille = new Famille();
            $famille->nom = $faker->firstName();
            $famille->dateEntree = $faker->dateTimeBetween('-1 year', 'now');
            $famille->dateSortie = $faker->dateTimeBetween('now', '+1 year');
            $manager->persist($famille);
            $familles[] = $famille;

            // Création des enfants
            $nbEnfants = $i % 20 == 0 ? 2 : 1; // 1 famille sur 20 aura 2 enfants
            for ($j = 0; $j < $nbEnfants; $j++) {
                $enfant = new Enfant();
                if ($j == 0) {
                    // Premier enfant
                    $enfant->dateEntree = $famille->dateEntree;
                    $enfant->dateSortie = $famille->dateSortie;
                    $enfant->nom = $famille->nom;
                } else {
                    // Tous les autres enfants
                    $enfant->dateEntree = $faker->dateTimeBetween($famille->dateEntree, 'now');
                    $enfant->dateSortie = $faker->dateTimeBetween('now', $famille->dateSortie);
                    $enfant->nom = $faker->firstName();
                }
                $enfant->famille = $famille;

                $manager->persist($enfant);
            }
        }

        // Création des mois planning
        $moisPrecedent = new DateTime(date("Y-m-d", strtotime("last month")));
        for ($i = 0; $i < 12; $i++) {
            $moisPlanning = new MoisPlanning();
            $moisPlanning->dateDebut = new DateTime(date("Y-m-d", strtotime("first monday of {$moisPrecedent->format('Y-m')}")));
            $moisPlanning->dateFin = new DateTime(date("Y-m-d", strtotime("friday this week", strtotime("last monday of {$moisPrecedent->format('Y-m')}"))));
            $manager->persist($moisPlanning);

            // Création des jours planning de ce mois
            foreach(new DatePeriod($moisPlanning->dateDebut, DateInterval::createFromDateString('1 day'), $moisPlanning->dateFin) as $date) {
                if (($date->format('w') + 6) % 7 < 5) { // le format 'w' renvoit le numéro du jour dans la semaine avec 0 pour dimanche, on décale donc de 6 jours (en modulo 7 = semaine) pour que le samedi et dimanche se retrouve à 5 et 6
                    $jourPlanning = new JourPlanning();
                    $jourPlanning->date = $date;
                    $jourPlanning->moisPlanning = $moisPlanning;

                    $manager->persist($jourPlanning);

                    // Création d'une garde d'ouverture, de 2 du matin et de 2 de l'après-midi, avec une chance sur 2 qu'elle soit pourvu par une famille
                    $this->creerGarde($jourPlanning, '8:00', '9:30', $familles, $manager);
                    for ($j = 0; $j < 2; $j++) {
                        $this->creerGarde($jourPlanning, '9:30', '13:30', $familles, $manager);
                    }
                    for ($j = 0; $j < 2; $j++) {
                        $this->creerGarde($jourPlanning, '13:30', '18:30', $familles, $manager);
                    }
                }
            }

            $moisPrecedent = new DateTime(date("Y-m-d", strtotime("next month", $moisPrecedent->getTimestamp())));
        }

        $manager->flush();
    }

    /**
     * @param Garde $garde
     * @param Famille[] $familles
     */
    public function assigneFamille(Garde $garde, array $familles) {
        if ($this->faker->boolean(66)) { // 2 chances sur 3
            $garde->famille = $familles[rand(0, sizeof($familles) - 1)];
        }
    }

    private function nouvelleGarde(JourPlanning $jourPlanning)
    {
        $garde = new Garde();
        $garde->jourPlanning = $jourPlanning;
        return $garde;
    }

    private function creerGarde(JourPlanning $jourPlanning, string $heureArrivee, string $heureDepart, array $familles, ObjectManager $manager)
    {
        $faker = $this->faker;

        $garde = new Garde();
        $garde->jourPlanning = $jourPlanning;
        $garde->heureArrivee = new DateTime($heureArrivee);
        $garde->heureDepart = new DateTime($heureDepart);
        // assignation d'une famille aléatoirement
        if ($faker->boolean(66)) { // 2 chances sur 3
            $garde->famille = $familles[rand(0, sizeof($familles) - 1)];
        }
        // commentaire aléatoire
        if ($faker->boolean(10)) { // 1 chances sur 10
            $garde->commentaire = $faker->realText(40);
        }
        $manager->persist($garde);
    }
}
