import { Controller } from '@hotwired/stimulus';
import { createCategoryListItem } from './category_utils.js';

/**
 * Controller Stimulus pour charger et afficher les catégories
 * sur un élément <ul>.
 *
 * Utilisation (exemple Twig) :
 *
 * <ul data-controller="category">
 * </ul>
 *
 * Optionnel : surcharger l'URL avec une value
 *
 * <ul
 *     data-controller="category"
 *     data-category-url-value="/mon/autre/url"
 * ></ul>
 */
export default class extends Controller {
    static values = {
        url: {
            type: String,
            default: '/api/category',
        },
    };

    connect() {
        this.loadCategories();
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
        // On vide d'abord la liste
        this.element.innerHTML = '';

        const li = document.createElement('li');
        li.textContent = 'Chargement des catégories...';
        li.setAttribute('aria-busy', 'true');
        this.element.appendChild(li);
    }

    showError() {
        this.element.innerHTML = '';

        const li = document.createElement('li');
        li.textContent = 'Impossible de charger les catégories.';
        this.element.appendChild(li);
    }

    /**
     * @param {Array<{id: number, name: string, color?: string, icone?: string}>} categories
     */
    renderCategories(categories) {
        this.element.innerHTML = '';

        if (categories.length === 0) {
            const li = document.createElement('li');
            li.textContent = 'Aucune catégorie disponible.';
            this.element.appendChild(li);
            return;
        }

        for (const category of categories) {
            const li = createCategoryListItem(category);
            this.element.appendChild(li);
        }
    }

    /**
     * Action appelée lorsqu'on clique sur le bouton crayon d'une catégorie.
     * Déclenche un événement personnalisé pour pré-remplir le formulaire.
     */
    edit(event) {
        event.preventDefault();

        const button = event.currentTarget;
        const li = button.closest('li');
        if (!li) {
            return;
        }

        const detail = {
            id: li.dataset.categoryId ?? null,
            name: li.dataset.categoryName ?? '',
            color: li.dataset.categoryColor ?? '',
            icone: li.dataset.categoryIcone ?? '',
        };

        const editEvent = new CustomEvent('category:edit', {
            detail,
            bubbles: true,
        });

        li.dispatchEvent(editEvent);
    }

}
