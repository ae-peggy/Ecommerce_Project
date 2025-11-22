/**
 * Aya Crafts Admin/Artisan shared utilities
 * Provides lightweight helpers used across dashboard pages
 */
(function windowAdminUIModule(window, document) {
  const AdminUI = {
    /**
     * Display a toast notification
     * @param {string} message
     * @param {'success'|'error'|'info'|'warning'} type
     * @param {number} duration
     */
    toast(message, type = 'info', duration = 3000) {
      const containerId = 'admin-toast-container';
      let container = document.getElementById(containerId);

      if (!container) {
        container = document.createElement('div');
        container.id = containerId;
        container.style.position = 'fixed';
        container.style.top = '20px';
        container.style.right = '20px';
        container.style.zIndex = '9999';
        container.style.display = 'flex';
        container.style.flexDirection = 'column';
        container.style.gap = '10px';
        document.body.appendChild(container);
      }

      const toast = document.createElement('div');
      toast.className = `admin-toast admin-toast-${type}`;
      toast.textContent = message;
      toast.style.padding = '12px 18px';
      toast.style.borderRadius = '8px';
      toast.style.color = '#fff';
      toast.style.fontSize = '14px';
      toast.style.boxShadow = '0 10px 25px rgba(0,0,0,0.15)';
      toast.style.opacity = '0';
      toast.style.transform = 'translateY(-10px)';
      toast.style.transition = 'opacity 0.3s ease, transform 0.3s ease';
      toast.style.background = ({
        success: 'linear-gradient(135deg, #10b981, #059669)',
        error: 'linear-gradient(135deg, #ef4444, #dc2626)',
        warning: 'linear-gradient(135deg, #f59e0b, #d97706)',
        info: 'linear-gradient(135deg, #3b82f6, #2563eb)',
      }[type] || 'linear-gradient(135deg, #6b7280, #4b5563)');

      container.appendChild(toast);

      requestAnimationFrame(() => {
        toast.style.opacity = '1';
        toast.style.transform = 'translateY(0)';
      });

      setTimeout(() => {
        toast.style.opacity = '0';
        toast.style.transform = 'translateY(-10px)';
        setTimeout(() => toast.remove(), 300);
      }, duration);
    },

    /**
     * Wrapper for fetch JSON with default headers/handling
     * @param {string} url
     * @param {RequestInit} options
     * @returns {Promise<any>}
     */
    fetchJson(url, options = {}) {
      const opts = {
        headers: {
          'X-Requested-With': 'XMLHttpRequest',
          ...options.headers,
        },
        ...options,
      };

      return fetch(url, opts).then(async (response) => {
        const data = await response.json().catch(() => ({}));
        if (!response.ok || data.success === false) {
          const message = data.message || 'An unexpected error occurred.';
          throw new Error(message);
        }
        return data;
      });
    },

    /**
     * Attach confirmation behavior to elements with data-confirm
     */
    bindConfirmations() {
      document.querySelectorAll('[data-confirm]').forEach((el) => {
        if (el.dataset.confirmBound) return;
        el.dataset.confirmBound = 'true';
        el.addEventListener('click', (event) => {
          const message = el.getAttribute('data-confirm') || 'Are you sure?';
          if (!window.confirm(message)) {
            event.preventDefault();
            event.stopPropagation();
          }
        });
      });
    },

    /**
     * Toggle modals with [data-modal-target] and [data-modal-close]
     */
    bindModals() {
      document.querySelectorAll('[data-modal-target]').forEach((trigger) => {
        trigger.addEventListener('click', (event) => {
          event.preventDefault();
          const targetId = trigger.getAttribute('data-modal-target');
          const modal = document.getElementById(targetId);
          if (modal) {
            modal.style.display = 'block';
            document.body.classList.add('modal-open');
          }
        });
      });

      document.querySelectorAll('[data-modal-close]').forEach((btn) => {
        btn.addEventListener('click', () => {
          const modal = btn.closest('.modal');
          if (modal) {
            modal.style.display = 'none';
            document.body.classList.remove('modal-open');
          }
        });
      });

      window.addEventListener('click', (event) => {
        if (event.target.classList && event.target.classList.contains('modal')) {
          event.target.style.display = 'none';
          document.body.classList.remove('modal-open');
        }
      });
    },
  };

  // Auto-init common bindings when DOM is ready
  document.addEventListener('DOMContentLoaded', () => {
    AdminUI.bindConfirmations();
    AdminUI.bindModals();
  });

  window.AdminUI = AdminUI;
}(window, document));

