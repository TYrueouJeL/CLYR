<?php

namespace App\DataFixtures;

use App\Entity\Chaussette;
use App\Entity\Couleur;
use App\Entity\Type;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Faker\Factory;

class AppFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        // On initialise Faker en français
        $faker = Factory::create('fr_FR');

        // --- 1. GÉNÉRATION DES COULEURS ---
        $nomsCouleurs = ['Noir', 'Blanc', 'Gris', 'Rouge', 'Bleu Marine', 'Vert Sapin', 'Jaune', 'Rose', 'Rayé', 'À pois'];
        $couleursEntities = [];

        foreach ($nomsCouleurs as $nom) {
            $couleur = new Couleur();
            $couleur->setNomCouleur($nom);
            $manager->persist($couleur);
            $couleursEntities[] = $couleur; // On stocke l'objet pour plus tard
        }

        // --- 2. GÉNÉRATION DES TYPES ---
        $nomsTypes = ['Socquettes', 'Sport', 'Classiques', 'Hautes', 'Mi-bas'];
        $typesEntities = [];

        foreach ($nomsTypes as $nom) {
            $type = new Type();
            $type->setNomType($nom);
            $manager->persist($type);
            $typesEntities[] = $type;
        }

        // --- 3. GÉNÉRATION DES 50 CHAUSSETTES ---
        $tailles = ['35-38', '39-42', '43-46'];

        for ($i = 1; $i <= 50; $i++) {
            $chaussette = new Chaussette();

            // Attribution aléatoire depuis nos listes fixes
            $chaussette->setCouleur($faker->randomElement($couleursEntities));
            $chaussette->setType($faker->randomElement($typesEntities));

            $chaussette->setNomChaussette('Modèle ' . $faker->colorName() . ' ' . $faker->lastName());
            $chaussette->setTaille($faker->randomElement($tailles));

            $chaussette->setCouple($faker->boolean(20));

            $chaussette->setCommentaire($faker->sentence(10));
            $chaussette->setDateCreation($faker->dateTimeBetween('-1 year', 'now'));

            $manager->persist($chaussette);
        }

        // On envoie tout en base de données
        $manager->flush();
    }
}
