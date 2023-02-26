// Écouter le clic sur le bouton d'ouverture de la modal

//quand la page est load
window.addEventListener('load', function () {

        document.getElementById('open-modal')?.addEventListener('click', function () {
            // Afficher la modal
            document.getElementById('modal').classList.remove('hidden');

            // Récupérer l'ID du groupe de paris
            const bettingGroupId =this.dataset.bettinggroupid;

            if(bettingGroupId == null){
                console.log("bettingGroupId is null");
                return;
            }

            // Faire une requête HTTP pour récupérer du texte
            const xhr = new XMLHttpRequest();
            xhr.open('GET', `/betting-group/${bettingGroupId}/join-link`);
            xhr.onload = function () {

                const {code, url} = JSON.parse(xhr.responseText);

                document.getElementById('modal-text-link').value = url;
                document.getElementById('modal-text-code').value = code;

            };

            xhr.onerror = function () {
                console.log('Request failed');
            }
            xhr.send();

        });

        // Écouter le clic sur le bouton de fermeture de la modal
        document.getElementById('close-group-modal')?.addEventListener('click', function () {
            // Masquer la modal
            document.getElementById('modal').classList.add('hidden');
        });

    });
