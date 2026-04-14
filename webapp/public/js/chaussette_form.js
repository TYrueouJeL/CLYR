document.addEventListener('DOMContentLoaded', function() {

    // ============================================================
    // SÉLECTEURS
    // ============================================================
    const form = document.querySelector('form');
    const flashContainer = document.getElementById('flash-messages-container'); // Assure-toi d'avoir cette div dans ton base.html.twig

    // ============================================================
    // FONCTION UTILITAIRE : AFFICHAGE D'ERREUR
    // ============================================================
    const showFlashError = (message) => {
        // Si tu n'as pas de conteneur d'erreur spécifique, on utilise une alerte classique en secours
        if (!flashContainer) {
            alert(message.replace(/<[^>]*>?/gm, '')); // Enlève les balises HTML pour l'alerte
            return;
        }

        const errorHtml = `
            <div class="alert alert-danger alert-dismissible fade show shadow-sm mt-3" role="alert">
                <i class="bi bi-exclamation-triangle-fill me-2"></i>
                ${message}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        `;
        flashContainer.innerHTML = errorHtml + flashContainer.innerHTML;
        window.scrollTo({ top: 0, behavior: 'smooth' });
    };

    // ============================================================
    // VALIDATION GLOBALE DU FORMULAIRE (CHAMPS REQUIS)
    // ============================================================
    if (form) {
        // On désactive la petite bulle d'erreur HTML5 par défaut du navigateur
        // pour utiliser notre propre système (les bordures rouges Bootstrap)
        form.setAttribute('novalidate', 'novalidate');

        const validateForm = () => {
            let hasErrors = false;
            let firstErrorField = null;

            // On cherche tous les champs qui ont l'attribut "required" dans le HTML généré par Symfony
            const requiredFields = form.querySelectorAll('input[required], select[required], textarea[required]');

            requiredFields.forEach(field => {
                // Si le champ est vide (ou ne contient que des espaces)
                if (!field.value.trim()) {
                    hasErrors = true;
                    field.classList.add('is-invalid'); // Ajoute la bordure rouge Bootstrap

                    // Dès que l'utilisateur tape quelque chose, on enlève le rouge
                    field.addEventListener('input', function() {
                        if(this.value.trim()) this.classList.remove('is-invalid');
                    });

                    if (!firstErrorField) firstErrorField = field;
                }
            });

            if (hasErrors) {
                showFlashError("<strong>Formulaire incomplet.</strong><br>Veuillez remplir les champs obligatoires encadrés en rouge.");
                if (firstErrorField) {
                    firstErrorField.scrollIntoView({behavior: 'smooth', block: 'center'});
                    firstErrorField.focus();
                }
                return false; // Bloque l'envoi
            }
            return true; // Autorise l'envoi
        };


        // ----------------------------------------------------
        // SCÉNARIO A : Page CRÉATION (Le bouton classique type="submit")
        // ----------------------------------------------------
        // On intercepte la soumission native du formulaire
        form.addEventListener('submit', function(event) {
            // Si la modale est en train de forcer l'envoi (Page Edit), on laisse passer
            if (form.dataset.isSubmitting === 'true') return;

            // Sinon on valide le formulaire
            if (!validateForm()) {
                event.preventDefault(); // Bloque l'envoi car il y a des erreurs
            }
        });

        // ----------------------------------------------------
        // SCÉNARIO B : Page ÉDITION (Bouton jaune + Modale de confirmation)
        // ----------------------------------------------------
        const btnPreSubmit = document.getElementById('btn-pre-submit');
        const btnHiddenTrigger = document.getElementById('btn-hidden-modal-trigger');
        const btnFinalSubmit = document.getElementById('btn-final-submit');

        // 1. Clic sur le bouton Jaune "Enregistrer les modifications"
        if (btnPreSubmit && btnHiddenTrigger) {
            btnPreSubmit.addEventListener('click', function(event) {
                event.preventDefault();
                // On vérifie que les champs obligatoires sont remplis AVANT d'ouvrir la fenêtre de confirmation
                if (validateForm()) {
                    btnHiddenTrigger.click(); // Tout est bon, on simule le clic qui ouvre la modale Bootstrap
                }
            });
        }

        // 2. Clic sur "Oui, enregistrer" DANS la fenêtre modale
        if (btnFinalSubmit) {
            btnFinalSubmit.addEventListener('click', function() {
                // Petit effet visuel pour faire patienter l'utilisateur pendant que le serveur travaille
                this.innerHTML = '<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> Enregistrement...';
                this.classList.add('disabled');

                // On met un marqueur caché pour dire à notre écouteur 'submit' plus haut : "Laisse passer, c'est bon !"
                form.dataset.isSubmitting = 'true';
                form.submit();
            });
        }
    }
});
