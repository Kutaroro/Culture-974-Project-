import { Controller } from '@hotwired/stimulus';
import { createCategoryListItem } from './category_utils.js';

/**
 * Controller Stimulus pour gérer le formulaire de création de catégorie.
 * Ajoute dynamiquement la nouvelle catégorie à la liste sans recharger la page.
 *
 * Utilisation (exemple Twig) :
 *
 * <form
 *     data-controller="category-create-form"
 *     data-action="submit->category-create-form#submit"
 * >
 *     <input type="text" name="name" required>
 *     <input type="text" name="color" required>
 *     <input type="text" name="icone" required>
 *     <button type="submit">Créer</button>
 * </form>
 *
 * <ul
 *     data-controller="category"
 *     data-category-create-form-target="list"
 * >
 * </ul>
 *
 * Note: La liste <ul> doit avoir l'attribut data-category-create-form-target="list"
 * pour que le controller puisse y ajouter la nouvelle catégorie.
 */
export default class extends Controller {
    static values = {
        url: {
            type: String,
            default: '/api/category',
        },
        mode: {
            type: String,
            default: 'create', // 'create' ou 'edit'
        },
        categoryId: Number,
    };
    static targets = ['list', 'title', 'submitButton', 'deleteButton', 'deleteModal', 'deleteCategoryName'];

    async submit(event) {
        event.preventDefault();

        const formData = new FormData(this.element);
        const name = formData.get('name');
        const color = formData.get('color');
        const icone = formData.get('icone');

        if (!name || !color || !icone) {
            this.showError('Tous les champs sont obligatoires');
            return;
        }

        try {
            this.setLoading(true);
            const data = {
                name: name.trim(),
                color: color.trim(),
                icone: icone.trim(),
            };

            // Création ou édition ?
            let url = this.urlValue;
            let method = 'PUT';

            if (this.hasCategoryIdValue && this.modeValue === 'edit') {
                url = `${this.urlValue}/${this.categoryIdValue}`;
                method = 'PATCH';
            }

            const response = await fetch(url, {
                method,
                headers: {
                    'Content-Type': 'application/json',
                    'Accept': 'application/json',
                },
                body: JSON.stringify(data),
            });

            if (!response.ok) {
                const errorData = await response.json().catch(() => ({}));
                throw new Error(errorData.error || `Erreur HTTP ${response.status}`);
            }

            const category = await response.json();

            if (this.modeValue === 'edit') {
                // Mettre à jour localement la ligne modifiée dans la liste
                this.updateCategoryInList(category);
            } else {
                // Ajouter la nouvelle catégorie à la liste
                this.addCategoryToList(category);
            }

            // Réinitialiser le formulaire et repasser en mode création
            this.element.reset();
            this.clearError();
            this.modeValue = 'create';
            if (this.hasCategoryIdValue) {
                this.categoryIdValue = null;
            }
            this.updateFormMode();

        } catch (error) {
            console.error('Erreur lors de la création de la catégorie :', error);
            this.showError(error.message || 'Impossible de créer la catégorie');
        } finally {
            this.setLoading(false);
        }
    }

    getListElement() {
        // Essayer d'abord avec le target Stimulus
        if (this.hasListTarget) {
            return this.listTarget;
        }
        
        // Fallback : chercher manuellement l'élément avec le target
        const targetElement = document.querySelector('[data-category-create-form-target="list"]');
        if (targetElement) {
            return targetElement;
        }
        
        // Dernier fallback : chercher l'élément ul avec le controller category
        const categoryList = document.querySelector('ul[data-controller*="category"]');
        if (categoryList) {
            return categoryList;
        }
        
        return null;
    }

    addCategoryToList(category) {
        const list = this.getListElement();

        if (!list) {
            console.error('Impossible de trouver la liste pour ajouter la catégorie');
            return;
        }

        // Si la liste contient le message "Aucune catégorie disponible", on le retire
        if (list.children.length === 1) {
            const firstChild = list.children[0];
            if (firstChild.textContent && firstChild.textContent.includes('Aucune catégorie disponible')) {
                list.innerHTML = '';
            }
        }

        // Retirer aussi le message de chargement s'il est présent
        if (list.children.length === 1) {
            const firstChild = list.children[0];
            if (firstChild.textContent && firstChild.textContent.includes('Chargement')) {
                list.innerHTML = '';
            }
        }

        // Créer le <li> avec la même structure que le controller category
        const li = createCategoryListItem(category);
        list.appendChild(li);
    }

    updateCategoryInList(category) {
        const list = this.getListElement();

        if (!list) {
            console.error('Impossible de trouver la liste pour mettre à jour la catégorie');
            return;
        }

        // Trouver le <li> correspondant à la catégorie modifiée
        const categoryId = String(category.id);
        const existingLi = list.querySelector(`li[data-category-id="${categoryId}"]`);

        if (existingLi) {
            // Remplacer le contenu du <li> existant avec les nouvelles données
            const newLi = createCategoryListItem(category);
            existingLi.replaceWith(newLi);
        } else {
            console.warn(`Catégorie avec l'ID ${categoryId} non trouvée dans la liste`);
        }
    }

