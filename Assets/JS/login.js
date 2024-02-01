document.addEventListener('DOMContentLoaded', function () {
    var loginForm = document.getElementById('loginForm');

    if (loginForm) {
        loginForm.addEventListener('submit', async function (e) {
            e.preventDefault();
            var formData = new FormData(this);

            try {
                const response = await fetch('index.php?controller=auth&action=login', {
                method: 'POST',
                body: formData
            });

            if (!response.ok) {
                throw new Error('HTTP error! status: ${response.status}');
            }
            const data = await response.json();

            if (data.success) {
                window.location.href = 'index.php?controller=user&action=dashboard';
            } else {
                alert('Identifiants incorrects!');
            }
        } catch (error) {
            console.error('Erreur:', error);
        }
    });
}
});