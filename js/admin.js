function showPasswordModal() {
                document.getElementById('passwordModal').style.display = 'block';
            }

            function showUpdateModal(id, currentStatus) {
                document.getElementById('update_saran_id').value = id;
                document.getElementById('status').value = currentStatus;
                document.getElementById('updateModal').style.display = 'block';
            }

            function deleteSaran(id) {
                document.getElementById('delete_saran_id').value = id;
                document.getElementById('deleteModal').style.display = 'block';
            }

            function showFullMessage(message) {
                document.getElementById('fullMessageContent').textContent = message;
                document.getElementById('messageModal').style.display = 'block';
            }

            function closeModal(modalId) {
                document.getElementById(modalId).style.display = 'none';
            }

            window.onclick = function(event) {
                const modals = ['updateModal', 'deleteModal', 'messageModal', 'passwordModal'];
                modals.forEach(modalId => {
                    const modal = document.getElementById(modalId);
                    if (event.target == modal) {
                        modal.style.display = 'none';
                    }
                });
            }

            document.getElementById('confirm_password').addEventListener('input', function() {
                const newPassword = document.getElementById('new_password').value;
                const confirmPassword = this.value;
                
                if (newPassword !== confirmPassword) {
                    this.setCustomValidity('Password tidak cocok');
                } else {
                    this.setCustomValidity('');
                }
            });

            function showUpdateModal(id, currentStatus) {
                document.getElementById('update_saran_id').value = id;
                document.getElementById('status').value = currentStatus;
                document.getElementById('updateModal').style.display = 'block';
            }

            function deleteSaran(id) {
                document.getElementById('delete_saran_id').value = id;
                document.getElementById('deleteModal').style.display = 'block';
            }

            function showFullMessage(message) {
                document.getElementById('fullMessageContent').innerHTML = message.replace(/\n/g, '<br>');
                document.getElementById('messageModal').style.display = 'block';
            }

            function showPasswordModal() {
                document.getElementById('passwordModal').style.display = 'block';
            }

            function closeModal(modalId) {
                document.getElementById(modalId).style.display = 'none';
            }

            window.onclick = function(event) {
                if (event.target.classList.contains('modal')) {
                    event.target.style.display = 'none';
                }
            }

            setTimeout(function() {
                const messages = document.querySelectorAll('.message');
                messages.forEach(function(message) {
                    message.style.opacity = '0';
                    setTimeout(function() {
                        message.style.display = 'none';
                    }, 300);
                });
            }, 5000);

            document.addEventListener('keydown', function(event) {
                if (event.key === 'Escape') {
                    const modals = document.querySelectorAll('.modal');
                    modals.forEach(function(modal) {
                        modal.style.display = 'none';
                    });
                }
            });