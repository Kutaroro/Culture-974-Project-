# Culture-974-Project-
PROJET GROUPE : Agenda d'Événements

- [Ajouter et exécuter les fixtures](#ajouter-et-exécuter-les-fixtures)

## Ajouter et exécuter les fixtures

Les fixtures permettent de remplir la base de données avec des données de test (catégories, événements et inscriptions) pour le projet.

1. **Installer le bundle de fixtures (si ce n’est pas déjà fait)**  
   Dans le répertoire du projet :

   ```bash
   composer require --dev orm-fixtures
   ```

2. **Vérifier la présence du fichier de fixtures**  
   Le fichier principal de fixtures se trouve dans :
   `src/DataFixtures/AppFixtures.php`

3. **Générer / mettre à jour le schéma de la base de données**  
   Selon votre configuration, exécutez par exemple :

   ```bash
   php bin/console doctrine:database:create
   php bin/console doctrine:migrations:migrate
   ```

4. **Charger les données de fixtures**  

   ```bash
   php bin/console doctrine:fixtures:load
   ```

   Cette commande va **vider** les tables concernées puis les remplir avec :
   - 10 catégories
   - 50 événements
   - 100 inscriptions

5. **Recharger les fixtures en cas de besoin**  
   Vous pouvez relancer la commande `doctrine:fixtures:load` à tout moment pour réinitialiser les données de démonstration.
