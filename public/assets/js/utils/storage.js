// Store data in localStorage
window.storeData = (key, data) => {
    localStorage.setItem(key, JSON.stringify(data));
};

// Retrieve data from localStorage
window.getData = (key) => {
    
    return JSON.parse(localStorage.getItem(key));
};

// Remove data from localStorage
window.removeData = (key) => {
    localStorage.removeItem(key);
};