    setLoading(loading) {
        const submitButton = this.element.querySelector('button[type="submit"]');
        if (submitButton) {
            submitButton.disabled = loading;
            if (loading) {
                submitButton.setAttribute('aria-busy', 'true');
            } else {
                submitButton.removeAttribute('aria-busy');
            }
        }
    }

    showError(message) {
        this.clearError();

        const errorElement = document.createElement('div');
        errorElement.setAttribute('role', 'alert');
        errorElement.textContent = message;
        this.element.appendChild(errorElement);
    }

    clearError() {
        const errorElement = this.element.querySelector('[role="alert"]');
        if (errorElement) {
            errorElement.remove();
        }
    }

    /**
     * Charge une catégorie dans le formulaire pour édition.
     * Événement écouté : category:edit (détails envoyés par le controller category)
     */
    loadCategory(event) {
        const detail = event.detail || {};
        if (!detail.id) {
            return;
        }

        const nameInput = this.element.querySelector('input[name="name"]');
        const colorInput = this.element.querySelector('input[name="color"]');
        const iconeInput = this.element.querySelector('input[name="icone"]');

        if (nameInput) {
            nameInput.value = detail.name ?? '';
        }
        if (colorInput) {
            // On enlève éventuellement le # pour rester cohérent avec le placeholder
            const color = detail.color ?? '';
            colorInput.value = color.startsWith('#') ? color.slice(1) : color;
        }
        if (iconeInput) {
            iconeInput.value = detail.icone ?? '';
        }

        this.categoryIdValue = Number(detail.id);
        this.modeValue = 'edit';
        this.updateFormMode();
    }

    /**
     * Met à jour le texte du bouton et du titre selon le mode (création/édition)
     */
    updateFormMode() {
        const isEditMode = this.modeValue === 'edit';

        // Mettre à jour le titre
        if (this.hasTitleTarget) {
            this.titleTarget.textContent = isEditMode ? 'Modifier la catégorie' : 'Créer une catégorie';
        }

        // Mettre à jour le bouton
        if (this.hasSubmitButtonTarget) {
            this.submitButtonTarget.textContent = isEditMode ? 'Mettre à jour la catégorie' : 'Créer la catégorie';
        }

        // Afficher/masquer le bouton supprimer
        if (this.hasDeleteButtonTarget) {
            this.deleteButtonTarget.style.display = isEditMode ? 'inline-block' : 'none';
        }
    }

    /**
     * Affiche le modal de confirmation de suppression
     */
    confirmDelete(event) {
        event.preventDefault();

        if (!this.hasCategoryIdValue || this.modeValue !== 'edit') {
            console.warn('Impossible d\'afficher le modal : mode édition non actif ou ID manquant');
            return;
        }

        const categoryName = this.element.querySelector('input[name="name"]')?.value || 'cette catégorie';

        if (!this.hasDeleteModalTarget) {
            console.error('Modal de suppression introuvable');
            return;
        }

        // Mettre à jour le nom de la catégorie
        if (this.hasDeleteCategoryNameTarget) {
            this.deleteCategoryNameTarget.textContent = categoryName;
        }

        // Afficher le modal
        this.deleteModalTarget.classList.add('active');
    }

    /**
     * Ferme le modal de suppression sans rien faire
     */
    cancelDelete(event) {
        event.preventDefault();
        
        if (this.hasDeleteModalTarget) {
            this.deleteModalTarget.classList.remove('active');
        }
    }

    /**
     * Confirme la suppression : appel API DELETE + retrait de la ligne de la liste
     */
    async deleteConfirmed(event) {
        event.preventDefault();

        if (!this.hasCategoryIdValue || this.modeValue !== 'edit') {
            this.cancelDelete(event);
            return;
        }

        try {
            const response = await fetch(`${this.urlValue}/${this.categoryIdValue}`, {
                method: 'DELETE',
                headers: {
                    'Accept': 'application/json',
                },
            });

            if (!response.ok && response.status !== 204) {
                throw new Error(`Erreur HTTP ${response.status}`);
            }

            // Retirer la ligne correspondante de la liste
            const list = this.getListElement();
            if (list) {
                const categoryId = String(this.categoryIdValue);
                const li = list.querySelector(`li[data-category-id="${categoryId}"]`);
                if (li) {
                    li.remove();
                }
            }

            // Réinitialiser le formulaire et repasser en mode création
            this.element.reset();
            this.clearError();
            this.modeValue = 'create';
            this.categoryIdValue = null;
            this.updateFormMode();

        } catch (error) {
            console.error('Erreur lors de la suppression de la catégorie :', error);
            this.showError(error.message || 'Impossible de supprimer la catégorie');
        } finally {
            this.cancelDelete(event);
        }
    }
}
