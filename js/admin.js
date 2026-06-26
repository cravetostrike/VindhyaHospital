/**
 * VHRC Admin Panel Interactive Logic
 */

document.addEventListener('DOMContentLoaded', () => {
    // Sidebar toggle for mobile/tablet screens
    const body = document.body;
    const sidebar = document.querySelector('.admin-sidebar');
    const headerTitle = document.querySelector('.header-title-area');
    
    // Add sidebar toggle button to header on smaller screens
    if (window.innerWidth <= 991 && headerTitle) {
        const toggleBtn = document.createElement('button');
        toggleBtn.className = 'sidebar-toggle-btn';
        toggleBtn.innerHTML = `
            <svg xmlns="http://www.w3.org/2000/svg" width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                <line x1="4" x2="20" y1="12" y2="12"></line>
                <line x1="4" x2="20" y1="6" y2="6"></line>
                <line x1="4" x2="20" y1="18" y2="18"></line>
            </svg>
        `;
        toggleBtn.style.marginRight = '1rem';
        toggleBtn.style.display = 'inline-flex';
        toggleBtn.style.alignItems = 'center';
        toggleBtn.style.color = 'var(--clr-admin-brand)';
        
        headerTitle.parentNode.insertBefore(toggleBtn, headerTitle);
        
        toggleBtn.addEventListener('click', (e) => {
            e.stopPropagation();
            body.classList.toggle('sidebar-open');
        });

        // Close sidebar when clicking outside of it
        body.addEventListener('click', (e) => {
            if (body.classList.contains('sidebar-open') && !sidebar.contains(e.target)) {
                body.classList.remove('sidebar-open');
            }
        });
    }

    // Modal Control: Add Doctor Form
    const openModalBtn = document.getElementById('btnOpenAddModal');
    const addDoctorModal = document.getElementById('addDoctorModal');
    
    if (openModalBtn && addDoctorModal) {
        const closeModalBtns = addDoctorModal.querySelectorAll('.btn-close-modal, .admin-modal-overlay');
        
        openModalBtn.addEventListener('click', () => {
            addDoctorModal.classList.add('active');
            addDoctorModal.setAttribute('aria-hidden', 'false');
            body.style.overflow = 'hidden';
        });

        const closeModal = () => {
            addDoctorModal.classList.remove('active');
            addDoctorModal.setAttribute('aria-hidden', 'true');
            body.style.overflow = '';
        };

        closeModalBtns.forEach(btn => {
            btn.addEventListener('click', closeModal);
        });

        // Close on Escape key press
        window.addEventListener('keydown', (e) => {
            if (e.key === 'Escape' && addDoctorModal.classList.contains('active')) {
                closeModal();
            }
        });
    }

    // Modal Control: Edit Doctor Form
    const editDoctorModal = document.getElementById('editDoctorModal');
    const editBtns = document.querySelectorAll('.btn-open-edit-modal');
    
    if (editDoctorModal && editBtns.length > 0) {
        const closeModalBtns = editDoctorModal.querySelectorAll('.btn-close-modal, .admin-modal-overlay');
        
        editBtns.forEach(btn => {
            btn.addEventListener('click', () => {
                const id = btn.getAttribute('data-id');
                const name = btn.getAttribute('data-name');
                const specialty = btn.getAttribute('data-specialty');
                const qualifications = btn.getAttribute('data-qualifications');
                const experience = btn.getAttribute('data-experience');
                const fb = btn.getAttribute('data-social-fb');
                const tw = btn.getAttribute('data-social-tw');
                const ig = btn.getAttribute('data-social-ig');
                const linkedin = btn.getAttribute('data-social-in');
                
                // Pre-fill fields
                document.getElementById('editDocId').value = id;
                document.getElementById('editDocName').value = name;
                document.getElementById('editDocSpecialty').value = specialty;
                document.getElementById('editDocQuals').value = qualifications;
                document.getElementById('editDocExp').value = experience;
                document.getElementById('editDocFb').value = fb;
                document.getElementById('editDocTw').value = tw;
                document.getElementById('editDocIg').value = ig;
                document.getElementById('editDocIn').value = linkedin;
                
                // Reset custom file input labels
                const customLabel = document.getElementById('editDocPhotoLabel');
                if (customLabel) {
                    customLabel.textContent = "Choose Photo";
                    customLabel.style.borderColor = '';
                    customLabel.style.color = '';
                }
                
                editDoctorModal.classList.add('active');
                editDoctorModal.setAttribute('aria-hidden', 'false');
                body.style.overflow = 'hidden';
            });
        });

        const closeModal = () => {
            editDoctorModal.classList.remove('active');
            editDoctorModal.setAttribute('aria-hidden', 'true');
            body.style.overflow = '';
        };

        closeModalBtns.forEach(btn => {
            btn.addEventListener('click', closeModal);
        });

        // Close on Escape key press
        window.addEventListener('keydown', (e) => {
            if (e.key === 'Escape' && editDoctorModal.classList.contains('active')) {
                closeModal();
            }
        });
    }

    // Confirmation dialogues for critical actions
    const confirmActions = document.querySelectorAll('.confirm-action');
    confirmActions.forEach(element => {
        element.addEventListener('click', (e) => {
            const message = element.getAttribute('data-confirm-message') || "Are you sure you want to perform this action?";
            if (!confirm(message)) {
                e.preventDefault(); // Stop form/link execution
            }
        });
    });

    // Custom File input labels (updates text preview when a file is selected)
    const fileInputs = document.querySelectorAll('input[type="file"]');
    fileInputs.forEach(input => {
        input.addEventListener('change', (e) => {
            const fileName = e.target.files[0] ? e.target.files[0].name : "Choose File";
            const customBtn = input.previousElementSibling;
            if (customBtn && customBtn.classList.contains('custom-file-btn')) {
                customBtn.textContent = fileName;
                customBtn.style.borderColor = 'var(--clr-admin-accent)';
                customBtn.style.color = 'var(--clr-admin-brand)';
            }
        });
    });

    // Tab switching logic for Homepage CMS editor page
    const tabButtons = document.querySelectorAll('.cms-tab-btn');
    const tabContents = document.querySelectorAll('.cms-tab-content');

    if (tabButtons.length > 0 && tabContents.length > 0) {
        tabButtons.forEach(btn => {
            btn.addEventListener('click', () => {
                const targetTab = btn.getAttribute('data-tab');

                // Toggle active class on nav buttons
                tabButtons.forEach(b => b.classList.remove('active'));
                btn.classList.add('active');

                // Toggle active class on content containers
                tabContents.forEach(content => {
                    if (content.id === `tab-${targetTab}`) {
                        content.classList.add('active');
                    } else {
                        content.classList.remove('active');
                    }
                });
            });
        });
    }
});
