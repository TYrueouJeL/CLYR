document.addEventListener('DOMContentLoaded', function() {
    const form = document.getElementById('searchForm');
    const zoneResultats = document.getElementById('resultats-zone');

    // Sélectionne tous les inputs visibles (texte, date, datalist) ET le select (menu déroulant)
    const formElements = form.querySelectorAll('input:not([type="hidden"]), select');

    const btnPdf = document.getElementById('btnExportPdf');

    // --- FONCTION DE RECHERCHE AJAX ---
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

    // --- ÉCOUTEUR BOUTON EXPORT PDF ---
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

            // Lance le téléchargement
            window.location.href = urlPdf + '?' + params;
        });
    }

    // --- ÉCOUTEUR SUR LES CHAMPS DU FORMULAIRE ---
    let timeout;
    formElements.forEach(element => {
        // L'événement 'input' capte la frappe au clavier, mais aussi les changements de select ou de type="date"
        element.addEventListener('input', function() {
            clearTimeout(timeout);
            timeout = setTimeout(lancerRecherche, 300); // Attend 300ms après la dernière frappe/clic
        });
    });

    // --- GESTION DE LA PAGINATION EN AJAX ---
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

            // On va chercher la nouvelle page en coulisses
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
                    document.getElementById('collection-chaussettes').scrollIntoView({ behavior: 'smooth' });
                })
                .catch(err => {
                    if (err !== 'Déconnexion détectée') console.error(err);
                });
        }
    });
});
