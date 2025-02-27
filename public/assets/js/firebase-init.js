// Import the functions you need from the SDKs you need
import { initializeApp } from "https://www.gstatic.com/firebasejs/10.8.0/firebase-app.js";
import { getAuth } from "https://www.gstatic.com/firebasejs/10.8.0/firebase-auth.js";
import { getDatabase } from "https://www.gstatic.com/firebasejs/10.8.0/firebase-database.js";

// Your web app's Firebase configuration
const firebaseConfig = {
    apiKey: "AIzaSyCXorxVYBpkpPAlKYMWcpcWEvkFCO6DSik",
    authDomain: "escola-vbr2t1.firebaseapp.com",
    databaseURL: "https://escola-vbr2t1-default-rtdb.firebaseio.com",
    projectId: "escola-vbr2t1",
    storageBucket: "escola-vbr2t1.firebasestorage.app",
    messagingSenderId: "726246427295",
    appId: "1:726246427295:web:ade4be318697c97f0930aa"
};

// Initialize Firebase
const app = initializeApp(firebaseConfig);
const auth = getAuth(app);
const database = getDatabase(app);

export { app, auth, database };