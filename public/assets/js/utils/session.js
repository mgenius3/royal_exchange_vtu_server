// import { storeData, getData, removeData } from './storage.js';

// Store user session data (generic for any session, not just admin)
window.storeSession = (key, data) =>{
    window.storeData(key, data);
}

// Get session data (generic)
window.getSession = (key) => {
    return window.getData(key);
}

// Clear session data (generic)
window.clearSession = (key) => {
    window.removeData(key);
}
