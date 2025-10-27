export default () => {
    console.log('✅ credentialsManager inicializado');

    return {
        showAddModal: false,
        showEditModal: false,
        showPinModal: false,
        showPasswordModal: false,
        showPassword: false,
        pin: '',
        pinError: '',
        currentCredentialId: null,
        revealedPassword: '',
        copied: false,
        editingCredential: {},
        formData: {
            title: '',
            username: '',
            password: '',
            url: '',
            notes: ''
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
