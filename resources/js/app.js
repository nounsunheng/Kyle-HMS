import './bootstrap';
import Alpine from 'alpinejs';

// Global Alpine Data Components
Alpine.data('toast', () => ({
    visible: false,
    message: '',
    type: 'success',

    show(message, type = 'success') {
        this.message = message;
        this.type = type;
        this.visible = true;

        setTimeout(() => {
            this.visible = false;
        }, 5000);
    },

    hide() {
        this.visible = false;
    }
}));

Alpine.data('imagePreview', () => ({
    imageUrl: null,

    previewImage(event) {
        const file = event.target.files[0];

        if (file) {
            const reader = new FileReader();

            reader.onload = (e) => {
                this.imageUrl = e.target.result;
            };

            reader.readAsDataURL(file);
        }
    },

    clearPreview() {
        this.imageUrl = null;
    }
}));

Alpine.data('tabs', (defaultTab = 0) => ({
    activeTab: defaultTab,

    switchTab(index) {
        this.activeTab = index;
    },

    isActive(index) {
        return this.activeTab === index;
    }
}));

Alpine.data('notification', () => ({
    notifications: [],

    add(message, type = 'info', duration = 5000) {
        const id = Date.now();
        this.notifications.push({ id, message, type });

        if (duration > 0) {
            setTimeout(() => {
                this.remove(id);
            }, duration);
        }
    },

    remove(id) {
        this.notifications = this.notifications.filter(n => n.id !== id);
    }
}));

// Utility Functions
window.formatDate = (date) => {
    return new Date(date).toLocaleDateString('en-US', {
        year: 'numeric',
        month: 'long',
        day: 'numeric'
    });
};

window.formatTime = (time) => {
    return new Date('1970-01-01T' + time).toLocaleTimeString('en-US', {
        hour: '2-digit',
        minute: '2-digit',
        hour12: true
    });
};

// Initialize Alpine
window.Alpine = Alpine;
Alpine.start();

// Global Event Listeners
document.addEventListener('DOMContentLoaded', () => {
    // Auto-hide flash messages
    const flashMessages = document.querySelectorAll('.flash-message');
    flashMessages.forEach(message => {
        setTimeout(() => {
            message.style.opacity = '0';
            setTimeout(() => {
                message.remove();
            }, 300);
        }, 5000);
    });
});

console.log('Kyle-HMS JavaScript Loaded ✅');
