<?php
//Composant footer réutilisable
?>
<link rel="stylesheet" href="styles2.css">
<footer>
    <div class="container">
        <div style="display: flex; flex-wrap: wrap; gap: 2rem; justify-content: space-between;">
            <div style="flex: 1 1 250px; min-width: 220px;">
                <h2>À propos de BookStore</h2>
                <p style="margin-top: 1rem;">
                    Votre destination privilégiée pour découvrir et acheter des livres dans tous les genres. Nous avons à cœur de connecter les lecteurs à leur prochain livre préféré.
                </p>
            </div>
            <div style="flex: 1 1 180px; min-width: 180px;">
                <h2>Liens utiles</h2>
                <ul>
                    <li><a href="index.php">Accueil</a></li>
                    <li><a href="catalog.php">Catalogue</a></li>
                    <li><a href="cart.php">Panier</a></li>
                    <li><a href="admin.php">Admin</a></li>
                </ul>
            </div>
            <div style="flex: 1 1 250px; min-width: 220px;">
                <h2>Contact</h2>
                <ul>
                    <li>Email : marshallironside7@gmail.com</li>
                    <li>Téléphone : +228 70 09 74 54</li>
                    <li>Adresse : Avédji-Lomé TOGO</li>
                </ul>
            </div>
        </div>
        <hr style="border: none; border-top: 1px solid #46607a; margin: 2rem 0 1rem 0;">
        <div style="text-align: center; color: #cfd8dc;">
            &copy; <?php echo date('Y'); ?> BookStore. Tous droits réservés.
        </div>
    </div>
</footer>