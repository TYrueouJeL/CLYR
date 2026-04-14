document.addEventListener('DOMContentLoaded', function() {
    const form = document.getElementById('searchForm');
    const zoneResultats = document.getElementById('resultats-zone');
    // Sélectionne tous les inputs visibles (texte, number, checkbox...)
    const inputs = form.querySelectorAll('input:not([type="hidden"])');

    // --- GESTION DES SWITCHS ---
    const switchCorbeille = document.getElementById('switchCorbeille');
    const inputCorbeille = document.getElementById('inputCorbeille');

    const switchPretEntrant = document.getElementById('switchPretEntrant');
    const inputPretEntrant = document.getElementById('inputPretEntrant');

    const switchPretSortant = document.getElementById('switchPretSortant');
    const inputPretSortant = document.getElementById('inputPretSortant');

    const btnPdf = document.getElementById('btnExportPdf');

    // Fonction pour lancer la recherche
    function lancerRecherche() {
        const params = new URLSearchParams();
        for (const [key, value] of new FormData(form)) {
            params.append(key, value);
        }
        params.append('ajax', '1');

        // Petit effet visuel
        zoneResultats.style.opacity = '0.5';

        const url = form.dataset.url;

        fetch(url + '?' + params.toString())
            .then(response => {
                if (response.redirected && response.url.includes('/login')) {
                    window.location.href = response.url; // On force la vraie redirection
                    return Promise.reject('Déconnexion détectée'); // On annule l'injection HTML
                }
                return response.text();
            })
            .then(html => {
                zoneResultats.innerHTML = html;
                zoneResultats.style.opacity = '1';
            })
            .catch(err => {
                if (err !== 'Déconnexion détectée') console.error(err);
            });
    }

    // 1. Écouteur sur la Corbeille
    if (switchCorbeille) {
        switchCorbeille.addEventListener('change', function() {
            inputCorbeille.value = this.checked ? '1' : '0';
            lancerRecherche();
        });
    }

    // 2. Écouteur sur Prêt Entrant
    if (switchPretEntrant) {
        switchPretEntrant.addEventListener('change', function() {
            inputPretEntrant.value = this.checked ? '1' : '0';
            lancerRecherche();
        });
    }

    // 3. Écouteur sur Prêt Sortant
    if (switchPretSortant) {
        switchPretSortant.addEventListener('change', function() {
            inputPretSortant.value = this.checked ? '1' : '0';
            lancerRecherche();
        });
    }

    // 4. Écouteur sur le bouton PDF
    if (btnPdf) {
        btnPdf.addEventListener('click', function() {
            // On récupère l'URL qui a été générée par Twig et stockée dans l'attribut data-url-pdf
            const urlPdf = this.dataset.urlPdf;

            // On récupère l'état actuel de tous les filtres de recherche
            const paramsObj = new URLSearchParams();
            for (const [key, value] of new FormData(form)) {
                paramsObj.append(key, value);
            }
            const params = paramsObj.toString();

            // lance le téléchargement
            window.location.href = urlPdf + '?' + params;
        });
    }

    // Écouteur sur les champs texte et nombre (avec délai anti-spam)
    let timeout;
    inputs.forEach(input => {
        // On ignore les switchs (ils sont gérés au-dessus avec 'change')
        if(input.id === 'switchCorbeille' || input.id === 'switchPretEntrant' || input.id === 'switchPretSortant') return;

        input.addEventListener('input', function() {
            clearTimeout(timeout);
            timeout = setTimeout(lancerRecherche, 300); // Attend 300ms
        });
    });

    // GESTION DE LA PAGINATION EN AJAX
    zoneResultats.addEventListener('click', function(e) {
        // On vérifie si l'élément cliqué (ou son parent) est un lien de la pagination
        const pageLink = e.target.closest('.pagination a');

        if (pageLink) {
            e.preventDefault(); // On bloque le rechargement brutal de la page

            // On récupère l'URL du lien cliqué (qui contient déjà le bon numéro de page et tes filtres)
            let url = pageLink.href;

            // On s'assure que le paramètre ajax=1 est bien présent
            if (!url.includes('ajax=1')) {
                url += (url.includes('?') ? '&' : '?') + 'ajax=1';
            }

            // Petit effet visuel de chargement
            zoneResultats.style.opacity = '0.5';

            /// On va chercher la nouvelle page en coulisses
            fetch(url)
                .then(response => {
                    if (response.redirected && response.url.includes('/login')) {
                        window.location.href = response.url;
                        return Promise.reject('Déconnexion détectée');
                    }
                    return response.text();
                })
                .then(html => {
                    zoneResultats.innerHTML = html;
                    zoneResultats.style.opacity = '1';

                    // On remonte la page en douceur vers le haut des résultats
                    document.getElementById('collection').scrollIntoView({ behavior: 'smooth' });
                })
                .catch(err => {
                    if (err !== 'Déconnexion détectée') console.error(err);
                });
        }
    });
});
