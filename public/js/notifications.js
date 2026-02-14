/**
 * Notification System using SweetAlert2
 */

const Notifications = {
    /**
     * Show toast notification
     */
    toast(type, message, duration = 4000) {
        if (typeof Swal !== 'undefined') {
            Swal.fire({
                icon: type,
                title: message,
                toast: true,
                position: 'top-end',
                showConfirmButton: false,
                timer: duration,
                timerProgressBar: true,
                didOpen: (toast) => {
                    toast.addEventListener('mouseenter', Swal.stopTimer);
                    toast.addEventListener('mouseleave', Swal.resumeTimer);
                }
            });
        } else {
            alert(message);
        }
    },

    /**
     * Show loading modal
     */
    loading(message = 'Processing...') {
        if (typeof Swal !== 'undefined') {
            Swal.fire({
                title: message,
                html: '<div class="animate-pulse">Please wait...</div>',
                allowOutsideClick: false,
                allowEscapeKey: false,
                showConfirmButton: false,
                didOpen: () => {
                    Swal.showLoading();
                }
            });
        }
    },

    /**
     * Close any open notification
     */
    close() {
        if (typeof Swal !== 'undefined') {
            Swal.close();
        }
    },

    /**
     * Confirmation dialog
     */
    confirm(title, message, confirmText = 'Confirm', cancelText = 'Cancel') {
        if (typeof Swal !== 'undefined') {
            return Swal.fire({
                title: title,
                text: message,
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: confirmText,
                cancelButtonText: cancelText
            });
        } else {
            return Promise.resolve({ isConfirmed: confirm(message) });
        }
    },

    /**
     * Async action with automatic loading/success/error handling
     */
    async withLoading(asyncFn, loadingMsg, successMsg, errorMsg) {
        this.loading(loadingMsg);
        
        try {
            const result = await asyncFn();
            this.close();
            this.toast('success', successMsg);
            return result;
        } catch (error) {
            this.close();
            this.toast('error', errorMsg || error.message);
            throw error;
        }
    }
};

// Export for use
window.Notifications = Notifications;