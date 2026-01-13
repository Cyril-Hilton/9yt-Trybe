/**
 * 9yt !Trybe - Alpine.js Reusable Components
 * Enhanced UX Components for Better User Experience
 */

document.addEventListener('alpine:init', () => {

    // ============================================
    // TOOLTIP COMPONENT
    // ============================================
    Alpine.data('tooltip', (text, position = 'top') => ({
        show: false,
        text: text,
        position: position,

        init() {
            // Debounce show/hide for better performance
            this.showDebounced = this.debounce(() => { this.show = true; }, 200);
            this.hideDebounced = this.debounce(() => { this.show = false; }, 100);
        },

        mouseenter() {
            this.showDebounced();
        },

        mouseleave() {
            this.show = false;
        },

        debounce(func, wait) {
            let timeout;
            return function executedFunction(...args) {
                const later = () => {
                    clearTimeout(timeout);
                    func(...args);
                };
                clearTimeout(timeout);
                timeout = setTimeout(later, wait);
            };
        }
    }));

    // ============================================
    // FORM VALIDATION COMPONENT
    // ============================================
    Alpine.data('formValidation', () => ({
        fields: {},

        // Register a field
        registerField(name, rules) {
            this.fields[name] = {
                value: '',
                error: '',
                valid: false,
                rules: rules || {}
            };
        },

        // Validate a single field
        validateField(name, value) {
            const field = this.fields[name];
            if (!field) return true;

            field.value = value;
            field.error = '';
            field.valid = true;

            // Required validation
            if (field.rules.required && !value) {
                field.error = field.rules.requiredMessage || 'This field is required';
                field.valid = false;
                return false;
            }

            // Email validation
            if (field.rules.email && value) {
                const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
                if (!emailRegex.test(value)) {
                    field.error = 'Please enter a valid email address';
                    field.valid = false;
                    return false;
                }
            }

            // Min length validation
            if (field.rules.minLength && value.length < field.rules.minLength) {
                field.error = `Minimum ${field.rules.minLength} characters required`;
                field.valid = false;
                return false;
            }

            // Max length validation
            if (field.rules.maxLength && value.length > field.rules.maxLength) {
                field.error = `Maximum ${field.rules.maxLength} characters allowed`;
                field.valid = false;
                return false;
            }

            // Phone validation (Ghana format)
            if (field.rules.phone && value) {
                const phoneRegex = /^(\+233|0)[0-9]{9}$/;
                if (!phoneRegex.test(value.replace(/\s/g, ''))) {
                    field.error = 'Please enter a valid phone number';
                    field.valid = false;
                    return false;
                }
            }

            // Custom pattern validation
            if (field.rules.pattern && value) {
                const regex = new RegExp(field.rules.pattern);
                if (!regex.test(value)) {
                    field.error = field.rules.patternMessage || 'Invalid format';
                    field.valid = false;
                    return false;
                }
            }

            return true;
        },

        // Validate all fields
        validateAll() {
            let allValid = true;
            Object.keys(this.fields).forEach(name => {
                if (!this.validateField(name, this.fields[name].value)) {
                    allValid = false;
                }
            });
            return allValid;
        },

        // Get field error
        getError(name) {
            return this.fields[name]?.error || '';
        },

        // Check if field is valid
        isValid(name) {
            return this.fields[name]?.valid !== false;
        }
    }));

    // ============================================
    // NOTIFICATION/TOAST COMPONENT
    // ============================================
    Alpine.data('notifications', () => ({
        items: [],

        add(message, type = 'info', duration = 5000) {
            const id = Date.now();
            this.items.push({
                id,
                message,
                type, // success, error, warning, info
                show: false
            });

            // Trigger show animation
            setTimeout(() => {
                const item = this.items.find(i => i.id === id);
                if (item) item.show = true;
            }, 10);

            // Auto remove
            if (duration > 0) {
                setTimeout(() => this.remove(id), duration);
            }
        },

        remove(id) {
            const item = this.items.find(i => i.id === id);
            if (item) {
                item.show = false;
                setTimeout(() => {
                    this.items = this.items.filter(i => i.id !== id);
                }, 300);
            }
        },

        success(message, duration) {
            this.add(message, 'success', duration);
        },

        error(message, duration) {
            this.add(message, 'error', duration);
        },

        warning(message, duration) {
            this.add(message, 'warning', duration);
        },

        info(message, duration) {
            this.add(message, 'info', duration);
        }
    }));

    // ============================================
    // CONFIRM DIALOG COMPONENT
    // ============================================
    Alpine.data('confirmDialog', () => ({
        show: false,
        title: '',
        message: '',
        confirmText: 'Confirm',
        cancelText: 'Cancel',
        onConfirm: null,
        onCancel: null,

        open(options) {
            this.title = options.title || 'Confirm Action';
            this.message = options.message || 'Are you sure?';
            this.confirmText = options.confirmText || 'Confirm';
            this.cancelText = options.cancelText || 'Cancel';
            this.onConfirm = options.onConfirm || (() => {});
            this.onCancel = options.onCancel || (() => {});
            this.show = true;
        },

        confirm() {
            this.show = false;
            if (this.onConfirm) this.onConfirm();
        },

        cancel() {
            this.show = false;
            if (this.onCancel) this.onCancel();
        }
    }));

    // ============================================
    // COPY TO CLIPBOARD COMPONENT
    // ============================================
    Alpine.data('copyToClipboard', (text) => ({
        copied: false,

        async copy() {
            try {
                await navigator.clipboard.writeText(text);
                this.copied = true;
                setTimeout(() => {
                    this.copied = false;
                }, 2000);
            } catch (err) {
                console.error('Failed to copy:', err);
            }
        }
    }));

    // ============================================
    // CHARACTER COUNTER COMPONENT
    // ============================================
    Alpine.data('charCounter', (maxLength) => ({
        count: 0,
        max: maxLength,

        update(value) {
            this.count = value.length;
        },

        get remaining() {
            return this.max - this.count;
        },

        get percentage() {
            return (this.count / this.max) * 100;
        },

        get warningLevel() {
            const pct = this.percentage;
            if (pct >= 100) return 'over';
            if (pct >= 90) return 'critical';
            if (pct >= 75) return 'warning';
            return 'normal';
        }
    }));

    // ============================================
    // AUTO-SAVE COMPONENT
    // ============================================
    Alpine.data('autoSave', (saveCallback, delay = 2000) => ({
        status: 'saved', // saved, saving, error
        timeout: null,

        onChange() {
            this.status = 'pending';
            clearTimeout(this.timeout);
            this.timeout = setTimeout(() => {
                this.save();
            }, delay);
        },

        async save() {
            this.status = 'saving';
            try {
                if (saveCallback) {
                    await saveCallback();
                }
                this.status = 'saved';
            } catch (error) {
                this.status = 'error';
                console.error('Auto-save failed:', error);
            }
        }
    }));

    // ============================================
    // DROPDOWN COMPONENT (Enhanced)
    // ============================================
    Alpine.data('dropdown', () => ({
        open: false,

        toggle() {
            this.open = !this.open;
        },

        close() {
            this.open = false;
        },

        // Close when clicking outside
        init() {
            this.$watch('open', value => {
                if (value) {
                    // Add click outside listener
                    setTimeout(() => {
                        document.addEventListener('click', this.clickOutside.bind(this));
                    }, 10);
                } else {
                    document.removeEventListener('click', this.clickOutside.bind(this));
                }
            });
        },

        clickOutside(e) {
            if (!this.$el.contains(e.target)) {
                this.close();
            }
        }
    }));

    // ============================================
    // SEARCH WITH DEBOUNCE COMPONENT
    // ============================================
    Alpine.data('searchDebounce', (callback, delay = 500) => ({
        query: '',
        timeout: null,

        search(value) {
            this.query = value;
            clearTimeout(this.timeout);
            this.timeout = setTimeout(() => {
                if (callback) callback(this.query);
            }, delay);
        },

        clear() {
            this.query = '';
            if (callback) callback('');
        }
    }));
});
