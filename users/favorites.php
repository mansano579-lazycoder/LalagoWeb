<?php
// users/favorites.php
include_once '../inc/firebase.php';
include_once '../assets/header.php';
?>

<div class="container" style="max-width: 800px; margin: 100px auto; padding: 20px;">
    <h1>My Favorites</h1>
    <div id="favoritesContent">
        <p>Loading favorites...</p>
    </div>
</div>

<script>
// Check if user is logged in
auth.onAuthStateChanged(user => {
    if (!user) {
        window.location.href = '../login.php';
        return;
    }
    
    // Fetch favorites
    db.collection('favorites').where('userId', '==', user.uid).get().then(querySnapshot => {
        const favoritesContent = document.getElementById('favoritesContent');
        
        if (querySnapshot.empty) {
            favoritesContent.innerHTML = '<p>No favorites yet.</p>';
            return;
        }
        
        let html = '<div class="favorites-grid" style="display: grid; grid-template-columns: repeat(auto-fill, minmax(200px, 1fr)); gap: 20px;">';
        querySnapshot.forEach(doc => {
            const favorite = doc.data();
            html += `
                <div style="background: white; padding: 15px; border-radius: 8px; box-shadow: 0 1px 5px rgba(0,0,0,0.1); text-align: center;">
                    <div style="width: 100px; height: 100px; background: #f0f0f0; border-radius: 50%; margin: 0 auto 10px; display: flex; align-items: center; justify-content: center;">
                        <i class="fa-solid fa-heart" style="font-size: 40px; color: #FF3B30;"></i>
                    </div>
                    <h4>${favorite.itemName || 'Favorite Item'}</h4>
                    <p>$${favorite.price || '0.00'}</p>
                </div>
            `;
        });
        html += '</div>';
        favoritesContent.innerHTML = html;
    });
});
</script>

