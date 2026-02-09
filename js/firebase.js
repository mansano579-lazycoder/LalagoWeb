// Firebase SDK already loaded in HTML via CDN
const firebaseConfig = {
  apiKey: "AIzaSyAeIUnO8hDJ19YnruXWNZSW7iCsO9XggPg",
  authDomain: "lalago-1d721.firebaseapp.com",
  projectId: "lalago-1d721",
  storageBucket: "lalago-1d721.appspot.com",
  messagingSenderId: "687925021779",
  appId: "1:687925021779:web:3ab7482380d692f0790aa6",
  measurementId: "G-BGJEW8T98H"
};

// Initialize Firebase
firebase.initializeApp(firebaseConfig);

// Firestore reference
const db = firebase.firestore();
