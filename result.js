// Wait for DOM to be fully loaded
document.addEventListener('DOMContentLoaded', function() {
    // Debug: Check if arrays exist
    console.log('post array:', typeof post !== 'undefined' ? post : 'undefined');
    console.log('postID array:', typeof postID !== 'undefined' ? postID : 'undefined');
    
    // Check if the arrays are defined, if not, create them from the DOM
    if (typeof post === 'undefined' || typeof postID === 'undefined') {
        console.log('Arrays not found, creating from DOM...');
        
        // Get all buttons in the header
        const buttons = document.querySelectorAll('#hb button');
        const containers = document.getElementsByClassName("container");
        
        // Add click handlers directly to buttons
        buttons.forEach((button, index) => {
            button.addEventListener('click', function() {
                console.log('Button clicked:', button.id, 'Index:', index);
                
                // Hide all containers
                for (let i = 0; i < containers.length; i++) {
                    containers[i].style.display = "none";
                }
                
                // Show the container at the same index
                if (containers[index]) {
                    containers[index].style.display = "block";
                    console.log('Showing container:', containers[index].id);
                }
            });
        });
    } else {
        // Original logic if arrays are properly defined
        const containers = document.getElementsByClassName("container");
        
        for (let n = 0; n < postID.length; n++) {
            const button = document.getElementById(postID[n]);
            console.log('Setting up button:', postID[n], 'Found:', !!button);
            
            if (button) {
                button.addEventListener('click', function() {
                    console.log('Button clicked:', postID[n], 'Target:', post[n]);
                    
                    // Hide all containers
                    for (let i = 0; i < containers.length; i++) {
                        containers[i].style.display = "none";
                    }
                    
                    // Show the selected container
                    const targetContainer = document.getElementById(post[n]);
                    if (targetContainer) {
                        targetContainer.style.display = "block";
                        console.log('Showing container:', post[n]);
                    } else {
                        console.error('Target container not found:', post[n]);
                    }
                });
            } else {
                console.error('Button not found:', postID[n]);
            }
        }
    }
});