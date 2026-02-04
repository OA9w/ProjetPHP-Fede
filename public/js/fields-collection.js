document.addEventListener('DOMContentLoaded', () => {
    const collection = document.getElementById('fields-collection');
    if (!collection) return;

    const addBtn = document.getElementById('btn-add-field');
    let index = parseInt(collection.dataset.index || '0', 10);

    const addRemoveButton = (item) => {
        const btn = item.querySelector('.btn-remove-field');
        if (!btn) return;
        btn.addEventListener('click', () => item.remove());
    };

    // Active suppression sur les champs déjà présents
    collection.querySelectorAll('.field-item').forEach(addRemoveButton);

    addBtn?.addEventListener('click', () => {
        const prototype = collection.dataset.prototype;
        const html = prototype.replace(/__name__/g, String(index));
        index++;
        collection.dataset.index = String(index);

        const wrapper = document.createElement('div');
        wrapper.classList.add('field-item');
        wrapper.innerHTML = html;

        // Ajoute un bouton supprimer (car le prototype ne l'a pas)
        const removeBtn = document.createElement('button');
        removeBtn.type = 'button';
        removeBtn.className = 'btn-remove-field';
        removeBtn.textContent = 'Supprimer';
        wrapper.appendChild(removeBtn);

        const hr = document.createElement('hr');
        wrapper.appendChild(hr);

        collection.appendChild(wrapper);

        removeBtn.addEventListener('click', () => wrapper.remove());
    });
});
