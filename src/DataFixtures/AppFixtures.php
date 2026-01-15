<?php

namespace App\DataFixtures;

use App\Entity\Category;
use App\Entity\Evenement;
use App\Entity\Inscription;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use DateTimeImmutable;

class AppFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        // Création de 10 catégories
        $categories = [];
        $categoryNames = [
            'Conférence', 'Formation', 'Séminaire', 'Workshop', 'Networking',
            'Exposition', 'Concert', 'Sport', 'Culture', 'Technologie'
        ];
        $categoryColors = [
            'FF5733', '33FF57', '3357FF', 'FF33F5', 'F5FF33',
            '33FFF5', 'FF8C33', '8C33FF', '33FF8C', 'FF338C'
        ];
        $categoryIcones = [
            '🎤', '📚', '💼', '🔧', '🤝',
            '🖼️', '🎵', '⚽', '🎭', '💻'
        ];

        for ($i = 0; $i < 10; $i++) {
            $category = new Category();
            $category->setName($categoryNames[$i]);
            $category->setColor($categoryColors[$i]);
            $category->setIcone($categoryIcones[$i]);
            $manager->persist($category);
            $categories[] = $category;
        }

        $manager->flush();

        // Création de 50 événements
        $evenements = [];
        $eventNames = [
            'Conférence Innovation 2024', 'Formation Symfony Avancé', 'Séminaire Management',
            'Workshop Design Thinking', 'Networking Entrepreneurs', 'Exposition Art Moderne',
            'Concert Jazz en Plein Air', 'Tournoi de Football', 'Festival de Théâtre',
            'Hackathon Tech', 'Conférence IA et Éthique', 'Formation React Native',
            'Séminaire Leadership', 'Workshop UX/UI', 'Networking Startups',
            'Exposition Photographie', 'Concert Rock Alternatif', 'Marathon de Paris',
            'Festival de Cinéma', 'Conférence Blockchain', 'Formation Docker',
            'Séminaire Marketing Digital', 'Workshop Agile', 'Networking Investisseurs',
            'Exposition Sculpture', 'Concert Classique', 'Tournoi de Tennis',
            'Festival de Musique', 'Conférence Cybersécurité', 'Formation Vue.js',
            'Séminaire RH', 'Workshop Data Science', 'Networking Freelances',
            'Exposition Peinture', 'Concert Électro', 'Trail Montagne',
            'Festival de Danse', 'Conférence Green Tech', 'Formation Angular',
            'Séminaire Finance', 'Workshop DevOps', 'Networking Designers',
            'Exposition Architecture', 'Concert Pop', 'Tournoi de Basketball',
            'Festival de Littérature', 'Conférence E-commerce', 'Formation Python',
            'Séminaire Communication', 'Workshop IA', 'Networking Développeurs'
        ];

        // Descriptions simples pour les événements (réutilisées en boucle)
        $eventDescriptions = [
            'Un événement professionnel pour échanger et apprendre.',
            'Une rencontre conviviale destinée aux passionnés du domaine.',
            'Une journée complète avec des intervenants de qualité.',
            'Un moment d\'échange, de partage et de networking.',
            'Un rendez-vous incontournable pour découvrir les nouvelles tendances.'
        ];

        // Lieux possibles
        $eventPlaces = [
            'Paris', 'Lyon', 'Marseille', 'Toulouse', 'Bordeaux',
            'Lille', 'Nantes', 'Strasbourg', 'Montpellier', 'Nice'
        ];

        // Images de démonstration (par exemple des fichiers dans /public/images)
        $eventImages = [
            'images/event1.jpg', 'images/event2.jpg', 'images/event3.jpg',
            'images/event4.jpg', 'images/event5.jpg'
        ];

        for ($i = 0; $i < 50; $i++) {
            $evenement = new Evenement();
            $evenement->setCategoryId($categories[$i % 10]);

            // Nouveau champs
            $evenement->setTitre($eventNames[$i]);
            $evenement->setDescription($eventDescriptions[$i % count($eventDescriptions)]);

            // Dates réparties sur les prochains jours
            $date = new \DateTime('+' . ($i % 30) . ' days');
            $evenement->setDate($date);

            $evenement->setLieu($eventPlaces[$i % count($eventPlaces)]);

            // Certaines images peuvent être nulles pour varier
            $image = $eventImages[$i % count($eventImages)];
            if ($i % 7 === 0) {
                $image = null;
            }
            $evenement->setImage($image);

            $manager->persist($evenement);
            $evenements[] = $evenement;
        }

        $manager->flush();

        // Création de 100 inscriptions
        $firstNames = [
            'Jean', 'Marie', 'Pierre', 'Sophie', 'Luc', 'Anne', 'Paul', 'Julie',
            'Marc', 'Claire', 'Thomas', 'Laura', 'Nicolas', 'Sarah', 'David',
            'Emma', 'Antoine', 'Léa', 'Julien', 'Camille', 'Olivier', 'Manon',
            'François', 'Chloé', 'Vincent', 'Émilie', 'Maxime', 'Marion',
            'Alexandre', 'Justine', 'Guillaume', 'Amélie', 'Romain', 'Pauline',
            'Sébastien', 'Céline', 'Jérôme', 'Audrey', 'Fabien', 'Caroline',
            'Matthieu', 'Nathalie', 'Baptiste', 'Isabelle', 'Rémi', 'Valérie',
            'Adrien', 'Stéphanie', 'Cédric', 'Nathalie', 'Florian', 'Sandrine'
        ];
        $lastNames = [
            'Martin', 'Bernard', 'Dubois', 'Thomas', 'Robert', 'Richard', 'Petit',
            'Durand', 'Leroy', 'Moreau', 'Simon', 'Laurent', 'Lefebvre', 'Michel',
            'Garcia', 'David', 'Bertrand', 'Roux', 'Vincent', 'Fournier', 'Morel',
            'Girard', 'André', 'Lefevre', 'Mercier', 'Dupont', 'Lambert', 'Bonnet',
            'François', 'Martinez', 'Legrand', 'Garnier', 'Faure', 'Rousseau',
            'Blanc', 'Guerin', 'Muller', 'Henry', 'Roussel', 'Nicolas', 'Perrin',
            'Morin', 'Mathieu', 'Clement', 'Gauthier', 'Dumont', 'Lopez', 'Fontaine',
            'Chevalier', 'Robin', 'Masson'
        ];
        $roles = [
            'Participant', 'Organisateur', 'Intervenant', 'Sponsor', 'Membre',
            'Invité', 'Bénévole', 'Partenaire', 'Visiteur', 'Média'
        ];

        $emailDomains = [
            'gmail.com', 'yahoo.fr', 'outlook.com', 'hotmail.com', 'free.fr',
            'orange.fr', 'laposte.net', 'sfr.fr', 'wanadoo.fr', 'live.fr'
        ];

        $telephones = [
            '0612345678', '0623456789', '0634567890', '0645678901', '0656789012',
            '0667890123', '0678901234', '0689012345', '0690123456', '0601234567',
            '0712345678', '0723456789', '0734567890', '0745678901', '0756789012'
        ];

        for ($i = 0; $i < 100; $i++) {
            $inscription = new Inscription();
            $firstName = $firstNames[$i % 50];
            $lastName = $lastNames[$i % 50];
            $inscription->setName($firstName . ' ' . $lastName);
            $inscription->setEmail(strtolower($firstName . '.' . $lastName . '@' . $emailDomains[$i % 10]));
            $inscription->setTelephone($telephones[$i % 15]);
            $inscription->setPlacesNumber(($i % 5) + 1);
            $inscription->setCreatedAt(new DateTimeImmutable('-' . ($i % 30) . ' days'));
            $inscription->setEventId($evenements[$i % 50]);
            $inscription->setRole($roles[$i % 10]);
            $manager->persist($inscription);
        }

        $manager->flush();
    }
}
