export default () => {
    console.log('✅ credentialsManager inicializado');

    return {
        showAddModal: false,
        showEditModal: false,
        showPinModal: false,
        showPinEditModal: false,
        showPinExportModal: false,
        showEncryptionPasswordModal: false,
        showDecryptionPasswordModal: false,
        showPasswordModal: false,
        showPassword: false,
        pin: '',
        pinError: '',
        pinEditError: '',
        pinExportError: '',
        encryptionPassword: '',
        encryptionPasswordConfirm: '',
        encryptionPasswordError: '',
        decryptionPassword: '',
        decryptionPasswordError: '',
        selectedFile: null,
        currentCredentialId: null,
        revealedPassword: '',
        copied: false,
        editingCredential: {},
        searchTerm: '',
        pendingEditCredential: null,
        formData: {
            title: '',
            username: '',
            password: '',
            url: '',
            notes: ''
        },

        shouldShowCredential(credentialTitle) {
            // Si el término de búsqueda tiene menos de 2 caracteres, mostrar todo
            if (this.searchTerm.trim().length < 2) {
                return true;
            }

            // Buscar solo por plataforma (title)
            return credentialTitle.toLowerCase().includes(this.searchTerm.trim().toLowerCase());
        },

        openPinModal(credentialId) {
            this.currentCredentialId = credentialId;
            this.showPinModal = true;
            this.pin = '';
            this.pinError = '';
        },

        closePinModal() {
            this.showPinModal = false;
            this.pin = '';
            this.pinError = '';
            this.currentCredentialId = null;
        },

        async verifyPin() {
            if (this.pin.length !== 4) {
                this.pinError = 'El PIN debe tener 4 dígitos';
                return;
            }

            try {
                const response = await fetch(`/credentials/${this.currentCredentialId}/verify-pin`, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                    },
                    body: JSON.stringify({
                        pin: this.pin
                    })
                });

                const data = await response.json();

                if (response.ok && data.success) {
                    this.revealedPassword = data.password;
                    this.closePinModal();
                    this.showPasswordModal = true;
                } else {
                    this.pinError = data.message || 'PIN incorrecto';
                }
            } catch (error) {
                console.error('Error:', error);
                this.pinError = 'Error al verificar el PIN';
            }
        },

        closePasswordModal() {
            this.showPasswordModal = false;
            this.revealedPassword = '';
            this.copied = false;
        },

        async copyToClipboard() {
            try {
                await navigator.clipboard.writeText(this.revealedPassword);
                this.copied = true;
                setTimeout(() => {
                    this.copied = false;
                }, 2000);
            } catch (error) {
                console.error('Error al copiar:', error);
            }
        },

        openPinEditModal(credential) {
            this.pendingEditCredential = credential;
            this.showPinEditModal = true;
            this.pin = '';
            this.pinEditError = '';
        },

        closePinEditModal() {
            this.showPinEditModal = false;
            this.pin = '';
            this.pinEditError = '';
            this.pendingEditCredential = null;
        },

        async verifyPinForEdit() {
            if (this.pin.length !== 4) {
                this.pinEditError = 'El PIN debe tener 4 dígitos';
                return;
            }

            try {
                const response = await fetch(`/credentials/${this.pendingEditCredential.id}/verify-pin`, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                    },
                    body: JSON.stringify({
                        pin: this.pin
                    })
                });

                const data = await response.json();

                if (response.ok && data.success) {
                    this.closePinEditModal();
                    this.openEditModal(this.pendingEditCredential);
                } else {
                    this.pinEditError = data.message || 'PIN incorrecto';
                }
            } catch (error) {
                console.error('Error:', error);
                this.pinEditError = 'Error al verificar el PIN';
            }
        },

        openPinExportModal() {
            this.showPinExportModal = true;
            this.pin = '';
            this.pinExportError = '';
        },

        closePinExportModal() {
            this.showPinExportModal = false;
            this.pin = '';
            this.pinExportError = '';
        },

        async verifyPinForExport() {
            if (this.pin.length !== 4) {
                this.pinExportError = 'El PIN debe tener 4 dígitos';
                return;
            }

            try {
                const response = await fetch('/credentials/verify-pin-export', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                    },
                    body: JSON.stringify({
                        pin: this.pin
                    })
                });

                const data = await response.json();

                if (response.ok && data.success) {
                    this.closePinExportModal();
                    // Mostrar modal de contraseña de encriptación
                    this.showEncryptionPasswordModal = true;
                } else {
                    this.pinExportError = data.message || 'PIN incorrecto';
                }
            } catch (error) {
                console.error('Error:', error);
                this.pinExportError = 'Error al verificar el PIN';
            }
        },

        closeEncryptionPasswordModal() {
            this.showEncryptionPasswordModal = false;
            this.encryptionPassword = '';
            this.encryptionPasswordConfirm = '';
            this.encryptionPasswordError = '';
        },

        async proceedWithExport() {
            // Validar contraseña
            if (this.encryptionPassword.length < 8) {
                this.encryptionPasswordError = 'La contraseña debe tener al menos 8 caracteres';
                return;
            }

            if (this.encryptionPassword !== this.encryptionPasswordConfirm) {
                this.encryptionPasswordError = 'Las contraseñas no coinciden';
                return;
            }

            try {
                // Hacer POST con la contraseña de encriptación
                const form = document.createElement('form');
                form.method = 'POST';
                form.action = '/credentials/export';

                const csrfToken = document.querySelector('meta[name="csrf-token"]').content;
                const csrfInput = document.createElement('input');
                csrfInput.type = 'hidden';
                csrfInput.name = '_token';
                csrfInput.value = csrfToken;
                form.appendChild(csrfInput);

                const passwordInput = document.createElement('input');
                passwordInput.type = 'hidden';
                passwordInput.name = 'encryption_password';
                passwordInput.value = this.encryptionPassword;
                form.appendChild(passwordInput);

                document.body.appendChild(form);
                form.submit();

                this.closeEncryptionPasswordModal();
            } catch (error) {
                console.error('Error:', error);
                this.encryptionPasswordError = 'Error al exportar';
            }
        },

        handleFileSelect(event) {
            const file = event.target.files[0];
            if (!file) return;

            this.selectedFile = file;

            // Si es archivo .encrypted, pedir contraseña de desencriptación
            if (file.name.endsWith('.encrypted')) {
                this.showDecryptionPasswordModal = true;
            } else {
                // Archivo JSON sin encriptar (compatibilidad con archivos antiguos)
                this.submitImportForm(null);
            }
        },

        closeDecryptionPasswordModal() {
            this.showDecryptionPasswordModal = false;
            this.decryptionPassword = '';
            this.decryptionPasswordError = '';
            this.selectedFile = null;
            // Resetear el input file
            this.$refs.importFile.value = '';
        },

        async proceedWithImport() {
            if (!this.decryptionPassword) {
                this.decryptionPasswordError = 'Debes ingresar la contraseña';
                return;
            }

            this.submitImportForm(this.decryptionPassword);
        },

        submitImportForm(decryptionPassword) {
            const form = document.createElement('form');
            form.method = 'POST';
            form.action = '/credentials/import';
            form.enctype = 'multipart/form-data';

            const csrfToken = document.querySelector('meta[name="csrf-token"]').content;
            const csrfInput = document.createElement('input');
            csrfInput.type = 'hidden';
            csrfInput.name = '_token';
            csrfInput.value = csrfToken;
            form.appendChild(csrfInput);

            // Agregar archivo
            const fileInput = document.createElement('input');
            fileInput.type = 'file';
            fileInput.name = 'file';
            fileInput.style.display = 'none';

            const dataTransfer = new DataTransfer();
            dataTransfer.items.add(this.selectedFile);
            fileInput.files = dataTransfer.files;

            form.appendChild(fileInput);

            // Si hay contraseña de desencriptación, agregarla
            if (decryptionPassword) {
                const passwordInput = document.createElement('input');
                passwordInput.type = 'hidden';
                passwordInput.name = 'decryption_password';
                passwordInput.value = decryptionPassword;
                form.appendChild(passwordInput);
            }

            document.body.appendChild(form);
            form.submit();

            this.closeDecryptionPasswordModal();
        },

        openEditModal(credential) {
            this.editingCredential = credential;
            this.formData = {
                title: credential.title,
                username: credential.username,
                password: '', // Don't pre-fill password
                url: credential.url || '',
                notes: credential.notes || ''
            };
            this.showEditModal = true;
        },

        closeModals() {
            this.showAddModal = false;
            this.showEditModal = false;
            this.editingCredential = {};
            this.formData = {
                title: '',
                username: '',
                password: '',
                url: '',
                notes: ''
            };
            this.showPassword = false;
        }
    };
};
