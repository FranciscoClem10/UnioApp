<script>
    // Elementos de pestañas
    const friendsPanel = document.getElementById('friendsPanel');
    const requestsPanel = document.getElementById('requestsPanel');
    const rejectedPanel = document.getElementById('rejectedPanel');
    const tabFriends = document.getElementById('tabFriendsBtn');
    const tabRequests = document.getElementById('tabRequestsBtn');
    const tabRejected = document.getElementById('tabRejectedBtn');

    function activateFriends() {
        friendsPanel.classList.remove('hidden');
        requestsPanel.classList.add('hidden');
        rejectedPanel.classList.add('hidden');
        setActiveTab(tabFriends, tabRequests, tabRejected);
    }
    function activateRequests() {
        friendsPanel.classList.add('hidden');
        requestsPanel.classList.remove('hidden');
        rejectedPanel.classList.add('hidden');
        setActiveTab(tabRequests, tabFriends, tabRejected);
    }
    function activateRejected() {
        friendsPanel.classList.add('hidden');
        requestsPanel.classList.add('hidden');
        rejectedPanel.classList.remove('hidden');
        setActiveTab(tabRejected, tabFriends, tabRequests);
    }
    function setActiveTab(active, ...others) {
        active.classList.add('tab-active');
        active.classList.remove('tab-inactive');
        others.forEach(tab => {
            tab.classList.add('tab-inactive');
            tab.classList.remove('tab-active');
        });
    }
    tabFriends.addEventListener('click', activateFriends);
    tabRequests.addEventListener('click', activateRequests);
    tabRejected.addEventListener('click', activateRejected);

    // El resto de funciones (búsqueda, AJAX, modal) se mantienen igual...
    // Asegúrate de que el modal solo se abra en la pestaña amigos (opcional)
    const fab = document.getElementById('fabAddFriendGlobal');
    if (fab) {
        fab.addEventListener('click', () => {
            if (!friendsPanel.classList.contains('hidden')) {
                // solo si estamos en amigos
                document.getElementById('addFriendModal').classList.remove('opacity-0', 'invisible');
            } else {
                alert('Cambia a la pestaña "Amigos" para agregar nuevas conexiones.');
            }
        });
    }

    // Buscador de usuarios (AJAX)
    const searchInputModal = document.getElementById('searchUserInput');
    const searchResultsDiv = document.getElementById('searchResults');
    const searchEmptyDiv = document.getElementById('searchEmpty');
    let searchTimeout = null;

    function renderSearchResults(users) {
        if (users.length === 0) {
            searchResultsDiv.classList.add('hidden');
            searchEmptyDiv.classList.remove('hidden');
            return;
        }
        searchEmptyDiv.classList.add('hidden');
        searchResultsDiv.classList.remove('hidden');
        searchResultsDiv.innerHTML = users.map(user => `
            <div class="flex items-center justify-between p-3 bg-surface-container-low rounded-xl border border-primary/20">
                <div class="flex gap-3 items-center">
                    ${user.foto_base64 ? `<img src="${user.foto_base64}" class="w-10 h-10 rounded-full object-cover">` : '<div class="w-10 h-10 bg-gray-200 rounded-full flex items-center justify-center text-xs">Sin foto</div>'}
                    <div>
                        <p class="font-semibold text-sm">${escapeHtml(user.nombre_completo)}</p>
                        <p class="text-xs text-outline">${escapeHtml(user.email)}</p>
                    </div>
                </div>
                <form action="<?= BASE_URL ?>?c=amigos&a=enviarSolicitud" method="POST" class="send-request-form">
                    <input type="hidden" name="id" value="${user.id_usuario}">
                    <button type="submit" class="bg-primary/10 text-primary hover:bg-primary/25 text-sm font-bold py-1.5 px-3 rounded-xl transition">
                        Enviar solicitud
                    </button>
                </form>
            </div>
        `).join('');
        
        document.querySelectorAll('.send-request-form').forEach(form => {
            form.removeEventListener('submit', handleFormSubmit);
            form.addEventListener('submit', handleFormSubmit);
        });
    }

    function escapeHtml(str) {
        return str.replace(/[&<>]/g, function(m) {
            if (m === '&') return '&amp;';
            if (m === '<') return '&lt;';
            if (m === '>') return '&gt;';
            return m;
        });
    }

    async function searchUsers(term) {
        if (term.length < 2) {
            searchResultsDiv.classList.add('hidden');
            searchEmptyDiv.classList.add('hidden');
            return;
        }
        try {
            const response = await fetch(`<?= BASE_URL ?>?c=amigos&a=buscarUsuariosJson&q=${encodeURIComponent(term)}`);
            if (!response.ok) throw new Error(`HTTP ${response.status}`);
            const text = await response.text();
            let data;
            try {
                data = JSON.parse(text);
            } catch(e) {
                console.error('Respuesta no JSON:', text);
                throw new Error('Respuesta inválida del servidor');
            }
            renderSearchResults(data);
        } catch (error) {
            console.error(error);
            searchResultsDiv.classList.add('hidden');
            searchEmptyDiv.classList.remove('hidden');
            searchEmptyDiv.innerText = 'Error al buscar. Intenta de nuevo.';
        }
    }

    searchInputModal?.addEventListener('input', (e) => {
        clearTimeout(searchTimeout);
        const term = e.target.value.trim();
        searchTimeout = setTimeout(() => searchUsers(term), 400);
    });

    function handleFormSubmit(event) {
        event.preventDefault();
        const form = event.target;
        const formData = new FormData(form);
        fetch(form.action, { method: 'POST', body: formData })
            .then(response => {
                if (response.redirected) window.location.href = response.url;
                else window.location.reload();
            })
            .catch(err => {
                console.error(err);
                window.location.reload();
            });
    }

    document.querySelectorAll('.send-request-form').forEach(form => {
        form.addEventListener('submit', handleFormSubmit);
    });

    // ========== CIERRE DEL MODAL ==========
    const modal = document.getElementById('addFriendModal');
    const closeModal = () => {
        if (modal) {
            modal.classList.add('opacity-0', 'invisible');
            // Opcional: limpiar búsqueda al cerrar
            const searchInput = document.getElementById('searchUserInput');
            if (searchInput) searchInput.value = '';
            const resultsDiv = document.getElementById('searchResults');
            const emptyDiv = document.getElementById('searchEmpty');
            if (resultsDiv) resultsDiv.classList.add('hidden');
            if (emptyDiv) emptyDiv.classList.add('hidden');
        }
    };

    // Botón X
    const closeBtn = document.getElementById('closeModalBtn');
    if (closeBtn) closeBtn.addEventListener('click', closeModal);

    // Botón Cancelar / Cerrar
    const cancelBtn = document.getElementById('modalCancelBtn');
    if (cancelBtn) cancelBtn.addEventListener('click', closeModal);

    // Clic fuera del contenido (backdrop)
    if (modal) {
        modal.addEventListener('click', (e) => {
            // Solo cierra si se hizo click directamente en el fondo (el div con clase fixed inset-0)
            if (e.target === modal) closeModal();
        });
    }
</script>