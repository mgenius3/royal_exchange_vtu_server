// document.addEventListener('DOMContentLoaded', function () {
//     const tabs = document.querySelectorAll('.nav-tabs .nav-link');
//     const tableRows = document.querySelectorAll('tbody tr');

//     // Function to filter rows based on status
//     const filterRowsByStatus = (status) => {
//         tableRows.forEach(row => {
//             const rowStatus = row.getAttribute('data-status');
//             if (status === 'all' || rowStatus === status) {
//                 row.style.display = ''; // Show the row
//             } else {
//                 row.style.display = 'none'; // Hide the row
//             }
//         });
//     };

//     // Add click event listeners to tabs
//     tabs.forEach(tab => {
//         tab.addEventListener('click', function (e) {
//             e.preventDefault();
//             const status = this.getAttribute('href').replace('#', ''); // Get status from href (e.g., "allTab" -> "all")
//             filterRowsByStatus(status.replace('Tab', '')); // Remove "Tab" suffix
//         });
//     });
// });

// document.addEventListener('DOMContentLoaded', function () {
//     const statusDropdownItems = document.querySelectorAll('.dropdown-menu .dropdown-item');
//     const sortDropdownItems = document.querySelectorAll('.dropdown-menu-end .dropdown-item');
//     const tableRows = document.querySelectorAll('tbody tr');

//     // Function to filter rows based on status
//     const filterRowsByStatus = (status) => {
//         tableRows.forEach(row => {
//             const rowStatus = row.getAttribute('data-status');
//             if (status === 'all' || rowStatus === status) {
//                 row.style.display = ''; // Show the row
//             } else {
//                 row.style.display = 'none'; // Hide the row
//             }
//         });
//     };

//     // Function to sort rows based on criteria
//     const sortRowsByCriteria = (criteria) => {
//         const rowsArray = Array.from(tableRows);

//         rowsArray.sort((a, b) => {
//             const aValue = a.querySelector(`td[data-${criteria}]`).textContent;
//             const bValue = b.querySelector(`td[data-${criteria}]`).textContent;

//             if (criteria === 'date-joined' || criteria === 'last-login') {
//                 return new Date(aValue) - new Date(bValue); // Sort by date
//             } else {
//                 return aValue.localeCompare(bValue); // Sort by name
//             }
//         });

//         // Re-append sorted rows to the table
//         const tbody = document.querySelector('tbody');
//         tbody.innerHTML = ''; // Clear the table
//         rowsArray.forEach(row => tbody.appendChild(row));
//     };

//     // Add click event listeners to status dropdown items
//     statusDropdownItems.forEach(item => {
//         item.addEventListener('click', function (e) {
//             e.preventDefault();
//             const status = this.textContent.toLowerCase(); // Get status from text (e.g., "Active" -> "active")
//             filterRowsByStatus(status);
//         });
//     });

//     // Add click event listeners to sort dropdown items
//     sortDropdownItems.forEach(item => {
//         item.addEventListener('click', function (e) {
//             e.preventDefault();
//             const criteria = this.textContent.toLowerCase().replace(' ', '-'); // Get criteria from text (e.g., "Date Joined" -> "date-joined")
//             sortRowsByCriteria(criteria);
//         });
//     });
// });


document.addEventListener('DOMContentLoaded', function () {
    const tableRows = document.querySelectorAll('tbody tr');
    const tbody = document.querySelector('tbody');

    // Function to filter rows based on status
    const filterRowsByStatus = (status) => {
        tableRows.forEach(row => {
            console.dir(status);
            console.log(row.getAttribute('data-status'))
            row.style.display = (status === 'all' || row.getAttribute('data-status') === status) ? '' : 'none';
        });
    };

    // Function to sort rows based on criteria
    const sortRowsByCriteria = (criteria) => {
        const tbody = document.querySelector('tbody');
        const rowsArray = Array.from(document.querySelectorAll('tbody tr'));
    
        rowsArray.sort((a, b) => {
            let aValue = a.querySelector(`td[data-${criteria}]`)?.getAttribute(`data-${criteria}`) || '';
            let bValue = b.querySelector(`td[data-${criteria}]`)?.getAttribute(`data-${criteria}`) || '';
    
            return (criteria.includes('date') || criteria.includes('login'))
                ? new Date(aValue) - new Date(bValue)
                : aValue.localeCompare(bValue);
        });
    
        tbody.innerHTML = '';
        rowsArray.forEach(row => tbody.appendChild(row));
    };
    

    // Event delegation for filtering and sorting
    document.body.addEventListener('click', function (e) {
        if (e.target.matches('.nav-tabs .nav-link, .dropdown-menu .dropdown-item')) {
            e.preventDefault();
            const target = e.target;
            if (target.closest('.nav-tabs')) {
                filterRowsByStatus(target.getAttribute('href').replace('#', '').replace('Tab', ''));
            } else if (target.closest('.dropdown-menu')) {
                if (target.closest('.dropdown-menu-end')) {
                    sortRowsByCriteria(target.textContent.toLowerCase().replace(' ', '-'));
                } else {
                    filterRowsByStatus(target.textContent.toLowerCase());
                }
            }
        }
    });

    document.querySelector('.form-control.ps-35px').addEventListener('input', function () {
        const searchTerm = this.value.toLowerCase();
        document.querySelectorAll('tbody tr').forEach(row => {
            row.style.display = row.textContent.toLowerCase().includes(searchTerm) ? '' : 'none';
        });
    });    
});
