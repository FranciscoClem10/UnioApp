<!DOCTYPE html>
<html class="light" lang="es">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0, viewport-fit=cover">
  <title>Unio · Amigos y Solicitudes</title>
  <!-- Tailwind + Google Fonts + Material Icons -->
  <script src="https://cdn.tailwindcss.com?plugins=forms,container-queries"></script>
  <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
  <link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:wght,FILL@100..700,0..1&display=swap" rel="stylesheet">
  <style>
    body { font-family: 'Plus Jakarta Sans', sans-serif; background-color: #f6f6f6; }
    .glass-nav {
      background: rgba(255, 255, 255, 0.75);
      backdrop-filter: blur(20px);
      border-bottom: 1px solid rgba(0,0,0,0.03);
    }
    .glass-bottom {
      background: rgba(255, 255, 255, 0.82);
      backdrop-filter: blur(18px);
      border-top: 1px solid rgba(0,0,0,0.05);
    }
    .card-hover {
      transition: all 0.25s ease;
    }
    .card-hover:hover {
      transform: translateY(-3px);
      box-shadow: 0 20px 30px -12px rgba(90,42,247,0.12);
    }
    .tab-active {
      border-bottom: 3px solid #5a2af7;
      color: #2d2f2f;
      font-weight: 700;
    }
    .tab-inactive {
      border-bottom: 3px solid transparent;
      color: #767777;
      font-weight: 500;
    }
    .tab-transition {
      transition: all 0.2s cubic-bezier(0.4, 0, 0.2, 1);
    }
    ::-webkit-scrollbar { width: 5px; }
    ::-webkit-scrollbar-track { background: #e2e4e4; border-radius: 10px; }
    ::-webkit-scrollbar-thumb { background: #b9b9b9; border-radius: 10px; }
  </style>
  <script>
    tailwind.config = {
      darkMode: "class",
      theme: {
        extend: {
          colors: {
            "surface": "#f6f6f6",
            "primary": "#5a2af7",
            "on-surface": "#2d2f2f",
            "surface-container-lowest": "#ffffff",
            "surface-container-low": "#f0f1f1",
            "surface-container": "#e7e8e8",
            "outline": "#767777",
            "error": "#b41340",
            "on-primary": "#f6f0ff",
            "primary-container": "#a292ff",
          },
          fontFamily: { sans: ["Plus Jakarta Sans"] },
          borderRadius: { xl: "0.75rem", "2xl": "1rem" }
        }
      }
    }
  </script>
</head>
<body class="bg-surface text-on-surface antialiased pb-24">

  <!-- ========== TOP NAV (header.php estructural) ========== -->
  <header class="fixed top-0 w-full z-50 glass-nav shadow-sm h-16 px-5 md:px-8 flex justify-between items-center">
    <div class="flex items-center gap-2">
      <img src="https://lh3.googleusercontent.com/aida/ADBb0uiSgBpCkSb6haruCTUz8zCgJIxItt-tJ63UHrAlg-Aw3Oh2ab6q-GWHOl_Ie_RqVQwlMOuI7n_S4N6o87HolUrEnNjsJ7eaD30217seFbp1j0lcfxbaia4MtHfbEHzMYySZdLuQa3i8TUZnShd9U2MDUzibCk3AWI9yKUScyhN5JPmVlzERCa1PPjqsDqxzN9bULfrBMooFr98lzx9bV0bl7oUNP_dFzHBPvRq8i5o4UjokX77PxeMK9BnPJQ4KHkI3edHQoUGQ" alt="Unio" class="h-8 md:h-10 w-auto object-contain">
    </div>
    <div class="flex items-center gap-2">
      <a href="#" class="p-2 rounded-full text-slate-600 hover:bg-slate-100 transition-all active:scale-95">
        <span class="material-symbols-outlined">person</span>
      </a>
    </div>
  </header>

  <main class="pt-28 pb-8 px-5 md:px-8">
    <div class="max-w-7xl mx-auto">
      <!-- Cabecera -->
      <div class="mb-6">
        <h1 class="text-3xl md:text-4xl font-extrabold tracking-tight">Red de amigos</h1>
        <p class="text-on-surface-variant text-base mt-1">Gestiona tus conexiones y solicitudes pendientes</p>
      </div>

      <!-- ========== TABS: Amigos | Solicitudes ========== -->
      <div class="flex border-b border-surface-container mb-6 gap-6">
        <button id="tabFriendsBtn" class="tab-active tab-transition py-3 px-1 text-base font-semibold flex items-center gap-2">
          <span class="material-symbols-outlined text-xl">group</span> Amigos
          <span id="friendsTabCount" class="ml-1 bg-surface-variant text-on-surface-variant text-xs font-bold px-2 py-0.5 rounded-full">0</span>
        </button>
        <button id="tabRequestsBtn" class="tab-inactive tab-transition py-3 px-1 text-base font-semibold flex items-center gap-2">
          <span class="material-symbols-outlined text-xl">person_add</span> Solicitudes
          <span id="requestsTabCount" class="ml-1 bg-primary/10 text-primary text-xs font-bold px-2 py-0.5 rounded-full">0</span>
        </button>
      </div>

      <!-- Panel de AMIGOS (con buscador dedicado) -->
      <div id="friendsPanel" class="transition-all duration-300">
        <!-- Buscador solo para amigos -->
        <div class="relative w-full md:w-96 mb-6">
          <span class="material-symbols-outlined absolute left-4 top-1/2 -translate-y-1/2 text-outline text-xl">search</span>
          <input type="text" id="searchFriendsInput" placeholder="Buscar amigos por nombre..." class="w-full pl-11 pr-4 py-3 bg-surface-container-lowest rounded-2xl border border-surface-container shadow-sm focus:ring-2 focus:ring-primary/30 outline-none transition">
        </div>
        
        <div id="friendsGrid" class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 gap-6">
          <!-- cards amigos dinámicos -->
        </div>
        <div id="emptyFriendsMessage" class="hidden text-center py-12 bg-surface-container-lowest rounded-2xl border border-dashed border-outline-variant mt-4">
          <span class="material-symbols-outlined text-5xl text-outline">people_outline</span>
          <p class="mt-2 text-on-surface-variant">No tienes amigos agregados. Usa el botón + para conectar.</p>
        </div>
      </div>

      <!-- Panel de SOLICITUDES (sin buscador, solo solicitudes entrantes) -->
      <div id="requestsPanel" class="hidden transition-all duration-300">
        <div class="mb-4 flex justify-between items-center">
          <p class="text-sm text-on-surface-variant">Estas son las solicitudes de amistad pendientes</p>
          <button id="refreshRequestsBtn" class="text-primary text-sm font-medium flex items-center gap-1 hover:bg-primary/5 px-3 py-1 rounded-full transition"><span class="material-symbols-outlined text-base">refresh</span> Actualizar</button>
        </div>
        <div id="pendingRequestsGrid" class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 gap-6">
          <!-- solicitudes dinámicas -->
        </div>
      </div>

      <!-- Botón flotante para agregar amigo (aparece en ambos paneles) -->
      <div class="fixed bottom-28 right-6 z-30">
        <button id="fabAddFriendGlobal" class="bg-primary text-white rounded-full p-4 shadow-xl shadow-primary/40 hover:bg-primary/90 transition-all active:scale-95 flex items-center justify-center">
          <span class="material-symbols-outlined text-2xl">person_add</span>
        </button>
      </div>
    </div>
  </main>

  <!-- MODAL ENVIAR SOLICITUD (sugerencias de usuarios) -->
  <div id="addFriendModal" class="fixed inset-0 bg-black/40 backdrop-blur-sm flex items-center justify-center z-50 opacity-0 invisible transition-all duration-200 px-4">
    <div class="bg-surface-container-lowest rounded-2xl w-full max-w-lg shadow-2xl transform scale-95 transition-all duration-200 overflow-hidden">
      <div class="p-5 border-b border-surface-container flex justify-between items-center">
        <h3 class="text-xl font-bold">Conectar con nueva persona</h3>
        <button id="closeModalBtn" class="text-outline hover:text-on-surface p-1 rounded-full"><span class="material-symbols-outlined">close</span></button>
      </div>
      <div class="p-5 max-h-[60vh] overflow-y-auto">
        <p class="text-sm text-on-surface-variant mb-4">Envía una solicitud a profesionales que aún no están en tu red.</p>
        <div id="suggestedUsersList" class="space-y-3">
          <!-- lista sugerencias -->
        </div>
        <div id="noSuggestionsMsg" class="hidden text-center py-8 text-outline">✨ No hay más personas por sugerir</div>
      </div>
      <div class="p-4 bg-surface-container-low border-t border-surface-container text-right">
        <button id="modalCancelBtn" class="px-4 py-2 text-outline font-medium rounded-xl hover:bg-surface-container transition">Cerrar</button>
      </div>
    </div>
  </div>

  <!-- BOTTOM NAV (componente bottom-nav.php) -->
  <nav class="fixed bottom-0 left-0 w-full glass-bottom z-40 py-2 px-5 flex justify-around items-center border-t border-white/30 shadow-[0_-4px_12px_-6px_rgba(0,0,0,0.05)]">
    <a href="#" class="bottom-nav-item active flex flex-col items-center text-primary" data-tab="friends">
      <span class="material-symbols-outlined text-2xl">group</span>
      <span class="text-[11px] font-medium">Amigos</span>
    </a>
    <a href="#" class="bottom-nav-item flex flex-col items-center text-outline" data-tab="requests">
      <span class="material-symbols-outlined text-2xl">person_add</span>
      <span class="text-[11px] font-medium">Solicitudes</span>
      <span id="bottomRequestBadge" class="hidden absolute -top-1 right-1/4 bg-error text-white text-[10px] font-bold rounded-full px-1 min-w-[18px] h-[18px] leading-[18px] text-center">0</span>
    </a>
    <button id="fabFromBottom" class="bg-primary text-white rounded-full p-3 -mt-6 shadow-xl shadow-primary/30">
      <span class="material-symbols-outlined text-2xl">add</span>
    </button>
    <a href="#" class="bottom-nav-item flex flex-col items-center text-outline" data-tab="profile">
      <span class="material-symbols-outlined text-2xl">account_circle</span>
      <span class="text-[11px] font-medium">Perfil</span>
    </a>
  </nav>

  <script>
    // ---------- DATOS INICIALES ----------
    let friends = [
      { id: "f1", name: "Lucía Fernández", title: "Diseñadora UX/UI", event: "Neon Pulse", avatar: "https://randomuser.me/api/portraits/women/68.jpg" },
      { id: "f2", name: "Mateo Jiménez", title: "Full Stack Developer", event: "Tech Summit 2024", avatar: "https://randomuser.me/api/portraits/men/32.jpg" },
      { id: "f3", name: "Elena Sorolla", title: "Content Creator", event: "Eco-Design Meetup", avatar: "https://randomuser.me/api/portraits/women/44.jpg" }
    ];
    
    let pendingRequests = [
      { id: "req1", requesterId: "u101", name: "Carlos Ruiz", title: "Product Manager", event: "Startup Nights", avatar: "https://randomuser.me/api/portraits/men/45.jpg" },
      { id: "req2", requesterId: "u102", name: "Sofía Méndez", title: "Data Scientist", event: "AI Conference 2025", avatar: "https://randomuser.me/api/portraits/women/22.jpg" }
    ];

    // Base de usuarios sugeridos (para enviar solicitud)
    let allSuggestions = [
      { id: "sug1", name: "Valentina Herrera", title: "Marketing Director", event: "Growth Summit", avatar: "https://randomuser.me/api/portraits/women/90.jpg" },
      { id: "sug2", name: "Diego Ramírez", title: "Frontend Architect", event: "React Conf", avatar: "https://randomuser.me/api/portraits/men/15.jpg" },
      { id: "sug3", name: "Camila Rojas", title: "Community Manager", event: "Creator Fest", avatar: "https://randomuser.me/api/portraits/women/33.jpg" },
      { id: "sug4", name: "Tomás Vera", title: "DevOps Engineer", event: "Cloud Expo", avatar: "https://randomuser.me/api/portraits/men/72.jpg" },
      { id: "sug5", name: "Isabel Córdoba", title: "Ilustradora", event: "Arte Urbano", avatar: "https://randomuser.me/api/portraits/women/55.jpg" }
    ];

    // Helper: sugerencias filtradas (excluir amigos y solicitudes pendientes)
    function getFilteredSuggestions() {
      const friendIds = new Set(friends.map(f => f.id));
      const pendingIds = new Set(pendingRequests.map(p => p.requesterId));
      return allSuggestions.filter(sug => !friendIds.has(sug.id) && !pendingIds.has(sug.id));
    }

    // Renderizar solicitudes pendientes
    function renderPendingRequests() {
      const container = document.getElementById('pendingRequestsGrid');
      const tabCount = document.getElementById('requestsTabCount');
      const bottomBadge = document.getElementById('bottomRequestBadge');
      
      if (pendingRequests.length === 0) {
        container.innerHTML = `<div class="col-span-full text-center py-12 bg-surface-container-lowest rounded-2xl text-outline-variant"><span class="material-symbols-outlined text-5xl">inbox</span><p class="mt-2">No hay solicitudes pendientes</p><p class="text-sm mt-1">Cuando alguien te envíe una solicitud, aparecerá aquí.</p></div>`;
        tabCount.innerText = '0';
        if(bottomBadge) bottomBadge.classList.add('hidden');
        return;
      }
      tabCount.innerText = pendingRequests.length;
      if(bottomBadge) { bottomBadge.innerText = pendingRequests.length; bottomBadge.classList.remove('hidden'); }
      
      container.innerHTML = pendingRequests.map(req => `
        <div class="bg-surface-container-lowest rounded-xl p-5 shadow-md border border-surface-container/80 card-hover" data-request-id="${req.id}">
          <div class="flex gap-4 items-start">
            <img src="${req.avatar}" class="w-14 h-14 rounded-2xl object-cover shadow-sm" alt="${req.name}">
            <div class="flex-1">
              <h3 class="font-bold text-lg text-on-surface">${req.name}</h3>
              <p class="text-xs font-semibold text-primary uppercase tracking-wide">${req.title}</p>
              <div class="flex items-center gap-1 mt-1.5 text-xs bg-primary/5 px-2 py-0.5 rounded-full w-fit">
                <span class="material-symbols-outlined text-primary text-[13px]">event</span>
                <span class="text-on-primary-container text-[11px] font-medium">${req.event}</span>
              </div>
            </div>
          </div>
          <div class="flex gap-3 mt-5">
            <button class="accept-request-btn flex-1 bg-primary/10 text-primary font-bold py-2.5 rounded-xl hover:bg-primary/20 transition flex items-center justify-center gap-1"><span class="material-symbols-outlined text-lg">check_circle</span> Aceptar</button>
            <button class="decline-request-btn flex-1 bg-error/5 text-error font-medium py-2.5 rounded-xl hover:bg-error/10 transition flex items-center justify-center gap-1"><span class="material-symbols-outlined text-lg">cancel</span> Rechazar</button>
          </div>
        </div>
      `).join('');
    }

    // Renderizar lista de amigos (con filtro de búsqueda)
    let currentSearchTerm = '';
    function renderFriends() {
      const container = document.getElementById('friendsGrid');
      const tabCount = document.getElementById('friendsTabCount');
      let filtered = [...friends];
      if (currentSearchTerm.trim() !== '') {
        filtered = filtered.filter(f => f.name.toLowerCase().includes(currentSearchTerm.toLowerCase()));
      }
      tabCount.innerText = friends.length;
      const emptyDiv = document.getElementById('emptyFriendsMessage');
      if (filtered.length === 0) {
        container.innerHTML = '';
        emptyDiv.classList.remove('hidden');
        return;
      }
      emptyDiv.classList.add('hidden');
      container.innerHTML = filtered.map(friend => `
        <div class="bg-surface-container-lowest rounded-xl p-5 shadow-md border border-surface-container/60 transition card-hover relative" data-friend-id="${friend.id}">
          <div class="flex gap-4 items-start">
            <div class="w-14 h-14 rounded-2xl overflow-hidden shadow-sm"><img class="w-full h-full object-cover" src="${friend.avatar}" alt="${friend.name}"></div>
            <div class="flex-1">
              <h3 class="font-bold text-lg text-on-surface">${friend.name}</h3>
              <p class="text-xs font-semibold text-primary uppercase tracking-wide">${friend.title}</p>
              <div class="flex items-center gap-1 mt-1.5 bg-primary/5 px-2 py-0.5 rounded-full w-fit">
                <span class="material-symbols-outlined text-primary text-[13px]">event_available</span>
                <span class="text-[10px] font-bold text-on-primary-container">Conectaron en: ${friend.event}</span>
              </div>
            </div>
            <button class="remove-friend-btn text-outline hover:text-error transition-colors p-1" data-id="${friend.id}">
              <span class="material-symbols-outlined text-2xl">person_remove</span>
            </button>
          </div>
          <div class="mt-2 pt-1 flex justify-end">
            <span class="text-xs text-on-surface-variant flex items-center gap-1"><span class="material-symbols-outlined text-sm">visibility</span> Ver perfil</span>
          </div>
        </div>
      `).join('');
    }

    // Refrescar toda la UI (amigos + solicitudes + contadores)
    function refreshAll() {
      renderFriends();
      renderPendingRequests();
      updateTabCounters();
    }

    function updateTabCounters() {
      document.getElementById('friendsTabCount').innerText = friends.length;
      document.getElementById('requestsTabCount').innerText = pendingRequests.length;
      const bottomBadge = document.getElementById('bottomRequestBadge');
      if (pendingRequests.length > 0) {
        bottomBadge.innerText = pendingRequests.length;
        bottomBadge.classList.remove('hidden');
      } else bottomBadge.classList.add('hidden');
    }

    // Eliminar amigo
    function removeFriendById(friendId) {
      friends = friends.filter(f => f.id !== friendId);
      refreshAll();
      showToast("Amigo eliminado", "info");
    }

    // Aceptar solicitud
    function acceptRequest(requestId) {
      const idx = pendingRequests.findIndex(r => r.id === requestId);
      if (idx === -1) return;
      const req = pendingRequests[idx];
      const newFriend = {
        id: req.requesterId,
        name: req.name,
        title: req.title,
        event: req.event,
        avatar: req.avatar
      };
      if (!friends.some(f => f.id === newFriend.id)) {
        friends.push(newFriend);
      }
      pendingRequests.splice(idx, 1);
      refreshAll();
      showToast(`${newFriend.name} ahora es tu amigo ✨`, "success");
    }

    // Rechazar solicitud
    function declineRequest(requestId) {
      const idx = pendingRequests.findIndex(r => r.id === requestId);
      if (idx !== -1) {
        const name = pendingRequests[idx].name;
        pendingRequests.splice(idx, 1);
        refreshAll();
        showToast(`Solicitud de ${name} rechazada`, "neutral");
      }
    }

    // Enviar una nueva solicitud (desde el modal de sugerencias)
    function sendFriendRequest(suggestedUser) {
      if (pendingRequests.some(r => r.requesterId === suggestedUser.id)) {
        showToast("Ya enviaste solicitud a esta persona", "warning");
        return false;
      }
      if (friends.some(f => f.id === suggestedUser.id)) {
        showToast("Esta persona ya es tu amiga", "warning");
        return false;
      }
      const newRequest = {
        id: `req_${Date.now()}_${suggestedUser.id}`,
        requesterId: suggestedUser.id,
        name: suggestedUser.name,
        title: suggestedUser.title,
        event: suggestedUser.event,
        avatar: suggestedUser.avatar
      };
      pendingRequests.push(newRequest);
      refreshAll();
      showToast(`Solicitud enviada a ${suggestedUser.name}`, "success");
      return true;
    }

    // Modal: construir lista de sugerencias y manejar envío
    function renderSuggestionsModal() {
      const suggestions = getFilteredSuggestions();
      const listContainer = document.getElementById('suggestedUsersList');
      const noMsg = document.getElementById('noSuggestionsMsg');
      if (suggestions.length === 0) {
        listContainer.innerHTML = '';
        noMsg.classList.remove('hidden');
        return;
      }
      noMsg.classList.add('hidden');
      listContainer.innerHTML = suggestions.map(user => `
        <div class="flex items-center justify-between p-3 bg-surface-container-low rounded-xl border border-transparent hover:border-primary/30 transition">
          <div class="flex gap-3 items-center">
            <img src="${user.avatar}" class="w-10 h-10 rounded-full object-cover">
            <div>
              <p class="font-semibold text-sm">${user.name}</p>
              <p class="text-xs text-outline">${user.title}</p>
              <p class="text-[10px] text-primary/70">${user.event}</p>
            </div>
          </div>
          <button class="send-suggestion-btn bg-primary/10 text-primary hover:bg-primary/25 text-sm font-bold py-1.5 px-3 rounded-xl transition" data-user-id="${user.id}" data-user-name="${user.name}" data-user-title="${user.title}" data-user-event="${user.event}" data-user-avatar="${user.avatar}">
            Enviar solicitud
          </button>
        </div>
      `).join('');
      document.querySelectorAll('.send-suggestion-btn').forEach(btn => {
        btn.removeEventListener('click', suggestionClickHandler);
        btn.addEventListener('click', suggestionClickHandler);
      });
    }
    
    function suggestionClickHandler(e) {
      const btn = e.currentTarget;
      const userObj = {
        id: btn.dataset.userId,
        name: btn.dataset.userName,
        title: btn.dataset.userTitle,
        event: btn.dataset.userEvent,
        avatar: btn.dataset.userAvatar
      };
      sendFriendRequest(userObj);
      renderSuggestionsModal();  // refrescar lista del modal
    }

    // Toast simple
    function showToast(msg, type) {
      let toast = document.getElementById('dynamicToast');
      if (!toast) {
        toast = document.createElement('div');
        toast.id = 'dynamicToast';
        toast.className = 'fixed bottom-28 left-1/2 -translate-x-1/2 bg-gray-900 text-white px-5 py-2.5 rounded-full shadow-lg text-sm font-medium z-[100] transition-all duration-300 opacity-0 pointer-events-none';
        document.body.appendChild(toast);
      }
      toast.innerText = msg;
      toast.style.opacity = '1';
      setTimeout(() => { toast.style.opacity = '0'; }, 2500);
    }

    // ----- TABS -----
    const friendsPanel = document.getElementById('friendsPanel');
    const requestsPanel = document.getElementById('requestsPanel');
    const tabFriendsBtn = document.getElementById('tabFriendsBtn');
    const tabRequestsBtn = document.getElementById('tabRequestsBtn');
    
    function activateFriendsTab() {
      friendsPanel.classList.remove('hidden');
      requestsPanel.classList.add('hidden');
      tabFriendsBtn.classList.add('tab-active');
      tabFriendsBtn.classList.remove('tab-inactive');
      tabRequestsBtn.classList.add('tab-inactive');
      tabRequestsBtn.classList.remove('tab-active');
      // Actualizar badge bottom nav visual
      document.querySelectorAll('.bottom-nav-item').forEach(el => {
        if(el.dataset.tab === 'friends') {
          el.classList.add('text-primary');
          el.classList.remove('text-outline');
        } else if(el.dataset.tab === 'requests') {
          el.classList.add('text-outline');
          el.classList.remove('text-primary');
        }
      });
    }
    
    function activateRequestsTab() {
      friendsPanel.classList.add('hidden');
      requestsPanel.classList.remove('hidden');
      tabRequestsBtn.classList.add('tab-active');
      tabRequestsBtn.classList.remove('tab-inactive');
      tabFriendsBtn.classList.add('tab-inactive');
      tabFriendsBtn.classList.remove('tab-active');
      document.querySelectorAll('.bottom-nav-item').forEach(el => {
        if(el.dataset.tab === 'requests') {
          el.classList.add('text-primary');
          el.classList.remove('text-outline');
        } else if(el.dataset.tab === 'friends') {
          el.classList.add('text-outline');
          el.classList.remove('text-primary');
        }
      });
    }
    
    tabFriendsBtn.addEventListener('click', activateFriendsTab);
    tabRequestsBtn.addEventListener('click', activateRequestsTab);
    
    // bottom nav clicks
    document.querySelectorAll('.bottom-nav-item').forEach(item => {
      item.addEventListener('click', (e) => {
        e.preventDefault();
        const tab = item.dataset.tab;
        if (tab === 'friends') activateFriendsTab();
        else if (tab === 'requests') activateRequestsTab();
        else if (tab === 'profile') showToast("Perfil - próximamente", "info");
      });
    });
    
    // Botones flotantes para agregar amigo
    const openModal = () => {
      renderSuggestionsModal();
      const modal = document.getElementById('addFriendModal');
      modal.classList.remove('opacity-0', 'invisible');
      modal.classList.add('opacity-100', 'visible');
    };
    document.getElementById('fabAddFriendGlobal')?.addEventListener('click', openModal);
    document.getElementById('fabFromBottom')?.addEventListener('click', openModal);
    document.getElementById('closeModalBtn')?.addEventListener('click', () => {
      document.getElementById('addFriendModal').classList.add('opacity-0', 'invisible');
    });
    document.getElementById('modalCancelBtn')?.addEventListener('click', () => {
      document.getElementById('addFriendModal').classList.add('opacity-0', 'invisible');
    });
    document.getElementById('addFriendModal')?.addEventListener('click', (e) => {
      if(e.target === document.getElementById('addFriendModal')) {
        document.getElementById('addFriendModal').classList.add('opacity-0', 'invisible');
      }
    });
    
    // Buscador de amigos
    const searchInput = document.getElementById('searchFriendsInput');
    searchInput?.addEventListener('input', (e) => {
      currentSearchTerm = e.target.value;
      renderFriends();
    });
    
    // Delegación de eventos (eliminar amigo, aceptar/rechazar solicitud)
    document.addEventListener('click', (e) => {
      const removeBtn = e.target.closest('.remove-friend-btn');
      if (removeBtn) {
        e.preventDefault();
        const fid = removeBtn.dataset.id;
        if (fid && confirm("¿Eliminar esta conexión?")) removeFriendById(fid);
        return;
      }
      const acceptBtn = e.target.closest('.accept-request-btn');
      if (acceptBtn) {
        const card = acceptBtn.closest('[data-request-id]');
        if (card) acceptRequest(card.dataset.requestId);
        return;
      }
      const declineBtn = e.target.closest('.decline-request-btn');
      if (declineBtn) {
        const card = declineBtn.closest('[data-request-id]');
        if (card) declineRequest(card.dataset.requestId);
        return;
      }
    });
    
    document.getElementById('refreshRequestsBtn')?.addEventListener('click', () => {
      renderPendingRequests();
      showToast("Solicitudes actualizadas", "info");
    });
    
    // Inicialización
    refreshAll();
    activateFriendsTab();  // por defecto mostrar amigos
  </script>
</body>
</html>