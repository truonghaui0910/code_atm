   function handleAvatarError(img) {
        // Set fallback image
//        img.src = '/images/avatar/default.svg';
        img.src = '/images/default-avatar.png';
        const id = img.dataset.id;
        // Create and append sync button if it doesn't exist
        if (!img.nextElementSibling.classList.contains('avatar-sync-btn')) {
            const syncBtn = document.createElement('button');
            syncBtn.className = 'avatar-sync-btn';
            syncBtn.innerHTML = '<i class="fas fa-sync-alt"></i>';
//            syncBtn.setAttribute('data-toggle', 'tooltip');
//            syncBtn.setAttribute('title', 'Resync avatar');
            syncBtn.onclick = function(e) {
                e.preventDefault();
                this.innerHTML = '<i class="fas fa-spinner fa-spin"></i>';
                
                if (this.dataset.syncing === 'true') {
                    return;
                }
                this.dataset.syncing = 'true';
                $.ajax({
                    type: "GET",
                    url: "syncAvatar",
                    data: {
                        "id": id
                    },
                    dataType: 'json',
                    success: function(data) {
                        syncBtn.innerHTML = '<i class="fas fa-sync-alt"></i>';
                        console.log(data);
                        if (data.status == "error") {
                            this.dataset.syncing = 'false';
                        } else {
                            img.src = data.thumb;                    
                            syncBtn.remove();
            
                        }


                    },
                    error: function(data) {
                        console.log('Error:', data);


                    }
                });
            };
            
            img.parentNode.appendChild(syncBtn);
        }
    }
    
    
