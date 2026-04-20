    <script>
        (function() {
            const searchInput = document.getElementById('searchConversacion');
            if (!searchInput) return;
            
            const conversationItems = document.querySelectorAll('.conversation-item');
            
            function filterConversations() {
                const searchTerm = searchInput.value.toLowerCase().trim();
                
                conversationItems.forEach(item => {
                    const nameElement = item.querySelector('h3');
                    const messageElement = item.querySelector('p.text-xs');
                    const name = nameElement ? nameElement.innerText.toLowerCase() : '';
                    const message = messageElement ? messageElement.innerText.toLowerCase() : '';
                    
                    const matches = searchTerm === '' || name.includes(searchTerm) || message.includes(searchTerm);
                    item.style.display = matches ? 'flex' : 'none';
                });
            }
            
            searchInput.addEventListener('input', filterConversations);
        })();
    </script>