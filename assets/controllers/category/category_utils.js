/**
 * Utilitaires partagés pour les controllers de catégories
 */

/**
 * Crée une icône SVG de crayon
 * @returns {SVGElement}
 */
export function createPencilIcon() {
    const pencilIcon = document.createElementNS('http://www.w3.org/2000/svg', 'svg');
    pencilIcon.setAttribute('width', '16');
    pencilIcon.setAttribute('height', '16');
    pencilIcon.setAttribute('viewBox', '0 0 24 24');
    pencilIcon.setAttribute('fill', 'none');
    pencilIcon.setAttribute('stroke', 'currentColor');
    pencilIcon.setAttribute('stroke-width', '2');

    const pencilPath = document.createElementNS('http://www.w3.org/2000/svg', 'path');
    pencilPath.setAttribute('d', 'M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7');
    pencilIcon.appendChild(pencilPath);

    const pencilPath2 = document.createElementNS('http://www.w3.org/2000/svg', 'path');
    pencilPath2.setAttribute('d', 'M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z');
    pencilIcon.appendChild(pencilPath2);

    return pencilIcon;
}

/**
 * Crée un bouton de modification avec une icône crayon
 * @returns {HTMLButtonElement}
 */
export function createEditButton() {
    const editButton = document.createElement('button');
    editButton.type = 'button';
    editButton.setAttribute('aria-label', 'Modifier la catégorie');
    editButton.appendChild(createPencilIcon());
    return editButton;
}

/**
 * Crée un bouton de suppression
 * @returns {HTMLButtonElement}
 */
export function createDeleteButton() {
    const deleteButton = document.createElement('button');
    deleteButton.type = 'button';
    deleteButton.setAttribute('aria-label', 'Supprimer la catégorie');
    deleteButton.textContent = 'Supprimer';
    return deleteButton;
}

/**
 * Normalise la valeur de couleur (ajoute # si absent)
 * @param {string} color - Couleur en hexa (avec ou sans #)
 * @returns {string} - Couleur normalisée avec #
 */
export function normalizeColor(color) {
    return color.startsWith('#') ? color : `#${color}`;
}

/**
 * Crée un élément <li> représentant une catégorie
 * @param {{id: number, name: string, color?: string, icone?: string}} category - Données de la catégorie
 * @returns {HTMLLIElement}
 */
export function createCategoryListItem(category) {
    const li = document.createElement('li');

    // Stocker les infos de la catégorie dans des data-attributes
    if (category.id !== undefined && category.id !== null) {
        li.dataset.categoryId = String(category.id);
    }
    if (category.name) {
        li.dataset.categoryName = category.name;
    }
    if (category.color) {
        li.dataset.categoryColor = category.color;
    }
    if (category.icone) {
        li.dataset.categoryIcone = category.icone;
    }

    // Nom de la catégorie
    const nameSpan = document.createElement('span');
    nameSpan.textContent = category.name ?? `Catégorie #${category.id}`;
    li.appendChild(nameSpan);

    // Carré de couleur (la couleur en BDD est en hexa sans le #)
    if (category.color) {
        const colorSquare = document.createElement('span');
        const colorValue = normalizeColor(category.color);
        colorSquare.dataset.color = colorValue;
        colorSquare.style.backgroundColor = colorValue;
        li.appendChild(colorSquare);
    }

    // Bouton modifier avec icône crayon
    const editButton = createEditButton();
    // Action Stimulus pour déclencher l'édition dans le controller category
    editButton.dataset.action = 'click->category#edit';
    li.appendChild(editButton);

    return li;
}
