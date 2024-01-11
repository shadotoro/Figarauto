document.addEventListener('DOMContentLoaded', function () {
    var loginForm = document.getElementById('loginForm');

    if (loginForm) {
        loginForm.addEventListener('submit', function (e) {
            e.preventDefault();
            var formData = new FormData(this);

            fetch('index.php?controller=auth&action=login', {
                method: 'POST',
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    window.location.href = 'index.php?controller=user&action=dashboard';
                } else {
                    alert('Identifiants incorrects!');
                }
            })
            .catch(error => {
                console.error('Erreur:', error);
            });
        });
    }
});