<script src="https://www.gstatic.com/firebasejs/9.22.2/firebase-app-compat.js"></script>
<script src="https://www.gstatic.com/firebasejs/9.22.2/firebase-auth-compat.js"></script>
<script src="https://www.gstatic.com/firebasejs/9.22.2/firebase-firestore-compat.js"></script>
<script src="https://www.gstatic.com/firebasejs/9.22.2/firebase-storage-compat.js"></script>
<!-- 👆 THIS WAS MISSING -->

<script>
const firebaseConfig = {
  apiKey: "AIzaSyAeIUnO8hDJ19YnruXWNZSW7iCsO9XggPg",
  authDomain: "lalago-1d721.firebaseapp.com",
  projectId: "lalago-1d721",
  storageBucket: "lalago-1d721.appspot.com",
  messagingSenderId: "687925021779",
  appId: "1:687925021779:web:3ab7482380d692f0790aa6",
  measurementId: "G-BGJEW8T98H"
};

firebase.initializeApp(firebaseConfig);

const auth = firebase.auth();
const db = firebase.firestore();
const storage = firebase.storage(); // ✅ REQUIRED
</script>
