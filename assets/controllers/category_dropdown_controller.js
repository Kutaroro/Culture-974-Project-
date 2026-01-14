import { Controller } from '@hotwired/stimulus';

/**
 * Controller Stimulus pour charger et afficher les catégories
 * dans un dropdown de navigation.
 *
 * Utilisation (exemple Twig) :
 *
 * <div data-controller="category-dropdown">
 *     <button data-category-dropdown-target="button">Catégories</button>
 *     <ul data-category-dropdown-target="menu" class="dropdown-menu">
 *     </ul>
 * </div>
 *
 * Optionnel : surcharger l'URL avec une value
 *
 * <div
 *     data-controller="category-dropdown"
 *     data-category-dropdown-url-value="/mon/autre/url"
 * >
 * </div>
 */
export default class extends Controller {
    static targets = ['button', 'menu'];
    static values = {
        url: {
            type: String,
            default: '/api/category',
        },
    };

    connect() {
        this.loadCategories();
        this.isOpen = false;
        this.setupToggle();
    }

    setupToggle() {
        if (this.hasButtonTarget) {
            this.buttonTarget.addEventListener('click', (e) => {
                e.stopPropagation();
                this.toggle();
            });
        }

        // Fermer le dropdown si on clique en dehors
        document.addEventListener('click', (e) => {
            if (!this.element.contains(e.target) && this.isOpen) {
                this.close();
            }
        });
    }

    toggle() {
        if (this.isOpen) {
            this.close();
        } else {
            this.open();
        }
    }

    open() {
        this.isOpen = true;
        this.element.setAttribute('aria-expanded', 'true');
        if (this.hasButtonTarget) {
            this.buttonTarget.setAttribute('aria-expanded', 'true');
        }
    }

    close() {
        this.isOpen = false;
        this.element.setAttribute('aria-expanded', 'false');
        if (this.hasButtonTarget) {
            this.buttonTarget.setAttribute('aria-expanded', 'false');
        }
    }

    async loadCategories() {
        // État de chargement
        this.showLoading();

        try {
            const response = await fetch(this.urlValue, {
                headers: {
                    'Accept': 'application/json',
                },
            });

            if (!response.ok) {
                throw new Error(`Erreur HTTP ${response.status}`);
            }

            const data = await response.json();

            if (!Array.isArray(data)) {
                throw new Error('Réponse inattendue du serveur');
            }

            this.renderCategories(data);
        } catch (error) {
            console.error('Erreur lors du chargement des catégories :', error);
            this.showError();
        }
    }

    showLoading() {
        if (!this.hasMenuTarget) {
            return;
        }

        // On vide d'abord le menu
        this.menuTarget.innerHTML = '';

        const li = document.createElement('li');
        li.className = 'dropdown-item';
        li.setAttribute('aria-busy', 'true');

        // Créer le spinner
        const spinner = document.createElement('div');
        spinner.className = 'dropdown-spinner';
        spinner.setAttribute('aria-hidden', 'true');

        // Créer le texte
        const text = document.createElement('span');
        text.textContent = 'Chargement...';

        li.appendChild(spinner);
        li.appendChild(text);
        this.menuTarget.appendChild(li);
    }

    showError() {
        if (!this.hasMenuTarget) {
            return;
        }

        this.menuTarget.innerHTML = '';

        const li = document.createElement('li');
        li.className = 'dropdown-item error';
        li.textContent = 'Impossible de charger les catégories.';
        this.menuTarget.appendChild(li);
    }

    /**
     * @param {Array<{id: number, name: string, color?: string, icone?: string}>} categories
     */
    renderCategories(categories) {
        if (!this.hasMenuTarget) {
            return;
        }

        this.menuTarget.innerHTML = '';

        if (categories.length === 0) {
            const li = document.createElement('li');
            li.className = 'dropdown-item';
            li.textContent = 'Aucune catégorie disponible.';
            this.menuTarget.appendChild(li);
            return;
        }

        for (const category of categories) {
            const li = this.createCategoryDropdownItem(category);
            this.menuTarget.appendChild(li);
        }
    }

    /**
     * Crée un élément <li> avec un lien vers les événements filtrés par catégorie
     * @param {{id: number, name: string, color?: string, icone?: string}} category
     * @returns {HTMLLIElement}
     */
    createCategoryDropdownItem(category) {
        const li = document.createElement('li');
        li.className = 'dropdown-item';

        const link = document.createElement('a');
        link.href = `/evenement?category=${category.id}`;
        link.className = 'dropdown-link';

        // Icône de la catégorie si disponible
        if (category.icone) {
            const iconSpan = document.createElement('span');
            iconSpan.className = 'category-icon';
            iconSpan.textContent = category.icone;
            link.appendChild(iconSpan);
        }

        // Nom de la catégorie
        const nameSpan = document.createElement('span');
        nameSpan.className = 'category-name';
        nameSpan.textContent = category.name ?? `Catégorie #${category.id}`;
        link.appendChild(nameSpan);

        li.appendChild(link);
        return li;
    }
}
