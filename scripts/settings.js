// Handle tab switching to ensure proper color changes
                    document.addEventListener('DOMContentLoaded', function() {
                        const tabButtons = document.querySelectorAll('[data-bs-toggle="tab"]');
                        
                        tabButtons.forEach(button => {
                            button.addEventListener('shown.bs.tab', function() {
                                // Remove pink color from all tabs
                                tabButtons.forEach(tab => {
                                    tab.style.color = '';
                                });
                                
                                // Apply pink color to inactive tabs
                                tabButtons.forEach(tab => {
                                    if (!tab.classList.contains('active')) {
                                        tab.style.color = '#ff6b6b';
                                    }
                                });
                            });
                        });
                    });